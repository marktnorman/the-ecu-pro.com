<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class weight_based_packing{

	/**
	 * WooCommerce Store Weight Unit.
	 */
	public static $store_weight_unit;

	function __construct($product_weight,$product_quantity,$packing_process,$box_max_weight,$calculator_class,$product_price = 0) {

		if(empty($box_max_weight))
		{
			$box_max_weight=99999;
		}

		if(isset($calculator_class->settings['volumatric_weight']) && $calculator_class->settings['volumatric_weight'] == 'yes'){
			$product_weight = $this->xa_get_volumatric_products_weight($product_weight);
		}
		$this->api_rates 				= $calculator_class->api_rates;
		$this->only_flat_rate 	=	false;
		$this->dim_unit 				=	"IN";
		$this->weight_unit 			=	"LBS";
		$this->flatrate 				= array();
		$this->product_weight 	= apply_filters('xa_multi_carrier_product_weights', $product_weight, $product_quantity);
		$this->product_quantity = $product_quantity;
		$this->product_price 	= $product_price;
		$this->packing_process 	= $packing_process;
		$this->calculator_class = $calculator_class;
		$this->box_max_weight 	= $box_max_weight;
		$this->debug 					= $calculator_class->debug;
		$this->seq_no 					= 0;
		$this->packages 				= array();
		$this->totalcost 				= 0;
		$this->request_packages 		= array();

		$this->sort_weight_by_packing_process($packing_process);
		$this->init();

		//var_dump($packing_process);

		foreach($this->calculator_class->product_rule_mapping  as $rule_no=>$pids)
		{
			$company= $this->calculator_class->rules_array[$rule_no]['shipping_companies'];

			if($company=='flatrate')
			{
				$service= '';
			}else{
				$service= $this->calculator_class->rules_array[$rule_no]['shipping_services'];
			}

			if(isset($pids['validforall'])) 
			{
				unset($pids['validforall']);
			}

			$this->createPackageAndAddToRequest($rule_no,$pids,$box_max_weight,$company,$service,$packing_process);
		}

		$this->create_req_from_packages();

		// $this->debug( 'Packages: <pre>' . print_r( $this->packages , true ) . '</pre>' );
	}

	/**
	 * Get Volumetric Weight.
	 * 
	 */
	private function xa_get_volumatric_products_weight( $weight_array ){

		if( empty(self::$store_weight_unit) ) {
			self::$store_weight_unit = get_option('woocommerce_weight_unit');
		}

		foreach ($weight_array as $product_id => &$product_weight) {
			$product            = wc_get_product($product_id);
			$volume             = $this->get_volume_in_cm($product);
			$volumatric_weight  = wc_get_weight( $volume/5000, self::$store_weight_unit, 'kg' );
			if( $volumatric_weight > $product_weight ){
				$product_weight = $volumatric_weight;
			}
		}
		return $weight_array;
	}

	private function get_volume_in_cm($product){

		$dim_unit 	= get_option('woocommerce_dimension_unit');
		$volume 		= $product->get_length()*$product->get_width()*$product->get_height();

		switch ($dim_unit) {
			case 'in':
			$volume = $volume*2.54*2.54*2.54;
			break;
			case 'yd':
			$volume = $volume*91.44*91.44*91.44;
			break;
			case 'mm':
			$volume = $volume*0.1*0.1*0.1;
			break;
			case 'm':
			$volume = $volume*100*100*100;
			break;
		}
		return $volume;
	}

	function createPackageAndAddToRequest($rule_no,$pids,$box_wt_max,$company,$service,$packing_process)
	{

		$package 			= array();
		$product_ids 	= array();
		$box_wt_empty = $box_wt_max;

		if($packing_process=='pack_simple')  
		{
			$total_weight	= 0;
			$price  		= 0;

			foreach($pids as $key=>$pid)
			{   
				if(array_search($pid, $this->calculator_class->executed_products)===false) 
				{
					$qnty 	= $this->product_quantity[$pid];
					$wt 	= $this->product_weight[$pid];
					$price 	+= $this->product_price[$pid] * $qnty; 		

					// Check for Prepacked
					$prepacked	= get_post_meta( $pid, '_ph_multi_carrier_pre_packed', true);

					if( $prepacked == 'yes' ) {

						$prepacked_price 	= $this->product_price[$pid] * $qnty;
						$price 				-= $prepacked_price;

						$this->add_pre_packed_product_to_package( $pid, $qnty, $company, $rule_no, $prepacked_price );
						continue;
					}

					$total_weight+=$wt * $qnty * 1.0;                                                    
				}else{
					unset($pids[$key]);
				}
			}

			if( $rule_no!==NULL && ! empty($total_weight) )
			{ 
				$this->create_packages_purely_divided_by_weight($pids,$total_weight,$company,$service,$rule_no,$box_wt_max,$price);
			}
		}
		else
		{     
			$price  		= 0;

			foreach($pids as $key=>$pid)
			{   
				if(array_search($pid, $this->calculator_class->executed_products)!==false) 
				{
					unset($pids[$key]);
					continue;
				}
				$qnty	= $this->product_quantity[$pid];
				$wt		= $this->product_weight[$pid];
				
				// Check for Prepacked
				$prepacked	= get_post_meta( $pid, '_ph_multi_carrier_pre_packed', true);

				if( $prepacked == 'yes' ) {

					$prepacked_price 	= $this->product_price[$pid] * $qnty;

					$this->add_pre_packed_product_to_package( $pid, $qnty, $company, $rule_no, $prepacked_price );

					continue;
				}

				// Directly Add Product as Package if Product Weight is more than Max Package Weight
				if($wt>=$box_wt_max)
				{  

    				$price 	+= $this->product_price[$pid] * $qnty; 	
					$this->add_to_package(array($pid),$wt,$company,$service,$qnty,$rule_no,$price);
				}
				else 	// Loop through Quantity to create Package 
				{   
					for($i=0;$i<$qnty;$i++)
					{
						// Goto Statment
						weight_based:
							
						if( !isset($package[$pid]) ) {  $package[$pid]=1;  }
						
						// Check if Box Empty Weight (Remaining Max Pacakge Weight) is greater than current product weight
						if($box_wt_empty>=$wt)
						{

							$package[$pid]++;
							$box_wt_empty 		-= $wt;							// Reduce remaining Max Pacakge Weight by Prodcut Weight
							$check_if_last_key 	 = array_keys($pids);			// Create array keys of Product Ids to check last product
							$price 				+= $this->product_price[$pid]; 	// Add each product price

							// Add package when final product is executed
							if($i+1==$qnty && end($check_if_last_key)==$key)
							{
								
								$box_wt 	= $box_wt_max - $box_wt_empty;	// Final Box/Package Weight

								$this->add_to_package(array_keys($package) ,$box_wt,$company,$service,1,$rule_no,$price);

								// Afetr adding Package, Reset the Variables
								$box_wt_empty 	= $box_wt_max;
								$package 		= array();                                       
							}
						}
						// When adding each Product Quantity, if current product weight exceeds Box Empty Weight (Remaining Max Pacakge Weight) add Previously created Package with Proper pid
						else
						{

							// Unset current package pid when adding previously created package
							if($package[$pid]==1)
							{
								unset($package[$pid]);
							}
							
							$box_wt 	=	$box_wt_max - $box_wt_empty;	// Final Box/Package Weight

							$this->add_to_package(array_keys($package) ,$box_wt,$company,$service,1,$rule_no,$price);

							// Afetr adding Package, Reset the Variables
							$box_wt_empty 	= $box_wt_max;
							$package 		= array();
							$price 			= 0;
							
							// Repeat the procedure with current pid
							goto weight_based;
						}
					}
				}
			}

			// Check $package contains any Package Data then add it to package
			// Scenario: When last item added is pre-packed and the above loop will not execute because end key of $check_if_last_key will not match $key of $pids, because pre-packed condition will continue the loop
			if ( !empty($package) ) {

				$box_wt 	= $box_wt_max - $box_wt_empty; // Final Box/Package Weight

				$this->add_to_package(array_keys($package) ,$box_wt,$company,$service,1,$rule_no,$price);
			}
			
		}
	}

	/**
	 * Add Prepacked Items to package.
	 * @param int $product_id Product Id.
	 * @param int $quantity Quantity
	 * @return void
	 */
	public function add_pre_packed_product_to_package( $product_id, $quantity, $company, $rule_no, $price = 0 ){

		$product = !empty($this->calculator_class->products_arr[$product_id]) ? $this->calculator_class->products_arr[$product_id] : wc_get_product($product_id);

		if( $company == 'fedex' )	$rounding_digit = 0;

		$length = $product->get_length();
		$width  = $product->get_width();
		$height = $product->get_height();
		$weight = $product->get_weight();

		$this->packages[] = array(
			"pid"					=> $product_id,
			"weight"			=> $weight,
			"length"			=> $length,
			"width"				=> $width,
			"height"			=> $height,
			"company"			=> $company,
			"service"			=> $this->calculator_class->rules_array[$rule_no]['shipping_services'],
			"no_of_packages"	=> $quantity,
			"rule_no"			=> $rule_no,
			"line_total" 		=> $price,
			"insured_amount" 	=> $price,
		);
		$this->calculator_class->executed_products[] = $product_id;
	}
	
	private  function add_to_package($in_box_products_pids,$box_weight,$company,$service,$no_of_package,$rule_no,$price = 0)
	{
		if( $company == 'flatrate' )
		{
			foreach($in_box_products_pids as $_pid)
			{
				$this->flatrate[$rule_no][$_pid] = $this->calculator_class->rules_array[$rule_no]['fee'];
				if(array_search($_pid, $this->calculator_class->executed_products)===false) 
				{                     
					$this->calculator_class->executed_products[]=$_pid;
				}                           
			}              
		}
		else
		{
			$this->packages[] = array(
				"pid" 					=> implode(",", $in_box_products_pids),
				"weight" 				=> $box_weight,
				"company" 				=> $company,
				"service" 				=> $service,
				"no_of_packages" 		=> $no_of_package,
				"rule_no" 				=> $rule_no,
				"line_total" 			=> $price,
				"insured_amount" 		=> $price,
			);

			foreach($in_box_products_pids as $_pid)
			{
				if(array_search($_pid, $this->calculator_class->executed_products)===false) 
				{                     
					$this->calculator_class->executed_products[]=$_pid;                                                       
				}                           
			} 
		}      
	}

	function create_packages_purely_divided_by_weight($pids,$total_weight,$company,$service,$rule_no,$box_max_weight,$price = 0)
	{
		if($company=='flatrate')
		{
			foreach($pids as $_pid)
			{
				$this->flatrate[$rule_no][$_pid] = $this->calculator_class->rules_array[$rule_no]['fee'];                     
			}              
		}
		else
		{
			while($total_weight>0)
			{
				$wt 					 = ($total_weight>$box_max_weight)?$box_max_weight:$total_weight;
				$total_weight -= $wt;

				$this->packages[] = array(
					"pid" 					=> implode(',',$pids),
					"weight" 				=> round($wt, 2),
					"company" 				=> $company,
					"service"	 			=> $service,
					"no_of_packages"		=> 1,
					"rule_no" 				=> $rule_no,
					"line_total" 			=> $price,
					"insured_amount" 		=> $price,
				);

			}            
		}
		foreach($pids as $_pid)
		{
			if(array_search($_pid, $this->calculator_class->executed_products)===false) 
			{                     
				$this->calculator_class->executed_products[]=$_pid;
			}                  
		}
	}

	function sort_weight_by_packing_process()
	{

		if($this->packing_process=='pack_descending')         // heavier first
		{
			arsort($this->product_weight);
		}
		elseif($this->packing_process=='pack_ascending')       // lighter first
		{
			asort($this->product_weight);
		}
	}

	private function init()
	{   
		$settings= $this->calculator_class->settings;
		$is_residential='false';
		if(isset($settings['is_recipient_address_residential']))
		{
			$is_residential=$settings['is_recipient_address_residential']=='yes'?'true':'false';
		}
		if($this->calculator_class->is_residential==1)
		{
			$is_residential='true';
		}

		if($settings['test_mode']=="yes")
		{
			$environment='sandbox';
		}
		else
		{
			$environment='live';
		}
		$cdate=getdate();

		$this->request=array(
			'Common_Params'=>array(
				'environment' => $environment,
				'emailid'=>$settings['emailid'],
				'key'=>$settings['apikey'],
				'host'=>    $_SERVER['HTTP_HOST']." ".$cdate['mday'],
				'os'=>php_uname() ,
				'currency'=>get_option('woocommerce_currency'),

				'Recipient_PersonName' => 'Recipient Name',
				'Recipient_CompanyName' => '',
				'Recipient_PhoneNumber' => '',
				'Recipient_Address_StreetLines' => $this->calculator_class->address,
				'Recipient_Address_City' =>$this->calculator_class->city,
				'Recipient_Address_StateOrProvinceCode' => $this->calculator_class->state,
				'Recipient_Address_PostalCode' =>$this->calculator_class->postalcode,
				'Recipient_Address_CountryCode' =>$this->calculator_class->country,
				'Recipient_Address_CountryName' =>$this->calculator_class->country_name,
				'Recipient_Address_Residential' => $is_residential,
			),

			'Request_Array'=>array(
				/* array(
					'id'=>'1',
					'company'=>array($this->comapny),
					'Weight_Units' => 'LB',
					'Weight_Value' => '50',
					'Dimensions_Length' => '108',
					'Dimensions_Width' => '5',
					'Dimensions_Height' => '5',
					'Dimensions_Units' => 'IN',
					'TotalWeight_Units' => 'LB',
					'TotalWeight_Value' => '50',
					"ServiceType"=> $this->service,
					//"RateRequestTypes"=> "LIST",
				),
				array(
					'id'=>'2',
					'company'=>array('fedex','ups'),
					'Weight_Units' => 'LB',
					'Weight_Value' => '40',
					'Dimensions_Length' => '108',
					'Dimensions_Width' => '5',
					'Dimensions_Height' => '5',
					'Dimensions_Units' => 'IN',
					'TotalWeight_Units' => 'LB',
					'TotalWeight_Value' => '40'
				), */
			),
		);

		$this->request = $this->calculator_class->xa_set_origin_address( $this->request );
		$this->request = $this->calculator_class->xa_set_carriers_accounts( $this->request );
	}


	function create_req_from_packages()
	{   

		$this->seq_no += 1;   
		$packages 		 = array();
		$seq_no 			 = 0;
		$PackageCount  = 0;

		if(count($this->packages)==0)
		{
			$this->only_flat_rate=true;
			return 0;
		}

		foreach( $this->packages as $_package)
		{   
			unset($packages);
			$packages=array();
			$seq_no+=1;
			$PackageCount+=1;
			$no_of_packages=$_package['no_of_packages'];
			$company=$_package['company'];
			$service=$_package['service'];
			$line_total=$_package['line_total'];
			$insured_amount=$_package['insured_amount'];
			$service=$service;
			$container=null;

			if(strpos($service,'REGIONALRATEBOXA') !== false )
			{
				$service_arrayA=explode("REGIONALRATEBOXA", $service);
				if(isset($service_arrayA[1]))
				{
					$service=$service_arrayA[0];
					$container='REGIONALRATEBOXA';
				}
			}
			elseif (strpos($service,'REGIONALRATEBOXB') !== false) {
				$service_arrayB=explode("REGIONALRATEBOXB", $service);
				if(isset($service_arrayB[1]))
				{
					$service=$service_arrayB[0];
					$container='REGIONALRATEBOXB';
				}
			}
			$weight=$_package['weight'];
			$pid=$_package['pid'];
			$rule_no=$_package['rule_no'];

			$package_limit=25;
			if($company=='ups')
			{
				$package_limit=200;
				$dimension_rounding = 0;
			}
			elseif($company=='fedex')
			{
				$package_limit=999;
				$dimension_rounding = 0;
			}elseif($company=='usps')
			{
				$package_limit=25;
				$dimension_rounding = 2;
			}
			elseif($company=='stamps_usps')
			{
				$package_limit=100;
				$dimension_rounding = 2;
			}
			elseif($company=='dhl')
			{
				$package_limit=99;
				$dimension_rounding = 2;
			}

			//echo "no of package=$no_of_package  and package limit=$package_limit";
			//if($no_of_package>$package_limit)
			//{
			while($no_of_packages>0)
			{   
				$packages	=	array();

				$package_details = array(
					"weight"			=>	$this->convert_to_lbs($weight),
					"unit"				=>	'LBS' ,                //ups
					"Description"		=>	"My Package",
					'no_of_packages'	=>	($no_of_packages>$package_limit) ? $package_limit : $no_of_packages,
					'SequenceNumber'	=>	$seq_no,
					'line_subtotal' 	=>	$line_total,
					'insured_amount' 	=>	$insured_amount,
				);

				// Handle Prepacked Case
				if( isset($_package['length']) ) {
					$package_details["length"]	=	number_format( (float) wc_get_dimension( $_package['length'], $this->dim_unit ), $dimension_rounding, '.', '');
					$package_details["width"]		=	number_format( (float) wc_get_dimension( $_package['width'], $this->dim_unit ), $dimension_rounding, '.', '');
					$package_details["height"]	=	number_format( (float) wc_get_dimension( $_package['height'], $this->dim_unit ), $dimension_rounding, '.', '');
				}

				$packages[] = $package_details;

				$this->request['Request_Array'][]=array(
					'id' 								=> "$pid:$rule_no",
					'company'						=> array($company),
					'Weight_Units' 			=> 'LB',             //fedex
					"ServiceType"				=> $service,
					'Container'					=> $container,
					"RateRequestTypes" 	=> "NONE",     // fedex request type for(account rate or list rates)       here writing list is not working correctly it adds both account and list
					//"PackageCount" 		=> $PackageCount,
					"packages" 					=> $packages
				);

				$this->request_packages[] = $packages;

				$seq_no=$seq_no+1;

				if($no_of_packages>$package_limit)                                      
				{
					$no_of_packages-=$package_limit;
				}
				else
				{
					$no_of_packages=0;
				}                                        
			}
			//}

		}
	}

/*        
	function add_product_to_req($pid,$rule_no,$company,$service)
	{             
		$this->seq_no+=1;
		//$id=count($this->request['Request_Array']);                        
		//$id+=1;
		$packages=array();
		$packages[]=array(
				"weight"=>$this->product_weight[$pid],
				"unit"=> 'LBS' ,                //ups
				"Description"=>"My Package",
				'no_of_packages'=>$this->product_quantity[$pid],
				'SequenceNumber'=> $this->seq_no,
		);
	
	 	$this->request['Request_Array'][]=array(    
	 		'id'=>"$pid:$rule_no",
			'company'=>array($company),
			'Weight_Units' => 'LB',             //fedex
			"ServiceType"=> $service,
			"RateRequestTypes"=>"NONE",     // fedex request type for(account rate or list rates)       here writing list is not working correctly it adds both account and list
			"PackageCount"=>$this->product_quantity[$pid],
			"packages"=>$packages
		);
	}
*/

	function convert_to_lbs($weight)
	{
		$shop_unit=get_option('woocommerce_weight_unit');
		if($shop_unit=="lbs")
		{
			return $weight;
		}
		elseif($shop_unit=="kg")
		{
			$weight=floatval($weight) * 2.20462262185;
		}
		elseif($shop_unit=="g")
		{
			$weight=floatval($weight) * 0.00220462262185;
		}
		elseif($shop_unit=="oz")
		{
			$weight=floatval($weight) / floatval(16);
		}
		return $weight;
	}

	function get_Rates_From_Api()
	{     
		$cost = 0;
		$flag = false;

		if($this->only_flat_rate==false)
		{                
			//if($this->debug=='yes') {echo "<pre>Request"; print_r($this->request); echo "</pre>"; }

			$this->request 	= apply_filters( 'xa_multicarrier_rate_request', $this->request );
			$settings 		= $this->calculator_class->settings;

			// $this->debug( 'Request: <pre>' . print_r( $this->request , true ) . '</pre>' );
			
			if( isset($settings['legacy_api_support']) && !empty($settings['legacy_api_support']) && $settings['legacy_api_support'] == 'yes' )
			{
				$url 	= $GLOBALS['eha_API_URL']."/api/shippings/rates";
			}else{
				$url 	= $GLOBALS['eha_API_URL']."/api/v2/shippings/rates";
			}

			$diff_package 		= 0;
			$content 			= json_encode($this->request);
			$request_packages 	= json_encode($this->request_packages);
			$request_packages 	= $this->encode($this->request['Common_Params']['key'],$request_packages);

			// $this->debug("JSON Request<pre>Request: ".$content." </pre>");

			if(isset($this->calculator_class->api_rates))
			{

				$api_rates 		= $this->calculator_class->api_rates; // All possible API Rates for a carrier
				$company 		= $this->request['Request_Array'][0]['company'][0];
				$ServiceType 	= $this->request['Request_Array'][0]['ServiceType'];

				if( isset($api_rates[$company]['request_packages']) )
				{
					$diff_package = strcmp($request_packages, $api_rates[$company]['request_packages']);
				}

				if( isset($api_rates[$company]) && isset($api_rates[$company][$ServiceType]) && $diff_package == 0 )
				{
					$pid_ruleno 	= $this->request['Request_Array'][0]['id'];
					$exp_array 		= explode(":",$pid_ruleno);
					$pid 			= $exp_array[0];

					if( isset($exp_array[1]) )
					{
						$rule_no 												= $exp_array[1];
						$this->calculator_class->rules_executed_successfully[]	= $rule_no;
					}

					$cost 	= $api_rates[$company][$ServiceType]['TotalNetChargeWithDutiesAndTaxes']['Amount'];
					$flag 	= true;

				}
				// To restrict the API hit if rate is not found and debug mode is off
				elseif ( isset($api_rates[$company]) && isset($api_rates[$company]['rate_result']) && (!isset($api_rates[$company][$ServiceType]) && !$this->debug ) && $diff_package == 0 ) 
				{
					$flag = true;

				}

			}

			if( !$flag )
			{
				$this->debug( 'Packages <pre>' . print_r( $this->packages , true ) . '</pre>' );
				$this->debug( 'Request 	<pre>' . print_r( $this->request , true ) . '</pre>' );
				$this->debug("JSON Request <pre>Request: ".$content." </pre>");

				$req 							= array();
				$req['emailid'] 	= $this->request['Common_Params']['emailid'];
				$req['data'] 			= $this->encode($this->request['Common_Params']['key'],$content);
				$content  				= json_encode($req);

				$this->debug( "Encoded Request<pre>Request: ".$content." </pre>"); 

				//  $curl = curl_init($url);
				//  curl_setopt($curl, CURLOPT_HEADER, false);
				//  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				//  curl_setopt($curl, CURLOPT_HTTPHEADER,  array("Content-type: application/json"));
				//
				//  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				//  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				//  curl_setopt($curl, CURLOPT_POST, true);
				//  curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				//
				//  $json_response = curl_exec($curl);
				//
				//  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

				$response = wp_remote_post( $url, array(
					'method' => 'POST',
					'timeout' => 20,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => $content,
					'cookies' => array(),
					'sslverify' => false
				) 
			);

				if ( is_wp_error( $response ) ) {
					$error_string = $response->get_error_message();
					$this->debug( 'Response Failed: <pre>' . print_r( htmlspecialchars( $error_string ), true ) . '</pre>' );
					return false;
				}

				$status 	 			= wp_remote_retrieve_response_code($response);
				$tmp 						= $response['body'];
				$json_response 	= json_decode($tmp, true);
				$response 			= $json_response;

				if ( $status != 200 ) 
				{
					if(isset($json_response['error'])) $this->debug($json_response['error']); return 0;
					$this->debug("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
				}

				$cost=0;

				// Get USPS rate for flat rate boxes

				if( !class_exists('Wf_MC_Extend_USPS') ) {
					require_once 'class-wf-mc-extend-usps.php';
				}

				$usps_flat_rate_boxes	= new Wf_MC_Extend_USPS();
				$response 						= $usps_flat_rate_boxes->get_usps_flat_rates($this->request, $response, 'weight_based_packing');

				// End of USPS rate for flat rate boxes

				$this->debug("Response <pre>".print_r($response,true)."</pre>");

				$i 				= 0;
				$final_rates 	= array();

				if(isset($response['rates']))
				{

					foreach($response['rates'] as $PidAndRuleNo=>$rate)
					{

						//echo $PidAndRuleNo;
						$exp 	= explode(":",$PidAndRuleNo);
						$pid 	= $exp[0];

						if(isset($exp[1]))
						{
							$rule_no=$exp[1];
						}

						if(is_array($rate)){

							foreach($rate as $irate){ 

								if(is_array($irate)){

									foreach($irate as $type => $data){

										if(!is_array($data) ) {
											continue; 
										}

										if( $type=='all' && !empty($data) )
										{

											foreach($data as  $individual_rates)
											{    

												if(isset($individual_rates['TotalNetChargeWithDutiesAndTaxes']))
												{
													$service_name 	= isset($individual_rates['Service'])?$individual_rates['Service']:$i++;
													$service_name 	= str_replace(" ", ".", $service_name);
													$shipping_cost 	= $individual_rates['TotalNetChargeWithDutiesAndTaxes']['Amount'];

													if(isset($final_rates[$service_name]))
													{
														$final_rates[$service_name]['TotalNetChargeWithDutiesAndTaxes']['Amount'] += $shipping_cost;
													}
													else
													{

														$final_rates[$service_name]=$individual_rates;
													}
												}
											}

											$this->calculator_class->api_rates[$company] = $final_rates;
											$this->calculator_class->api_rates[$company]['rate_result'] = true;
											$this->calculator_class->api_rates[$company]['request_packages'] = $request_packages;
											
											continue;
										}

										foreach($data as $package_rate)
										{    
											if(isset($package_rate['TotalNetChargeWithDutiesAndTaxes']))
											{
												$cost+=$package_rate['TotalNetChargeWithDutiesAndTaxes']['Amount'];
												foreach(explode(',',$pid) as $p)
												{
													$this->calculator_class->executed_products[]=$p;
												}
												$this->calculator_class->rules_executed_successfully[]=$rule_no;
											}
										}
									}
								}
							}                         
						}
					}
				}
			}
			
			// Handle Empty rates response from API
			if( empty($cost) )	
				return false;
		}

		if( ! empty($this->flatrate) ) {
			$this->debug( 'Flat Rate Calculation: <pre>' . print_r( $this->flatrate , true ) . '</pre>' );
			$this->cart = WC()->cart;
		}

		foreach($this->flatrate as $rule_no => $f_pids)
		{
			$cost_adjustment = 0;
			$xa_multicarrier_settings = $this->calculator_class->settings;    // Multi Carrier Settings
			$cost_per_unit = $xa_multicarrier_settings['rate_matrix'][$rule_no]['cost_per_unit'] ;

			if( ! empty($cost_per_unit) ) {

				foreach( $f_pids as $product_id => $rule_flatrate ) {

					if( empty($this->product_details[$product_id]) ) {
						foreach( $this->cart->cart_contents as $line_item ) {
							if( $line_item['product_id'] == $product_id || $line_item['variation_id'] == $product_id ) {
								$this->product_details[$product_id] = array(
									'weight'  =>  $line_item['quantity'] * $line_item['data']->get_weight(),
									'item'    =>  $line_item['quantity'],
									'price'   =>  $line_item['quantity'] * $line_item['data']->get_price()
								);
							}
						}
					}

					// To add cost per Unit need to check it for possibility to move in calculator
					// Cost adjustment

					if( $xa_multicarrier_settings['rate_matrix'][$rule_no]['cost_based_on'] == 'weight' ) {
						$cost_adjustment += (float) $cost_per_unit * $this->product_details[$product_id]['weight'];
					}
					elseif( $xa_multicarrier_settings['rate_matrix'][$rule_no]['cost_based_on'] == 'item' ) {
						$cost_adjustment += (float) $cost_per_unit * $this->product_details[$product_id]['item'];
					}
					else {
						$cost_adjustment += (float) $cost_per_unit * $this->product_details[$product_id]['price'];
					}

				}
			}

			$cost+= ( (float)current($f_pids) + $cost_adjustment );
		}
		return $cost;
	}

	function encode($key,$data)
	{
		$encryptionMethod = "AES-256-CBC";
		$iv = substr($key, 0, 16);
		if (version_compare(phpversion(), '5.3.2', '>')) {
			$encryptedMessage = openssl_encrypt($data, $encryptionMethod,$key,OPENSSL_RAW_DATA,$iv);                    
		}else
		{
			$encryptedMessage = openssl_encrypt($data, $encryptionMethod,$key,OPENSSL_RAW_DATA);                    
		}
		return bin2hex($encryptedMessage);
	}

	public  function debug( $message, $type = 'notice' ) 
	{
		if ( $this->debug && !is_admin()) 
		{ 
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
				wc_add_notice( $message, $type );
			} else {
				global $woocommerce;
				$woocommerce->add_message( $message );
			}
		}
	}

}
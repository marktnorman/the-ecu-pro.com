<?php
if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}

	class box_packing{
		private $mode='volume_based';
		function __construct($product_quantity,$product_rule_mapping,$boxes,$calculator_class,$product_price = 0) {

			$this->only_flat_rate 		= false;
			$this->dim_unit 			= "IN";
			$this->weight_unit 			= "LBS";
			$this->flatrate 			= array();
			$this->product_quantity 	= $product_quantity;
			$this->product_price 		= $product_price;
			$this->calculator_class 	= $calculator_class;
			$this->debug 				= $calculator_class->debug;
			$this->seq_no 				= 0;
			$this->packages 			= array();
			$this->totalcost 			= 0;
			$this->request_packages 	= array();

			$this->init();
			$this->process_packing( $this->calculator_class->product_rule_mapping,$boxes,$calculator_class );
			$this->create_req_from_packages();

			// if(count($this->packages)>0){
			// 	$this->debug( '<span style="background:red;color:white;" >Rule Group='.$this->calculator_class->group_name.' </span> and Packages: <pre>' . print_r( $this->packages , true ) . '</pre>' );
			// }
		}
		
		private function process_packing( $product_rule_mapping,$boxes,$calculator_class ) 
		{
			global $woocommerce;

			$pre_packed_contents = array();

			include('class-wf-legacy.php');

			if ( ! class_exists( 'WF_Boxpack' ) ) {
				include_once 'box_packing/class-wf-packing.php';
			}
			if ( ! class_exists( 'WF_Boxpack_Stack' ) ) {
				include_once 'box_packing/class-wf-packing-stack.php';
			}

			// Add items
			$ctr = 0;
			foreach ( $product_rule_mapping as $rule_no => $mapping )
			{ 

				volume_based:
				if(isset($this->mode) && $this->mode=='stack_first'){
					$boxpack = new WF_Boxpack_Stack();
				}
				else{
					$boxpack = new WF_Boxpack($this->mode);
				}

				if ( ! empty( $boxes ) ) 
				{
					foreach ( $boxes as $box ) {

						$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'], $box['box_weight'] );				
						$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );

						if ( $box['max_weight'] ) {
							$newbox->set_max_weight( $box['max_weight'] );
						}
						if ( isset( $box['id'] ) ) {
							$newbox->set_id( current( explode( ':', $box['id'] ) ) );
						}
					}
				}

				$company 	= $this->calculator_class->rules_array[$rule_no]['shipping_companies'];
				$price 		= 0;

				foreach ( $mapping as $key=>$pid )                                                    
				{
					if($company=='flatrate')
					{     
						foreach($mapping as $_key=>$_pid)
						{
							if($_key!=="validforall" && !in_array($_pid,$this->calculator_class->executed_products))
							{

								$this->flatrate[$rule_no][$_pid] = $this->calculator_class->rules_array[$rule_no]['fee'];

								if(array_search($_pid, $this->calculator_class->executed_products)===false) 
								{                     
									$this->calculator_class->executed_products[]=$_pid;
								} 									
							}

						}              
					}
					else
					{
						if(array_search($pid, $calculator_class->executed_products)===false  && $key!=="validforall") 
						{
							$calculator_class->executed_products[]=$pid;
							$product = new wf_product($pid);
							$qnty=$this->product_quantity[$pid];
							$ctr++;
							$skip_product = apply_filters('wf_multi_carrier_shipping_skip_product',false, $pid);
							if($skip_product) {    continue;   }

							// Check for Prepacked
							$prepacked	= get_post_meta( $pid, '_ph_multi_carrier_pre_packed', true);
							if( $prepacked == 'yes' ) {
								$price 		+= $this->product_price[$pid] * $qnty;
								$this->add_pre_packed_product_to_package( $pid, $qnty, $company, $rule_no, $price );
								continue;
							}

							if ( !( $qnty > 0 && $product->needs_shipping() ) ) 
							{
								$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'eha_multi_carrier_shipping' ), $ctr ) );
								continue;
							}
							if ( $product->length && $product->height && $product->width && $product->weight ) {

								$dimensions = array( $product->length, $product->height, $product->width );

								for ( $i = 0; $i < $qnty; $i ++ ) {
									$boxpack->add_item(
										number_format( (float) $dimensions[2], 2, '.', ''),
										number_format( (float) $dimensions[1], 2, '.', ''),
										number_format( (float) $dimensions[0], 2, '.', ''),
										number_format( (float) $product->get_weight(), 2, '.', ''),
										$product->get_price(),
										$product // Adding Item as meta
									);
								}

							} else {
								$this->debug( sprintf( __( 'Parcel Packing Method is set to Pack into Boxes. Product #%d is missing dimensions. Aborting.', 'eha_multi_carrier_shipping' ), $ctr ), 'error' );
								return;
							}
						}
					}
				}

				// Pack it
				$boxpack->pack();
				// Get packages
				$box_packages = $boxpack->get_packages();

				if(isset($this->mode) && $this->mode=='stack_first')
				{ 
					foreach($box_packages as $key => $box_package)
					{  
						$box_volume=$box_package->length * $box_package->width * $box_package->height ;
						$box_used_volume=$box_package->volume;
						$box_used_volume_percentage=($box_used_volume * 100 )/$box_volume;
						if(isset($box_used_volume_percentage) && $box_used_volume_percentage<44)
						{   
							$this->mode='volume_based';
							$this->debug( '(FALLBACK) : Stack First Option changed to Volume Based' );
							goto volume_based;
							break;
						}
					}
				}

				$ctr=0;
				foreach ( $box_packages as $key => $box_package ) 
				{                   
					$ctr++;
					//$this->debug( "PACKAGE " . $ctr . " (" . $key . ")\n<pre>" . print_r( $box_package,true ) . "</pre>", 'error' );
					$weight     = $box_package->weight;
					$dimensions = array( $box_package->length, $box_package->width, $box_package->height );
					sort( $dimensions );
					$service='';
					if($this->calculator_class->rules_array[$rule_no]['shipping_companies']!=='flatrate')
					{
						$service= $this->calculator_class->rules_array[$rule_no]['shipping_services'];
						$this->add_to_package($mapping,$weight,$dimensions[0],$dimensions[1],$dimensions[2],$company,$service,1,$rule_no, $box_package->id, $box_package->value );
						$this->seq_no=$this->seq_no+1;
																					// Getting packed items
						$packed_items	=	array();
						if(!empty($box_package->packed) && is_array($box_package->packed))
						{				
							foreach( $box_package->packed as $item ) {
								$item_product	=	$item->meta;
								$packed_items[] = $item_product;					
							}
						}
					}



				}
			}			
			//die("<pre>".print_r($this->request,true)."</pre>");
		}

		private  function add_to_package($in_box_products_pids,$box_weight,$length,$width,$height,$company,$service,$no_of_package,$rule_no, $box_id, $line_total = 0 )
		{
			if( $company == 'flatrate' )
				{     foreach($in_box_products_pids as $_pid)
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
						"pid"				=> implode(",", $in_box_products_pids),
						"weight"			=> $box_weight,
						"length"			=> $length,
						"width"				=> $width,
						"height"			=> $height,
						"company"			=> $company,
						"service"			=> $service,
						"no_of_packages"	=> $no_of_package,
						"rule_no"			=> $rule_no,
						"box_id"			=> $box_id,
						"line_total" 		=> $line_total,
						"insured_amount" 	=> $line_total,
					);

					foreach($in_box_products_pids as $_pid) {
						if(array_search($_pid, $this->calculator_class->executed_products)===false) {
							$this->calculator_class->executed_products[]=$_pid;
						}
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
		$product = ! empty($this->calculator_class->products_arr[$product_id]) ? $this->calculator_class->products_arr[$product_id] : wc_get_product($product_id);
		if( $company == 'fedex' )	$rounding_digit = 0;
		$length = $product->get_length();
		$width  = $product->get_width();
		$height = $product->get_height();
		$weight = $product->get_weight();

		$this->packages[] = array(
			"pid"				=> $product_id,
			"weight"			=> $weight,
			"length"			=> $length,
			"width"				=> $width,
			"height"			=> $height,
			"company"			=> $company,
			"service"			=> $this->calculator_class->rules_array[$rule_no]['shipping_services'],
			"no_of_packages"	=> $quantity,
			"rule_no"			=> $rule_no,
			"box_id"			=> null,
			"line_total" 		=> $price,
			"insured_amount" 	=> $price,
		);
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
		if(!empty($settings['origin_country_state']))
		{
			$data=$settings['origin_country_state'];
			$countryState=explode(':',$data);
			$country=!empty($countryState[0])?$countryState[0]:'';
			$state=!empty($countryState[1])?$countryState[1]:'';                
		}elseif(!empty($settings['origin_country']) && !empty($settings['origin_custom_state'])  )
		{
			$country=$settings['origin_country'];
			$state=$settings['origin_custom_state'];
		}else
		{
			$country='US';
			$state='CA';
		}
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
			),
		);
		$this->request = $this->calculator_class->xa_set_origin_address( $this->request );
		$this->request = $this->calculator_class->xa_set_carriers_accounts( $this->request );
		$this->fedex_one_rate_package_ids = array(
			'FEDEX_SMALL_BOX',
			'FEDEX_MEDIUM_BOX',
			'FEDEX_LARGE_BOX',
			'FEDEX_EXTRA_LARGE_BOX',
			'FEDEX_PAK',
			'FEDEX_ENVELOPE',
		);

	}

	
	function create_req_from_packages()
	{
		$this->seq_no+=1;
		$packages=array();
		$seq_no=0;
		$PackageCount=0;
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
			$_package['length'] = number_format( (float) wc_get_dimension( $_package['length'], $this->dim_unit ), 2, '.', '');
			$_package['width']	= number_format( (float) wc_get_dimension( $_package['width'], $this->dim_unit ), 2, '.', '');
			$_package['height'] = number_format( (float) wc_get_dimension( $_package['height'], $this->dim_unit ), 2, '.', '');
			$pid=$_package['pid'];
			$rule_no=$_package['rule_no'];

			$package_limit=25;
			if($company=='ups')
			{
				$package_limit=200;
				$length = (int) $_package['length'];
				$width  = (int) $_package['width'];
				$height = (int) $_package['height'];
			}
			elseif($company=='fedex')
			{
				$length = (int) $_package['length'];
				$width  = (int) $_package['width'];
				$height = (int) $_package['height'];
				$package_limit=999;
			}elseif($company=='usps')
			{
				$length = (float) $_package['length'];
				$width  = (float) $_package['width'];
				$height = (float) $_package['height'];
				$package_limit=25;
			}
			elseif($company=='stamps_usps')
			{
				$length = (float) $_package['length'];
				$width  = (float) $_package['width'];
				$height = (float) $_package['height'];
				$package_limit=100;
			}
			elseif($company=='dhl')
			{
				$length = (int) $_package['length'];
				$width  = (int) $_package['width'];
				$height = (int) $_package['height'];
				$package_limit=99;
			}

			//echo "no of package=$no_of_package  and package limit=$package_limit";
			//if($no_of_package>$package_limit)
			//{
			while($no_of_packages>0)
			{   
				$packages=array();
				$packages[]=array(
					"weight"			=> $this->convert_to_lbs($weight),
					"unit"				=> 'LBS' ,                //ups
					"Description"		=> "My Package",
					"length"			=> $length,
					"width"				=> $width,
					"height"			=> $height,
					'no_of_packages'	=> ($no_of_packages>$package_limit)?$package_limit:$no_of_packages,
					'SequenceNumber'	=> $seq_no,
					'line_subtotal' 	=> $line_total,
					'insured_amount' 	=> $insured_amount,
				);
				$this->request['Request_Array'][] = array(
					'id'				=> "$pid:$rule_no",
					'company'			=> array($company),
					'Weight_Units'		=> 'LB',             //fedex
					"ServiceType"		=> $service,
					"Container" 		=> $container,
					"RateRequestTypes"	=> "NONE",     // fedex request type for(account rate or list rates)       here writing list is not working correctly it adds both account and list
					//"PackageCount" 	=> $PackageCount,
					"box_id"			=> $_package['box_id'],		// For FEDEX_ONE_RATE
					"fedexOneRate"		=> ($this->calculator_class->settings['fedex_one_rate']=='yes' && in_array( $_package['box_id'], $this->fedex_one_rate_package_ids ) && $this->request['Common_Params']['Recipient_Address_CountryCode']=='US' && $this->request['Common_Params']['Shipper_Address_CountryCode']=='US')?true:false,
					"packages"			=> $packages
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
		$cost=0;
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

				$req 				= array();
				$req['emailid'] 	= $this->request['Common_Params']['emailid'];
				$req['data'] 		= $this->encode($this->request['Common_Params']['key'],$content);
				$content 			= json_encode($req);
				
				$this->debug( "Encoded Request<pre>Request: ".$content." </pre>"); 

				// $curl = curl_init($url);
				// curl_setopt($curl, CURLOPT_HEADER, false);
				// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($curl, CURLOPT_HTTPHEADER,  array("Content-type: application/json"));


				// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				// curl_setopt($curl, CURLOPT_POST, true);
				// curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

				// $json_response = curl_exec($curl);

				// $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

				$response=wp_remote_post( $url, array(
					'method' => 'POST',
					'timeout' => 10,
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

				$status 		= wp_remote_retrieve_response_code($response);
				$tmp 			= $response['body'];
				$json_response 	= json_decode($tmp, true);
				$response 		= $json_response;

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

				$usps_flat_rate_boxes 	= new Wf_MC_Extend_USPS();
				$response 				= $usps_flat_rate_boxes->get_usps_flat_rates($this->request, $response );
				
				// End of USPS rate for flat rate boxes

				$this->debug("Response <pre>".print_r($response,true)."</pre>");

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
						if(is_array($rate))
						{   
							foreach($rate as $irate)
							{    
								if(is_array($irate))
								{  
									foreach($irate as $type => $data)
									{  
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
											if( isset($package_rate['error']) )
											{
												$cost = 0;
												break;
											}

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
			if( empty($cost) )	return false;
		}

		if( ! empty($this->flatrate) ){
			$this->debug( '<span style="background:red;color:white;" >Rule Group='.$this->calculator_class->group_name.' </span> and Flat Rate Calculation : <pre>' . print_r( $this->flatrate , true ) . '</pre>' );
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
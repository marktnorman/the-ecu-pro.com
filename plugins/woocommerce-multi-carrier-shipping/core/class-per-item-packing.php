<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class per_item_packing{

	function __construct($product_weight,$product_quantity,$calculator_class) {
		$this->request 				= array();
		$this->flatrate 			= array();
		$this->only_flat_rate 		= false;
		$this->dim_unit 			= "IN";
		$this->product_weight 		= $product_weight;
		$this->product_quantity 	= $product_quantity;
		$this->calculator_class 	= $calculator_class;
		$this->debug 				= $calculator_class->debug;
		$this->packages 			= array();
		$this->seq_no 				= 0;
		$this->request_packages 	= array();

		$this->init();
	}

	function per_item_packing_add_package_to_request($bucket, $company,$service,$rule_no)
	{
		if($company=='flatrate')
		{    
			$this->calculator_class->rules_executed_successfully[]=$rule_no;
			foreach($bucket as $_pid)
			{
				$this->flatrate[$rule_no][$_pid] = $this->calculator_class->rules_array[$rule_no]['fee'];
				if(array_search($_pid, $this->calculator_class->executed_products)===false) 
				{                     
					$this->calculator_class->executed_products[]=$_pid;
					//echo "adding $_pid into executed array";
				}
			}
		}
		else
		{   
			foreach($bucket as $_pid){
				$this->add_product_to_req($_pid,$rule_no,$company,$service);
			}
		}

		$this->only_flat_rate = ( count($this->packages) == 0 ) ? true : false;

	}
	private function init()
	{ 
		$settings 		= $this->calculator_class->settings;
		$is_residential = 'false';

		if(isset($settings['is_recipient_address_residential']))
		{
			$is_residential=$settings['is_recipient_address_residential']=='yes'?'true':'false';
		}

		if($this->calculator_class->is_residential==1)
		{
			$is_residential='true';
		}

		$environment 	= ( $settings['test_mode']=="yes" ) ? 'sandbox' : 'live';
		$cdate 			= getdate();

		if(!empty($settings['origin_country_state']))
		{
			$data 			= $settings['origin_country_state'];
			$countryState 	= explode(':',$data);
			$country 		= !empty($countryState[0])?$countryState[0]:'';
			$state 			= !empty($countryState[1])?$countryState[1]:'';                
		}
		elseif(!empty($settings['origin_country']) && !empty($settings['origin_custom_state'])  )
		{
			$country 	= $settings['origin_country'];
			$state 		= $settings['origin_custom_state'];
		}else
		{
			$country 	= 'US';
			$state 		= 'CA';
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

			'Request_Array'=>array(),
		);

		$this->request = $this->calculator_class->xa_set_origin_address( $this->request );
		$this->request = $this->calculator_class->xa_set_carriers_accounts( $this->request );

	}

	function add_product_to_req($pid,$rule_no,$company,$service)
	{             
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

		$this->seq_no+=1;        
		$package_limit=0;

		if($company=='ups')
		{
			$package_limit=200;
		}
		elseif($company=='fedex')
		{
			$package_limit=999;
		}elseif($company=='usps')
		{
			$package_limit=25;
		}
		elseif($company=='stamps_usps')
		{
			$package_limit=100;
		}
		elseif($company=='dhl')
		{
			$package_limit=99;
		}

		// Product being added
		foreach( $this->calculator_class->package['contents'] as $line_item ) {
			if( $line_item['product_id'] == $pid || $line_item['variation_id'] == $pid ) {
				$current_product = $line_item;
				break;
			}
		}

		if($this->product_quantity[$pid]>$package_limit)
		{

			$no_of_packages=$this->product_quantity[$pid];

			while($no_of_packages>0)
			{
				$length = number_format( (float) wc_get_dimension( $current_product['data']->get_length(), $this->dim_unit ), 2, '.', '');
				$width  = number_format( (float) wc_get_dimension( $current_product['data']->get_width(), $this->dim_unit ), 2, '.', '');
				$height = number_format( (float) wc_get_dimension( $current_product['data']->get_height(), $this->dim_unit ), 2, '.', '');

				if( $company == 'fedex' || $company == 'ups' )
				{
					$length = ceil($length);
					$width  = ceil($width);
					$height = ceil($height);
				}

				$packages  		= array();
				$line_subtotal 	= isset($current_product['line_subtotal']) ? $current_product['line_subtotal'] : 0;

				$package   = array(
					"weight"          =>$this->convert_to_lbs($this->product_weight[$pid]),
					"unit"            => 'LBS' ,
					'length'          => $length,
					'width'           => $width,
					'height'          => $height,
					'no_of_packages'  => ($no_of_packages>$package_limit) ? $package_limit : $no_of_packages,
					'SequenceNumber'  => $this->seq_no,
					'line_subtotal'   => $line_subtotal,
					'insured_amount'  => $line_subtotal,
				);

				$packages[]       = $package;
				$this->packages[] = $package;

				$this->request['Request_Array'][]=array(
					'id' 				=> "$pid:$rule_no",
					'company' 			=> array($company),
					'Weight_Units' 		=> 'LB',
					"ServiceType" 		=> $service,
					"Container" 		=> $container,
					"RateRequestTypes" 	=> "NONE",     // fedex request type for(account rate or list rates)       here writing list is not working correctly it adds both account and list
					//"PackageCount"=>($no_of_packages>$package_limit)?$package_limit:$no_of_packages,
					"packages" 			=> $packages
				);

				$this->request_packages[] = $packages;

				$this->seq_no++;

				if($no_of_packages>$package_limit)                                      
				{
					$no_of_packages-=$package_limit;
				}
				else
				{
					$no_of_packages=0;
				}                                        

			}
			$this->debug("<pre> packages print".print_r($packages,true)."</pre>");

		}
		else
		{
			$length = number_format( (float) wc_get_dimension( $current_product['data']->get_length(), $this->dim_unit ), 2, '.', '');
			$width  = number_format( (float) wc_get_dimension( $current_product['data']->get_width(), $this->dim_unit ), 2, '.', '');
			$height = number_format( (float) wc_get_dimension( $current_product['data']->get_height(), $this->dim_unit ), 2, '.', '');

			if( $company == 'fedex' || $company == 'ups' )
			{
				$length = ceil($length);
				$width  = ceil($width);
				$height = ceil($height);
			}

			$packages 		= array();
			$line_subtotal 	= isset($current_product['line_subtotal']) ? $current_product['line_subtotal'] : 0;

			$package  = array(
				"weight" 			=> $this->convert_to_lbs($this->product_weight[$pid]),
				"unit" 				=> 'LBS' ,
				'length'  			=> $length,
				'width'   			=> $width,
				'height'  			=> $height,
				'no_of_packages' 	=> $this->product_quantity[$pid],
				'SequenceNumber' 	=> $this->seq_no,
				'line_subtotal'   	=> $line_subtotal,
				'insured_amount' 	=> $line_subtotal,
			);

			$packages[] 		= $package;
			$this->packages[] 	= $package;

			$this->request['Request_Array'][]=array(
				'id' 				=> "$pid:$rule_no",
				'company' 			=> array($company),
				'Weight_Units' 		=> 'LB',
				"ServiceType" 		=> $service,
				"Container"  		=> $container,
				"RateRequestTypes" 	=> "NONE",     // fedex request type for(account rate or list rates)       here writing list is not working correctly it adds both account and list
				//"PackageCount"=>$this->product_quantity[$pid],
				"packages" 			=> $packages
			);

			$this->request_packages[] = $packages;
		}

		// echo "<pre>";
		// print_r($this->request['Request_Array']);
		// echo "</pre>";
		// die();
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
			$this->request 	= apply_filters( 'xa_multicarrier_rate_request', $this->request );
			$settings 		= $this->calculator_class->settings;

			// $this->debug("<pre>Request".print_r($this->request,true)."</pre>");
			
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

					$cost=$api_rates[$company][$ServiceType]['TotalNetChargeWithDutiesAndTaxes']['Amount'];
					$flag = true;
				}
				// To restrict the API hit if rate is not found and debug mode is off
				elseif ( isset($api_rates[$company]) && isset($api_rates[$company]['rate_result']) && (!isset($api_rates[$company][$ServiceType]) && !$this->debug ) && $diff_package == 0 )
				{ 
					$flag = true;
				}
			}

			if( !$flag )
			{	
				
				$this->debug( 'Request 	<pre>' . print_r( $this->request , true ) . '</pre>' );
				$this->debug( "JSON Request <pre>Request: ".$content." </pre>");

				$req 			= array();
				$req['emailid'] = $this->request['Common_Params']['emailid'];
				$req['data'] 	= $this->encode($this->request['Common_Params']['key'],$content);
				$content 		= json_encode($req);

				$this->debug( "Encoded Request<pre>Request: ".$content." </pre>"); 

				// $curl = curl_init($url);
				// curl_setopt($curl, CURLOPT_HEADER, false);
				// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
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
				$usps_flat_rate_boxes	= new Wf_MC_Extend_USPS();
				$response 				= $usps_flat_rate_boxes->get_usps_flat_rates($this->request, $response );

				// End of USPS rate for flat rate boxes

				$this->debug("Response <pre>".print_r($response,true)."</pre>");
				try
				{
					$final_rates = array();

					if(isset($response['rates']))
					{
						foreach($response['rates'] as $PidAndRuleNo=>$rate)
						{

							//echo $PidAndRuleNo;
							$exp = explode(":",$PidAndRuleNo);
							$pid = $exp[0];
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
										foreach($irate as $type =>  $data)
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
						
						// Handle Empty rates response from API
						if( empty($cost) )	return false;
					}
				} catch (Exception $ex) {

				}
			}
			if( empty($cost) )	return false;
		}

		if( ! empty($this->flatrate) ) {
			$this->debug("Flat Rate  as( Rule=>( Productid=>Cost)) </br><pre>".print_r($this->flatrate,true)."</pre>");
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

	public  function debug( $message, $type = 'notice' ) {
		if ( $this->debug && !is_admin()) { //WF: is_admin check added.
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
				wc_add_notice( $message, $type );
			} else {
				global $woocommerce;
				$woocommerce->add_message( $message );
			}
		}
	}
}
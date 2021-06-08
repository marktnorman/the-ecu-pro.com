<?php

if( !class_exists('Wf_MC_Extend_USPS') ) {
	/**
	 * Class to support USPS Flat rate boxes (Predefined) .
	 */
	class Wf_MC_Extend_USPS {
		
		/**
		 * USPS Flat Rate Boxes
		 * @return array Return the USPS flat rate boxes
		 */
		public static function usps_flat_rate_boxes() {
			return array(
				// Priority Mail Express
				"d13"     => array(
					"name"     => "Priority Mail Express Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "0.25",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "express"
				),
				"d30"     => array(
					"name"     => "Priority Mail Express Legal Flat Rate Envelope",
					"length"   => "9.5",
					"width"    => "15",
					"height"   => "0.25",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "express"
				),
				"d63"     => array(
					"name"     => "Priority Mail Express Padded Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "1",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "express"
				),

				// Priority Mail
				"d16"     => array(
					"name"     => "Priority Mail Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "0.25",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"d17"     => array(
					"name"     => "Priority Mail Medium Flat Rate Box - 2",
					"length"   => "13.625",
					"width"    => "11.875",
					"height"   => "3.375",
					"weight"   => "70",
					"box_type" => "priority"
				),
				"d17b"     => array(
					"name"     => "Priority Mail Medium Flat Rate Box - 1",
					"length"   => "11",
					"width"    => "8.5",
					"height"   => "5.5",
					"weight"   => "70",
					"box_type" => "priority"
				),
				"d22"     => array(
					"name"		=> "Priority Mail Large Flat Rate Box",
					"length"	=> "12",
					"width"		=> "12",
					"height"	=> "5.5",
					"weight"	=> "70",
					"box_type" => "priority"
				),
				"d22a"     => array(
					"name"     => "Priority Mail Large Flat Rate Board Game Box",
					"length"   => "23.69",
					"width"    => "11.75",
					"height"   => "3",
					"weight"   => "70",
					"box_type" => "priority"
				),
				"d28"     => array(
					"name"     => "Priority Mail Small Flat Rate Box",
					"length"   => "5.375",
					"width"    => "8.625",
					"height"   => "1.625",
					"weight"   => "70",
					"box_type" => "priority"
				),
				"d29"     => array(
					"name"     => "Priority Mail Padded Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "1",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"d38"     => array(
					"name"     => "Priority Mail Gift Card Flat Rate Envelope",
					"length"   => "10",
					"width"    => "7",
					"height"   => "0.25",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"d40"     => array(
					"name"     => "Priority Mail Window Flat Rate Envelope",
					"length"   => "5",
					"width"    => "10",
					"height"   => "0.25",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"d42"     => array(
					"name"     => "Priority Mail Small Flat Rate Envelope",
					"length"   => "6",
					"width"    => "10",
					"height"   => "0.25",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"d44"     => array(
					"name"     => "Priority Mail Legal Flat Rate Envelope",
					"length"   => "9.5",
					"width"    => "15",
					"height"   => "0.5",
					"weight"   => "70",
					"type"     => "envelope",
					"box_type" => "priority"
				),

				// International Priority Mail Express
				"i13"     => array(
					"name"     => "Priority Mail Express International Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "0.25",
					"weight"   => "4",
					"type"     => "envelope",
					"box_type" => "express"
				),
				"i30"     => array(
					"name"     => "Priority Mail Express International Legal Flat Rate Envelope",
					"length"   => "9.5",
					"width"    => "15",
					"height"   => "0.25",
					"weight"   => "4",
					"type"     => "envelope",
					"box_type" => "express"
				),
				"i63"     => array(
					"name"     => "Priority Mail Express International Padded Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "1",
					"weight"   => "4",
					"type"     => "envelope",
					"box_type" => "express"
				),

				// International Priority Mail
				"i8"      => array(
					"name"     => "Priority Mail International Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "0.25",
					"weight"   => "4",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"i29"     => array(
					"name"     => "Priority Mail International Padded Flat Rate Envelope",
					"length"   => "12.5",
					"width"    => "9.5",
					"height"   => "1",
					"weight"   => "4",
					"type"     => "envelope",
					"box_type" => "priority"
				),
				"i16"     => array(
					"name"     => "Priority Mail International Small Flat Rate Box",
					"length"   => "5.375",
					"width"    => "8.625",
					"height"   => "1.625",
					"weight"   => "4",
					"box_type" => "priority"
				),
				"i9"      => array(
					"name"     => "Priority Mail International Medium Flat Rate Box - 2",
					"length"   => "11.875",
					"width"    => "13.625",
					"height"   => "3.375",
					"weight"   => "20",
					"box_type" => "priority"
				),
				"i9b"      => array(
					"name"     => "Priority Mail International Medium Flat Rate Box - 1",
					"length"   => "11",
					"width"    => "8.5",
					"height"   => "5.5",
					"weight"   => "70",
					"box_type" => "priority"
				),
				"i11"     => array(
					"name"     => "Priority Mail International Large Flat Rate Box",
					"length"   => "12",
					"width"    => "12",
					"height"   => "5.5",
					"weight"   => "20",
					"box_type" => "priority"
				),
				"i20"     => array(
					"name"     => "Priority Mail International DVD Flat Rate priced box",
					"length"   => "7.56",
					"width"    => "5.43",
					"height"   => "0.62",
					"weight"   => "4",
					"box_type" => "priority"
				),
				"i21"     => array(
					"name"     => "Priority Mail International Large Video Flat Rate priced box",
					"length"   => "9.25",
					"width"    => "6.25",
					"height"   => "2",
					"weight"   => "4",
					"box_type" => "priority"
				),
			);
		}
		
		/**
		 * Array of Cost of USPS Flat rate boxes .
		 * @return array Cost .
		 */
		public static function usps_flat_rate_boxes_cost() {

		   return array(

			   // Priority Mail Express

				   // Priority Mail Express Flat Rate Envelope
				   "d13"     => array(
					   "retail" => "25.50",
					   "online" => "25.50",
				   ),
				   // Priority Mail Express Legal Flat Rate Envelope
				   "d30"     => array(
					   "retail" => "25.70",
					   "online" => "25.70",
				   ),
				   // Priority Mail Express Padded Flat Rate Envelope
				   "d63"     => array(
					   "retail" => "26.20",
					   "online" => "26.20",
				   ),

			   // Priority Mail Boxes

				   // Priority Mail Flat Rate Medium Box
				   "d17"     => array(
					   "retail" => "14.35",
					   "online" => "14.35",
				   ),
				   // Priority Mail Flat Rate Medium Box
				   "d17b"     => array(
					   "retail" => "14.35",
					   "online" => "14.35",
				   ),
				   // Priority Mail Flat Rate Large Box
				   "d22"     => array(
					   "retail" => "19.95",
					   "online" => "19.95",
				   ),

				   // Priority Mail Flat Rate Large Box
				   "d22a"     => array(
					   "retail" => "19.95",
					   "online" => "19.95",
				   ),
				   // Priority Mail Flat Rate Small Box
				   "d28"     => array(
					   "retail" => "7.90",
					   "online" => "7.90",
				   ),

			   // Priority Mail Envelopes

				   // Priority Mail Flat Rate Envelope
				   "d16"     => array(
					   "retail" => "7.35",
					   "online" => "7.35",
				   ),
				   // Priority Mail Padded Flat Rate Envelope
				   "d29"     => array(
					   "retail" => "8.00",
					   "online" => "8.00",
				   ),
				   // Priority Mail Gift Card Flat Rate Envelope
				   "d38"     => array(
					   "retail" => "7.35",
					   "online" => "7.35",
				   ),
				   // Priority Mail Window Flat Rate Envelope
				   "d40"     => array(
					   "retail" => "7.35",
					   "online" => "7.35",
				   ),
				   // Priority Mail Small Flat Rate Envelope
				   "d42"     => array(
					   "retail" => "7.35",
					   "online" => "7.35",
				   ),
				   // Priority Mail Legal Flat Rate Envelope
				   "d44"     => array(
					   "retail" => "7.65",
					   "online" => "7.65",
				   ),

			   // International Priority Mail Express

				   // Priority Mail Express Flat Rate Envelope
				   "i13"     => array(
					   "retail"    => array(
						   '*'  => "57.50",
						   'CA' => "44.50"
					   )
				   ),
				   // Priority Mail Express Legal Flat Rate Envelope
				   "i30"     => array(
					   "retail"    => array(
						   '*'  => "57.50",
						   'CA' => "44.50"
					   )
				   ),
				   // Priority Mail Express Padded Flat Rate Envelope
				   "i63"     => array(
					   "retail"    => array(
						   '*'  => "57.50",
						   'CA' => "44.50"
					   )
				   ),

			   // International Priority Mail

				   // Priority Mail Flat Rate Envelope
				   "i8"      => array(
					   "retail"    => array(
						   '*'  => "29.95",
						   'CA' => "25.85"
					   )
				   ),
				   // Priority Mail Padded Flat Rate Envelope
				   "i29"      => array(
					   "retail"    => array(
						   '*'  => "29.95",
						   'CA' => "25.85"
					   )
				   ),
				   // Priority Mail Flat Rate Small Box
				   "i16"     => array(
					   "retail"    => array(
						   '*'  => "33.95",
						   'CA' => "26.85"
					   )
				   ),
				   // Priority Mail Flat Rate Medium Box
				   "i9"      => array(
					   "retail"    => array(
						   '*'  => "66.50",
						   'CA' => "49.60"
					   )
				   ),
				   // Priority Mail Flat Rate Medium Box
				   "i9b"      => array(
					   "retail"    => array(
						   '*'  => "66.50",
						   'CA' => "49.60"
					   )
				   ),
				   // Priority Mail Flat Rate Large Box
				   "i11"     => array(
					   "retail"    => array(
						   '*'  => "86.95",
						   'CA' => "64.50"
					   )
				   ),
				   // Priority Mail International DVD Flat Rate priced box
				   "i20"     => array(
					   "retail"    => array(
						   '*'  => "30.95",
						   'CA' => "26.85"
					   )
				   ),
				   // Priority Mail International Large Video Flat Rate priced box
				   "i21"     => array(
					   "retail"    => array(
						   '*'  => "30.95",
						   'CA' => "26.85"
					   )
				   ),
		   );

		}
		
		/**
		 * Get the array of all USPS services including flat rates boxes .
		 * @return array All USPS Services List sorted .
		 */
		public static function usps_service_names() {
			
			// Normal USPS Services
			$services = array(	   
									'First-Class:LETTER'							=> 'First Class:LETTER',
									'First-Class:FLAT'								=> 'First Class:FLAT',
									'First-Class:PARCEL'							=> 'First Class:PARCEL',
									'First-Class:POSTCARD'							=> 'First Class:POSTCARD',
									'First-Class:PACKAGE-SERVICE'					=> 'First Class:PACKAGE-SERVICE',
									'First-Class-Commercial:PACKAGE-SERVICE'		=> 'First Class Commercial:PACKAGE-SERVICE',
									'First-Class-HFP-Commercial:PACKAGE-SERVICE'	=> 'First Class HFP Commercial:PACKAGE-SERVICE',
									'Priority'										=> 'Priority',
									'Priority-Commercial'							=> 'Priority Commercial',
									'Priority-Cpp'									=> 'Priority Cpp',
									'Priority-HFP-Commercial'						=> 'Priority HFP Commercial',
									'Priority-HFP-Cpp'								=> 'Priority HFP Cpp',


									'Priority-CommercialREGIONALRATEBOXA'							=> 'Priority Commercial Regional Rate Box A',
									'Priority-CppREGIONALRATEBOXA'									=> 'Priority Cpp Regional Rate Box A',
									'Priority-HFP-CommercialREGIONALRATEBOXA'						=> 'Priority HFP Commercial Regional Rate Box A',
									'Priority-HFP-CppREGIONALRATEBOXA'								=> 'Priority HFP Cpp Regional Rate Box A',
									'Priority-CommercialREGIONALRATEBOXB'							=> 'Priority Commercial Regional Rate Box B',
									'Priority-CppREGIONALRATEBOXB'									=> 'Priority Cpp Regional Rate Box B',
									'Priority-HFP-CommercialREGIONALRATEBOXB'						=> 'Priority HFP Commercial Regional Rate Box B',
									'Priority-HFP-CppREGIONALRATEBOXB'								=> 'Priority HFP Cpp Regional Rate Box B',

									'Priority-Mail-Express'							=> 'Priority Mail Express',
									'Priority-Mail-Express-Commercial'				=> 'Priority Mail Express Commercial',
									'Priority-Mail-Express-Cpp'						=> 'Priority Mail Express Cpp',
									'Priority-Mail-Express-HFP'						=> 'Priority Mail Express HFP',
									'Priority-Mail-Express-HFP-Commercial'			=> 'Priority Mail Express HFP Commercial',
									'Standard-Post'									=> 'Standard Post',
									'Retail-Ground'									=> 'Retail Ground',
									'Media'											=> 'Media',
									'Library'										=> 'Library',
									'Online-Plus'									=> 'Online Plus',
									'12'											=> 'Global-Express Guaranteed',
									'1'												=> 'Priority Mail Express International',
									'2'												=> 'Priority Mail International',
									'9'												=> 'Priority Mail International Medium Flat Rate Box',
									'11'											=> 'Priority Mail International Large Flat-Rate Box',
									'16'											=> 'Priority Mail International Small Flat Rate Box',
									'15'											=> 'First Class Package International Service',
									'all_flat_rate_boxes'							=> 'All Flat Rates Boxes'
			);
			
			// Flat rate boxes services ( Predefined boxes, rates won't come via api )
			$flat_rate_services = array(
									"d13"		=>	"Priority Mail Express Flat Rate Envelope",
									"d30"		=>	"Priority Mail Express Legal Flat Rate Envelope",
									"d63"		=>	"Priority Mail Express Padded Flat Rate Envelope",
									"d16"		=>	"Priority Mail Flat Rate Envelope",
									"d17"		=>	"Priority Mail Medium Flat Rate Box - 2",
									"d17b"		=>	"Priority Mail Medium Flat Rate Box - 1",
									"d22"		=>	"Priority Mail Large Flat Rate Box",
									"d22a"		=>	"Priority Mail Large Flat Rate Board Game Box",
									"d28"		=>	"Priority Mail Small Flat Rate Box",
									"d29"		=>	"Priority Mail Padded Flat Rate Envelope",
									"d38"		=>	"Priority Mail Gift Card Flat Rate Envelope",
									"d40"		=>	"Priority Mail Window Flat Rate Envelope",
									"d42"		=>	"Priority Mail Small Flat Rate Envelope",
									"d44"		=>	"Priority Mail Legal Flat Rate Envelope",
									"i13"		=>	"Priority Mail Express International Flat Rate Envelope",
									"i30"		=>	"Priority Mail Express International Legal Flat Rate Envelope",
									"i63"		=>	"Priority Mail Express International Padded Flat Rate Envelope",
									"i8"		=>	"Priority Mail International Flat Rate Envelope",
									"i29"		=>	"Priority Mail International Padded Flat Rate Envelope",
									"i16"		=>	"Priority Mail International Small Flat Rate Box",
									"i9" 		=>	"Priority Mail International Medium Flat Rate Box - 2",
									"i9b"		=>	"Priority Mail International Medium Flat Rate Box - 1",
									"i11"		=>	"Priority Mail International Large Flat Rate Box",
									"i20"		=>	"Priority Mail International DVD Flat Rate priced box",
									"i21"		=>	"Priority Mail International Large Video Flat Rate priced box",
			);
			
			$usps_services =  $services + $flat_rate_services;	// Don't use array merge that will reindex numeric keys
			asort($usps_services);		// asort will maintain index association
			return $usps_services;
		}
		
		/**
		 * Get the rate response including USPS flat rate boxes response.
		 * @param array $request Request
		 * @param array $response Rate response without USPS flat rate boxes response.
		 * @param string $packaging_type Packaging Type
		 * @return array Rate response including the 
		 */
		public function get_usps_flat_rates( $request, $response, $packaging_type = null ) {
			$new_request = $request;
			$total_rule_matches=0;
			foreach( $request['Request_Array'] as $key => $package ) {
				if( in_array( 'usps', $package['company'] ) && ( preg_match( "/[i,d][0,1,2,3,4,5,6,7,8,9]+/", $package['ServiceType'], $matches) || $package['ServiceType'] == 'all_flat_rate_boxes') ) {
					$new_request['Request_Array'] = array( $key	=>	$package );
					$usps_response[$key] = $this->calculate_usps_flat_rate_boxes_shipping_cost( $new_request, $packaging_type );
					if(!empty($usps_response[$key]))
						$total_rule_matches++;
				}
			}
			// Add the USPS flat rate boxes rate to response
			if( ! empty($usps_response) && $total_rule_matches==sizeof($request['Request_Array']) ) {
				$response_rates_keys = array_keys( $response['rates']);
				foreach( $usps_response as $key => $val ) {
					$response_rate_key_to_set = $response_rates_keys[$key];
					$response['rates'][$response_rate_key_to_set] = current($val['rates']);
				}
			}
			return $response;
		}
		/**
		 * Calculate USPS rates for Flat rate boxes (Predefined) .
		 * @param array $request MultiCarrier request
		 * @return boolean|array Rates for flat rate boxes .
		 */
		public function calculate_usps_flat_rate_boxes_shipping_cost( $request, $packaging_type = null ) {
			
			$failed					= false;
			$flat_rate_boxes		= $this->usps_flat_rate_boxes();
			$shipment_type			= ($request['Common_Params']['Shipper_Address_CountryCode'] == $request['Common_Params']['Recipient_Address_CountryCode']) ? 'domestic' : 'international' ;
			$recipient_country		= $request['Common_Params']['Recipient_Address_CountryCode'];
			$i						=		0;
			// Unset the boxes based on shipment type (domestic or international)
			switch ($shipment_type) {
				case	'domestic'		:	$key_to_unset = 'i';		// i has been used as prefix for international boxes key
											break;
				case	'international'	:	$key_to_unset = 'd';		// d has been used as prefix for domestic boxes key
											break;
			}
			
			foreach( $flat_rate_boxes as $key => $box ) {
				if( substr( $key, 0, 1) == $key_to_unset ) {
					unset($flat_rate_boxes[$key]);
				}
			}
			
			// Code End for Unset the boxes based on shipment type (domestic or international)
			
			foreach($request['Request_Array'] as  $usps_flat_rate_request ) {
					
					$box_id = $usps_flat_rate_request['ServiceType'];
					$quantity = 0;
					
					// Get box ids if all the flat rate boxes has been selected  in settings page, it won't work in case of weight based packing
					if( current($usps_flat_rate_request['company'] ) == 'usps' && $box_id == 'all_flat_rate_boxes' && $packaging_type != 'weight_based_packing' ) {
							foreach( $usps_flat_rate_request['packages'] as $package_key => $package ) {
								if( ! empty($package['weight']) ) {
									$package_dimension = array( $package['length'], $package['width'], $package['height'] );
									sort($package_dimension);

									foreach( $flat_rate_boxes as $box_key => $box ) {
										$box_dimension = array( $box['length'], $box['width'], $box['height'] );
										sort($box_dimension);
										if( $package['weight'] <= $box['weight'] && $package_dimension[2] <= $box_dimension[2] && $package_dimension[1] <= $box_dimension[1] && $package_dimension[0] <= $box_dimension[0]) {
											if( empty($temp_box_id[$package_key]) ) {
												$temp_box_id = $box_key;
											}
											else {
												$current_id				= $temp_box_id;
												$current_box_dimension	= array( $flat_rate_boxes[$current_id]['length'], $flat_rate_boxes[$current_id]['width'], $flat_rate_boxes[$current_id]['height'] );
												if( array_sum($current_box_dimension) > array_sum($box_dimension) ) {
													$temp_box_id = $box_key;
												}
											}
										}
									}
								}
								$quantity += $package['no_of_packages'];
							}
							$box_id = isset($temp_box_id) ? $temp_box_id : 'no_box_selected';
					}
					elseif( current($usps_flat_rate_request['company'] ) == 'usps' && isset($flat_rate_boxes[$box_id]) ) {
						$package				= current($usps_flat_rate_request['packages']);
						$quantity 				= $package['no_of_packages'];
						$current_box			= $flat_rate_boxes[$box_id];

						if($package['weight'] > $current_box['weight']) {
							continue;
						}

						// Dimension is not set in case of weight based packing
						if( isset($package['length']) ) {
							$package_dimension		= array( $package['length'], $package['width'], $package['height'] );
							$current_box_dimension	= array( $current_box['length'], $current_box['width'], $current_box['height'] );
							sort($package_dimension);
							sort($current_box_dimension);
							if( $package_dimension[2] > $current_box_dimension[2] || $package_dimension[1] > $current_box_dimension[1] || $package_dimension[0] > $current_box_dimension[0] ) {
								continue;
							}
						}
					}
					// Code end for Get box ids if all the flat rate boxes has been selected in settings page

					if( current($usps_flat_rate_request['company'] ) == 'usps' && isset($flat_rate_boxes[$box_id]) ) {
						$shipping_cost = $this->get_flat_rate_boxes_cost( $box_id, $shipment_type, $recipient_country) * $quantity;
						if( ! empty($shipping_cost) ) {
							$temp = array(
									$usps_flat_rate_request['id'].':'.$i++	=> array(
										array(
											'cached'	=>	1,
											'code'		=>	$box_id,
											'usps'		=>	array(
												array(
													'TotalNetChargeWithDutiesAndTaxes'	=>	array(
														'Currency'	=>	'USD',
														'Amount'	=>	$shipping_cost,
													),
												),
											),
										),
									),
								);
							$rates = empty($rates) ? $temp : array_merge($rates, $temp);
						}
					}
				}
				
				if( ! empty($rates) && count($request['Request_Array']) == count($rates) ) {
					$response = array(
							'notification'	=>	'Rates for standard Flat rate boxes .',
							'rates'	=>	$rates,
						);
				}
				
				return ( ! empty($response) ) ? $response : false;
		}
		
		/**
		 * Get the Shipping cost for any USPS flat rate box id .
		 * @param int $box_id Box id in which it will be packed.
		 * @param string $shipment_type Shipment type, Possible values domestic or international.
		 * @param string $recipient_country Recipient Country Code .
		 * @return int	Shipping cost for the given box id .
		 */
		public function get_flat_rate_boxes_cost( $box_id, $shipment_type, $recipient_country ) {
				$shipping_cost_array	= $this->usps_flat_rate_boxes_cost();
				$cost					= 0;
				$rate_type				= apply_filters( 'xa_change_usps_rate_type', 'retail' );							// It can be retail or online, online will be cheaper
				switch($shipment_type) {
					case 'domestic'			:	$cost = $shipping_cost_array[$box_id][$rate_type];
												break;
					case 'international'	:	$cost = isset($shipping_cost_array[$box_id]['retail'][$recipient_country]) ? $shipping_cost_array[$box_id]['retail'][$recipient_country] : $shipping_cost_array[$box_id]['retail']['*'];
												break;
						
				}
				return $cost;
		}
		
	}
}
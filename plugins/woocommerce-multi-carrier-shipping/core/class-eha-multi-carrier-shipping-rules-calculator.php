<?php

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

class eha_multi_carrier_shipping_rules_calculator {

	function __construct($rules_array,$package,$debug,$group_name='',$main_class='') 
	{  
		$this->api_rates=$main_class->api_rates;
		$this->main_class=$main_class;
		$this->package      = $package;
		$this->group_name   = $group_name;
		$this->debug        = $debug;
		$this->rule_product_mapping=array();
		$this->rules_array  =$rules_array;
		$this->country      =$package['destination']['country'];
		$this->country_name =($package['destination']['country']=='GB')?'United Kingdom of Great Britain and Northern Ireland':WC()->countries->countries[$this->country];
		$this->state        =$package['destination']['state'];
		$this->city         =$package['destination']['city'];
		$this->address      =isset($package['destination']['address1'])?$package['destination']['address1']:(isset($package['destination']['address'])?$package['destination']['address']:'');
		// $this->address2     =$package['destination']['address2'];
		$this->postalcode   =$package['destination']['postcode'];
		if( class_exists('WC_Shipping_Zones') ){
			$this->zone=WC_Shipping_Zones::get_zone_matching_package($package);
		}
		$this->shipping_class=array();
		$this->category=array();
		$this->line_items=$package['contents'];
		$this->total_units=0;
		$this->total_weight=0;
		$this->total_price=0;
		$this->product_weight=array();
		$this->product_quantity=array();
		$this->product_price=array();
		$this->rules_executed_successfully= array();
		$this->empty_responce_shipping_cost=50;
		$this->settings=get_option('woocommerce_wf_multi_carrier_shipping_settings');
		$boxes = ! empty($this->settings['boxes']) ? $this->settings['boxes'] : array();
		$this->fedex_default_boxes( $boxes );		// add the Fedex Default boxes
		$this->executed_products=array();
		$this->product_rule_mapping=array();
		$post_data=array();
		if ( isset( $_POST['post_data'] ) ) 
		{
			parse_str( $_POST['post_data'], $post_data );
		} 
		$recipient_addresss_residential=-1;
		if( is_array($post_data) && !empty($post_data))
		{   
			if( isset($post_data['ship_to_different_address']) )
			{
				if( isset($post_data['ph_shipping_is_residential']) )
				{
					$recipient_addresss_residential = $post_data['ph_shipping_is_residential'];
				}

			}else{

				if( isset($post_data['ph_billing_is_residential']) )
				{
					$recipient_addresss_residential = $post_data['ph_billing_is_residential'];
				}
			}
		}
		elseif(isset($_POST['ship_to_different_address']) && $_POST['ship_to_different_address']==1 && isset($_POST['ph_shipping_is_residential']))
		{
			$recipient_addresss_residential = $_POST['ph_shipping_is_residential'];
		}
		elseif(!isset($_POST['ship_to_different_address']) && isset($_POST['ph_billing_is_residential']))
		{
			$recipient_addresss_residential = $_POST['ph_billing_is_residential'];
		}

		
		$this->is_residential = $recipient_addresss_residential;
		
		foreach($this->line_items as $line_item)
		{   
			// WPML Global Object
			global $sitepress;

			$product 	= $line_item['data'];
			$quantity 	= $line_item['quantity'];
			$weight 	= (float)$product->get_weight();
			$price 		= $product->get_price();                            
			$pid 		= (WC()->version < '2.7.0')?$product->id:$product->get_id();  
			$unit_qnty 	= apply_filters( 'wf_multi_carrier_item_quantity', $quantity,$pid);
			$cur_total_weight 	= $weight * $quantity;
			$cur_total_price 	= $price * $quantity;
			$this->total_units 	+= $unit_qnty;
			$this->total_weight += $weight * $quantity;
			$this->total_price 	+= $cur_total_price;


			if(!isset($this->product_weight[$pid]))
			{
				$this->product_weight[$pid]=0;
			}
			if(!isset($this->product_quantity[$pid]))
			{
				$this->product_quantity[$pid]=0;
			}
			if(!isset($this->product_price[$pid]))
			{
				$this->product_price[$pid]=0;
			}
			$this->product_weight[$pid] 	= $weight;
			$this->products_arr[$pid] 		= $product;		// Array of Products. Format - array( 'product_id'  =>  WC_Product object);
			$this->product_quantity[$pid] 	+= $quantity;
			$this->product_price[$pid] 		= $price;

			if ( $product->needs_shipping() ) 
			{
				$cur_shipping_class 		=	'';
				$cur_shipping_class_id 		=	'';

				try {

					// Check for WPML Plugin
					if( $sitepress && ICL_LANGUAGE_CODE != null )
					{
						$wpml_default_lang 	= $sitepress->get_default_language();
						
						// Switch to the Default Language
						$sitepress->switch_lang( $wpml_default_lang );

						$cur_shipping_class_id 	= $product->get_shipping_class_id();

						$term 		= get_term_by( 'id', $cur_shipping_class_id, 'product_shipping_class' );

						// Switch back to the Current Language
						$sitepress->switch_lang( ICL_LANGUAGE_CODE );
						
						if ( $term && !is_wp_error( $term ) && ($term instanceof  WP_Term) ) 
						{
							$cur_shipping_class= $term->slug;
						}

					}else{
						$cur_shipping_class=$product->get_shipping_class();
					}

				} catch (Exception $e) {

				}                                    

				if(empty($cur_shipping_class) && ($product instanceof WC_Product_Variation))
				{
					$parent_data=$product->get_parent_data();   
					$cur_shipping_class_id='';
					if(isset($parent_data['shipping_class_id']))
					{
						$cur_shipping_class_id=$parent_data['shipping_class_id'];
					}

					if( $sitepress && ICL_LANGUAGE_CODE != null )
					{
						$wpml_default_lang 		= $sitepress->get_default_language();
						$cur_shipping_class_id 	= apply_filters( 'wpml_object_id', $cur_shipping_class_id, 'product_shipping_class', TRUE, $wpml_default_lang );

						// Switch to the Default Language
						$sitepress->switch_lang( $wpml_default_lang );

						$term = get_term_by( 'id', $cur_shipping_class_id, 'product_shipping_class' );

						// Switch back to the Current Language
						$sitepress->switch_lang( ICL_LANGUAGE_CODE );

					}else{

						$term = get_term_by( 'id', $cur_shipping_class_id, 'product_shipping_class' );
					}

					if ( $term && ! is_wp_error( $term ) ) 
					{
						$cur_shipping_class= $term->slug;
					}

				}

				if( $product->get_type() == 'variation' ){
					$id = ( WC()->version < '2.7.0' ) ? $product->id : $product->get_parent_id();
				}else{
					$id = (WC()->version < '2.7.0') ? $product->id : $product->get_id();
				}

				$cur_category_list=wp_get_post_terms( $id, 'product_cat', array( "fields" => "ids" ) );
				$this->add_meta_in_shipping_class($cur_shipping_class,$cur_total_weight,$cur_total_price,$unit_qnty,$pid);
				$this->add_meta_in_category($cur_category_list,$cur_total_weight,$cur_total_price,$unit_qnty,$pid);  
			}
		}
		$legacy = ! empty($this->settings['legacy_support']) ? $this->settings['legacy_support'] : 'yes';
		if( $legacy == 'yes' )
			$this->filter_the_rules();		// To set the selected rules. Set in $this->product_rule_mapping variable
		else
			$this->filter_the_rules_new();
	}

	public function xa_set_origin_address( $request ){

		if( ! empty($this->settings['origin_country_state']) ){
			$data           = $this->settings['origin_country_state'];
			$countryState   = explode(':',$data);
			$country        = ! empty($countryState[0]) ? $countryState[0] : '';
			$state          = ! empty($countryState[1]) ? $countryState[1] : '';                
		}
		elseif(!empty($this->settings['origin_country']) && !empty($this->settings['origin_custom_state'])  ){
			$country    = $this->settings['origin_country'];
			$state      = $this->settings['origin_custom_state'];
		}
		else{
			$country    = 'US';
			$state      = 'CA';
		} 
		// !empty($package['origin']) ? $package['origin'] : '';

		if( !empty($this->package['origin']['postcode']) && !empty($this->package['origin']['country']) ){
			$request['Common_Params']['Shipper_PersonName']          = '';
			$request['Common_Params']['Shipper_CompanyName']         = '';
			$request['Common_Params']['Shipper_PhoneNumber']         = '';
			$request['Common_Params']['Shipper_Address_StreetLines'] = !empty($this->package['origin']['address']) ? $this->package['origin']['address'] : '';
			$request['Common_Params']['Shipper_Address_City']        = !empty($this->package['origin']['city']) ? $this->package['origin']['city'] : '';
			$request['Common_Params']['Shipper_Address_StateOrProvinceCode'] = !empty($this->package['origin']['state']) ? $this->package['origin']['state'] : '';
			$request['Common_Params']['Shipper_Address_PostalCode']  =  $this->package['origin']['postcode'];
			$request['Common_Params']['Shipper_Address_CountryCode'] = $this->package['origin']['country'];
		}else{
			$request['Common_Params']['Shipper_PersonName']          = '';
			$request['Common_Params']['Shipper_CompanyName']         = '';
			$request['Common_Params']['Shipper_PhoneNumber']         = $this->settings['phone_number'];
			$request['Common_Params']['Shipper_Address_StreetLines'] = $this->settings['origin_addressline'];
			$request['Common_Params']['Shipper_Address_City']        = $this->settings['origin_city'];
			$request['Common_Params']['Shipper_Address_StateOrProvinceCode'] = $state;
			$request['Common_Params']['Shipper_Address_PostalCode']  =  $this->settings['origin_postcode'];
			$request['Common_Params']['Shipper_Address_CountryCode'] = $country;
		}
		$request['Common_Params']['TaxInformationIndicator']=$this->settings['tax_status'];
		return $request;
	}

	public function xa_set_carriers_accounts($request){
		$account_details = array(
			'fedex' => array(
				'api_key'           => $this->settings['fedex_api_key'],
				'api_password'      => $this->settings['fedex_api_pass'],
				'account_number'    => $this->settings['fedex_account_number'],
				'meter_number'      => $this->settings['fedex_meter_number'],
				'smartpost_indicia' => $this->settings['fedex_smartpost_indicia'],
				'smartpost_hubid'   => $this->settings['fedex_smartpost_hubid'],
			),
			'ups' => array(
				'key' 				=> $this->settings['ups_access_key'],
				'password' 			=> $this->settings['ups_password'],
				'account_number' 	=> $this->settings['ups_account_number'],
				'username' 			=> $this->settings['ups_user_id'],
				'negotiated'		=> $this->settings['negotiated'],
			),
			'usps' => array(
				'username' => $this->settings['usps_user_id'],
				'password' => $this->settings['usps_password'],
			),
			'stamps' => array(
				'username' => $this->settings['stamps_usps_user_id'],
				'password' => $this->settings['stamps_usps_password'],
			),
			'dhl' => array(
				'account_number' 	=> $this->settings['dhl_account_number'],
				'siteid' 			=> $this->settings['dhl_siteid'],
				'password' 			=> $this->settings['dhl_password'],
				'is_dutiable'		=> ( isset($this->settings['dhl_dutiable']) && !empty($this->settings['dhl_dutiable']) && $this->settings['dhl_dutiable'] == 'yes' ) ? true : false,
				'is_insured'		=> ( isset($this->settings['dhl_insured']) && !empty($this->settings['dhl_insured']) && $this->settings['dhl_insured'] == 'yes' ) ? 'yes' : 'no',
			),
		);
		$account_details = apply_filters('xa_multicarrier_carriers_accounts', $account_details, $this->package);

		$request['Common_Params']['fedex_key']              = $account_details['fedex']['api_key'];
		$request['Common_Params']['fedex_password']         = $account_details['fedex']['api_password'];
		$request['Common_Params']['fedex_account_number']   = $account_details['fedex']['account_number'];
		$request['Common_Params']['fedex_meter_number']     = $account_details['fedex']['meter_number'];
		$request['Common_Params']['fedex_smartpost_indicia']= $account_details['fedex']['smartpost_indicia'];
		$request['Common_Params']['fedex_smartpost_hubid']  = $account_details['fedex']['smartpost_hubid'];
		$request['Common_Params']['fedex_shipment_purpose'] = 'SOLD';
		$request['Common_Params']['ups_key']                = $account_details['ups']['key'];
		$request['Common_Params']['ups_password']           = $account_details['ups']['password'];
		$request['Common_Params']['ups_account_number']     = $account_details['ups']['account_number'];
		$request['Common_Params']['ups_username']           = $account_details['ups']['username'];
		$request['Common_Params']['ups_negotiated']         = isset($account_details['ups']['negotiated'])?$account_details['ups']['negotiated']:'yes';
		$request['Common_Params']['usps_username']          = $account_details['usps']['username'];
		$request['Common_Params']['usps_password']          = $account_details['usps']['password'];
		$request['Common_Params']['stamps_usps_username']   = $account_details['stamps']['username'];
		$request['Common_Params']['stamps_usps_password']   = $account_details['stamps']['password'];
		$request['Common_Params']['dhl_account_number']     = $account_details['dhl']['account_number'];
		$request['Common_Params']['dhl_siteid']             = $account_details['dhl']['siteid'];
		$request['Common_Params']['dhl_password']           = $account_details['dhl']['password'];
		$request['Common_Params']['dhl_dutiable']           = $account_details['dhl']['is_dutiable'];
		$request['Common_Params']['dhl_insured']           	= $account_details['dhl']['is_insured'];

		return $request;
	}

	function add_meta_in_shipping_class($cur_shipping_class,$weight,$price,$quantity,$pid)
	{
		if(!isset($this->shipping_class[$cur_shipping_class]))
		{
			$this->shipping_class[$cur_shipping_class]['weight']=0;
			$this->shipping_class[$cur_shipping_class]['price']=0;
			$this->shipping_class[$cur_shipping_class]['item']=0;                                
		}
		$this->shipping_class[$cur_shipping_class]['weight']+=$weight;
		$this->shipping_class[$cur_shipping_class]['price']+=$price;
		$this->shipping_class[$cur_shipping_class]['item']+= $quantity;            
		$this->shipping_class[$cur_shipping_class]['pid'][]=$pid;
	}

	function add_meta_in_category($cur_category_list,$weight,$price,$quantity,$pid)
	{
		foreach($cur_category_list as $category)
		{
			if(!isset($this->category[$category]))
			{
				$this->category[$category]['weight']=0;
				$this->category[$category]['price']=0;
				$this->category[$category]['item']=0;                                
			}
			$this->category[$category]['weight']+=$weight;
			$this->category[$category]['price']+=$price;
			$this->category[$category]['item']+= $quantity;        
			$this->category[$category]['pid'][]= $pid;              
		}	
	}

	function remove_meta_in_shipping_class($cur_shipping_class,$weight,$price,$quantity,$pid)
	{
		$this->shipping_class[$cur_shipping_class]['weight']-=$weight;
		$this->shipping_class[$cur_shipping_class]['price']-=$price;
		$this->shipping_class[$cur_shipping_class]['item']-= $quantity;    
		if(($key = array_search($pid, $this->shipping_class[$cur_shipping_class]['pid'])) !== false) 
		{
			unset($this->shipping_class[$cur_shipping_class]['pid'][$key]);
		}
	}

	function remove_meta_in_category($cur_category_list,$weight,$price,$quantity,$pid)
	{
		foreach($cur_category_list as $category)
		{
			$this->category[$category]['weight']-=$weight;
			$this->category[$category]['price']-=$price;
			$this->category[$category]['item']-= $quantity;    
			if(($key = array_search($pid, $this->category[$category]['pid'])) !== false) 
			{
				unset($this->category[$category]['pid'][$key]);
			}
		}
	}


	/**
	 * Function to get all the selected rules for the shipping rate calculation. Selected rules are getting set in varibale 
	 * $this->product_rule_mapping
	 */
	function filter_the_rules() {

		$order_total['weight']  = $this->total_weight;
		$order_total['item']    = $this->total_units;
		$order_total['price']   = $this->total_price;

		// Loop through all the rules array and get all the rules satisfying the condition
		foreach($this->rules_array as $key=>$rule) {

			// If area list not set for the rule then leave this rule
			$area_list=$rule['area_list'];
			if( empty($area_list) ) {
				continue;
			}

			foreach($area_list as $_area_key) {

				// Proceed if area list satisfy
				if( $this->check_rule_area($_area_key,$this->country,$this->state,$this->postalcode)===true)
				{
					$based_on           = ! empty($rule['cost_based_on']) ? $rule['cost_based_on'] : 'weight' ;		//default considered as weight
					$rule['min_weight'] = ! empty($rule['min_weight']) ? $rule['min_weight'] : 0 ;
					$rule['max_weight'] = ! empty($rule['max_weight']) ? $rule['max_weight'] : 999999999999 ;

					// To Check whether rule shipping class  matches or not
					if( ! empty($rule['shipping_class']) ) {

						foreach( $rule['shipping_class'] as $_shipping_class)
						{
							// If rule shipping class is set to any shipping class
							if($_shipping_class == 'any_shipping_class') {

								// If rule min weight not equal to zero and more than order total weight
								if( !empty($rule['min_weight']) && $order_total[$based_on] < $rule['min_weight'] ) {
									continue(3); 
								}

								// If rule max weight not equal to zero and less than order total weight
								if( !empty($rule['max_weight']) && $order_total[$based_on] > $rule['max_weight'] ) {
									continue(3); 
								}

								$this->product_rule_mapping[$key]['validforall']=1;
								break;
							}
							// end for If rule shipping class is set to any shipping class

							if(isset($this->shipping_class[$_shipping_class])) {

								if( $this->shipping_class[$_shipping_class][$based_on] >= $rule['min_weight'] &&
									$this->shipping_class[$_shipping_class][$based_on] <= $rule['max_weight'] ) {

									// If and logic is enabled, and cart contains more shipping class or more weight than rule then ignore the rule
									if( $this->settings['apply_and_logic'] == 'yes' ) {
										if( count($this->shipping_class) != count($rule['shipping_class']) || $order_total[$based_on] < $rule['min_weight'] || $order_total[$based_on] > $rule['max_weight'] ) {
											continue(3);
										}
									}

									if(!isset($this->product_rule_mapping[$key])) {
										$this->product_rule_mapping[$key]=array();
									}
									$this->product_rule_mapping[$key] = array_merge( $this->product_rule_mapping[$key],$this->shipping_class[$_shipping_class]['pid']);
								}
							}
							elseif( $this->settings['apply_and_logic'] == 'yes' ) {
								unset( $this->product_rule_mapping[$key] );
								continue(3);	// No rules to this shipping class, because of and logic failed
							}
						}	//end foreach of shipping class

						// If nothing matched depending on shipping class then go to next rule
						if( empty($this->product_rule_mapping[$key]) ) {
							continue(2);
						}
					}	// end if shipping class not empty

					$category_or = false;	// If and logic is not active then if any product category stisfies then consider the rule

					// Product Category test for the current rule
					if( ! empty($rule['product_category']) ) {

						foreach( $rule['product_category'] as $_category) {

							if( $_category=='any_product_category' ) {

								if( !empty($rule['min_weight']) && $order_total[$based_on] < $rule['min_weight'] ) {
									continue(3); 
								}
								if( !empty($rule['max_weight']) && $order_total[$based_on] > $rule['max_weight'] ) {
									continue(3); 
								}

								// If valid for all shipping class is set i.e any_shipping_class
								if( isset($this->product_rule_mapping[$key]['validforall']) ) {
									$this->product_rule_mapping[$key] = array_merge( $this->product_rule_mapping[$key],array_keys($this->product_weight));
								}
								continue(3);
							}

							// If rule product category is set but not set to any product category
							if(isset($this->category[$_category])) {

								// Unset valid for all since product category has been given
								unset($this->product_rule_mapping[$key]['validforall']);
								
								// If and logic is enabled, and cart contains more shipping class or more weight than rule then ignore the rule
								if( $this->settings['apply_and_logic'] == 'yes' ) {
									if( count($this->category) != count($rule['product_category']) || $order_total[$based_on] < $rule['min_weight'] || $order_total[$based_on] > $rule['max_weight'] ) {
										unset($this->product_rule_mapping[$key]);
										continue(3);
									}
								}

								if( $this->category[$_category][$based_on] >= $rule['min_weight'] &&
									$this->category[$_category][$based_on] <= $rule['max_weight'] ) {

									if(!isset($this->product_rule_mapping[$key])) {
										$this->product_rule_mapping[$key]=array();
									}
									$this->product_rule_mapping[$key] = array_unique( array_merge( $this->product_rule_mapping[$key], $this->category[$_category]['pid'] ) );
								}
								$category_or = true;
							}
							elseif( $this->settings['apply_and_logic'] == 'yes') {
								// Shipping category didn't matched so unset this rule from the selected rule
								unset($this->product_rule_mapping[$key]);
								continue(3);
							}
							elseif( ! $category_or ) {
								unset($this->product_rule_mapping[$key]);
							}
						}
					}	// End of product category operation

					if( empty($rule['shipping_class']) && empty( $rule['product_category'] ) ) {
						$this->product_rule_mapping[$key]['validforall']=1;
					}
					continue(2);
				}	// End of if condition for area check
			}	// end for, for loop of area list
		}
		// Now all selected rules has been set into $this->product_rule_mapping
	}

	public function filter_the_rules_new() {
		$this->order_total              = array(
			'weight'    =>  $this->total_weight,
			'item'      =>  $this->total_units,
			'price'     =>  $this->total_price
		);
		$this->all_shipping_class = array_keys($this->shipping_class);
		$this->all_prod_categoies = array_keys($this->category);

		$this->filter_the_rule_based_on_area();
		if( ! empty($this->selected_rules) ) {
			if( $this->settings['apply_and_logic'] == 'yes' ) {
				$this->filter_rule_for_and_logic();
			}
			else{
				$this->filter_the_rule_based_on_shipping_class_and_categories();
			}
		}
	}

	/**
	 * Filter the rules based on Area.
	 * Sets the filtered rule in $this->selected_rules
	 */
	private function filter_the_rule_based_on_area() {
		foreach($this->rules_array as $key=>$rule) {
			$area_list = ! empty($rule['area_list']) ? $rule['area_list'] : array();
			foreach($area_list as $_area_key) {
				if( $this->check_rule_area($_area_key,$this->country,$this->state,$this->postalcode) === true ) {
					$this->selected_rules[$key] = $rule;
				}
			}
		}
	}

	/**
	 * Filter the rules if and logic is enabled.
	 * Sets the filtered rule in $this->selected_rules
	 */
	private function filter_rule_for_and_logic() {
		foreach( $this->selected_rules as $key => $rule ) {
			$cost_based_on = $rule['cost_based_on'];
			if( $this->order_total[$cost_based_on] < $rule['min_weight'] || $this->order_total[$cost_based_on] > $rule['max_weight'] ) {
				unset( $this->selected_rules[$key]);
				continue;
			}

			$matched_categories             = array_intersect( $rule['product_category'], $this->all_prod_categoies );
			$matched_categories_count       = count($matched_categories);
			$all_product_categories_count   = count($this->all_prod_categoies);
			$rule_categories_count          = count($rule['product_category']);

			// Empty Shipping Class or ANy Shipping Class
			if( ( empty($rule['shipping_class']) || in_array( 'any_shipping_class', $rule['shipping_class']) ) ) {
				// Empty Product Category or Any Product Category
				if( ( empty($rule['product_category']) || in_array( 'any_product_category', $rule['product_category']) ) ) {
					continue;
				}
				// Product Categories set
				else{
					if( ! ($matched_categories_count == $all_product_categories_count && $matched_categories_count == $rule_categories_count) ) {
						unset( $this->selected_rules[$key]);        // Rule Not Matched
					}
				}
			}
			// Shipping Class Set
			else{
				$matched_shipping_classes       = array_intersect( $rule['shipping_class'], $this->all_shipping_class );
				$matched_shipping_class_count   = count($matched_shipping_classes);
				$all_shipping_class_count       = count($this->all_shipping_class);
				$rule_shipping_class_count      = count($rule['shipping_class']);
				if( $matched_shipping_class_count == $rule_shipping_class_count && $all_shipping_class_count == $rule_shipping_class_count) {
					if( ( empty($rule['product_category']) || in_array( 'any_product_category', $rule['product_category']) ) ) {
						continue;
					}
					// Product Categories set
					else{
						if( ! ($matched_categories_count == $all_product_categories_count && $matched_categories_count == $rule_categories_count) ) {
							unset( $this->selected_rules[$key]);        // Rule Not Matched
						}
					}
				}
				else{
					unset( $this->selected_rules[$key]);        // Rule Not Matched
				}
			}
		}
		// Set the Rule Mapping
		if( ! empty($this->selected_rules) ) {
			$ids=array();
			foreach( $this->line_items as $line_item ) {
				if(isset($line_item['data']) && $line_item['data']->get_id())
					$ids[] = $line_item['data']->get_id();
			}
			// Add all the product ids to product mapping since strict and
			foreach( $this->selected_rules as $key => $rule ) {
				$this->product_rule_mapping[$key] = $ids;
			}
		}
	}

	private function filter_the_rule_based_on_shipping_class_and_categories() {

		// WPML Global Object
		global $sitepress;

		foreach( $this->selected_rules as $key => $rule ) {
			$product_details = array(
				'weight'    =>  null,
				'price'     =>  null,
				'item'      =>  null,
				'ids'       =>  array()
			);

			$status 				= false;
			$shipping_class 		= null;
			$product_categories 	= array();

			foreach( $this->line_items as $line_item ) {

				// Check for WPML Plugin
				if( $sitepress && ICL_LANGUAGE_CODE != null )
				{
					$wpml_default_lang 	= $sitepress->get_default_language();

					// Switch to the Default Language
					$sitepress->switch_lang( $wpml_default_lang );

					$cur_shipping_class_id 	= $line_item['data']->get_shipping_class_id();

					$term 		= get_term_by( 'id', $cur_shipping_class_id, 'product_shipping_class' );

					// Switch back to the Current Language
					$sitepress->switch_lang( ICL_LANGUAGE_CODE );

					if ( $term && !is_wp_error( $term ) && ($term instanceof  WP_Term) ) 
					{
						$shipping_class= $term->slug;
					}

				}else{
					$shipping_class = $line_item['data']->get_shipping_class();
				}

				if( empty($rule['shipping_class']) || in_array( 'any_shipping_class', $rule['shipping_class']) || in_array( $shipping_class, $rule['shipping_class']) ) {
					$product                    = is_a( $line_item['data'], 'WC_Product_Variation' ) ? wc_get_product($line_item['product_id']) : $line_item['data'];
					
					// Check for WPML Plugin
					if( $sitepress )
					{
						// Product Categories
						$product_categories 		= wp_get_post_terms( $line_item['product_id'], 'product_cat', array( "fields" => "ids" ) );
					}else{
						// Product Categories
						$product_categories         = $product->get_category_ids();
					}
					
					$matched_product_categories = array_intersect( $product_categories, $rule['product_category'] );
					if( ( empty($rule['product_category']) || in_array( 'any_product_category', $rule['product_category']) ) || ! empty($matched_product_categories) ) {
						$product_details['weight']  = $product_details['weight'] + (float)$line_item['data']->get_weight() * (int) $line_item['quantity'];
						$product_details['price']   = $product_details['price'] + (float)$line_item['data']->get_price() * (int) $line_item['quantity'];
						$product_details['item']    = $product_details['item'] + $line_item['quantity'];
						$product_details['ids'][]   = $line_item['data']->get_id();
						$status = true;
					}
				}
			}
			
			$cost_based_on = $rule['cost_based_on'];
			if( $status ) {
				if( $rule['min_weight'] < $product_details[$cost_based_on] && ( empty($rule['max_weight']) || $rule['max_weight'] >= $product_details[$cost_based_on] ) ) {
					$this->product_rule_mapping[$key] = $product_details['ids'];
				}
			}
			else{
				unset($this->selected_rule[$key]);
			}
		}
	}

	/**
	 * Check whether Rule Matches or not based on Area Settings.
	 * @param int $rule_area_key Rule Area key
	 * @param string $country Country Code
	 * @param string $state State Code
	 * @param string $postalcode Postal Code
	 * @return boolean
	 */
	public function check_rule_area( $rule_area_key, $country, $state, $postalcode ) {

		if( empty($this->area_settings) ) {
			$this->area_settings = get_option('woocommerce_wf_multi_carrier_shipping_area_settings');
		}
		$status         = false;
		$tmp            = $this->area_settings;
		$area_matrix    = $tmp['area_matrix'];
		$area           = $area_matrix[$rule_area_key];
		$selected_areas = array();
		// Area Name and details like zone_list or country_list etc should be set
		if( count($area) > 1 ) {
			if( isset($area['zone_list']) ) {               // Zone Check
				if( isset($this->zone) ) {
					$package_zone = (string) ( (WC()->version < '2.7.0') ? $this->zone->get_zone_id() : $this->zone->get_id()) ;
					if( in_array($package_zone, $area['zone_list'] ) !== false ) {
						$status = true;
					}
				}
			}
			elseif( isset($area['country_list']) ) {        // Country Check
				if( in_array($country, $area['country_list']) ) {
					$status = true;
				}
			}
			elseif( isset($area['state_list']) ) {          // State Check
				if( ! empty($state) && ( in_array( 'any_state', $area['state_list']) || in_array($country.':'.$state, $area['state_list']) ) )
					$status = true;
			}
			elseif( isset($area['postal_code']) ) {     // Postal Code Check
				if( ! empty($postalcode) && ( preg_match( '/'.$area['postal_code'].'/', $postalcode, $array) || strpos( $area['postal_code'], $postalcode) !== false ) )
					$status = true;
			}
		}
		
		return $status;
	}
	
	function calculate_shipping_cost($rules=array(),$product_rule_mapping=array())
	{
		$totalcost=0;

		if(empty($rules))
		{
			$rules=$this->rules_array;
		}
		if(empty($product_rule_mapping))
		{
			$product_rule_mapping=$this->product_rule_mapping;
		}
		//var_dump($this->product_rule_mapping);
		$this->empty_responce_shipping_cost=$this->settings['empty_responce_shipping_cost'];
		$this->empty_responce_shipping_cost_on=$this->settings['empty_responce_shipping_cost_on'];

		if(!empty($product_rule_mapping))
		{
			//var_dump($this->settings['weight_packing_process']);
			$packing_method=$this->settings['packing_method'];
			$packing_process=$this->settings['weight_packing_process'];
			$box_max_weight=$this->settings['box_max_weight'];
			if($packing_method=='per_item')
			{
				if(!class_exists('per_item_packing')) {include('class-per-item-packing.php');}
				$pack=new per_item_packing($this->product_weight,$this->product_quantity,$this);

				foreach($product_rule_mapping as $rule_no=>$pids)
				{   
					//creating packages according to form field packaging process and getting rates from api
					$company= $this->rules_array[$rule_no]['shipping_companies']? $this->rules_array[$rule_no]['shipping_companies']:'';
					$service= isset($this->rules_array[$rule_no]['shipping_services'])?$this->rules_array[$rule_no]['shipping_services']:'';
					$bucket=array();
					if(isset($product_rule_mapping[$rule_no]['validforall']))
					{
						$pids= array_keys($this->product_quantity);
					}
					foreach($pids as $_pid)
					{
						if(array_search($_pid, $this->executed_products)===false) 
						{                                           
							$this->executed_products[]=$_pid;
							$bucket[]=$_pid;
						}
					}
					$pack->per_item_packing_add_package_to_request($bucket, $company,$service,$rule_no);
				}

				$totalcost= $pack->get_Rates_From_Api();
				$this->main_class->api_rates=$this->api_rates;
				//return $total_cost;
			}
			elseif($packing_method=='weight_based')
			{

				if(!class_exists('weight_based_packing'))  include('class-weight-based-packing.php');
				$pack=new weight_based_packing($this->product_weight,$this->product_quantity,$packing_process,$box_max_weight,$this,$this->product_price);
				$totalcost= $pack->get_Rates_From_Api();
				$this->main_class->api_rates=$this->api_rates;

			}
			elseif($packing_method=='box_packing')
			{
				if(!class_exists('box_packing'))  include('class-box-packing.php');
				$boxes = $this->settings['boxes'];

				// Need to do check for current
				$pack=new box_packing($this->product_quantity,array(current(array_keys($product_rule_mapping)) => current($product_rule_mapping)),$boxes,$this,$this->product_price);
				$totalcost= $pack->get_Rates_From_Api();
				$this->main_class->api_rates=$this->api_rates;
			}
			else
			{
				new WP_Error( 'broke', __( "No Packing Method Found!!", "eha_multi_carrier_shipping" ) );
			}

			// Handle API rates with no response
			if( $totalcost === false ) {
				return false;
			}
			// Apply conversion rate to the rate returned by Shipping Carriers
			if( ! empty($totalcost) ) {
				$totalcost = $this->apply_conversion_rate_on_shipping_rate( $totalcost, $rules, $product_rule_mapping);
			}
		}
		else{
			new WP_Error( 'broke', __( "No Rules Applied", "eha_multi_carrier_shipping" ) );
		}

		// Flat Rate With Zero Cost
		if( empty($totalcost) ) {
			return 0;
		}

		return $this->check_executed_rules_and_add_extra_cost($totalcost);
	}


	/**
	 * Get the Shipping cost after the conversion rate has been applied.
	 * @param $shipping_cost int | float Shipping Cost
	 * @param $rules array Array of rules
	 * @param $product_rule_mapping array Selected rule from the groups
	 * @return float | int Shipping Cost
	*/
	private function apply_conversion_rate_on_shipping_rate( $shipping_cost, $rules, $product_rule_mapping ) {

		reset($product_rule_mapping);                           // Set the array pointer to first key 
		$key                    = key($product_rule_mapping);   // Get the key of the selected rule in group of rules
		$shipping_carrier_name  = $rules[$key]['shipping_companies']; // Get the selected rule shipping carrier name

		switch( $shipping_carrier_name ) {

			case 'fedex'        :  if( ! empty($this->settings['fedex_conversion_rate']) ) {
				$shipping_cost *= $this->settings['fedex_conversion_rate'];
			}
			break;

			case 'ups'          :  if( ! empty($this->settings['ups_conversion_rate']) ) {
				$shipping_cost *= $this->settings['ups_conversion_rate'];
			}
			break;

			case 'usps'         :  if( ! empty($this->settings['usps_conversion_rate']) ) {
				$shipping_cost *= $this->settings['usps_conversion_rate'];
			}
			break;

			case 'stamps_usps'  :  if( ! empty($this->settings['stamps_usps_conversion_rate']) ) {
				$shipping_cost *= $this->settings['stamps_usps_conversion_rate'];
			}
			break;

			case 'dhl'          :  if( ! empty($this->settings['dhl_conversion_rate']) ) {
				$shipping_cost *= $this->settings['dhl_conversion_rate'];
			}
			break;

			default      :      break;
		}
		return $shipping_cost;
	}


	/**
	 * Include Fedex default boxes to the boxes in settings .
	 * @param $boxes array Array of boxes .
	 */
	public function fedex_default_boxes( $boxes = array() ) {

		$this->dimension_unit       = strtolower( get_option( 'woocommerce_dimension_unit' ));
		$this->weight_unit          = strtolower(strtolower( get_option('woocommerce_weight_unit') ));
		$this->fedex_default_boxes  = array();
		
		if( $this->dimension_unit == 'cm' && $this->weight_unit == 'kg' ) {
			$this->fedex_default_boxes    = include('data-wf-fedex-box-sizes-cm.php');
		}
		elseif( $this->dimension_unit == 'in' && $this->weight_unit == 'lbs' ) {
			$this->fedex_default_boxes    = include('data-wf-fedex-box-sizes-in.php');
		}

		if ( version_compare(PHP_VERSION, '5.5', '<') )
		{
			$default_boxes_ids_arr = array_map(function($element){return $element['id'];}, $this->fedex_default_boxes);
		}else{
			$default_boxes_ids_arr = array_column( $this->fedex_default_boxes, 'id' );
		}
		
		foreach ( $boxes as $key => $value ) {
			if( ! empty($key) && in_array( $key, $default_boxes_ids_arr) ) {
				// 0 is always available in any array, our fedex boxes id contains FEDEX keyword so empty check won't affect anything

				$default_box_key = array_search( $key, $default_boxes_ids_arr);
				if( $value['enabled'] ) {
					$boxes[$key] = $this->fedex_default_boxes[$default_box_key];
				}
				else {
					unset($boxes[$key]);
				}
			}
		}
		$this->settings['boxes'] = $boxes;
	}

	/**
	 * Function to check executed rule and add base cost and cost per unit to the shipping rates returned by api.
	*/

	function check_executed_rules_and_add_extra_cost($totalcost) {

		if($totalcost<=0 && empty($this->rules_executed_successfully) && isset($this->settings['show_shipping_group']) && $this->settings['show_shipping_group']=='yes' ) {      
			return 0;
		}

		// To add flat rate and Cost per unit to the api rates
		$rule_executed_successfully = $this->rules_executed_successfully;
		$rule_executed_successfully = array_unique($rule_executed_successfully);
		foreach( $rule_executed_successfully as $rule_no) {

			$rule = $this->rules_array[$rule_no];

			if( $rule['shipping_companies'] !== 'flatrate' ) {
				// Cost adjustment
				if( $rule['cost_based_on'] == 'weight' || empty($rule['cost_based_on']) ) {
					$cost_adjustment = (float)$rule['cost_per_unit'] * $this->total_weight; 
				}
				elseif( $rule['cost_based_on'] == 'item' ) {
					$cost_adjustment = (float)$rule['cost_per_unit'] * $this->total_units; 
				}
				else {
					$cost_adjustment = (float)$rule['cost_per_unit'] * $this->total_price; 
				}
				
				$totalcost+= ( (float)$rule['fee'] + $cost_adjustment );

				// Calculate % Adjustment
				if( isset($rule['adjustment']) && !empty($rule['adjustment']) )
				{
					$totalcost = $totalcost + ( $totalcost * ( (float)$rule['adjustment'] / 100 ) );
				}
			}
		}
		return apply_filters('ph_multicarrier_total_shipping_cost',$totalcost);
	}

}
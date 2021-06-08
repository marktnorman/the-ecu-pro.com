<?php
if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	class eha_multi_carrier_shipping_method extends WC_Shipping_Method {
		function __construct() {

			// WPML Global Object
			global $sitepress;

			// Javascript call for registering the api won't work for Site Running on HTTPS
			if(isset($_SERVER['HTTPS']) && isset($_POST['btn_getkey'])) {
				$register_api_url = $GLOBALS['eha_API_URL']."/api/shippings/register";
				$content 	='{"email":"'.$_POST['woocommerce_wf_multi_carrier_shipping_emailid'].'","host":"'.$_SERVER['SERVER_NAME'].'"}';
				$response = wp_remote_post( $register_api_url, array(
					'body'	=>	$content,
				));

				if( is_wp_error($response) ){
					die(print_r($response->get_error_message(),true));
				}
				elseif( ! empty($response['body'])){
					die(print_r($response['body'],true));
				}
				else{
					die(print_r($response,true));
				}
			}

			$plugin_config = wf_plugin_configuration_mcp();
			$this->id		   = $plugin_config['id']; 
			$this->method_title	 = __( $plugin_config['method_title'], 'eha_multi_carrier_shipping' );
			$this->method_description = __( $plugin_config['method_description'], 'eha_multi_carrier_shipping' );
			$this->wf_multi_carrier_shipping_init_form_fields();
			$this->init_settings();
			$this->title 	= $this->settings['title'];
			$this->enabled = $this->settings['enabled'];
			//$this->debug = $this->get_option('debug');				
			$this->ship_from_address	= !empty($this->settings['ship_from_address']) ? $this->settings['ship_from_address'] : 'origin_address';
			$this->tax_status	   		= $this->settings['tax_status'];
			$this->rate_matrix	   		= $this->settings['rate_matrix'];
			$this->multiselect_act_class	=	'multiselect';
			$this->drop_down_style	=	'chosen_select select2';			
			$this->debug					= isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : false;
			$this->drop_down_style.=	$this->multiselect_act_class;
			$this->dimension_unit 	=strtolower( get_option( 'woocommerce_dimension_unit' ));
			$this->weight_unit 		= strtolower(strtolower( get_option('woocommerce_weight_unit') ));
			$this->boxes		   	= $this->get_option( 'boxes', array( ));

			// Fedex default boxes
			$this->default_boxes	= array();
			if( $this->dimension_unit == 'cm' && $this->weight_unit == 'kg' ) {
				$this->default_boxes 	= include('data-wf-fedex-box-sizes-cm.php');
			}
			elseif( $this->dimension_unit == 'in' && $this->weight_unit == 'lbs' ) {
				$this->default_boxes 	= include('data-wf-fedex-box-sizes-in.php');
			}
			$this->shipping_classes =WC()->shipping->get_shipping_classes();

			// Product Category Taxonomy
			$taxonomy 	= 'product_cat';
			
			// Check for WPML Plugin
			if ( $sitepress && ICL_LANGUAGE_CODE != null) {

				$wpml_default_lang 	= apply_filters( 'wpml_default_language', NULL );

				// Switch to the Default Language
				$sitepress->switch_lang( $wpml_default_lang );
				
				// Get All Product Categories
				$this->product_category = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'id=>name' ) );

				// Switch back to the Current Language
				$sitepress->switch_lang( ICL_LANGUAGE_CODE );

			} else {
				$this->product_category = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'id=>name' ) );
			}

			// Save settings in admin
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		public function wf_product_category_dropdown_options( $selected_categories = array())
		{
			if ($this->product_category) foreach ( $this->product_category as $product_id=>$product_name) :
				echo '<option value="' . $product_id .'"';
			if (!empty($selected_categories) && in_array($product_id,$selected_categories)) echo ' selected="selected"';
			echo '>' . esc_js( $product_name ) . '</option>';
		endforeach;
	}

	public function wf_shipping_class_dropdown_options( $selected_class = array())
	{
		if ($this->shipping_classes) foreach ( $this->shipping_classes as $class) :
			echo '<option value="' . esc_attr($class->slug) .'"';
		if (!empty($selected_class) && in_array($class->slug,$selected_class)) echo ' selected="selected"';
		echo '>' . esc_js( $class->name ) . '</option>';
	endforeach;
}

	function wf_multi_carrier_shipping_init_form_fields()
	{
		global $woocommerce;
		$ship_from_address_options = apply_filters('wf_filter_label_ship_from_address_options', array('origin_address' => __('Origin Address', 'eha_multi_carrier_shipping') ) );

		$this->form_fields = array(
			
			'enabled'	=> array(
				'title'   => __( 'Enable/Disable', 'eha_multi_carrier_shipping' ),
				'type'	=> 'checkbox',
				'label'   => __( 'Enable this shipping method', 'eha_multi_carrier_shipping' ),
				'default' => 'yes',
			),
			'emailid'	=> array(
				'title'   => __( 'Shipping API Email ID', 'eha_multi_carrier_shipping' ),
				'type'	=> 'text',
				'description'   => __( 'Enter your email id to enable shipping api', 'eha_multi_carrier_shipping' )
			),					
			'apikey'	=> array(
				'title'   => __( 'Shipping API Key', 'eha_multi_carrier_shipping' ),
				'type'	=> 'text',
				'description'   => __( 'Enter your api key received in email', 'eha_multi_carrier_shipping' ),
				'class'=>'keybtn',
			),
			'test_mode'	=> array(
				'title'   => __( 'Test Mode', 'eha_multi_carrier_shipping' ),
				'type'	=> 'checkbox',
				'label'   => __( 'Use test environment in all shipping carriers', 'eha_multi_carrier_shipping' ),
				'default' => 'yes',
			),
			'debug'	=> array(
				'title'   => __( 'Debug', 'eha_multi_carrier_shipping' ),
				'type'	=> 'checkbox',
				'label'   => __( 'Debug this shipping method', 'eha_multi_carrier_shipping' ),
				'default' => 'no',
			),
			'title'	  => array(
				'title'	   => __( 'Method Title', 'eha_multi_carrier_shipping' ),
				'type'		=> 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'eha_multi_carrier_shipping' ),
				'default'	 => __( $this->method_title, 'eha_multi_carrier_shipping' ),
			),
			'shipper_settings'   => array(
				'title'		   => __( 'Shipper Settings', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title',
				'class'			=> 'wf_settings_hidden_tab'
			),
			'ship_from_address'   => array(
				'title'		   => __( 'Ship From Address Preference', 'eha_multi_carrier_shipping' ),
				'type'			=> 'select',
				'default'		 => 'origin_address',
				'options'		 => $ship_from_address_options,
				'description'	 => __( 'You can choose vendo address, if you are configured with multivendor plugin', 'eha_multi_carrier_shipping' ),
				'desc_tip'		=> true
			),			
			'origin_addressline'  => array(
				'title'		   => __( 'Origin Address', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Shipping address (ship from address).', 'eha_multi_carrier_shipping' ),
				'default'		 => 'Address Line 1',
				'desc_tip'		=> true
			),
			'origin_city'	  	  => array(
				'title'		   => __( 'Origin City', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'City (ship from city)', 'eha_multi_carrier_shipping' ),
				'default'		 => 'Los Angeles',
				'desc_tip'		=> true
			),
			'origin_country_state'		=> array(
				'type'			=> 'state_list',
				'desc_tip'		=> true,
			),
			'origin_postcode'	 => array(
				'title'		   => __( 'Origin Postcode', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Ship from zip/postcode.', 'eha_multi_carrier_shipping' ),
				'default'		 => '90001',
				'desc_tip'		=> true
			),
			'phone_number'		=> array(
				'title'		   => __( 'Origin Phone Number', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Your contact phone number.', 'eha_multi_carrier_shipping' ),
				'default'		 => '5555555555',
				'desc_tip'		=> true
			),					
			'fedex_settings'   => array(
				'title'		   => __( 'FedEx Settings', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title',
				'class'			=> 'wf_settings_hidden_tab'
			),
			'fedex_account_number'		   => array(
				'title'		   => __( 'FedEx Account Number', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => '',
				'default'		 => '',
				'class'=>'fedex_api_setting'
			),
			'fedex_meter_number'		   => array(
				'title'		   => __( 'FedEx Meter Number', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => '',
				'default'		 => '',
				'class'=>'fedex_api_setting'
			),
			'fedex_api_key'		   => array(
				'title'		   => __( 'FedEx Web Services Key', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => '',
				'default'		 => '',
				'custom_attributes' => array(
					'autocomplete' => 'off'
				),
				'class'=>'fedex_api_setting'
			),
			'fedex_api_pass'		   => array(
				'title'		   => __( 'FedEx Web Services Password', 'eha_multi_carrier_shipping' ),
				'type'			=> 'password',
				'description'	 => '',
				'default'		 => '',
				'custom_attributes' => array(
					'autocomplete' => 'off'
				),
				'class'=>'fedex_api_setting'
			),
			'fedex_smartpost_indicia'		   => array(
				'title'		   => __( 'FedEx SmartPost Indicia (Optional)', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => '',
				'default'		 => 'PARCEL_SELECT',
				'custom_attributes' => array(
					'autocomplete' => 'off'
				),
				'class'=>'fedex_api_setting'
			),
			'fedex_smartpost_hubid'		   => array(
				'title'		   => __( 'FedEx Smartpost Hub ID (Optional)', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => '',
				'default'		 => '',
				'custom_attributes' => array(
					'autocomplete' => 'off'
				),
				'class'=>'fedex_api_setting'
			),

			'fedex_conversion_rate'	=> array(
				'title'					=> __( 'Conversion Rate', 'eha_multi_carrier_shipping' ),
				'type'					=> 'text',
				'description'			=> __( 'Provide the conversion rate for FedEx shipping rates.', 'eha_multi_carrier_shipping' ),
				'desc_tip'				=> true
			),
			'fedex_one_rate'	=> array(
				'title'		   => __( 'FedEx One Rate', 'eha_multi_carrier_shipping' ),
				'label'		   => __( 'Enable/Disable', 'eha_multi_carrier_shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __( 'FedEx One Rates will be offered if the items are packed into a valid FedEx One box, and the origin and destination is the US. For other countries this option will enable FedEx packing. Note: All FedEx boxes are not available for all countries, disable this option or disable different boxes if you are not receiving any shipping services.', 'eha_multi_carrier_shipping' ),
				'desc_tip'		=> true
			),

			'ups_settings'   => array(
				'title'		   => __( 'UPS Settings', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title',
				'class'			=> 'wf_settings_hidden_tab'
			),
			'ups_user_id'			 => array(
				'title'		   => __( 'UPS User ID', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Obtained from UPS after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'ups_password'			=> array(
				'title'		   => __( 'UPS Password', 'eha_multi_carrier_shipping' ),
				'type'			=> 'password',
				'description'	 => __( 'Obtained from UPS after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'ups_access_key'		  => array(
				'title'		   => __( 'UPS Access Key', 'eha_multi_carrier_shipping' ),
				'type'			=> 'password',
				'description'	 => __( 'Obtained from UPS after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'ups_account_number'	  => array(
				'title'		   => __( 'UPS Account Number', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Obtained from UPS after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'negotiated'		  => array(
				'title'		   => __( 'Negotiated Rates', 'eha_multi_carrier_shipping' ),
				'label'		   => __( 'Enable', 'eha_multi_carrier_shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'yes',
				'description'	 => __( 'Enable this if this shipping account has negotiated rates available.', 'eha_multi_carrier_shipping' ),
				'desc_tip'		=> true
			),

			'ups_conversion_rate'	=> array(
				'title'					=> __( 'Conversion Rate', 'eha_multi_carrier_shipping' ),
				'type'					=> 'text',
				'description'			=> __( 'Provide the conversion rate for UPS shipping rates.', 'eha_multi_carrier_shipping' ),
				'desc_tip'				=> true
			),

			'usps_settings'   => array(
				'title'		   => __( 'U.S.P.S Settings', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title',
				'class'			=> 'wf_settings_hidden_tab'
			),
			'usps_user_id'			 => array(
				'title'		   => __( 'USPS User ID', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Obtained from USPS after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'usps_password'			=> array(
				'title'		   => __( 'USPS Password', 'eha_multi_carrier_shipping' ),
				'type'			=> 'password',
				'description'	 => __( 'Obtained from USPS after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),

			'usps_conversion_rate'	=> array(
				'title'					=> __( 'Conversion Rate', 'eha_multi_carrier_shipping' ),
				'type'					=> 'text',
				'description'			=> __( 'Provide the conversion rate for USPS shipping rates.', 'eha_multi_carrier_shipping' ),
				'desc_tip'				=> true
			),

			'stamps_usps_settings'   => array(
				'title'		   => __( 'Stamps USPS Settings', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title',
				'class'			=> 'wf_settings_hidden_tab'
			),
			'stamps_usps_user_id'			 => array(
				'title'		   => __( 'Stamps USPS User ID', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Obtained from Stamps after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'stamps_usps_password'			=> array(
				'title'		   => __( 'Stamps USPS Password', 'eha_multi_carrier_shipping' ),
				'type'			=> 'password',
				'description'	 => __( 'Obtained from Stamps after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),

			'stamps_usps_conversion_rate'	=> array(
				'title'					=> __( 'Conversion Rate', 'eha_multi_carrier_shipping' ),
				'type'					=> 'text',
				'description'			=> __( 'Provide the conversion rate for Stamps shipping rates.', 'eha_multi_carrier_shipping' ),
				'desc_tip'				=> true
			),


			'dhl_settings'   => array(
				'title'		   => __( 'DHL Express Settings', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title',
				'class'			=> 'wf_settings_hidden_tab'
			),
			'dhl_account_number'			 => array(
				'title'		   => __( 'DHL Account No.', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Obtained from DHL after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'dhl_siteid'			=> array(
				'title'		   => __( 'DHL Site ID', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'description'	 => __( 'Obtained from DHL after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'dhl_password'			=> array(
				'title'		   => __( 'DHL Password', 'eha_multi_carrier_shipping' ),
				'type'			=> 'password',
				'description'	 => __( 'Obtained from DHL after getting an account.', 'eha_multi_carrier_shipping' ),
				'default'		 => '',
				'desc_tip'		=> true
			),
			'dhl_conversion_rate'	=> array(
				'title'					=> __( 'Conversion Rate', 'eha_multi_carrier_shipping' ),
				'type'					=> 'text',
				'description'			=> __( 'Provide the conversion rate for DHL shipping rates.', 'eha_multi_carrier_shipping' ),
				'desc_tip'				=> true
			),
			'dhl_dutiable'		  => array(
				'title'		   => __( 'Dutiable Shipments', 'eha_multi_carrier_shipping' ),
				'label'		   => __( 'Enable', 'eha_multi_carrier_shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __( 'Enable this for Dutiable Shipments', 'eha_multi_carrier_shipping' ),
				'desc_tip'		=> true
			),
			'dhl_insured'		  => array(
				'title'		   => __( 'Insurance', 'eha_multi_carrier_shipping' ),
				'label'		   => __( 'Enable Insurance', 'eha_multi_carrier_shipping' ),
				'type'			=> 'checkbox',
				'default'		 => 'no',
				'description'	 => __( 'Enable if your products are insured', 'eha_multi_carrier_shipping' ),
				'desc_tip'		=> true
			),


			'rate_matrix_title'   => array(
				'title'		   => __( 'Rule Table', 'eha_multi_carrier_shipping' ),
				'type'			=> 'title'
			),

			'rate_matrix' => array(
				'type' 			=> 'rate_matrix'
			),
			'show_shipping_group'	=> array(
				'title'   => __( 'Show Method Groups', 'eha_multi_carrier_shipping' ),
				'type'	=> 'checkbox',
				'label'   => __( '  (Show multiple shipping methods, one for every group)', 'eha_multi_carrier_shipping' ),
				'default' => 'no',
			), 
			'is_recipient_address_residential'			  => array(
				'title'			  => __( 'Recipient is Residential Address', 'eha_multi_carrier_shipping' ),
				'label'			  => __( 'Yes', 'eha_multi_carrier_shipping' ),
				'type'			   => 'checkbox',
				'default'			=> 'no'
			),
			'apply_and_logic'	=> array(
				'title'			=> __( 'AND Logic', 'eha_multi_carrier_shipping' ),
				'label'			=> __( 'Enable', 'eha_multi_carrier_shipping' ),
				'type'			=> 'checkbox',
				'description'	=> __( 'If it is enabled then it will apply and logic else or logic on shipping class, on product category field.', 'eha_multi_carrier_shipping' ),
				'default'		=> 'no'
			),
			'legacy_support'	=>	array(
				'title'			=>	__( 'Legacy Support', 'eha_multi_carrier_shipping' ),
				'label'			=>	__( 'Enable', 'eha_multi_carrier_shipping' ),
				'type'			=>	'checkbox',
				'default'		=>	'yes',
				'description'	=>	__( 'Enable it to Support the previously configured rule. (Not recommended)', 'eha_multi_carrier_shipping')
			),
			'legacy_api_support'	=>	array(
				'label'			=>	__( 'Enable it to use Multi-Carrier API 1.0 (Old)', 'eha_multi_carrier_shipping' ),
				'type'			=>	'checkbox',
				'default'		=>	'no',
				'description'	=>	__( 'This feature will be deprecated in upcoming releases.', 'eha_multi_carrier_shipping')
			),
			'packing_method'	  => array(
				'title'		   => __( 'Parcel Packing', 'eha_multi_carrier_shipping' ),
				'type'			=> 'select',
				'default'		 => 'weight_based',
				'class'		   => 'packing_method',
				'options'		 => array(
					'per_item'	=> __( 'Default: Pack items individually', 'eha_multi_carrier_shipping' ),
					'box_packing'	=> __( 'Recommended: Pack into boxes with weights and dimensions', 'wf-shipping-fedex' ),
					'weight_based'=> __( 'Weight based: Calculate shipping on the basis of order total weight', 'eha_multi_carrier_shipping' ),
				),
			),
			'box_max_weight'		   => array(
				'title'		   => __( 'Max Package Weight', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'default'		 => '10',
				'class'		   => 'weight_based_option',
				'desc_tip'	=> true,
				'description'	 => __( 'Maximum weight allowed for single box.', 'eha_multi_carrier_shipping' ),
			),
			'weight_packing_process'   => array(
				'title'		   => __( 'Packing Process', 'eha_multi_carrier_shipping' ),
				'type'			=> 'select',
				'default'		 => '',
				'class'		   => 'weight_based_option',
				'options'		 => array(
					'pack_descending'	   => __( 'Pack heavier items first', 'eha_multi_carrier_shipping' ),
					'pack_ascending'		=> __( 'Pack lighter items first.', 'eha_multi_carrier_shipping' ),
					'pack_simple'			   => __( 'Pack purely divided by weight.', 'eha_multi_carrier_shipping' ),
				),
				'desc_tip'	=> true,
				'description'	 => __( 'Select your packing order.', 'eha_multi_carrier_shipping' ),
			),
			'volumatric_weight'	=> array(
				'title'   => __( 'Enable Volumatric weight', 'eha_multi_carrier_shipping' ),
				'type'	=> 'checkbox',
				'class'		   => 'weight_based_option',
				'label'   => __( 'This option will calculate the volumetric weight. Then a comparison is made on the total weight of cart to the volumetric weight.</br>The higher weight of the two will be sent in the request.', 'eha_multi_carrier_shipping' ),
				'default' => 'no',
			), 
			'boxes'  => array(
				'type'			=> 'box_packing'
			),
			'tax_status' => array(
				'title'	   => __( 'Tax Status', 'eha_multi_carrier_shipping' ),
				'type'		=> 'select',
				'description' => '',
				'default'	 => 'none',
				'options'	 => array(
					'taxable' => __( 'Taxable', 'eha_multi_carrier_shipping' ),
					'none'	=> __( 'None', 'eha_multi_carrier_shipping' ),
				),
			),
			'empty_responce_shipping_cost_on'		   => array(
				'title' 			=> __( 'Fallback Rate Type', 'eha_multi_carrier_shipping' ),
				'type' 				=> 'select',
				'default' 			=> 'per_unit_weight',
				'description' 		=> __( 'Fallback rate will be displayed if shipping rates are not returned. Select any one <br/> - Fixed Cost will display rate given under Fallback Rate <br/> - Per Unit Weight will display rate for every unit product weight <br/> - Per Unit Quantity will display rate for every unit product quantity', 'eha_multi_carrier_shipping' ),
				'desc_tip' 			=> true,
				'options' 			=> array(
					'fixed_fallbackrate' 	=> __( 'Fixed Cost', 'eha_multi_carrier_shipping' ),
					'per_unit_weight' 		=> __( 'Per Unit Weight', 'eha_multi_carrier_shipping' ),
					'per_unit_quantity' 	=> __( 'Per Unit Quantity', 'eha_multi_carrier_shipping' ),
				),
			),
			'empty_responce_shipping_cost'		   => array(
				'title' 		=> __( 'Fallback Rate', 'eha_multi_carrier_shipping' ),
				'type'			=> 'text',
				'default'		=> '50',
			),

		);
}

public function generate_state_list_html() {
	if(!empty($this->settings['origin_country_state']))
	{
		$data=$this->settings['origin_country_state'];
		$countryState=explode(':',$data);
		$country=!empty($countryState[0])?$countryState[0]:'';
		$state=!empty($countryState[1])?$countryState[1]:'';				
	}elseif(!empty($this->settings['origin_country']) && !empty($this->settings['origin_custom_state'])  )
	{
		$country=$this->settings['origin_country'];
		$state=$this->settings['origin_custom_state'];
	}else
	{
		$country='US';
		$state='CA';
	}
	ob_start();
	?><tr valign="top">
		<th scope="row" class="titledesc">
			<label for="origin_country_state"><?php _e( 'Origin State Code', 'eha_multi_carrier_shipping' ); ?></label>
			<?php echo wc_help_tip(  __( 'Specify shipper state province code if state not listed with Origin Country.', 'eha_multi_carrier_shipping' ) ); ?>
		</th>
		<td class=""><select name="origin_country_state" style="min-width:350px;" data-placeholder="<?php esc_attr_e( 'Choose country/state &hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country', 'woocommerce' ) ?>" class="wc-enhanced-select">
			<?php WC()->countries->country_dropdown_options( $country, ! empty($state) ? $state : '*' ); ?>
			</select> <?php  ?>
		</td>
		</tr><?php
		return ob_get_clean();
	}
	public function validate_state_list_field( $key ) {
		$countryState   = !empty( $_POST['origin_country_state'] ) ? $_POST['origin_country_state'] : 'US:CA';				
		return $countryState;
	}		
	public function generate_box_packing_html() {
		ob_start();
		include( 'html-wf-box-packing.php' );
		return ob_get_clean();
	}
	
	public function validate_box_packing_field( $key ) {
		$box_type	 		= isset( $_POST['box_type'] ) ? $_POST['box_type'] : array();
		$boxes_length	 	= isset( $_POST['boxes_length'] ) ? $_POST['boxes_length'] : array();
		$boxes_width	  	= isset( $_POST['boxes_width'] ) ? $_POST['boxes_width'] : array();
		$boxes_height	 	= isset( $_POST['boxes_height'] ) ? $_POST['boxes_height'] : array();

		$boxes_inner_length	= isset( $_POST['boxes_inner_length'] ) ? $_POST['boxes_inner_length'] : array();
		$boxes_inner_width	= isset( $_POST['boxes_inner_width'] ) ? $_POST['boxes_inner_width'] : array();
		$boxes_inner_height	= isset( $_POST['boxes_inner_height'] ) ? $_POST['boxes_inner_height'] : array();

		$boxes_box_weight 	= isset( $_POST['boxes_box_weight'] ) ? $_POST['boxes_box_weight'] : array();
		$boxes_max_weight 	= isset( $_POST['boxes_max_weight'] ) ? $_POST['boxes_max_weight'] :  array();
		$boxes_enabled		= isset( $_POST['boxes_enabled'] ) ? $_POST['boxes_enabled'] : array();

		$boxes = array();
		if ( ! empty( $boxes_length ) && sizeof( $boxes_length ) > 0 ) {
			for ( $i = 0; $i <= max( array_keys( $boxes_length ) ); $i ++ ) {

				if ( ! isset( $boxes_length[ $i ] ) )
					continue;

				if ( $boxes_length[ $i ] && $boxes_width[ $i ] && $boxes_height[ $i ] ) {

					$boxes[] = array(
						'box_type' 	 => isset( $box_type[ $i ] ) ? $box_type[ $i ] : '',
						'length'	 => floatval( $boxes_length[ $i ] ),
						'width'	  => floatval( $boxes_width[ $i ] ),
						'height'	 => floatval( $boxes_height[ $i ] ),

						/* Old version compatibility: If inner dimensions are not provided, assume outer dimensions as inner.*/
						'inner_length' 	=> isset( $boxes_inner_length[ $i ] ) ? floatval( $boxes_inner_length[ $i ] ) : floatval( $boxes_length[ $i ] ),
						'inner_width' 	=> isset( $boxes_inner_width[ $i ] ) ? floatval( $boxes_inner_width[ $i ] ) : floatval( $boxes_width[ $i ] ), 
						'inner_height' 	=> isset( $boxes_inner_height[ $i ] ) ? floatval( $boxes_inner_height[ $i ] ) : floatval( $boxes_height[ $i ] ),

						'box_weight' => floatval( $boxes_box_weight[ $i ] ),
						'max_weight' => floatval( $boxes_max_weight[ $i ] ),
						'enabled'	=> isset( $boxes_enabled[ $i ] ) ? true : false
					);
				}
			}
		}
		foreach ( $this->default_boxes as $box ) {
			$boxes[ $box['id'] ] = array(
				'enabled' => isset( $boxes_enabled[ $box['id'] ] ) ? true : false
			);
		}
		return $boxes;
	}

	function wf_hidden_matrix_column($column_name){
		if($column_name=='shipping_group')
		{
			if(!isset($this->settings['show_shipping_group']) || $this->settings['show_shipping_group']!='no')
			{
				return $column_name;
			}else
			{
				return 'hidden';
			}
		}
		return $column_name;
	}

	public function validate_rate_matrix_field( $key ) {
		$rate_matrix		 = isset( $_POST['rate_matrix'] ) ? $_POST['rate_matrix'] : array();
		return $rate_matrix;
	}

	/**
	 * Generate Rate Matrix in Plugin settings Page.
	 */
	public function generate_rate_matrix_html() {
		
		ob_start();
		$cost = 0;			   
		?>
		<!-- <tr> To get estimated shipping rates for multiple carriers, refer this <a href="http://shippingcalculator.storepep.com/" target="_blank">Shipping Calculator Tool</a></tr> -->
		<tr valign="top" id="packing_rate_matrix">
			<td class="titledesc" colspan="2" style="padding-left:0px">
				<br>
				<style type="text/css">
					.multi_carrier_shipping_boxes .row_data td
					{
						border-bottom: 1pt solid #e1e1e1;
					}

					.multi_carrier_shipping_boxes input, 
					.multi_carrier_shipping_boxes select, 
					.multi_carrier_shipping_boxes textarea,
					.multi_carrier_shipping_boxes .select2-container-multi .select2-choices{
						background-color: #fbfbfb;
						border: 1px solid #e9e9e9;

					}
					.wf_settings_hidden_tab::after {
						content: ' \25BC';
					}
					.wf_settings_hidden_tab {
						cursor:pointer;
					}

					.multi_carrier_shipping_boxes td, .multi_carrier_shipping_services td {
						vertical-align: top;
						padding: 4px 4px;

					}
					.multi_carrier_shipping_boxes th, .multi_carrier_shipping_services th {
						padding: 9px 7px;
					}
					.multi_carrier_shipping_boxes td input {
						margin-right: 4px;
					}
					.multi_carrier_shipping_boxes .check-column {
						vertical-align: top;
						text-align: left;
						padding: 4px 7px;
					}
					.multi_carrier_shipping_services th.sort {
						width: 16px;
					}
					.multi_carrier_shipping_services td.sort {
						cursor: move;
						width: 16px;
						padding: 0 16px;
						cursor: move;
						background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
					}
					@media screen and (min-width: 781px) 
					{
						th.tiny_column
						{
							width:4em;
							max-width:4em;
							min-width:4em;									  
						}
						th.small_column
						{
							width:6.5em;	
							max-width:6.5em; 	
							min-width:6.5em;
						}
						th.smallp_column
						{
							width:4.5em;	
							max-width:4.5em; 	
							min-width:4.5em;
						}
						th.medium_column
						{
							width:8em;
							max-width:8em;
							min-width:8em;
						}
						th.big_column
						{
							min-width:100px;
						}									
					}
					th.hidecolumn,
					td.hidecolumn
					{
						display:none;
					}
					.chosen_select
					{
						max-width:50px !important;
						width:50px !important;											
					}
					.select2-selection{			   
						width:auto !important;  
					}
					.woocommerce table.form-table .select2-container {
						min-width: 160px!important;
					}

					.help_tip
					{
						margin: -4px 0 -2px -3px !important;
					}
				</style>

				<table class="multi_carrier_shipping_boxes widefat" style="background-color:#f6f6f6; margin-right: 20px">
					<thead>
						<tr>
							<th class="check-column tiny_column"><input type="checkbox" /></th>

							<th class="tiny_column <?php echo $this->wf_hidden_matrix_column('shipping_name');?>">
								<?php _e( 'Method Title', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Would you like this shipping rule to have its own shipping service name? If so, please choose a name. Leaving it blank will use Method Title as shipping service name.', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>

							<th class="tiny_column <?php echo $this->wf_hidden_matrix_column('shipping_group');?>">
								<?php _e( 'Method Group', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Set groups if you want to show more than one shipping methods on cart page.', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>

							<th class="tiny_column <?php echo $this->wf_hidden_matrix_column('area_list');?>" >
								<?php _e( 'Area List', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'You can choose the Areas here once you configured', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>

							<th class="tiny_column <?php echo $this->wf_hidden_matrix_column('shipping_class');?>" >
								<?php _e( 'Shipping Class', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of shipping class which this rule will be applicable', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>

							<th class="big_column <?php echo $this->wf_hidden_matrix_column('product_category');?>">
								<?php _e( 'Product Category', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select list of product category which this rule will be applicable. Only the product category directly assigned to the products will be considered.', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>

							<th class="small_column <?php echo $this->wf_hidden_matrix_column('cost_based_on');?>">
								<?php _e( 'Based on', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Shipping rate calculation based on Weight/Item/Price.', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" />
							</th>

							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('weight');?>" style='	padding-left: 0px;'>
								<?php _e( 'Min-Max', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'if the min value entered is .25 and the total category/Shipping Class weight is .24 then this rule will be ignored. if the min value entered is .25 and the total category/Shipping weight is .26 then this rule will be be applicable for calculating shipping cost. if the max value entered is .25 and the total category/Shipping weight is .26 then this rule will be ignored. if the max value entered is .25 and the total category/Shipping weight is .25 or .24 then this rule will be be applicable for calculating shipping cost.', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br><?php _e( '(Wt/Price/Qty)', 'eha_multi_carrier_shipping' );  ?>
							</th>

							<th class="small_column <?php echo $this->wf_hidden_matrix_column('fee');?>" style='left: 7px;'>
								<?php _e( 'Flat Rate', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Fixed shipping cost irrespective of the weight, item count & price', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" />
							</th>

							<th class="small_column <?php echo $this->wf_hidden_matrix_column('cost_per_unit');?>" style='left: 7px;'>
								<?php _e( 'Cost/Unit', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Cost adjustment based on per unit quantity / price / weight.', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" />
							</th>

							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('adjustment');?>" style='left: 7px;'>
								<?php _e( 'Adjustment', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Enter the amount(in %) you want to add / substract from the final shipping cost', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /> 
							</th>

							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('fee');?>">
								<?php _e( 'Shipping Method', 'eha_multi_carrier_shipping' );  ?>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select Shipping Method', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br> 
							</th>

							<th class="medium_column <?php echo $this->wf_hidden_matrix_column('fee');?>" style=" text-align: center; ">
								<span style=" text-align: center; "><?php _e( 'Service', 'eha_multi_carrier_shipping' );  ?> </span>
								<img class="help_tip" style="float:none;" data-tip="<?php _e( 'Select Shipping Service', 'eha_multi_carrier_shipping' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" />
							</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="10">
								<a href="#" class="button insert"><?php _e( 'Add Rule', 'eha_multi_carrier_shipping' ); ?></a>
								<a href="#" class="button remove"><?php _e( 'Remove Rule(s)', 'eha_multi_carrier_shipping' ); ?></a>
								<a href="#" class="button duplicate"><?php _e( 'Duplicate Rule(s)', 'eha_multi_carrier_shipping' ); ?></a>
								<!-- </th> -->
								<!-- <th colspan="6"> -->
									<small class="description">
										<a href="<?php echo admin_url( 'admin.php?import=multicarriershipping_rate_matrix_csv' ); ?>" class="button"><?php _e( 'Import Rule(s)', 'eha_multi_carrier_shipping' ); ?></a>
										<a href="<?php echo admin_url( 'admin.php?wf_export_multicarriershipping_rate_matrix_csv=true' ); ?>" class="button"><?php _e( 'Export Rule(s)', 'eha_multi_carrier_shipping' ); ?></a>&nbsp;
										<!-- <label  style="float:right;margin-right: 10px;"><?php //_e( 'Weight Unit & Dimensions Unit as per WooCommerce settings.', 'eha_multi_carrier_shipping' ); ?></label> -->
									</small>
								</th>
							</tr>
						</tfoot>
						<script>
							jQuery(document).ready(function () {																			  
								var apikey=jQuery('#woocommerce_wf_multi_carrier_shipping_apikey').val();
								if(apikey.length!==32){
									var $input = jQuery('<input type="<?php if(isset($_SERVER['HTTPS'])) {echo "submit";}else {echo "button";}?>" id="btn_getkey" name="btn_getkey" value="Get API Key (Free)" />');
									jQuery(".keybtn").closest('td').append($input);
								}

								<?php if(!isset($_SERVER['HTTPS'])) { ?>
									jQuery("#btn_getkey").click(function(){
										jQuery(this).attr("disabled","disabled");

										var fullUrl = window.location.protocol + "//" + window.location.hostname+window.location.port + window.location.pathname;
										var data={};
										data.email=jQuery('#woocommerce_wf_multi_carrier_shipping_emailid').val();
										data.host=fullUrl;

										jQuery.post(
											<?php  echo "'". $GLOBALS['eha_API_URL']."/api/shippings/register"."'" ?>,
											JSON.stringify(data),
											)
										.done(function(resultText) {
											jQuery('#btn_getkey').removeAttr("disabled");
											alert( resultText );
										})
										.fail(function(resultText) {
											jQuery('#btn_getkey').removeAttr("disabled");
											alert( 'Unable to communicate remote server. Please try again later' );
										});
									});
								<?php } ?>	

								function any_shipping_selected(obj)
								{

						   // jQuery(this).closest('td').find("li:div:contains('Any Shipping Class')").remove('li.select2-search-choice');
						   jQuery(obj).closest('td').find("div:contains('Any Shipping Class')").closest('li').siblings(".select2-search-choice").remove();
						   jQuery(obj).closest('td').find("li.select2-selection__choice:contains('Any Shipping Class')").siblings().remove();
						   jQuery(obj).closest('td').find("*").removeAttr("selected");
						   jQuery(obj).closest("td select").find("option[value='any_shipping_class']").attr('selected',true);

						}

						function any_category_selected(obj)
						{
							jQuery(obj).closest('td').find("div:contains('Any Product category')").closest('li').siblings(".select2-search-choice").remove();
							jQuery(obj).closest('td').find("li.select2-selection__choice:contains('Any Product category')").siblings().remove();

							jQuery(obj).closest('td').find("*").removeAttr("selected");
							jQuery(obj).closest("td select").find("option[value='any_product_category']").attr('selected',true);
							
						}


		 //jQuery(".woocommerce-save-button").click(function () {
		 	jQuery("#mainform").submit(function () {
		 		var success=true;
		 		var empty_shipping_class=[];
		 		var empty_category=[];

		 		jQuery('.area_list>.chosen_select.multiselect').not('.select2-container-multi').each(function( index ) {
		 			if(jQuery('option:selected',this).length==0)
		 			{
		 				success=false;																																		 
		 				alert("Area List is not specified in a rule");	 
		 			}
		 		});																												   

		 		jQuery('.shipping_class>.chosen_select.multiselect').not('.select2-container-multi').each(function( index ) {
		 			if(jQuery('option:selected',this).length==0 && jQuery(this).attr('disabled')!='disabled')
		 			{
		 				empty_shipping_class[index]=true;																																		 
																			   //alert("Shipping Class is not specified in a rule");	 
																			   //return false;
																			}else
																			{
																				empty_shipping_class[index]=false;	 
																			}
																		});	 
		 		jQuery('.product_category>.chosen_select.multiselect').not('.select2-container-multi').each(function( index ) {
																		  //alert(jQuery(this).attr('disabled'));
																		  if(jQuery('option:selected',this).length==0 && jQuery(this).attr('disabled')!='disabled')
																		  {
																		  	empty_category[index]=true;																																		 
																			   //alert("Product Category is not specified in a rule");	 
																			   //return false;
																			}else
																			{
																				empty_category[index]=false;  
																			}
																		}); 

		 		for(var i in empty_category )
		 		{
		 			if(empty_category[i] ===true && empty_shipping_class[i]===true)
		 			{
		 				alert("Both Shipping Class & Product Category not specified in rule no : "+( parseInt(i) +1) +" , atleast one should be defined");	 
		 				success=false;	
		 			}																																			 
		 		}

		 		return success;  
		 	});

		 	jQuery('.multiselect').each(function( index ) {

		 		if(jQuery( 'option:selected',this).val()=='any_shipping_class')
		 		{
		 			any_shipping_selected(this);																																			 
		 		}else if(jQuery( 'option:selected',this).val()=='any_product_category')
		 		{
		 			any_category_selected(this);
		 		}
		 	}
		 	);
		 	jQuery('#rates').on('change','.shipping_class>.multiselect',function () {
		 		if (jQuery('option:selected',this).val()== 'any_shipping_class') {
		 			any_shipping_selected(this);	 
		 		}																						   
		 		else {  
						jQuery(this).closest('td').next('td').find( "*" ).removeAttr('readonly'); // mark it as read only
						jQuery(this).closest('td').next('td').next('td').find( "*" ).removeAttr('disabled'); // mark it as read only
						jQuery(this).closest('td').next('td').next('td').find( "*" ).css('background-color' , ''); // change the background color
						jQuery(this).closest('td').next('td').find( "*" ).css('background-color' , ''); 
						
						jQuery(this).closest('td').prev('td').find( "*" ).css('background-color' , '');// mark it as read only

						jQuery(this).closest('td').next('td').find( "*" ).removeAttr("disabled");  // mark it as read only
						jQuery(this).closest('td').next('td').next('td').next('td').find( "*" ).removeAttr("disabled");  // mark it as read only
						jQuery(this).closest('td').prev('td').find( "*" ).removeAttr("disabled");// mark it as read only																								 
						jQuery(this).closest('td').next('td').find( "*" ).removeAttr('disabled'); // mark it as read only
						jQuery(this).closest('td').next('td').find( "*" ).css('background-color' , ''); // change the background color																							   
						jQuery(this).closest('td').next('td').next('td').find( "*" ).removeAttr("disabled");   // mark it as read only
					}
				});
		 	jQuery('#rates').on('change','.product_category>.multiselect',function () {
		 		if (jQuery('option:selected',this).val() == 'any_product_category')
		 		{
		 			any_category_selected(this);
		 		}																							  
		 		else {  
						jQuery(this).closest('td').next('td').find( "*" ).removeAttr('readonly'); // mark it as read only
						jQuery(this).closest('td').next('td').next('td').find( "*" ).removeAttr('disabled'); // mark it as read only
						jQuery(this).closest('td').next('td').next('td').find( "*" ).css('background-color' , ''); // change the background color
						jQuery(this).closest('td').next('td').find( "*" ).css('background-color' , ''); 
						
						jQuery(this).closest('td').prev('td').find( "*" ).css('background-color' , '');// mark it as read only

						jQuery(this).closest('td').next('td').find( "*" ).removeAttr("disabled");  // mark it as read only
						jQuery(this).closest('td').next('td').next('td').next('td').find( "*" ).removeAttr("disabled");  // mark it as read only
						jQuery(this).closest('td').prev('td').find( "*" ).removeAttr("disabled");// mark it as read only																								 
						jQuery(this).closest('td').next('td').find( "*" ).removeAttr('disabled'); // mark it as read only
						jQuery(this).closest('td').next('td').find( "*" ).css('background-color' , ''); // change the background color																							   
						jQuery(this).closest('td').next('td').next('td').find( "*" ).removeAttr("disabled");   // mark it as read only
					}
				});


		 	jQuery('#woocommerce_wf_multi_carrier_shipping_packing_method').change(function () {
		 		if (jQuery('#woocommerce_wf_multi_carrier_shipping_packing_method option:selected').val() === 'weight_based') {
		 			jQuery('#woocommerce_wf_multi_carrier_shipping_weight_packing_process').show();
		 			jQuery(".weight_based_option").closest("tr").show();
		 		}else {
		 			jQuery(".weight_based_option").closest("tr").hide();
		 		}
		 	});
		 	if (jQuery('#woocommerce_wf_multi_carrier_shipping_packing_method').val() === 'per_item') {
		 		jQuery('#woocommerce_wf_multi_carrier_shipping_packing_method').closest('tr').next('tr').hide();
		 		jQuery('#woocommerce_wf_multi_carrier_shipping_packing_method').closest('tr').next('tr').next('tr').hide();
		 	}

		 	jQuery('.multi_carrier_shipping_boxes').on("change",".company_select",function () {
		 		var fedexservices='';
		 		var upsservicees='';

		 		<?php
		 		echo "fedexservices=";
		 		echo "'";
		 		$fedex_services = self::get_fedex_services();
		 		foreach( $fedex_services as  $fedex_service_id => $fedex_service_name ) {
		 			echo "<option value=$fedex_service_id> $fedex_service_name </option>";
		 		}

		 		echo "';";
		 		echo "upsservices=";
		 		echo "'";
		 		$ups_services = self::get_ups_services();
		 		foreach( $ups_services as  $ups_service_id => $ups_service_name ) {
		 			echo "<option value=$ups_service_id> $ups_service_name </option>";
		 		}

		 		echo "';";

		 		echo "usps_services=";
		 		echo "'";
		 		$usps_services = self::get_usps_services();
		 		foreach( $usps_services as  $usps_service_id => $usps_service_name ) {
		 			echo "<option value=$usps_service_id> $usps_service_name </option>";
		 		}

		 		echo "';";
		 		echo "stamps_usps_services=";
		 		echo "'";
		 		$stamps_usps_services = self::get_stamps_usps_services();
		 		foreach( $stamps_usps_services as  $stamps_usps_service_id => $stamps_usps_service_name ) {
		 			echo "<option value=$stamps_usps_service_id>$stamps_usps_service_name</option>";
		 		}

		 		echo "';";
		 		echo "dhl_services=";
		 		echo "'";
		 		$dhl_services = self::get_dhl_services();
		 		foreach( $dhl_services as  $dhl_service_id => $dhl_service_name ) {
		 			echo "<option value=$dhl_service_id> $dhl_service_name </option>";
		 		}

		 		echo "';";

		 		?>
		 		if (jQuery(this).val() === 'ups') 
		 		{   
		 			var selectbox=jQuery(this).closest('td').next('td').find('select').empty();
		 			selectbox.append(upsservices);
		 		} 
		 		else if(jQuery(this).val() === 'fedex') 
		 		{
		 			var selectbox=jQuery(this).closest('td').next('td').find('select').empty();
		 			selectbox.append(fedexservices);
		 		}
		 		else if(jQuery(this).val() === 'usps') 
		 		{
		 			var selectbox=jQuery(this).closest('td').next('td').find('select').empty();
		 			selectbox.append(usps_services);
		 		}
		 		else if(jQuery(this).val() === 'stamps_usps') 
		 		{
		 			var selectbox=jQuery(this).closest('td').next('td').find('select').empty();
		 			selectbox.append(stamps_usps_services);
		 		}
		 		else if(jQuery(this).val() === 'dhl') 
		 		{
		 			var selectbox=jQuery(this).closest('td').next('td').find('select').empty();
		 			selectbox.append(dhl_services);
		 		}
		 		else if(jQuery(this).val() === 'flatrate') 
		 		{
		 			jQuery(this).closest('td').next('td').find('select').empty();
		 		}
		 	});


		 });
		</script>
		<tbody id="rates">
			<?php								
			$matrix_rowcount = 0;
			if ( $this->rate_matrix ) {
				foreach ( $this->rate_matrix as $key => $box ) {
					$defined_areas = isset($box['area_list']) ? $box['area_list'] : array();
					$defined_shipping_classes = isset($box['shipping_class']) ? $box['shipping_class'] : array();
					$defined_product_category = isset($box['product_category']) ? $box['product_category'] : array();
					?>

					<tr class="rule_text"><td colspan="6" style="font-style:italic; color:#a8a8a8;"><strong><?php //echo $this->wf_rule_to_text($key ,$box);?></strong></td></tr>
					<tr class="row_data"><td class="check-column"><input type="checkbox" /></td>

						<td class="<?php echo $this->wf_hidden_matrix_column('shipping_name');?>"><input type='text' size='20' name='rate_matrix[<?php echo $key;?>][shipping_name]' placeholder='<?php echo $this->title;?>' title='<?php echo isset($box['shipping_name']) ? $box['shipping_name']:$this->title;?>' value='<?php echo isset($box['shipping_name']) ? $box['shipping_name']:"";?>' /></td>

						<td class="<?php echo $this->wf_hidden_matrix_column('shipping_group');?>"><input type='text' size='15' name='rate_matrix[<?php echo $key;?>][shipping_group]' placeholder='<?php echo 'Primary Group'; ?>'  value='<?php echo isset($box['shipping_group']) ? $box['shipping_group']:"";?>' /></td>

						<td class="<?php echo $this->wf_hidden_matrix_column('area_list');?>" style='overflow:visible'>
							<select id="area_list_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="area_list_combo" multiple="true" style="" name='rate_matrix[<?php echo $key;?>][area_list][]'>
								<?php 
								$area_list = $this->wf_get_area_list();
								foreach($area_list as $zoneKey => $zoneValue){ ?>
									<option value="<?php echo $zoneKey;?>" <?php selected(in_array($zoneKey,$defined_areas),true);?>><?php echo $zoneValue;?>
								</option>
							<?php } ?>															
						</select>
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('shipping_class');?>" style='overflow:visible;'>
						<select id="shipping_class_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="shipping_class_combo" multiple="true" style="" name='rate_matrix[<?php echo $key;?>][shipping_class][]'>
							<option value="any_shipping_class" <?php selected(in_array('any_shipping_class',$defined_shipping_classes),true);?>>Any Shipping Class</option>
							<?php $this->wf_shipping_class_dropdown_options($defined_shipping_classes); ?>															
						</select>
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('product_category');?>" style='overflow:visible'>
						<select id="product_category_combo_<?php echo $key;?>" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="product_category_combo"  multiple="true" style="" name='rate_matrix[<?php echo $key;?>][product_category][]'>
							<option value="any_product_category" <?php selected(in_array('any_product_category',$defined_product_category),true); echo empty($defined_product_category)?"selected":'';?>>Any Product category</option>
							<?php $this->wf_product_category_dropdown_options($defined_product_category); ?>															
						</select>
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('cost_based_on');?>">
						<select id="cost_based_on_<?php echo $key;?>" class="select singleselect" name="rate_matrix[<?php echo $key;?>][cost_based_on]" data-identifier="cost_based_on">
							<option value="weight" <?php selected(isset($box['cost_based_on']) ? $box['cost_based_on'] : '','weight');?> >Weight</option>
							<option value="item" <?php selected(isset($box['cost_based_on']) ? $box['cost_based_on'] : '','item');?>>Item Qty</option>
							<option value="price" <?php selected(isset($box['cost_based_on']) ? $box['cost_based_on'] : '','price');?>>Price</option>
						</select>
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('weight');?>">
						<input type='number'  style="width: 100%" step='any' name='rate_matrix[<?php echo $key;?>][min_weight]' 	style='clear: both;float:left;'	value='<?php  echo isset($box['min_weight']) ? $box['min_weight']:''; ?>' />
						<br/><br/>
						<input type='number'  style="width: 100%" step='any' name='rate_matrix[<?php echo $key;?>][max_weight]' 	style='clear: both;float:left;'	value='<?php echo isset($box['max_weight']) ? $box['max_weight']:''; ?>' />
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('fee');?>">
						<input type='number' style="width: 100%" step='any' name='rate_matrix[<?php echo $key;?>][fee]'	value='<?php  echo isset($box['fee'])?$box['fee']:''; ?>' />
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('cost_per_unit');?>">
						<input type='number' style="width: 100%" step='any' name='rate_matrix[<?php echo $key;?>][cost_per_unit]'	value='<?php  echo isset($box['cost_per_unit']) ? $box['cost_per_unit']:''; ?>' />
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('adjustment');?>">
						<input type='number' style="width: 70%" step='any' name='rate_matrix[<?php echo $key;?>][adjustment]'	value='<?php  echo isset($box['adjustment'])?$box['adjustment']:''; ?>' /><?php echo ' %'; ?>
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('shipping_companies');?>">
						<select id="shipping_companies_<?php echo $key;?>" class="select singleselect company_select" name="rate_matrix[<?php echo $key;?>][shipping_companies]" data-identifier="shipping_companies">
							<option value="flatrate" <?php selected(isset($box['shipping_companies']) ? $box['shipping_companies'] : '','flatrate');?> >Flat Rate</option>
							<option value="fedex" <?php selected(isset($box['shipping_companies']) ? $box['shipping_companies'] : '','fedex');?> >FedEx</option>
							<option value="ups" <?php selected(isset($box['shipping_companies']) ? $box['shipping_companies'] : '','ups');?>>UPS</option>
							<option value="usps" <?php selected(isset($box['shipping_companies']) ? $box['shipping_companies'] : '','usps');?>>USPS</option>
							<option value="stamps_usps" <?php selected(isset($box['shipping_companies']) ? $box['shipping_companies'] : '','stamps_usps');?>>Stamps USPS</option>
							<option value="dhl" <?php selected(isset($box['shipping_companies']) ? $box['shipping_companies'] : '','dhl');?>>DHL Express</option>
						</select>
					</td>

					<td class="<?php echo $this->wf_hidden_matrix_column('shipping_services');?>">
						<select id="shipping_services_<?php echo $key;?>" class="select singleselect shipping_service" name="rate_matrix[<?php echo $key;?>][shipping_services]" data-identifier="shipping_services" style="width: 150px;">
							<?php
							if($box['shipping_companies']=='fedex')
							{
								$response = self::get_fedex_services();
							}
							elseif($box['shipping_companies']=='ups')
							{
								$response = self::get_ups_services();
							}
							elseif($box['shipping_companies']=='usps')
							{
								$response = self::get_usps_services();
							}
							elseif($box['shipping_companies']=='stamps_usps')
							{
								$response = self::get_stamps_usps_services();
							}
							elseif($box['shipping_companies']=='dhl')
							{
								$response= self::get_dhl_services();
							}
							else
							{
								unset($response);
								$response=array();
							}

							foreach($response as  $key2=>$val)
							{
								echo "<option value='$key2' "; echo selected(isset($box['shipping_services']) ? $box['shipping_services'] : '',$key2); echo " >$val</option>";
							}
							?>
						</select>
					</td>
				</tr>

				<?php
				if(!empty($key) && $key >= $matrix_rowcount)
					$matrix_rowcount = $key;
			}
		}
		?>

		<input type="hidden" id="matrix_rowcount" value="<?php echo$matrix_rowcount;?>" />

	</tbody>

</table>
<table>
	<tr class="row_data"><td colspan='8' style="font-size:12px;">   <a target="_blank" href="https://www.usps.com/business/web-tools-apis/rate-calculator-api.pdf" style="color:darkblue;"  ><span  style="color:red;">NOTE : </span>Please check 'Appendix A' of this USPS document for weight based restriction on services (pdf)</a>
	</td>   </tr>
	<tr class="row_data"><td colspan='8' style="color:darkred;font-size:12px;"  > <span  style="color:red;">KEYS : </span>  *HFP =Hold For Pickup  ,  *CPP =Commercial Plus Rate  , *SH =Special Handling ,  
	</td>   </tr>
</table>
<script type="text/javascript">																	
	jQuery(window).load(function(){

		jQuery('.wf_settings_hidden_tab').next('table').hide();
		jQuery('.wf_settings_hidden_tab').click(function(){
			jQuery(this).next('table').toggle();
		});									
		jQuery('.multi_carrier_shipping_boxes .insert').click( function() {
			var $tbody = jQuery('.multi_carrier_shipping_boxes').find('tbody');
			var size = $tbody.find('#matrix_rowcount').val();
			if(size){
				size = parseInt(size)+1;
			}
			else
				size = 0;

			var code = '<tr class="new row_data"><td class="check-column"><input type="checkbox" /></td>\
			<td class="<?php echo $this->wf_hidden_matrix_column('shipping_name');?>"><input type="text" size="20" name="rate_matrix['+size+'][shipping_name]" placeholder="<?php echo $this->title;?>" /></td>\
			<td class="<?php echo $this->wf_hidden_matrix_column('shipping_group');?>"><input type="text" size="15" name="rate_matrix['+size+'][shipping_group]" placeholder="<?php echo 'Primary Group'; ?>"  /></td>\n\
			<td class="<?php echo $this->wf_hidden_matrix_column('area_list');?>" style="overflow:visible">\
			<select id="area_list_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="area_list_combo" multiple="true" style="" name="rate_matrix['+size+'][area_list][]">\
			<?php 
			$area_list = $this->wf_get_area_list();
			foreach($area_list as $zoneKey => $zoneValue){ ?><option value="<?php echo esc_attr( $zoneKey ); ?>" ><?php echo esc_attr( $zoneValue ); ?></option>\
		<?php } ?>
	</select>\
</td>\
<td class="<?php echo $this->wf_hidden_matrix_column('shipping_class');?>" style="overflow:visible">\
	<select id="shipping_class_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="shipping_class_combo" multiple="true" style="" name="rate_matrix['+size+'][shipping_class][]">\
		<option value="any_shipping_class">Any Shipping class</option>\
		<?php $this->wf_shipping_class_dropdown_options(); ?></select>\
	</td>\
	<td class="<?php echo $this->wf_hidden_matrix_column('product_category');?>" style="overflow:visible">\
		<select id="product_category_combo_'+size+'" class="<?php echo $this->drop_down_style;?> enabled" data-identifier="product_category_combo"  multiple="true" style="" name="rate_matrix['+size+'][product_category][]">\
			<option value="any_product_category">Any Product Category</option>\
			<?php $this->wf_product_category_dropdown_options(); ?></select>\
		</td>\
		<td class="<?php echo $this->wf_hidden_matrix_column('cost_based_on');?>"><select id="cost_based_on_'+size+'" class="select singleselect" data-identifier="cost_based_on" name="rate_matrix['+size+'][cost_based_on]"><option value="weight" selected>Weight</option><option value="item">Item Qty</option><option value="price">Price</option></select></td> \
		<td class="<?php echo $this->wf_hidden_matrix_column('weight');?>"><input type="number"  style="width: 100%" step="any" name="rate_matrix['+size+'][min_weight]"  /><br/><br/><input type="number"  style="width: 100%" step="any" name="rate_matrix['+size+'][max_weight]" /></td> \
		<td class="<?php echo $this->wf_hidden_matrix_column('fee');?>"><input type="number" style="width: 100%" step="any" name="rate_matrix['+size+'][fee]" /></td>\
		<td class="<?php echo $this->wf_hidden_matrix_column('cost_per_unit');?>"><input type="number" style="width: 100%" step="any" name="rate_matrix['+size+'][cost_per_unit]" /></td>\
		<td class="<?php echo $this->wf_hidden_matrix_column('adjustment');?>"><input type="number" style="width: 70%" step="any" name="rate_matrix['+size+'][adjustment]" /><?php echo ' %';?></td>\
		<td class="<?php echo $this->wf_hidden_matrix_column('shipping_companies');?>">\
			<select id="shipping_companies_'+size+'" class="select singleselect company_select" name="rate_matrix['+size+'][shipping_companies]" data-identifier="shipping_companies">\
				<option value="flatrate" >Flat Rate</option>\
				<option value="fedex"  >FedEx</option>\
				<option value="ups">UPS</option>\
				<option value="usps" >USPS</option>\
				<option value="stamps_usps" >Stamps USPS</option>\
				<option value="dhl" >DHL Express</option>\
			</select></td>\
			<td class="<?php echo $this->wf_hidden_matrix_column('shipping_services');?>">\
				<select id="shipping_services_'+size+'" class="select singleselect shipping_service" name="rate_matrix['+size+'][shipping_services]" data-identifier="shipping_services" style="width: 150px;">\
				</select></td>\
			</tr>';

			$tbody.append( code );

			if(typeof wc_enhanced_select_params == 'undefined')
			{
				$tbody.find('tr:last').find("select.chosen_select").chosen();
			}
			else
			{
				$tbody.find('tr:last').find("select.chosen_select").trigger( 'wc-enhanced-select-init' );
			}

			$tbody.find('#matrix_rowcount').val(size);
			return false;
		} );

		jQuery('.multi_carrier_shipping_boxes .remove').click(function()
		{
			var $tbody = jQuery('.multi_carrier_shipping_boxes').find('tbody');

			$tbody.find('.check-column input:checked').each(function()
			{
				jQuery(this).closest('tr').prev('.rule_text').remove();
				jQuery(this).closest('tr').remove();
			});

			return false;
		});

		jQuery('.multi_carrier_shipping_boxes .duplicate').click(function()
		{
			var $tbody = jQuery('.multi_carrier_shipping_boxes').find('tbody');

			var new_trs = [];

			$tbody.find('.check-column input:checked').each(function()
			{
				var $tr	= jQuery(this).closest('tr');
				var $clone = $tr.clone();
				var size = jQuery('#matrix_rowcount').val();
				if(size)
				size = parseInt(size)+1;
				else
				size = 0;

				$tr.find('select.multiselect').each(function(i)
				{
					var selecteddata;
					if(typeof wc_enhanced_select_params == 'undefined')
					selecteddata = jQuery(this).chosen().val();
					else
					selecteddata = jQuery(this).select2('data');

					if( selecteddata )
					{
						var arr = [];
						jQuery.each( selecteddata, function( id, text )
						{

							if(typeof wc_enhanced_select_params == 'undefined')
							arr.push(text);
							else
							arr.push(text.id);											
						});

						var currentIdentifierAttr = jQuery(this).attr('data-identifier'); 

						if(currentIdentifierAttr)
						{
							$clone.find("select[data-identifier='"+currentIdentifierAttr+"']").val(arr);
							//$clone.find('select#' + this.id).val(arr);
						}										
					}
				});

				$tr.find('select.no_multiselect').each(function(i)
				{
					var selecteddata = [];

					jQuery.each(jQuery(this).find("option:selected"), function()
					{		 
						selecteddata.push(jQuery(this).val());
					});

					var currentIdentifierAttr = jQuery(this).attr('data-identifier');

					if(currentIdentifierAttr)
					{
						$clone.find("select[data-identifier='"+currentIdentifierAttr+"']").val(selecteddata);
					}
				});

				$tr.find('select.singleselect').each(function(i)
				{
					var selecteddata = jQuery(this).val();

					if ( selecteddata )
					{
						var currentIdentifierAttr = jQuery(this).attr('data-identifier'); 

						if(currentIdentifierAttr)
						{
							$clone.find("select[data-identifier='"+currentIdentifierAttr+"']").val(selecteddata);
							//$clone.find('select#' + this.id).val(selecteddata);										
						}
					}
				});


				if(typeof wc_enhanced_select_params == 'undefined')
				$clone.find('div.chosen-container, div.chzn-container').remove();									
				else
				$clone.find('div.multiselect').remove();								

				$clone.find('.multiselect').show();
				$clone.find('.multiselect').removeClass("enhanced chzn-done");
				// find all the inputs within your new clone and for each one of those
				$clone.find('input[type=text], select').each(function()
				{
					var currentNameAttr = jQuery(this).attr('name'); 
					if(currentNameAttr)
					{
						var newNameAttr = currentNameAttr.replace(/\d+/, size);
						jQuery(this).attr('name', newNameAttr);   // set the incremented name attribute 
					}
					var currentIdAttr = jQuery(this).attr('id'); 
					if(currentIdAttr){
					var currentIdAttr = currentIdAttr.replace(/\d+/, size);
					jQuery(this).attr('id', currentIdAttr);   // set the incremented name attribute 
				}
			});
			//$tr.after($clone);
			//$clone.find('select.chosen_select').trigger( 'chosen_select-init' );
			new_trs.push($clone);
			jQuery('#matrix_rowcount').val(size);
			//jQuery("select.chosen_select").trigger( 'chosen_select-init' );							
		});

		if(new_trs)
		{
			var lst_tr	= $tbody.find('.check-column :input:checkbox:checked:last').closest('tr');
			jQuery.each( new_trs.reverse(), function( id, text )
			{
				//adcd.after(text);
				lst_tr.after(text);
				if(typeof wc_enhanced_select_params == 'undefined')
				text.find('select.chosen_select').chosen();			
				else
				text.find('select.chosen_select').trigger( 'wc-enhanced-select-init' );																	
			});
		}
		$tbody.find('.check-column input:checked').removeAttr('checked');
		return false;
	});									
});
</script>
</td>

</tr>


<?php
return ob_get_clean();
}

private function wf_get_zone_list(){
	$zone_list = array();
	if( class_exists('WC_Shipping_Zones') ){
		$zones_obj = new WC_Shipping_Zones;
		$zones = $zones_obj::get_zones();
					//$zone_list[0] = 'Rest of the World'; //rest of the zone always have id 0, which is not available in the method get_zone()
		foreach ($zones as $key => $zone) {
			$zone_list[$key] = $zone['zone_name'];
		}
	}
	return $zone_list;
}
private function wf_get_area_list()
{
	$area_list=array();
	$area_matrix = array();
	$tmp=get_option('woocommerce_wf_multi_carrier_shipping_area_settings');
	$area_matrix=$tmp['area_matrix'];
	if(is_array($area_matrix))
		foreach ($area_matrix as $key => $area) {
			$area_list[$key]=$area['area_name'] ;
		}
		return $area_list;
	}

	public function wf_find_zone($package){
		$matching_zones=array();		
		if( class_exists('WC_Shipping_Zones') ){
			$zones_obj = new WC_Shipping_Zones;
			$matches = $zones_obj::get_zone_matching_package($package);
			array_push( $matching_zones, (WC()->version < '2.7.0')?$matches->get_zone_id():$matches->get_id() );
		}
		return $matching_zones;
	}

	/**
	 * Calculate Shipping trigger when calculated on cart.
	 * @param $package array WooCommerce Cart Package.
	 */
	function calculate_shipping( $package = array() ) 
	{
		$shipping_required = $this->required_address_detail_check($package);
		if( ! $shipping_required ) {
			if( $this->debug ) $this->debug( __( 'Multi-Carrier : Shipping calculation stopped. Required shipping address fields are missing. Please check shipping address.', 'eha_multi_carrier_shipping' ) );
			return;
		}
		$this->found_rates 	= array();
		$any_rule_matched 	= false;		// To apply fallback rate only when no rate returned from api and any rule has been matched.
		// Notice if Fallback rate is enabled
		if( $this->debug && ! empty($this->settings['empty_responce_shipping_cost']) ) {
			$this->debug( __( 'Fallback Rate is enabled.', 'eha_multi_carrier_shipping' ) );
		}
		if( strlen($this->settings['apikey'])===0 ) {						   
			$this->debug( '<div style="background:red;color:white"> Warning: Multi Carrier Shipping Plugin:  Please Enter API KEY  in (Woocommerce>Settings>Shipping>Multi_Carrier_Shipping Page) </div>');
			return;
		}elseif( strlen($this->settings['apikey'])!==32 ) {
			$this->debug( '<div style="background:red;color:white"> Warning: Multi Carrier Shipping Plugin:  API KEY You Entered is Wrong it should be 32 in length in (Woocommerce>Settings>Shipping>Multi_Carrier_Shipping Page) </div>');
			return;
		}

		$rules=array();
		if( !class_exists('eha_multi_carrier_shipping_rules_calculator') )
			include('class-eha-multi-carrier-shipping-rules-calculator.php');
		
		$grouped_rules=array();

		// If any rules is defined then only proceed
		if( ! empty($this->rate_matrix) && is_array($this->rate_matrix) ) {

			foreach ( $this->rate_matrix as $key => $box ){
				if(!isset($box['shipping_group']) || empty($box['shipping_group']) ||  !isset($this->settings['show_shipping_group']) || $this->settings['show_shipping_group']!='yes' ){
					$grouped_rules['primary'][$key]= $box;
				}
				else {
					$grp=$box['shipping_group'];
					$grouped_rules[$grp][$key]= $box;
				}
			}

			//For Multivendor.
			$packages = apply_filters('wf_filter_package_address', array($package) , $this->ship_from_address);
			foreach ($packages as $package) {
				
				$this->api_rates=array(
					"fedex" 	=> array(),
					"ups" 		=> array(),
					"stamps" 	=> array(),
					"dhl" 		=> array()
					);
				foreach($grouped_rules as $grp=>$rules) {
					
					$calculator=new eha_multi_carrier_shipping_rules_calculator($rules,$package, $this->debug ,$grp ,$this);
					
					//Need to do 
					$rule_key_to_apply = current(array_keys($calculator->product_rule_mapping));			// Only one rule has to be shown per group, $calculator->product_rule_mapping are filtered rules

					// In general Array[0] = Array[false] so strict check is required if false is returned
					if( $rule_key_to_apply === false || empty($rules[$rule_key_to_apply]) ) {
						continue;
					}
					//End of need to do

					$current_rule 			= $rules[$rule_key_to_apply];
					$any_rule_matched 		= true;
					$cost					= $calculator->calculate_shipping_cost();
					$method_name			= ! empty($current_rule['shipping_name']) ? $current_rule['shipping_name'] : $this->settings['title'];
					$rule_shipping_carrier	= $current_rule['shipping_companies'];
					// False in case of Call is made for Live API rates but no response received, don't perform empty check because flat rate with Zero Cost might return 0

					if( $cost !== false ){
						$this->prepare_rate( PH_MULTICARRIER_ID.':'.$rule_shipping_carrier.'_'.$grp, $method_name, $cost, $current_rule );
					}
				}
			}
			
			// Ensure rates were found for all packages
			$packages_to_quote_count = sizeof( $packages );
			if ( !empty($this->found_rates) ) {
				foreach ( $this->found_rates as $key => $value ) {
					if ( $value['packages'] < $packages_to_quote_count ) {
						unset( $this->found_rates[ $key ] );
					}
				}
			}
			// Handle Fallback rate
			elseif( $any_rule_matched && ! empty($this->settings['empty_responce_shipping_cost']) ){
				$this->handle_fallback_rate($package);
			}
			
			if( $this->settings['debug'] == 'yes' && $any_rule_matched === false ) {
				wc_add_notice( __( 'Multi Carrier : No shipping rules matched', 'eha_multi_carrier_shipping') );
			}

			$this->add_found_rates();
		}
		elseif( $this->settings['debug'] == 'yes' ) {
			wc_add_notice( __( 'Multi Carrier : No shipping rules defined in settings page. Kindly define one or more rule to get the rates.', 'eha_multi_carrier_shipping'), 'notice' );
		}
	}

	/**
	 * Check required address field for getting the rates.
	 */
	public function required_address_detail_check( $package ) {
		$shipping_required = false;
		$require_postal_code = array( 'US', 'CA', 'IN', 'UK' );
		if( ! empty($package['destination']) ) {
			$destination = $package['destination'];
			if( ! empty($destination['postcode']) || ( ! empty($destination['city']) && ! in_array($destination['country'], $require_postal_code) ) ) {
				$shipping_required = true;
			}
		}
		return $shipping_required;
	}
	
	/**
	 * Add fallback rate.
	 * @param $package array WooCommerce Cart package.
	 */
	public function handle_fallback_rate($package){
		$total_product_quantity = null;
		$total_product_weight 	= null;
		foreach( $package['contents'] as $line_item ) {
			if( $line_item['data']->needs_shipping() ) {
				if( $this->settings['empty_responce_shipping_cost_on'] == 'per_unit_quantity' ) {
					$total_product_quantity += (int) $line_item['quantity'];
				}
				else if( $this->settings['empty_responce_shipping_cost_on'] == 'per_unit_weight' ){
					$total_product_weight += ( (int) $line_item['quantity'] * (float)$line_item['data']->get_weight() );
				}
			}
		}
		
		if( $this->settings['empty_responce_shipping_cost_on'] == 'per_unit_quantity' ) {
			$cost = $total_product_quantity * $this->settings['empty_responce_shipping_cost'];
		}
		else if( $this->settings['empty_responce_shipping_cost_on'] == 'per_unit_weight' ){
			$cost = $total_product_weight * $this->settings['empty_responce_shipping_cost'];
		}else{
			$cost = $this->settings['empty_responce_shipping_cost'];
		}

		if( ! empty($cost) ) {
			$this->debug( __( 'No rate returned from API. Fallback rate has been shown', 'eha_multi_carrier_shipping' ) );
			$this->prepare_rate( PH_MULTICARRIER_ID.':fallback_rate', $this->settings['title'], $cost );
		}
	}

	private function add_found_rates(){
		foreach ( $this->found_rates as $key => $rate ) {
			$this->add_rate( $rate );
		}
	}

	private function prepare_rate( $rate_id, $rate_name, $rate_cost, $rule = array() ) {
		// Merging
		if ( isset( $this->found_rates[ $rate_id ] ) ) {
			$rate_cost = $rate_cost + $this->found_rates[ $rate_id ]['cost'];
			$packages  = 1 + $this->found_rates[ $rate_id ]['packages'];
		} else {
			$packages  = 1;
		}

		if( $rate_id == PH_MULTICARRIER_ID.':fallback_rate' ) {
			$shipping_service_code = 'fallback_rate';
			$shipping_carrier = 'fallback_rate';
		}
		else{
			$shipping_service_code = ! empty($rule['shipping_services']) ? $rule['shipping_services'] : "";
			$shipping_carrier = $rule['shipping_companies'];
		}
		$this->found_rates[ $rate_id ] = array(
			'id'		=> $rate_id,
			'label'		=> $rate_name,
			'cost'		=> $rate_cost,
			// 'taxes'		=> '',
			// 'calc_tax'	=> '0',
			'packages'	=> $packages,
			'meta_data'	=> array(
				'_ph_multicarrier_method'	=> array(
					'id'				=> $rate_id,
					'shipping_service'		=> $shipping_carrier,
					'shipping_service_code'	=> $shipping_service_code,
				),
			),
		);
	}

	/**
	 * Get all the FedEx Services.
	 * @return array FedEx Services .
	 */
	public static function get_fedex_services()
	{
		$services = array(
			"FEDEX_2_DAY"							=> "FEDEX 2 DAY",
			"FEDEX_2_DAY_AM"						=> "FEDEX 2 DAY AM",
			"FEDEX_DISTANCE_DEFERRED"				=> "FEDEX DISTANCE DEFERRED",
			"FEDEX_EXPRESS_SAVER"					=> "FEDEX EXPRESS SAVER",
			"FEDEX_GROUND"							=> "FEDEX GROUND",
			"FEDEX_NEXT_DAY_AFTERNOON"				=> "FEDEX NEXT DAY AFTERNOON",
			"FEDEX_NEXT_DAY_EARLY_MORNING"			=> "FEDEX NEXT DAY EARLY MORNING",
			"FEDEX_NEXT_DAY_END_OF_DAY"				=> "FEDEX NEXT DAY END OF DAY",
			"FEDEX_NEXT_DAY_MID_MORNING"			=> "FEDEX NEXT DAY MID MORNING",
			"FIRST_OVERNIGHT"						=> "FIRST OVERNIGHT",
			"GROUND_HOME_DELIVERY"					=> "GROUND HOME DELIVERY",
			"EUROPE_FIRST_INTERNATIONAL_PRIORITY"	=> "EUROPE FIRST INTERNATIONAL PRIORITY",
			"INTERNATIONAL_ECONOMY"					=> "INTERNATIONAL ECONOMY",
			"INTERNATIONAL_FIRST"					=> "INTERNATIONAL FIRST",
			"INTERNATIONAL_PRIORITY"				=> "INTERNATIONAL PRIORITY",
			"PRIORITY_OVERNIGHT"					=> "PRIORITY OVERNIGHT",
			"SAME_DAY"								=> "SAME DAY",
			"SAME_DAY_CITY"							=> "SAME DAY CITY",
			"SMART_POST"							=> "SMART POST",
			"STANDARD_OVERNIGHT"					=> "STANDARD OVERNIGHT"
		);

		return $services;
	}

	/**
	 * Get list of UPS Services.
	 * @return array UPS Services.
	 */
	public static function get_ups_services()
	{
		$services = array(
			'01'	=> 'UPS Next Day Air',
			'02'	=> 'UPS Second Day Air',
			'03'	=> 'UPS Ground',
			'07'	=> 'UPS Worldwide Express',
			'08'	=> 'UPS Worldwide Expedited',
			'11'	=> 'UPS Standard',
			'12'	=> 'UPS Three-Day Select',
			'13'	=> 'UPS Next Day Air Saver',
			'14'	=> 'UPS Next Day Air Early A.M.',
			'54'	=> 'UPS Worldwide Express Plus',
			'59'	=> 'UPS Second Day Air A.M.',
			'65'	=> 'UPS Saver',
			'82'	=> 'UPS Today Standard',
			'83'	=> 'UPS Today Dedicated Courier',
			'84'	=> 'UPS Today Intercity',
			'85'	=> 'UPS Today Express',
			'86'	=> 'UPS Today Express Saver'
		);
		return $services;
	}
	
	/**
	 * Get list of USPS Services.
	 * @return array USPS Services.
	 */
	public static function get_usps_services()
	{   
		if( ! class_exists('Wf_MC_Extend_USPS')) {
			require_once 'class-wf-mc-extend-usps.php';
		}

		return Wf_MC_Extend_USPS::usps_service_names();
	}
	
	/**
	 * Stamps Services list
	 * @param type $responce
	 * @return array Stamps Services
	 */
	public static function get_stamps_usps_services()
	{
		$response = array(
			// Priority Mail Services
			"US-PM:Package"						=> "Priority Mail:Package",
			"US-PM:Letter"						=> "Priority Mail:Letter",
			"US-PM:Large.Envelope.or.Flat"		=> "Priority Mail:Large Envelope or Flat",
			"US-PM:Thick.Envelope"				=> "Priority Mail:Thick Envelope",
			"US-PM:Large.Package"				=> "Priority Mail:Large Package",
			"US-PM:Small.Flat.Rate.Box"			=> "Priority Mail:Small Flat Rate Box",
			"US-PM:Flat.Rate.Box"				=> "Priority Mail:Flat Rate Box",
			"US-PM:Large.Flat.Rate.Box"			=> "Priority Mail:Large Flat Rate Box",
			"US-PM:Flat.Rate.Envelope"			=> "Priority Mail:Flat Rate Envelope",
			"US-PM:Flat.Rate.Padded.Envelope"	=> "Priority Mail:Flat Rate Padded Envelope",
			"US-PM:Oversized.Package"			=> "Priority Mail:Oversized Package",
			"US-PM:Regional.Rate.Box.A"			=> "Priority Mail:Regional Rate Box A",
			"US-PM:Regional.Rate.Box.B"			=> "Priority Mail:Regional Rate Box B",
			"US-PM:Legal.Flat.Rate.Envelope"	=> "Priority Mail:Legal Flat Rate Envelope",

			// Priority Mail Express Services
			"US-XM:Package"						=> "Priority Mail Express:Package",
			"US-XM:Letter"						=> "Priority Mail Express:Letter",
			"US-XM:Large.Envelope.or.Flat"		=> "Priority Mail Express:Large Envelope or Flat",
			"US-XM:Thick.Envelope"				=> "Priority Mail Express:Thick Envelope",
			"US-XM:Large.Package"				=> "Priority Mail Express:Large Package",
			"US-XM:Flat.Rate.Envelope"			=> "Priority Mail Express:Flat Rate Envelope",
			"US-XM:Flat.Rate.Padded.Envelope"	=> "Priority Mail Express:Flat Rate Padded Envelope",
			"US-XM:Legal.Flat.Rate.Envelope"	=> "Priority Mail Express:Legal Flat Rate Envelope",

			// First-Class Mail Services
			"US-FC:Package"						=> "First-Class Mail:Package",
			"US-FC:Postcard"					=> "First-Class Mail:Postcard",
			"US-FC:Letter"						=> "First-Class Mail:Letter",
			"US-FC:Large.Envelope.or.Flat"		=> "First-Class Mail:Large Envelope or Flat",
			"US-FC:Thick.Envelope"				=> "First-Class Mail:Thick Envelope",
			"US-FC:Large.Package"				=> "First-Class Mail:Large Package",

			// Media Mail Services
			"US-MM:Package"						=> "Media Mail:Package",
			"US-MM:Large.Envelope.or.Flat"		=> "Media Mail:Large Envelope or Flat",
			"US-MM:Thick.Envelope"				=> "Media Mail:Thick Envelope",
			"US-MM:Large.Package"				=> "Media Mail:Large Package",

			// Parcel Post Services - Service is Replaced By Parcel Select
			//"US-PP:Package"						=> "Parcel Post:Package",

			// Parcel Select Ground Services
			"US-PS:Package"						=> "Parcel Select Ground:Package",
			"US-PS:Thick.Envelope"				=> "Parcel Select Ground:Thick Envelope",
			"US-PS:Large.Package"				=> "Parcel Select Ground:Large Package",
			"US-PS:Oversized.Package"			=> "Parcel Select Ground:Oversized Package",

			// Library Mail Services
			"US-LM:Package"						=> "Library Mail:Package",
			"US-LM:Large.Envelope.or.Flat"		=> "Library Mail:Large Envelope or Flat",
			"US-LM:Thick.Envelope"				=> "Library Mail:Thick Envelope",

			// Priority Mail Express International Services
			"US-EMI:Package"					=> "Priority Mail Express International:Package",
			"US-EMI:Large.Envelope.or.Flat"		=> "Priority Mail Express International:Large Envelope or Flat",
			"US-EMI:Thick.Envelope"				=> "Priority Mail Express International:Thick Envelope",
			"US-EMI:Large.Package"				=> "Priority Mail Express International:Large Package",
			"US-EMI:Flat.Rate.Envelope"			=> "Priority Mail Express International:Flat Rate Envelope",
			"US-EMI:Flat.Rate.Padded.Envelope"	=> "Priority Mail Express International:Flat Rate Padded Envelope",
			"US-EMI:Oversized.Package"			=> "Priority Mail Express International:Oversized Package",
			"US-EMI:Legal.Flat.Rate.Envelope"	=> "Priority Mail Express International:Legal Flat Rate Envelope",

			// Priority Mail International Services
			"US-PMI:Package"					=> "Priority Mail International:Package",
			"US-PMI:Large.Envelope.or.Flat"		=> "Priority Mail International:Large Envelope or Flat",
			"US-PMI:Thick.Envelope"				=> "Priority Mail International:Thick Envelope",
			"US-PMI:Large.Package"				=> "Priority Mail International:Large Package",
			"US-PMI:Small.Flat.Rate.Box"		=> "Priority Mail International:Small Flat Rate Box",
			"US-PMI:Flat.Rate.Box"				=> "Priority Mail International:Flat Rate Box",
			"US-PMI:Large.Flat.Rate.Box"		=> "Priority Mail International:Large Flat Rate Box",
			"US-PMI:Flat.Rate.Envelope"			=> "Priority Mail International:Flat Rate Envelope",
			"US-PMI:Flat.Rate.Padded.Envelope"	=> "Priority Mail International:Flat Rate Padded Envelope",
			"US-PMI:Oversized.Package"			=> "Priority Mail International:Oversized Package",
			"US-PMI:Legal.Flat.Rate.Envelope"	=> "Priority Mail International:Legal Flat Rate Envelope",

			// First Class Mail International Services
			"US-FCI:Package"					=> "First Class Mail International:Package",
			"US-FCI:Letter"						=> "First Class Mail International:Letter",
			"US-FCI:Large.Envelope.or.Flat"		=> "First Class Mail International:Large Envelope or Flat",
			"US-FCI:Thick.Envelope"				=> "First Class Mail International:Thick Envelope",
			"US-FCI:Large.Package"				=> "First Class Mail International:Large Package",
			"US-FCI:Oversized.Package"			=> "First Class Mail International:Oversized Package",
		);
return $response;
}

	/**
	 * Get DHL Services.
	 * @return array DHL Services.
	 */
	public static function get_dhl_services()
	{
		$services = array(
			"1"		=>	"DOMESTIC EXPRESS 12:00 - DOT - Doc",
			"2"		=>	"B2C - BTC - Doc",
			"3"		=>	"B2C - B2C - Non Doc",
			"4"		=>	"JETLINE - NFO - Non Doc",
			"5"		=>	"SPRINTLINE - SPL - Doc",
			"7"		=>	"EXPRESS EASY - XED - Doc",
			"8"		=>	"EXPRESS EASY - XEP - Non Doc",
			"9"		=>	"EUROPACK - EPA - Doc",
			"B"		=>	"BREAKBULK EXPRESS - BBX - Doc",
			"C"		=>	"MEDICAL EXPRESS - CMX - Doc",
			"D"		=>	"EXPRESS WORLDWIDE - DOX - Doc",
			"E"		=>	"EXPRESS 9:00 - TDE - Non Doc",
			"F"		=>	"FREIGHT WORLDWIDE - FRT - Non Doc",
			"G"		=>	"DOMESTIC ECONOMY SELECT - DES - Doc",
			"H"		=>	"ECONOMY SELECT - ESI - Non Doc",
			"I"		=>	"DOMESTIC EXPRESS 9:00 - DOK - Doc",
			"J"		=>	"JUMBO BOX - JBX - Non Doc",
			"K"		=>	"EXPRESS 9:00 - TDK - Doc",
			"L"		=>	"EXPRESS 10:30 - TDL - Doc",
			"M"		=>	"EXPRESS 10:30 - TDM - Non Doc",
			"N"		=>	"DOMESTIC EXPRESS - DOM - Doc",
			"O"		=>	"DOMESTIC EXPRESS 10:30 - DOL - Doc",
			"P"		=>	"EXPRESS WORLDWIDE - WPX - Non Doc",
			"Q"		=>	"MEDICAL EXPRESS - WMX - Non Doc",
			"R"		=>	"GLOBALMAIL BUSINESS - GMB - Doc",
			"S"		=>	"SAME DAY - SDX - Doc",
			"T"		=>	"EXPRESS 12:00 - TDT - Doc",
			"U"		=>	"EXPRESS WORLDWIDE - ECX - Doc",
			"V"		=>	"EUROPACK - EPP - Non Doc",
			"W"		=>	"ECONOMY SELECT - ESU - Doc",
			"X"		=>	"EXPRESS ENVELOPE - XPD - Doc",
			"Y"		=>	"EXPRESS 12:00 - TDY - Non Doc"
		);
		return $services;
	}
	
	public function wf_list_of_states($only_states=false)
	{
		global $woocommerce;
		$countries_obj   = new WC_Countries();
		$states = $countries_obj->get_states();
		if($only_states)
		{
			$res=array();
			foreach($states as  $country=>$statelist)
			{
				if(!empty($statelist))
				{
					$res= array_merge($res,$statelist);
				}
			}
			return $res;
		}
		else
		{
			return $states;
		}
	}
	
	/**
	 * Add debug messages if debug mode is on.
	 */
	public function debug( $message, $type = 'notice' ) {
		if ( $this->debug && !is_admin() ) {
			wc_add_notice( $message, $type );
		}
	}

}
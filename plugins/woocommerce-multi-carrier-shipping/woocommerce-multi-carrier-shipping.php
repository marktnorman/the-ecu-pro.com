<?php
/*
	Plugin Name: Multi-Carrier Shipping Plugin for WooCommerce
	Plugin URI: https://www.pluginhive.com/product/multiple-carrier-shipping-plugin-woocommerce/
	Description: Intuitive Rule Based Multi-Carrier Shipping Plugin for WooCommerce. Set shipping rates based on rules based by Country, State, Post Code, Product Category, Shipping Class, Weight, Shipping Company and Shipping Service.
	Version: 1.6.7
	Author: PluginHive
	Author URI: https://www.pluginhive.com/about/
	Copyright: 2016-2017 PluginHive.
	WC requires at least: 2.6.0
	WC tested up to: 3.8.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define PH_MULTI_CARRIER_PLUGIN_VERSION
if ( !defined( 'PH_MULTI_CARRIER_PLUGIN_VERSION' ) )
{
    define( 'PH_MULTI_CARRIER_PLUGIN_VERSION', '1.6.7' );
}

// Include API Manager
if ( !class_exists( 'PH_Multi_Carrier_API_Manager' ) ) {

    include_once( 'ph-api-manager/ph_api_manager_multi_carrier.php' );
}

if ( class_exists( 'PH_Multi_Carrier_API_Manager' ) ) {

    $product_title      = 'Multi-Carrier'; 
    $server_url         = 'https://www.pluginhive.com/';
    $product_id         = '';

    $ph_multi_carrier_api_obj   = new PH_Multi_Carrier_API_Manager( __FILE__, $product_id, PH_MULTI_CARRIER_PLUGIN_VERSION, 'plugin', $server_url, $product_title );

}

// Plugin activation check
register_activation_hook( __FILE__, function() {
    //check if basic version is there
    if ( is_plugin_active('multi-carrier-shipping-for-woocommerce/woocommerce-multi-carrier-shipping.php') ) {
        deactivate_plugins( basename( __FILE__ ) );
        wp_die( __("Oops! You tried installing the premium version without deactivating and deleting the basic version. Kindly deactivate and delete Multi-Carrier Shipping Plugin for WooCommerce (Basic) and then try again.", "eha_multi_carrier_shipping" ), "", array('back_link' => 1 ));
    }
});

$GLOBALS['eha_API_URL']="http://shippingapi.storepep.com";			//Live server
// $GLOBALS['eha_API_URL']="http://beta-shippingapi.storepep.com";	// Beta or Testing server
// $GLOBALS['eha_API_URL']="http://localhost:3000";    
load_plugin_textdomain( 'eha_multi_carrier_shipping', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


if( ! function_exists('check_if_woocommerce_active') ) {
    function check_if_woocommerce_active()
    {
        $act=get_option( 'active_plugins' );

        // Multi-Site Compatibility
        if ( is_multisite() ) {
            $act = array_merge( $act, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
        }
        
        foreach($act as $pname)
        {
            if (strpos($pname, 'woocommerce.php') !== false)
            {
                return true;
            }
        }
        return false;
    }
}

// Multicarrier Shipping method Id
if( ! defined('PH_MULTICARRIER_ID') ) {
    define( 'PH_MULTICARRIER_ID', 'wf_multi_carrier_shipping' );
}

if (check_if_woocommerce_active()===true) {
	include( 'eha-multi-carrier-shipping-common.php' );

	// Database Migration because of Ireland State Removal
	if( ! class_exists('Ph_Mc_Db_Migration') ) {
		require_once "db-migration.php";
	}
	new Ph_Mc_Db_Migration();
   
    if (!function_exists('wf_plugin_configuration_mcp')){
       function wf_plugin_configuration_mcp(){
            return array(
                'id' => 'wf_multi_carrier_shipping',
                'method_title' => __('Multi Carrier Shipping', 'eha_multi_carrier_shipping' ),
                'method_description' => __('<strong>*Note: These fields are mandatory - Email ID, API Key, Shipper Settings, Carrier Settings</strong>', 'eha_multi_carrier_shipping' ));		
        }
    }

}
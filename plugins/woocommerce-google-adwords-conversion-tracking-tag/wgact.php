<?php

/**
 * Plugin Name:  WooCommerce Pixel Manager
 * Description:  Visitor and conversion value tracking for WooCommerce. Highly optimized for data accuracy (which drives optimal campaign performance).
 * Author:       woopt
 * Plugin URI:   https://wordpress.org/plugins/woocommerce-google-adwords-conversion-tracking-tag/
 * Author URI:   https://woopt.com
 * Version:      1.11.2
 * License:      GPLv2 or later
 * Text Domain:  woocommerce-google-adwords-conversion-tracking-tag
 * WC requires at least: 2.6
 * WC tested up to: 5.5
 *
 *
 **/
// TODO export settings function
// TODO add option checkbox on uninstall and ask if user wants to delete options from db
// TODO ask inverse cookie approval. Only of cookies have been allowed, fire the pixels.
// TODO remove google_business_vertical cleanup
use  WGACT\Classes\Admin\Admin ;
use  WGACT\Classes\Admin\Ask_For_Rating ;
use  WGACT\Classes\Admin\Environment_Check ;
use  WGACT\Classes\Db_Upgrade ;
use  WGACT\Classes\Default_Options ;
use  WGACT\Classes\Pixels\Cookie_Consent_Management ;
use  WGACT\Classes\Pixels\Pixel_Manager ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( function_exists( 'wga_fs' ) ) {
    wga_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'wga_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wga_fs()
        {
            global  $wga_fs ;
            
            if ( !isset( $wga_fs ) ) {
                // Include Freemius SDK.
                //                require_once dirname(__FILE__) . '/freemius/start.php';
                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                $wga_fs = fs_dynamic_init( [
                    'navigation'      => 'tabs',
                    'id'              => '7498',
                    'slug'            => 'woocommerce-google-adwords-conversion-tracking-tag',
                    'premium_slug'    => 'woopt-pixel-manager-pro',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_d4182c5e1dc92c6032e59abbfdb91',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Pro',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => [
                    'days'               => 14,
                    'is_require_payment' => true,
                ],
                    'has_affiliation' => 'customers',
                    'menu'            => [
                    'slug'           => 'wgact',
                    'override_exact' => true,
                    'contact'        => false,
                    'support'        => false,
                    'parent'         => [
                    'slug' => 'woocommerce',
                ],
                ],
                    'is_live'         => true,
                ] );
            }
            
            return $wga_fs;
        }
        
        // Init Freemius.
        wga_fs();
        // Signal that SDK was initiated.
        do_action( 'wga_fs_loaded' );
        function wga_fs_settings_url()
        {
            return admin_url( 'admin.php?page=wgact&section=main&subsection=google-ads' );
        }
        
        wga_fs()->add_filter( 'connect_url', 'wga_fs_settings_url' );
        wga_fs()->add_filter( 'after_skip_url', 'wga_fs_settings_url' );
        wga_fs()->add_filter( 'after_connect_url', 'wga_fs_settings_url' );
        wga_fs()->add_filter( 'after_pending_connect_url', 'wga_fs_settings_url' );
    }
    
    // ... Your plugin's main file logic ...
    define( 'WOOPTPM_PLUGIN_PREFIX', 'wooptpm_' );
    define( 'WOOPTPM_DB_VERSION', '3' );
    define( 'WOOPTPM_DB_OPTIONS_NAME', 'wgact_plugin_options' );
    define( 'WOOPTPM_DB_RATINGS', 'wgact_ratings' );
    define( 'WOOPTPM_DB_NOTIFICATIONS_NAME', 'wgact_notifications' );
    define( 'WOOPTPM_PLUGIN_DIR_PATH', plugin_dir_url( __FILE__ ) );
    class WGACT
    {
        protected  $options ;
        protected  $environment_check ;
        public function __construct()
        {
            // check if WooCommerce is running
            // currently this is the most reliable test for single and multisite setups
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            
            if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                // autoloader
                require_once 'lib/autoload.php';
                if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
                    require __DIR__ . '/vendor/autoload.php';
                }
                $this->setup_freemius_environment();
                $this->environment_check = new Environment_Check();
                $plugin_data = get_file_data( __FILE__, [
                    'Version' => 'Version',
                ], false );
                $plugin_version = $plugin_data['Version'];
                define( 'WGACT_CURRENT_VERSION', $plugin_version );
                // running the DB updater
                if ( get_option( WOOPTPM_DB_OPTIONS_NAME ) ) {
                    ( new Db_Upgrade() )->run_options_db_upgrade();
                }
                // load the options
                $this->wgact_options_init();
                if ( isset( $this->options['google']['gads']['dynamic_remarketing'] ) && $this->options['google']['gads']['dynamic_remarketing'] ) {
                    // make sure to disable the WGDR plugin in case we use dynamic remarketing in this plugin
                    add_filter( 'wgdr_third_party_cookie_prevention', '__return_true' );
                }
                // run environment workflows
                add_action( 'admin_notices', [ $this, 'run_admin_compatibility_checks' ] );
                add_action( 'admin_notices', [ $this, 'environment_check_admin_notices' ] );
                add_action( 'admin_notices', function () {
                    $this->environment_check->run_incompatible_plugins_checks();
                } );
                $this->environment_check->permanent_compatibility_mode();
                $this->run_compatibility_modes();
                $this->environment_check->flush_cache_on_plugin_changes();
                register_activation_hook( __FILE__, [ $this, 'plugin_activated' ] );
                register_deactivation_hook( __FILE__, [ $this, 'plugin_deactivated' ] );
                $this->init();
            }
        
        }
        
        public function plugin_activated()
        {
            $this->environment_check->flush_cache_of_all_cache_plugins();
        }
        
        public function plugin_deactivated()
        {
            $this->environment_check->flush_cache_of_all_cache_plugins();
        }
        
        public function environment_check_admin_notices()
        {
            //            if (apply_filters('wooptpm_show_admin_notifications', true))
            //            {
            //
            //            }
            if ( apply_filters( 'wooptpm_show_admin_alerts', true ) ) {
                $this->environment_check->check_active_off_site_payment_gateways();
            }
        }
        
        private function run_compatibility_modes()
        {
            /*
             * Compatibility modes
             */
            if ( $this->options['general']['maximum_compatibility_mode'] ) {
                $this->environment_check->enable_maximum_compatibility_mode();
            }
            if ( $this->options['general']['maximum_compatibility_mode'] && $this->options['facebook']['microdata'] ) {
                $this->environment_check->enable_maximum_compatibility_mode_yoast_seo();
            }
        }
        
        // startup all functions
        public function init()
        {
            // display admin views
            new Admin( $this->options );
            // ask visitor for rating
            new Ask_For_Rating();
            // add a settings link on the plugins page
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'wgact_settings_link' ] );
            // inject pixels into front end
            // in order to time it correctly so that the prevention filter works we need to use the after_setup_theme action
            // 	https://stackoverflow.com/a/19279650
            add_action( 'after_setup_theme', [ $this, 'inject_pixels' ] );
        }
        
        public function inject_pixels()
        {
            // check if cookie prevention has been activated
            // load the cookie consent management functions
            $cookie_consent = new Cookie_Consent_Management();
            $cookie_consent->setPluginPrefix( WOOPTPM_PLUGIN_PREFIX );
            if ( $cookie_consent->is_cookie_prevention_active() == false ) {
                // inject pixels
                new Pixel_Manager( $this->options );
            }
        }
        
        public function run_admin_compatibility_checks()
        {
            $this->environment_check->run_checks();
        }
        
        // initialise the options
        private function wgact_options_init()
        {
            // set options equal to defaults
            //            global $wgact_plugin_options;
            $this->options = get_option( WOOPTPM_DB_OPTIONS_NAME );
            
            if ( false === $this->options ) {
                // if no options have been set yet, initiate default options
                //            error_log('options empty, loading default');
                //                $this->options = $this->wgact_get_default_options();
                $this->options = ( new Default_Options() )->get_default_options();
                update_option( WOOPTPM_DB_OPTIONS_NAME, $this->options );
                //            $options = get_option(WGACT_DB_OPTIONS_NAME);
                //		    error_log(print_r($options, true));
            } else {
                // Check if each single option has been set. If not, set them. That is necessary when new options are introduced.
                // cleanup the db of this setting
                // remove by end of 2021 latest
                if ( array_key_exists( 'google_business_vertical', $this->options ) ) {
                    unset( $this->options['google_business_vertical'] );
                }
                // cleanup the db of this setting
                // remove by end of 2021 latest
                // accidentally had this dummy id left in the default options in 1.7.13
                if ( $this->options['facebook']['pixel_id'] === '767038516805171' ) {
                    $this->options['facebook']['pixel_id'] = '';
                }
                // add new default options to the options db array
                $this->options = ( new Default_Options() )->update_with_defaults( $this->options, ( new Default_Options() )->get_default_options() );
                update_option( WOOPTPM_DB_OPTIONS_NAME, $this->options );
            }
        
        }
        
        // adds a link on the plugins page for the wgdr settings
        // ! Can't be required. Must be in the main plugin file.
        public function wgact_settings_link( $links )
        {
            $links[] = '<a href="' . admin_url( 'admin.php?page=wgact' ) . '">Settings</a>';
            return $links;
        }
        
        protected function setup_freemius_environment()
        {
            wga_fs()->add_filter( 'show_trial', function () {
                
                if ( $this->is_development_install() ) {
                    return false;
                } else {
                    return apply_filters( 'wooptpm_show_admin_trial_promo', true ) && apply_filters( 'wooptpm_show_admin_notifications', true );
                }
            
            } );
            // don't reshow trial message for 10 years
            wga_fs()->add_filter( 'reshow_trial_after_every_n_sec', function () {
                return 60 * 60 * 24 * 7 * 52 * 10;
            } );
        }
        
        protected function is_development_install() : bool
        {
            
            if ( class_exists( 'FS_Site' ) ) {
                return FS_Site::is_localhost_by_address( get_site_url() );
            } else {
                return false;
            }
        
        }
    
    }
    new WGACT();
}

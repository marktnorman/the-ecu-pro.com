<?php

// TODO move script for copying debug info into a proper .js enqueued file, or switch tabs to JavaScript switching and always save all settings at the same time
// TODO debug info list of active payment gateways
namespace WGACT\Classes\Admin;

use  WC_Geolocation ;
use  WC_Order ;
use  WGACT\Classes\Pixels\Google\Trait_Google ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class Admin
{
    use  Trait_Google ;
    public  $ip ;
    protected  $text_domain ;
    protected  $options ;
    protected  $plugin_hook ;
    protected  $documentation_host ;
    public function __construct( $options )
    {
        $this->options = $options;
        $this->plugin_hook = 'woocommerce_page_wgact';
        $this->documentation_host = 'docs.woopt.com';
        add_action( 'admin_enqueue_scripts', [ $this, 'wgact_admin_scripts' ] );
        // add the admin options page
        add_action( 'admin_menu', [ $this, 'wgact_plugin_admin_add_page' ], 99 );
        // install a settings page in the admin console
        add_action( 'admin_init', [ $this, 'wgact_plugin_admin_init' ] );
        // Load textdomain
        add_action( 'init', [ $this, 'load_plugin_textdomain' ] );
        wga_fs()->add_filter( 'checkout/purchaseCompleted', [ $this, 'my_after_purchase_js' ] );
        wga_fs()->add_filter( 'templates/checkout.php', [ $this, 'my_checkout_enrich' ] );
    }
    
    public function my_after_purchase_js( $js_function ) : string
    {
        return "function ( response ) {\n\n            let\n                isTrial = (null != response.purchase.trial_ends),\n                isSubscription = (null != response.purchase.initial_amount),\n                total = isTrial ? 0 : (isSubscription ? response.purchase.initial_amount : response.purchase.gross).toString(),\n                productName = 'WooCommerce Pixel Manager',\n                // storeUrl = 'https://woopt.com',\n                storeName = 'woopt WooCommerce Pixel Manager';\n            \n            window.dataLayer = window.dataLayer || [];\n\n            function gtag() {\n                dataLayer.push(arguments);\n            }\n    \n            gtag('js', new Date());            \n    \n            gtag('config', 'UA-39746956-10', {'anonymize_ip':'true'});\n            gtag('config', 'G-LWRCPMQS9T');\n            gtag('config', 'AW-406204436');\n            \n            gtag('event', 'purchase', {\n                'send_to':['UA-39746956-10', 'G-LWRCPMQS9T'],\n                'transaction_id':response.purchase.id.toString(),\n                'currency':'USD',\n                'discount':0,\n                'items':[{\n                    'id':response.purchase.plan_id.toString(),\n                    'quantity':1,\n                    'price':total,\n                    'name':productName,\n                    'category': 'Plugin',\n                }],\n                'affiliation': storeName,\n                'value':response.purchase.initial_amount.toString()\n            });\n            \n            gtag('event', 'conversion', {\n              'send_to': 'AW-406204436/XrUYCK3J8YoCEJTg2MEB',\n              'value': response.purchase.initial_amount.toString(),\n              'currency': 'USD',\n              'transaction_id': response.purchase.id.toString()\n            });\n            \n            !function(f,b,e,v,n,t,s)\n            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?\n            n.callMethod.apply(n,arguments):n.queue.push(arguments)};\n            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\n            n.queue=[];t=b.createElement(e);t.async=!0;\n            t.src=v;s=b.getElementsByTagName(e)[0];\n            s.parentNode.insertBefore(t,s)}(window, document,'script',\n            'https://connect.facebook.net/en_US/fbevents.js');\n            fbq('init', '257839909406661');\n            fbq('track', 'PageView');\n            \n            fbq('track', 'Purchase', {\n                currency: 'USD',\n                value: total\n            });\n        }";
    }
    
    public function my_checkout_enrich( $html ) : string
    {
        return '<script async src="https://www.googletagmanager.com/gtag/js?id=UA-39746956-10"></script>' . $html;
    }
    
    public function wgact_admin_scripts( $hook_suffix )
    {
        // only output the admin scripts on the plugin pages
        //        if ($this->plugin_hook != $hook_suffix) {
        //            return;
        //        }
        if ( !strpos( $hook_suffix, 'page_wgact' ) ) {
            return;
        }
        //        wp_enqueue_script('wooptpm-script-blocker-warning', plugin_dir_url(__DIR__) . '../js/admin/script-blocker-warning.js', ['jquery'], WGACT_CURRENT_VERSION, false);
        //        wp_enqueue_script('wooptpm-admin-helpers', plugin_dir_url(__DIR__) . '../js/admin/helpers.js', ['jquery'], WGACT_CURRENT_VERSION, false);
        //        wp_enqueue_script('wooptpm-admin-tabs', plugin_dir_url(__DIR__) . '../js/admin/tabs.js', ['jquery'], WGACT_CURRENT_VERSION, false);
        //        wp_enqueue_script('wooptpm-selectWoo', plugin_dir_url(__DIR__) . '../js/admin/selectWoo.full.min.js', ['jquery'], WGACT_CURRENT_VERSION, false);
        //
        //        wp_enqueue_style('wooptpm-admin', plugin_dir_url(__DIR__) . '../css/admin.css', [], WGACT_CURRENT_VERSION);
        //        wp_enqueue_style('wooptpm-selectWoo', plugin_dir_url(__DIR__) . '../css/selectWoo.min.css', [], WGACT_CURRENT_VERSION);
        wp_enqueue_script(
            'wooptpm-script-blocker-warning',
            WOOPTPM_PLUGIN_DIR_PATH . 'js/admin/script-blocker-warning.js',
            [ 'jquery' ],
            WGACT_CURRENT_VERSION,
            false
        );
        wp_enqueue_script(
            'wooptpm-admin-helpers',
            WOOPTPM_PLUGIN_DIR_PATH . 'js/admin/helpers.js',
            [ 'jquery' ],
            WGACT_CURRENT_VERSION,
            false
        );
        wp_enqueue_script(
            'wooptpm-admin-tabs',
            WOOPTPM_PLUGIN_DIR_PATH . 'js/admin/tabs.js',
            [ 'jquery' ],
            WGACT_CURRENT_VERSION,
            false
        );
        wp_enqueue_script(
            'wooptpm-selectWoo',
            WOOPTPM_PLUGIN_DIR_PATH . 'js/admin/selectWoo.full.min.js',
            [ 'jquery' ],
            WGACT_CURRENT_VERSION,
            false
        );
        wp_enqueue_style(
            'wooptpm-admin',
            WOOPTPM_PLUGIN_DIR_PATH . 'css/admin.css',
            [],
            WGACT_CURRENT_VERSION
        );
        wp_enqueue_style(
            'wooptpm-selectWoo',
            WOOPTPM_PLUGIN_DIR_PATH . 'css/selectWoo.min.css',
            [],
            WGACT_CURRENT_VERSION
        );
    }
    
    // Load text domain function
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain( 'woocommerce-google-adwords-conversion-tracking-tag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    
    // add the admin options page
    public function wgact_plugin_admin_add_page()
    {
        //add_options_page('WGACT Plugin Page', 'WGACT Plugin Menu', 'manage_options', 'wgact', array($this, 'wgact_plugin_options_page'));
        add_submenu_page(
            'woocommerce',
            esc_html__( 'woopt Pixel Manager', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            esc_html__( 'woopt Pixel Manager', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'manage_options',
            'wgact',
            [ $this, 'wgact_plugin_options_page' ]
        );
    }
    
    // add the admin settings and such
    public function wgact_plugin_admin_init()
    {
        register_setting( 'wgact_plugin_options_group', 'wgact_plugin_options', [ $this, 'wgact_options_validate' ] );
        $this->add_section_main();
        $this->add_section_advanced();
        $this->add_section_beta();
        $this->add_section_support();
        $this->add_section_author();
    }
    
    public function add_section_main()
    {
        $section_ids = [
            'title'         => esc_html__( 'Main', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'          => 'main',
            'settings_name' => 'wgact_plugin_main_section',
        ];
        $this->output_section_data_field( $section_ids );
        add_settings_section(
            $section_ids['settings_name'],
            esc_html__( $section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_section_main_description' ],
            'wgact_plugin_options_page'
        );
        $this->add_section_main_subsection_google_ads( $section_ids );
        $this->add_section_main_subsection_facebook( $section_ids );
        $this->add_section_main_subsection_more_pixels( $section_ids );
    }
    
    public function add_section_main_subsection_google_ads( $section_ids )
    {
        $sub_section_ids = [
            'title' => esc_html__( 'Google', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'  => 'google',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add the field for the conversion id
        add_settings_field(
            'wgact_plugin_conversion_id',
            esc_html__( 'Google Ads Conversion ID', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_google_ads_conversion_id' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add the field for the conversion label
        add_settings_field(
            'wgact_plugin_conversion_label',
            esc_html__( 'Google Ads Conversion Label', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_google_ads_conversion_label' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        add_settings_field(
            'wgact_plugin_analytics_ua_property_id',
            esc_html__( 'Google Analytics UA', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_google_analytics_universal_property' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        add_settings_field(
            'wgact_plugin_analytics_4_measurement_id',
            esc_html__( 'Google Analytics 4', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_google_analytics_4_id' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        add_settings_field(
            'wgact_plugin_google_optimize_container_id',
            esc_html__( 'Google Optimize', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_google_optimize_container_id' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function add_section_main_subsection_facebook( $section_ids )
    {
        $sub_section_ids = [
            'title' => esc_html__( 'Facebook', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'  => 'facebook',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add the field for the conversion label
        add_settings_field(
            'wgact_plugin_facebook_id',
            esc_html__( 'Facebook pixel ID', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_facebook_pixel_id' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function add_section_main_subsection_more_pixels( $section_ids )
    {
        $sub_section_ids = [
            'title' => esc_html__( 'more pixels', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'  => 'more-pixels',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        
        if ( wga_fs()->is__premium_only() || $this->options['general']['pro_version_demo'] ) {
            // add the field for the Bing Ads UET tag ID
            add_settings_field(
                'wgact_plugin_bing_uet_tag_id',
                esc_html__( 'Microsoft Advertising UET tag ID', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_option_html_bing_uet_tag_id' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add the field for the Twitter pixel
            add_settings_field(
                'wgact_plugin_twitter_pixel_id',
                esc_html__( 'Twitter pixel ID', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_option_html_twitter_pixel_id' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add the field for the Pinterest pixel
            add_settings_field(
                'wgact_plugin_pinterest_pixel_id',
                esc_html__( 'Pinterest pixel ID', 'woocommerce-google-adwords-conversion-tracking-tag' ) . $this->html_beta(),
                [ $this, 'wgact_option_html_pinterest_pixel_id' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add the field for the Snapchat pixel
            add_settings_field(
                'wgact_plugin_snapchat_pixel_id',
                esc_html__( 'Snapchat pixel ID', 'woocommerce-google-adwords-conversion-tracking-tag' ) . $this->html_beta(),
                [ $this, 'wgact_option_html_snapchat_pixel_id' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add the field for the TikTok pixel
            add_settings_field(
                'wgact_plugin_tiktok_pixel_id',
                esc_html__( 'TikTok pixel ID', 'woocommerce-google-adwords-conversion-tracking-tag' ) . $this->html_beta(),
                [ $this, 'wgact_option_html_tiktok_pixel_id' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
        }
        
        // add the field for the Hotjar pixel
        add_settings_field(
            'wgact_plugin_hotjar_site_id',
            esc_html__( 'Hotjar site ID', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_hotjar_site_id' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function add_section_advanced()
    {
        $section_ids = [
            'title'         => esc_html__( 'Advanced', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'          => 'advanced',
            'settings_name' => 'wgact_plugin_advanced_section',
        ];
        add_settings_section(
            $section_ids['settings_name'],
            esc_html__( $section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_section_advanced_description' ],
            'wgact_plugin_options_page'
        );
        $this->output_section_data_field( $section_ids );
        $this->add_section_advanced_subsection_shop( $section_ids );
        $this->add_section_advanced_subsection_google( $section_ids );
        
        if ( wga_fs()->is__premium_only() || $this->pro_version_demo_active() ) {
            $this->add_section_advanced_subsection_facebook( $section_ids );
            $this->add_section_advanced_subsection_cookie_consent_mgmt( $section_ids );
        }
    
    }
    
    public function add_section_advanced_subsection_shop( $section_ids )
    {
        $sub_section_ids = [
            'title' => 'Shop',
            'slug'  => 'shop',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add fields for the order total logic
        add_settings_field(
            'wgact_plugin_order_total_logic',
            esc_html__( 'Order Total Logic', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_shop_order_total_logic' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add checkbox for order duplication prevention
        add_settings_field(
            'wgact_setting_order_duplication_prevention',
            esc_html__( 'Order Duplication Prevention', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_order_duplication_prevention' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add checkbox for maximum compatibility mode
        add_settings_field(
            'wgact_setting_maximum_compatibility_mode',
            esc_html__( 'Maximum Compatibility Mode', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_maximum_compatibility_mode' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function add_section_advanced_subsection_google( $section_ids )
    {
        $sub_section_ids = [
            'title' => 'Google',
            'slug'  => 'google',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        if ( $this->options['google']['gtag']['deactivation'] ) {
            // add fields for the gtag insertion
            add_settings_field(
                'wgact_plugin_gtag',
                esc_html__( 'gtag Deactivation', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_setting_html_google_gtag_deactivation' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
        }
        // add the field for the aw_merchant_id
        add_settings_field(
            'wgact_plugin_aw_merchant_id',
            esc_html__( 'Conversion Cart Data', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_setting_aw_merchant_id' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        
        if ( wga_fs()->is__premium_only() || $this->pro_version_demo_active() ) {
            // add fields for the Google enhanced e-commerce
            add_settings_field(
                'wgact_setting_google_analytics_eec',
                esc_html__( 'Enhanced E-Commerce', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_setting_html_google_analytics_eec' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add fields for the Google GA 4 API secret
            add_settings_field(
                'wgact_setting_google_analytics_4_api_secret',
                esc_html__( 'GA 4 API secret', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_setting_html_google_analytics_4_api_secret' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
        }
        
        // add fields for the Google Analytics link attribution
        add_settings_field(
            'wgact_setting_google_analytics_link_attribution',
            esc_html__( 'Enhanced Link Attribution', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_google_analytics_link_attribution' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        
        if ( wga_fs()->is__premium_only() || $this->pro_version_demo_active() ) {
            // add user_id for the Google
            add_settings_field(
                'wgact_setting_google_user_id',
                esc_html__( 'Google User ID', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_setting_html_google_user_id' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add Google Ads Enhanced Conversions
            add_settings_field(
                'wooptpm_setting_google_ads_enhanced_conversions',
                esc_html__( 'Google Ads Enhanced Conversions', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wooptpm_setting_html_google_ads_enhanced_conversions' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
        }
        
        
        if ( wga_fs()->is__premium_only() || $this->pro_version_demo_active() ) {
            // add fields for the Google Ads phone conversion number
            add_settings_field(
                'wgact_setting_google_ads_phone_conversion_number',
                esc_html__( 'Google Ads phone conversion number', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_setting_html_google_ads_phone_conversion_number' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
            // add fields for the Google Ads phone conversion label
            add_settings_field(
                'wgact_setting_google_ads_phone_conversion_label',
                esc_html__( 'Google Ads phone conversion label', 'woocommerce-google-adwords-conversion-tracking-tag' ),
                [ $this, 'wgact_setting_html_google_ads_phone_conversion_label' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
        }
    
    }
    
    public function add_section_advanced_subsection_cookie_consent_mgmt( $section_ids )
    {
        $sub_section_ids = [
            'title' => esc_html__( 'Cookie Consent Management', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'  => 'cookie-consent-mgmt',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add fields for the Google Consent beta
        add_settings_field(
            'wgact_setting_google_consent_mode_active',
            esc_html__( 'Google Consent Mode', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_google_consent_mode_active' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add fields for the Google consent regions
        add_settings_field(
            'wgact_setting_google_consent_regions',
            esc_html__( 'Google Consent Regions', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_google_consent_regions' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        if ( ( new Environment_Check() )->is_borlabs_cookie_active() ) {
            // add fields for the Borlabs support
            add_settings_field(
                'wgact_setting_borlabs_support',
                esc_html__( 'Borlabs support', 'woocommerce-google-adwords-conversion-tracking-tag' ) . $this->html_beta(),
                [ $this, 'wgact_setting_html_borlabs_support' ],
                'wgact_plugin_options_page',
                $section_ids['settings_name']
            );
        }
        // add fields for the Cookiebot support
        add_settings_field(
            'wgact_setting_cookiebot_active',
            esc_html__( 'Cookiebot support', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_cookiebot_support' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function add_section_advanced_subsection_facebook( $section_ids )
    {
        $sub_section_ids = [
            'title' => 'Facebook',
            'slug'  => 'facebook',
        ];
        add_settings_field(
            'wgact_plugin_subsection_' . $sub_section_ids['slug'] . '_opening_div',
            esc_html__( $sub_section_ids['title'], 'woocommerce-google-adwords-conversion-tracking-tag' ),
            function () use( $section_ids, $sub_section_ids ) {
            $this->wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add field for the Facebook CAPI token
        add_settings_field(
            'wgact_setting_facebook_capi_token',
            esc_html__( 'Facebook CAPI: token', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_html_facebook_capi_token' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add field for the Facebook CAPI user transparency process anonymous hits
        add_settings_field(
            'wgact_setting_facebook_capi_user_transparency_process_anonymous_hits',
            esc_html__( 'Facebook CAPI: process anonymous hits', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_facebook_capi_user_transparency_process_anonymous_hits' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add field for the Facebook CAPI user transparency send additional client identifiers
        add_settings_field(
            'wgact_setting_facebook_capi_user_transparency_send_additional_client_identifiers',
            esc_html__( 'Facebook CAPI: send additional visitor identifiers', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_setting_facebook_capi_user_transparency_send_additional_client_identifiers' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
        // add fields for Facebook microdata
        add_settings_field(
            'wgact_setting_facebook_microdata_active',
            esc_html__( 'Facebook Microdata Tags for Catalogues', 'woocommerce-google-adwords-conversion-tracking-tag' ) . $this->html_beta(),
            [ $this, 'wgact_setting_html_facebook_microdata' ],
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function add_section_beta()
    {
        $section_ids = [
            'title'         => esc_html__( 'Dynamic Remarketing', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'          => 'dynamic-remarketing',
            'settings_name' => 'wgact_plugin_beta_section',
        ];
        $this->output_section_data_field( $section_ids );
        // add new section for cart data
        add_settings_section(
            'wgact_plugin_beta_section',
            esc_html__( 'Dynamic Remarketing', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_section_add_cart_data_description' ],
            'wgact_plugin_options_page'
        );
        // add fields for cart data
        //        add_settings_field(
        //            'wgact_plugin_add_cart_data',
        //            esc_html__(
        //                'Activation',
        //                'woocommerce-google-adwords-conversion-tracking-tag'
        //            ),
        //            [$this, 'wgact_option_html_google_ads_add_cart_data'],
        //            'wgact_plugin_options_page',
        //            'wgact_plugin_beta_section'
        //        );
        // add the field for the aw_feed_country
        //        add_settings_field(
        //            'wgact_plugin_aw_feed_country',
        //            esc_html__(
        //                'aw_feed_country',
        //                'woocommerce-google-adwords-conversion-tracking-tag'
        //            ),
        //            [$this, 'wgact_plugin_setting_aw_feed_country'],
        //            'wgact_plugin_options_page',
        //            'wgact_plugin_beta_section'
        //        );
        // add the field for the aw_feed_language
        //        add_settings_field(
        //            'wgact_plugin_aw_feed_language',
        //            esc_html__(
        //                'aw_feed_language',
        //                'woocommerce-google-adwords-conversion-tracking-tag'
        //            ),
        //            [$this, 'wgact_plugin_setting_aw_feed_language'],
        //            'wgact_plugin_options_page',
        //            'wgact_plugin_beta_section'
        //        );
        // add checkbox for dynamic remarketing
        add_settings_field(
            'wgact_plugin_option_dynamic_remarketing',
            esc_html__( 'Dynamic Remarketing', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_google_ads_dynamic_remarketing' ],
            'wgact_plugin_options_page',
            'wgact_plugin_beta_section'
        );
        // add fields for the product identifier
        add_settings_field(
            'wgact_plugin_option_product_identifier',
            esc_html__( 'Product Identifier', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_option_product_identifier' ],
            'wgact_plugin_options_page',
            'wgact_plugin_beta_section'
        );
        // add checkbox for variations output
        add_settings_field(
            'wgact_plugin_option_variations_output',
            esc_html__( 'Variations output', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_option_html_variations_output' ],
            'wgact_plugin_options_page',
            'wgact_plugin_beta_section'
        );
        if ( wga_fs()->is__premium_only() || $this->pro_version_demo_active() ) {
            // google_business_vertical
            add_settings_field(
                'wgact_plugin_option_google_business_vertical',
                esc_html__( 'Google Business Vertical', 'woocommerce-google-adwords-conversion-tracking-tag' ) . $this->html_pro_feature(),
                [ $this, 'wgact_plugin_option_google_business_vertical' ],
                'wgact_plugin_options_page',
                'wgact_plugin_beta_section'
            );
        }
    }
    
    public function add_section_support()
    {
        $section_ids = [
            'title'         => esc_html__( 'Support', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'          => 'support',
            'settings_name' => 'wgact_plugin_support_section',
        ];
        $this->output_section_data_field( $section_ids );
        add_settings_section(
            'wgact_plugin_support_section',
            esc_html__( 'Support', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_section_support_description' ],
            'wgact_plugin_options_page'
        );
    }
    
    public function add_section_author()
    {
        $section_ids = [
            'title'         => esc_html__( 'Author', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            'slug'          => 'author',
            'settings_name' => 'wgact_plugin_author_section',
        ];
        $this->output_section_data_field( $section_ids );
        add_settings_section(
            'wgact_plugin_author_section',
            esc_html__( 'Author', 'woocommerce-google-adwords-conversion-tracking-tag' ),
            [ $this, 'wgact_plugin_section_author_description' ],
            'wgact_plugin_options_page'
        );
    }
    
    protected function output_section_data_field( array $section_ids )
    {
        add_settings_field(
            'wgact_plugin_section_' . $section_ids['slug'] . '_opening_div',
            '',
            function () use( $section_ids ) {
            $this->wgact_section_generic_opening_div_html( $section_ids );
        },
            'wgact_plugin_options_page',
            $section_ids['settings_name']
        );
    }
    
    public function wgact_section_generic_opening_div_html( $section_ids )
    {
        echo  '<div class="section" data-section-title="' . $section_ids['title'] . '" data-section-slug="' . $section_ids['slug'] . '"></div>' ;
    }
    
    public function wgact_subsection_generic_opening_div_html( $section_ids, $sub_section_ids )
    {
        echo  '<div class="subsection" data-section-slug="' . $section_ids['slug'] . '" data-subsection-title="' . $sub_section_ids['title'] . '" data-subsection-slug="' . $sub_section_ids['slug'] . '"></div>' ;
    }
    
    // display the admin options page
    public function wgact_plugin_options_page()
    {
        ?>
        <div id="script-blocker-notice" class="notice notice-error"
             style="width:90%; float: left; margin: 5px; font-weight: bold">
            <p>
                <?php 
        esc_html_e( 'It looks like you are using some sort of ad or script blocker which is blocking the script and CSS files of this plugin.
                    In order for the plugin to work properly you need to disable the script blocker.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
            </p>
            <p>
                <a href="https://docs.woopt.com/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=script-blocker-error#/script-blockers"
                   target="_blank">
                    <?php 
        esc_html_e( 'Learn more', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                </a>
            </p>

            <script>
                if (typeof wgact_hide_script_blocker_warning === "function") {
                    wgact_hide_script_blocker_warning();
                }
            </script>

        </div>

        <div style="width:90%; margin: 5px">

            <?php 
        settings_errors();
        ?>

            <h2 class="nav-tab-wrapper">
            </h2>

            <form id="wgact_settings_form" action="options.php" method="post">

                <?php 
        settings_fields( 'wgact_plugin_options_group' );
        do_settings_sections( 'wgact_plugin_options_page' );
        submit_button();
        ?>

                <div class="developer-banner">
                    <div style="display: flex; justify-content: space-between">
                        <span >
                            <?php 
        esc_html_e( 'Profit Driven Marketing by woopt', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                        </span>

                        <?php 
        ?>

                            <div style="float: right; padding-left: 20px">
                                <span style="padding-right: 6px">
                                    Enable Pro version demo features
                                </span>
                                <label class="switch" id="wgact_pro_version_demo">

                                    <input type='hidden' value='0'
                                           name='wgact_plugin_options[general][pro_version_demo]'>
                                    <input type="checkbox" value='1'
                                           name='wgact_plugin_options[general][pro_version_demo]'
                                    <?php 
        checked( $this->options['general']['pro_version_demo'] );
        ?>
                                    />
                                    <span class="slider round"></span>
                                </label>
                            </div>

                        <?php 
        ?>

                        <span style=" padding-left: 20px;">
                            <?php 
        esc_html_e( 'Visit us here:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                            <a href="https://woopt.com/?utm_source=plugin&utm_medium=banner&utm_campaign=wooptpm"
                               target="_blank">https://woopt.com
                            </a>
					    </span>

                    </div>
                </div>

            </form>
        </div>
        <?php 
    }
    
    private function get_link_locale() : string
    {
        
        if ( substr( get_user_locale(), 0, 2 ) === 'de' ) {
            return 'de';
        } else {
            return 'en';
        }
    
    }
    
    /*
     * descriptions
     */
    public function wgact_plugin_section_main_description()
    {
        // do nothing
    }
    
    public function wgact_plugin_section_advanced_description()
    {
        // do nothing
    }
    
    public function wgact_plugin_section_add_cart_data_description()
    {
        //        echo '<div id="beta-description" style="margin-top:20px">';
        //        esc_html_e('Find out more about this new feature: ', 'woocommerce-google-adwords-conversion-tracking-tag');
        //        echo '<a href="https://support.google.com/google-ads/answer/9028254" target="_blank">https://support.google.com/google-ads/answer/9028254</a><br>';
        //        echo '</div>';
    }
    
    public function wgact_plugin_section_support_description()
    {
        ?>
        <div style="margin-top:20px">
            <h2><?php 
        esc_html_e( 'Support contacts', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></h2>
            <?php 
        esc_html_e( 'Use the following two resources for support: ', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </div>
        <div style="margin-bottom: 30px;">
            <ul>

                <li>
                    <?php 
        esc_html_e( 'Post a support request in the WordPress support forum here: ', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                    <a href="https://wordpress.org/support/plugin/woocommerce-google-adwords-conversion-tracking-tag/"
                       target="_blank">
                        <?php 
        esc_html_e( 'Support forum', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                    </a>
                    &nbsp;
                    <span class="dashicons dashicons-info"></span>
                    <?php 
        esc_html_e( '(Never post the debug or other sensitive information to the support forum. Instead send us the information by email.)', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                </li>
                <li>
                    <?php 
        esc_html_e( 'Or send us an email to the following address: ', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                    <a href="mailto:support@woopt.com" target="_blank">support@woopt.com</a>
                </li>
            </ul>
        </div>
        <hr style="border: none;height: 1px; color: #333; background-color: #333;">
        <div style="margin-bottom: 20px">
            <h2><?php 
        esc_html_e( 'Translations', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></h2>
            <?php 
        esc_html_e( 'If you want to participate improving the translations of this plugin into your language, please follow this link:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
            <a href="https://translate.wordpress.org/projects/wp-plugins/woocommerce-google-adwords-conversion-tracking-tag/"
               target="_blank">translate.wordpress.org</a>

        </div>
        <hr style="border: none;height: 1px; color: #333; background-color: #333;">
        <div>
            <h2><?php 
        esc_html_e( 'Debugging information', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></h2>

            <div>
                <textarea id="debug-info-textarea" class=""
                          style="display:block; margin-bottom: 10px; width: 100%;resize: none;color:dimgrey;"
                          cols="100%" rows="30"
                          readonly><?php 
        echo  ( new Debug_info() )->get_debug_info() ;
        ?>
                </textarea>
                <button id="debug-info-button"
                        type="button"><?php 
        esc_html_e( 'copy to clipboard', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></button>
            </div>

        </div>
        <hr style="border: none;height: 1px; color: #333; background-color: #333;">


        <?php 
    }
    
    public function wgact_plugin_section_author_description()
    {
        ?>
        <div style="margin-top:20px;margin-bottom: 30px">
            <?php 
        esc_html_e( 'More details about the developer of this plugin: ', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </div>
        <div style="margin-bottom: 30px;">
            <div><?php 
        esc_html_e( 'Developer: woopt', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></div>
            <div>
                <?php 
        esc_html_e( 'Website: ', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
                <a href="https://woopt.com/?utm_source=plugin&utm_medium=banner&utm_campaign=woopt"
                   target="_blank">https://woopt.com</a>
            </div>
        </div>
        <?php 
    }
    
    public function wgact_option_html_google_analytics_universal_property()
    {
        echo  "<input id='wgact_plugin_analytics_ua_property_id' name='wgact_plugin_options[google][analytics][universal][property_id]' size='40' type='text' value='{$this->options['google']['analytics']['universal']['property_id']}' />" ;
        echo  $this->get_status_icon( $this->options['google']['analytics']['universal']['property_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-conversion-id#/pixels/google-analytics-ua' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The Google Analytics Universal property ID looks like this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>UA-12345678-1</i>' ;
    }
    
    public function wgact_option_html_google_analytics_4_id()
    {
        echo  "<input id='wgact_plugin_analytics_4_measurement_id' name='wgact_plugin_options[google][analytics][ga4][measurement_id]' size='40' type='text' value='{$this->options['google']['analytics']['ga4']['measurement_id']}' />" ;
        echo  $this->get_status_icon( $this->options['google']['analytics']['ga4']['measurement_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-conversion-id#/pixels/google-analytics-4' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The Google Analytics 4 measurement ID looks like this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>G-R912ZZ1MHH0</i>' ;
    }
    
    public function wgact_option_html_google_ads_conversion_id()
    {
        echo  "<input id='wgact_plugin_conversion_id' name='wgact_plugin_options[google][ads][conversion_id]' size='40' type='text' value='{$this->options['google']['ads']['conversion_id']}' />" ;
        echo  $this->get_status_icon( $this->options['google']['ads']['conversion_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-conversion-id#/pixels/google-ads?id=configure-the-plugin' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The conversion ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>123456789</i>' ;
    }
    
    public function wgact_option_html_google_ads_conversion_label()
    {
        echo  "<input id='wgact_plugin_conversion_label' name='wgact_plugin_options[google][ads][conversion_label]' size='40' type='text' value='{$this->options['google']['ads']['conversion_label']}' />" ;
        echo  $this->get_status_icon( $this->options['google']['ads']['conversion_label'], $this->options['google']['ads']['conversion_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-conversion-label#/pixels/google-ads?id=configure-the-plugin' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The conversion Label looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>Xt19CO3axGAX0vg6X3gM</i>' ;
        
        if ( $this->options['google']['ads']['conversion_label'] && !$this->options['google']['ads']['conversion_id'] ) {
            echo  '<p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'Requires an active Google Ads Conversion ID', 'woocommerce-google-adwords-conversion-tracking-tag' );
        }
        
        echo  '</p>' ;
    }
    
    public function wgact_option_html_google_optimize_container_id()
    {
        echo  "<input id='wgact_plugin_google_optimize_container_id' name='wgact_plugin_options[google][optimize][container_id]' size='40' type='text' value='{$this->options['google']['optimize']['container_id']}' />" ;
        echo  $this->get_status_icon( $this->options['google']['optimize']['container_id'] ) ;
        //        echo $this->get_documentation_html('/wgact/#/plugin-configuration?id=configure-the-plugin');
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-conversion-id#/pixels/google-optimize' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The Google Optimize container ID looks like this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>GTM-WMAB1BM</i>' ;
    }
    
    public function wgact_option_html_facebook_pixel_id()
    {
        echo  "<input id='wgact_plugin_facebook_pixel_id' name='wgact_plugin_options[facebook][pixel_id]' size='40' type='text' value='{$this->options['facebook']['pixel_id']}' />" ;
        echo  $this->get_status_icon( $this->options['facebook']['pixel_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=facebook-pixel-id#/pixels/facebook' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The Facebook pixel ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>765432112345678</i>' ;
    }
    
    public function wgact_option_html_bing_uet_tag_id()
    {
        echo  "<input id='wgact_plugin_bing_uet_tag_id' name='wgact_plugin_options[bing][uet_tag_id]' size='40' type='text' value='{$this->options['bing']['uet_tag_id']}' {$this->disable_if_demo()} />" ;
        echo  $this->get_status_icon( $this->options['bing']['uet_tag_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=microsoft-advertising-uet-tag-id#/pixels/microsoft-advertising?id=setting-up-the-uet-tag' ) ;
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        esc_html_e( 'The Microsoft Advertising UET tag ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>12345678</i>' ;
    }
    
    public function wgact_option_html_twitter_pixel_id()
    {
        echo  "<input id='wgact_plugin_twitter_pixel_id' name='wgact_plugin_options[twitter][pixel_id]' size='40' type='text' value='{$this->options['twitter']['pixel_id']}' {$this->disable_if_demo()} />" ;
        echo  $this->get_status_icon( $this->options['twitter']['pixel_id'] ) ;
        //        echo $this->get_documentation_html('/wgact/#/twitter');
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        esc_html_e( 'The Twitter pixel ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>a1cde</i>' ;
    }
    
    public function wgact_option_html_pinterest_pixel_id()
    {
        echo  "<input id='wgact_plugin_pinterest_pixel_id' name='wgact_plugin_options[pinterest][pixel_id]' size='40' type='text' value='{$this->options['pinterest']['pixel_id']}' {$this->disable_if_demo()} />" ;
        echo  $this->get_status_icon( $this->options['pinterest']['pixel_id'] ) ;
        //        echo $this->get_documentation_html('/wgact/#/pinterest');
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        esc_html_e( 'The Pinterest pixel ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>1234567890123</i>' ;
    }
    
    public function wgact_option_html_snapchat_pixel_id()
    {
        echo  "<input id='wgact_plugin_snapchat_pixel_id' name='wgact_plugin_options[snapchat][pixel_id]' size='40' type='text' value='{$this->options['snapchat']['pixel_id']}' {$this->disable_if_demo()} />" ;
        echo  $this->get_status_icon( $this->options['snapchat']['pixel_id'] ) ;
        //        echo $this->get_documentation_html('/wgact/#/snapchat');
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        esc_html_e( 'The Snapchat pixel ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>1a2345b6-cd78-9012-e345-fg6h7890ij12</i>' ;
    }
    
    public function wgact_option_html_tiktok_pixel_id()
    {
        echo  "<input id='wgact_plugin_tiktok_pixel_id' name='wgact_plugin_options[tiktok][pixel_id]' size='40' type='text' value='{$this->options['tiktok']['pixel_id']}' {$this->disable_if_demo()} />" ;
        echo  $this->get_status_icon( $this->options['tiktok']['pixel_id'] ) ;
        //        echo $this->get_documentation_html('/wgact/#/tiktok');
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        esc_html_e( 'The TikTok pixel ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>ABCD1E2FGH3IJK45LMN6</i>' ;
    }
    
    public function wgact_option_html_hotjar_site_id()
    {
        echo  "<input id='wgact_plugin_hotjar_site_id' name='wgact_plugin_options[hotjar][site_id]' size='40' type='text' value='{$this->options['hotjar']['site_id']}' />" ;
        echo  $this->get_status_icon( $this->options['hotjar']['site_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=hotjar-site-id#/pixels/hotjar' ) ;
        echo  '<br><br>' ;
        esc_html_e( 'The Hotjar site ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  '&nbsp;<i>1234567</i>' ;
    }
    
    public function wgact_option_html_shop_order_total_logic()
    {
        ?>
        <label>
            <input type='radio' id='wgact_plugin_order_total_logic_0'
                   name='wgact_plugin_options[shop][order_total_logic]'
                   value='0' <?php 
        echo  checked( 0, $this->options['shop']['order_total_logic'], false ) ;
        ?> ><?php 
        esc_html_e( 'Use order_subtotal: Doesn\'t include tax and shipping (default)', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_order_total_logic_1'
                   name='wgact_plugin_options[shop][order_total_logic]'
                   value='1' <?php 
        echo  checked( 1, $this->options['shop']['order_total_logic'], false ) ;
        ?> ><?php 
        esc_html_e( 'Use order_total: Includes tax and shipping', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br><br>
        <?php 
        esc_html_e( 'This is the order total amount reported back to Google Ads', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        <?php 
    }
    
    protected function get_documentation_html( $path ) : string
    {
        $html = '<a class="documentation-icon" href="//' . $this->documentation_host . $path . '" target="_blank">';
        $html .= '<span style="vertical-align: top; margin-top: 0px" class="dashicons dashicons-info-outline tooltip"><span class="tooltiptext">';
        $html .= esc_html__( 'open the documentation', 'woocommerce-google-adwords-conversion-tracking-tag' );
        $html .= '</span></span></a>';
        return $html;
    }
    
    public function wgact_setting_html_google_gtag_deactivation()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][gtag][deactivation]'>
            <input type='checkbox' id='wgact_plugin_option_gtag_deactivation'
                   name='wgact_plugin_options[google][gtag][deactivation]'
                   value='1' <?php 
        checked( $this->options['google']['gtag']['deactivation'] );
        ?> />
            <?php 
        esc_html_e( 'Disable gtag.js insertion, if another plugin is inserting it already', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=gtag-js#/faq?id=google-tag-assistant-reports-multiple-installations-of-global-site-tag-gtagjs-detected-what-shall-i-do' ) ;
        ?>
        <br>
        <p>
            <span class="dashicons dashicons-info"></span>
            <?php 
        esc_html_e( 'Only do this, if the other plugin does insert the gtag above this pixel. If not, keep the gtag active.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </p>
        <p>
            <span class="dashicons dashicons-info"></span>
            <?php 
        esc_html_e( 'This setting is deprecated. It will be removed in future versions of the plugin. Read more about it in the documentation.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </p>
        <?php 
    }
    
    public function wgact_setting_html_google_consent_mode_active()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][consent_mode][active]'>
            <input type='checkbox' id='wgact_setting_google_consent_mode_active'
                   name='wgact_plugin_options[google][consent_mode][active]'
                   value='1'
                <?php 
        checked( $this->options['google']['consent_mode']['active'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Enable Google consent mode with standard settings', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['consent_mode']['active'], true, true ) ;
        ?>
        <?php 
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-consent-mode#/consent-mgmt/google-consent-mode' ) ;
        echo  $this->html_pro_feature() ;
    }
    
    public function wgact_setting_html_google_consent_regions()
    {
        // https://semantic-ui.com/modules/dropdown.html#multiple-selection
        // https://developer.woocommerce.com/2017/08/08/selectwoo-an-accessible-replacement-for-select2/
        // https://github.com/woocommerce/selectWoo
        ?>
        <select id="wgact_setting_google_consent_regions" multiple="multiple"
                name="wgact_plugin_options[google][consent_mode][regions][]"
                style="width:350px" data-placeholder="Choose countries&hellip;" aria-label="Country"
                class="wc-enhanced-select"
            <?php 
        echo  $this->disable_if_demo() ;
        ?>
        >
            <?php 
        foreach ( $this->get_consent_mode_regions() as $region_code => $region_name ) {
            ?>
                <option value="<?php 
            echo  $region_code ;
            ?>" <?php 
            echo  ( in_array( $region_code, $this->options['google']['consent_mode']['regions'] ) ? 'selected' : '' ) ;
            ?>><?php 
            echo  $region_name ;
            ?></option>
            <?php 
        }
        ?>

        </select>
        <script>
            jQuery('#wgact_setting_google_consent_regions').select2({
                // theme: "classic"
            });
        </script>
        <?php 
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-consent-mode-regions#/consent-mgmt/google-consent-mode?id=regions' ) ;
        echo  $this->html_pro_feature() ;
        ?>
        <p>
            <span class="dashicons dashicons-info"></span>
            <?php 
        esc_html_e( 'If no region is set, then the restrictions are enabled for all regions. If you specify one or more regions, then the restrictions only apply for the specified regions.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </p>
        <?php 
    }
    
    public function wgact_setting_html_google_analytics_eec()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][analytics][eec]'>
            <input type='checkbox' id='wgact_setting_google_analytics_eec'
                   name='wgact_plugin_options[google][analytics][eec]'
                   value='1'
                <?php 
        checked( $this->options['google']['analytics']['eec'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Enable Google Analytics enhanced e-commerce', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['analytics']['eec'], $this->options['google']['analytics']['universal']['property_id'] || $this->options['google']['analytics']['ga4']['measurement_id'], true ) ;
        echo  $this->html_pro_feature() ;
        //        echo $this->get_documentation_html('/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-consent-mode#/consent-mgmt/google-consent-mode');
        ?>
        <?php 
        
        if ( $this->options['google']['analytics']['eec'] && (!$this->options['google']['analytics']['universal']['property_id'] && !$this->options['google']['analytics']['ga4']['measurement_id']) ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate at least Google Analytics UA or Google Analytics 4', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_setting_html_google_analytics_4_api_secret()
    {
        echo  "<input \n                id='wgact_setting_google_analytics_4_api_secret' \n                name='wgact_plugin_options[google][analytics][ga4][api_secret]' \n                size='40' \n                type='text' \n                value='{$this->options['google']['analytics']['ga4']['api_secret']}' \n                {$this->disable_if_demo()}\n                />" ;
        echo  $this->get_status_icon( $this->options['google']['analytics']['ga4']['api_secret'], $this->options['google']['analytics']['eec'] ) ;
        //        echo $this->get_documentation_html('/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-phone-conversion-number#/pixels/google-ads?id=phone-conversion-number');
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        
        if ( !$this->options['google']['analytics']['ga4']['measurement_id'] ) {
            echo  '<p></p><span class="dashicons dashicons-info" style="margin-right: 10px"></span>' ;
            esc_html_e( 'Google Analytics 4 activation required', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p>' ;
        }
        
        
        if ( !$this->options['google']['analytics']['eec'] ) {
            echo  '<p></p><span class="dashicons dashicons-info" style="margin-right: 10px"></span>' ;
            esc_html_e( 'Enhanced E-Commerce activation required', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
        
        esc_html_e( 'If enabled, purchase and refund events will be sent to Google through the measurement protocol for increased accuracy.', 'woocommerce-google-adwords-conversion-tracking-tag' );
    }
    
    public function wgact_setting_html_google_analytics_link_attribution()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][analytics][link_attribution]'>
            <input type='checkbox' id='wgact_setting_google_analytics_link_attribution'
                   name='wgact_plugin_options[google][analytics][link_attribution]'
                   value='1' <?php 
        checked( $this->options['google']['analytics']['link_attribution'] );
        ?> />
            <?php 
        esc_html_e( 'Enable Google Analytics enhanced link attribution', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['analytics']['link_attribution'], $this->options['google']['analytics']['universal']['property_id'] || $this->options['google']['analytics']['ga4']['measurement_id'], true ) ;
        ?>
        <?php 
        //        echo $this->get_documentation_html('/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-consent-mode#/consent-mgmt/google-consent-mode');
        ?>
        <?php 
        
        if ( $this->options['google']['analytics']['link_attribution'] && (!$this->options['google']['analytics']['universal']['property_id'] && !$this->options['google']['analytics']['ga4']['measurement_id']) ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate at least Google Analytics UA or Google Analytics 4', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_setting_html_google_user_id()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][user_id]'>
            <input type='checkbox' id='wgact_setting_google_user_id'
                   name='wgact_plugin_options[google][user_id]'
                   value='1'
                <?php 
        checked( $this->options['google']['user_id'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Enable Google user ID', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['user_id'], $this->options['google']['analytics']['universal']['property_id'] || $this->options['google']['analytics']['ga4']['measurement_id'] || $this->is_google_ads_active(), true ) ;
        echo  $this->html_pro_feature() ;
        //        echo $this->get_documentation_html('/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-consent-mode#/consent-mgmt/google-consent-mode');
        ?>
        <?php 
        
        if ( $this->options['google']['analytics']['eec'] && (!$this->options['google']['analytics']['universal']['property_id'] && !$this->options['google']['analytics']['ga4']['measurement_id']) ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate at least Google Analytics UA or Google Analytics 4', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wooptpm_setting_html_google_ads_enhanced_conversions()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][ads][enhanced_conversions]'>
            <input type='checkbox' id='wgact_setting_google_user_id'
                   name='wgact_plugin_options[google][ads][enhanced_conversions]'
                   value='1'
                <?php 
        checked( $this->options['google']['ads']['enhanced_conversions'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Enable Google Ads Enhanced Conversions', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['ads']['enhanced_conversions'], $this->is_google_ads_active(), false ) ;
        echo  $this->html_pro_feature() ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-enhanced-conversions#/pixels/google-ads?id=enhanced-conversions' ) ;
        ?>
        <?php 
        
        if ( !$this->is_google_ads_active() ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate Google Ads', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_setting_html_google_ads_phone_conversion_number()
    {
        echo  "<input \n                id='wgact_plugin_google_ads_phone_conversion_number' \n                name='wgact_plugin_options[google][ads][phone_conversion_number]' \n                size='40' \n                type='text' \n                value='{$this->options['google']['ads']['phone_conversion_number']}' \n                {$this->disable_if_demo()}\n                />" ;
        echo  $this->get_status_icon( $this->options['google']['ads']['phone_conversion_number'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-phone-conversion-number#/pixels/google-ads?id=phone-conversion-number' ) ;
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        esc_html_e( 'The Google Ads phone conversion number must be in the same format as on the website.', 'woocommerce-google-adwords-conversion-tracking-tag' );
    }
    
    public function wgact_setting_html_google_ads_phone_conversion_label()
    {
        echo  "<input \n                id='wgact_plugin_google_ads_phone_conversion_label' \n                name='wgact_plugin_options[google][ads][phone_conversion_label]' \n                size='40' \n                type='text' \n                value='{$this->options['google']['ads']['phone_conversion_label']}' \n                {$this->disable_if_demo()}\n                />" ;
        echo  $this->get_status_icon( $this->options['google']['ads']['phone_conversion_label'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-phone-conversion-number#/pixels/google-ads?id=phone-conversion-number' ) ;
        echo  $this->html_pro_feature() ;
        echo  '<br><br>' ;
        //        esc_html_e('The Google Ads phone conversion label must be in the same format as on the website.', 'woocommerce-google-adwords-conversion-tracking-tag');
    }
    
    public function wgact_setting_html_borlabs_support()
    {
        esc_html_e( 'Borlabs detected. Automatic support is:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        echo  $this->get_status_icon( true, true, true ) ;
        echo  $this->html_pro_feature() ;
    }
    
    public function wgact_setting_html_cookiebot_support()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[shop][cookie_consent_mgmt][cookiebot][active]'>
            <input type='checkbox' id='wgact_setting_cookiebot_active'
                   name='wgact_plugin_options[shop][cookie_consent_mgmt][cookiebot][active]'
                   value='1'
                <?php 
        checked( $this->options['shop']['cookie_consent_mgmt']['cookiebot']['active'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Enable Cookiebot settings', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></label>
        <?php 
        echo  $this->get_status_icon( $this->options['shop']['cookie_consent_mgmt']['cookiebot']['active'], $this->options['google']['consent_mode']['active'], true ) ;
        echo  $this->html_pro_feature() ;
        
        if ( $this->options['shop']['cookie_consent_mgmt']['cookiebot']['active'] && !$this->options['google']['consent_mode']['active'] ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate the Google consent mode', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_setting_html_facebook_capi_token()
    {
        ?><textarea id='wgact_setting_facebook_capi_token'
                    name='wgact_plugin_options[facebook][capi][token]'
                    cols='60'
                    rows='5'
        <?php 
        echo  $this->disable_if_demo() ;
        ?>
        ><?php 
        echo  $this->options['facebook']['capi']['token'] ;
        ?></textarea>
        <?php 
        echo  $this->get_status_icon( $this->options['facebook']['capi']['token'], $this->options['facebook']['pixel_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=facebook-capi-token#/pixels/facebook?id=facebook-conversion-api-capi' ) ;
        echo  $this->html_pro_feature() ;
        
        if ( !$this->options['facebook']['pixel_id'] ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate the Facebook pixel', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
        
        //        echo $this->get_documentation_html('/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=google-ads-conversion-id#/pixels/google-ads?id=configure-the-plugin');
        echo  '<br><br>' ;
        //        esc_html_e('The conversion ID looks similar to this:', 'woocommerce-google-adwords-conversion-tracking-tag');
        //        echo '&nbsp;<i>123456789</i>';
    }
    
    public function wgact_setting_facebook_capi_user_transparency_process_anonymous_hits()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0'
                   name='wgact_plugin_options[facebook][capi][user_transparency][process_anonymous_hits]'>
            <input type='checkbox' id='wgact_setting_facebook_capi_user_transparency_process_anonymous_hits'
                   name='wgact_plugin_options[facebook][capi][user_transparency][process_anonymous_hits]'
                   value='1'
                <?php 
        checked( $this->options['facebook']['capi']['user_transparency']['process_anonymous_hits'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Send CAPI hits for anonymous visitors who likely have blocked the Facebook pixel.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['facebook']['capi']['user_transparency']['process_anonymous_hits'], $this->options['facebook']['pixel_id'], true ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=facebook-capi-token#/pixels/facebook?id=user-transparency-settings' ) ;
        echo  $this->html_pro_feature() ;
        
        if ( $this->options['facebook']['capi']['user_transparency']['process_anonymous_hits'] && !$this->options['facebook']['pixel_id'] ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate the Facebook pixel', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_setting_facebook_capi_user_transparency_send_additional_client_identifiers()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0'
                   name='wgact_plugin_options[facebook][capi][user_transparency][send_additional_client_identifiers]'>
            <input type='checkbox' id='wgact_setting_facebook_capi_user_transparency_send_additional_client_identifiers'
                   name='wgact_plugin_options[facebook][capi][user_transparency][send_additional_client_identifiers]'
                   value='1'
                <?php 
        checked( $this->options['facebook']['capi']['user_transparency']['send_additional_client_identifiers'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Include additional visitor\'s identifiers, such as IP address, email and shop ID in the CAPI hit.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['facebook']['capi']['user_transparency']['send_additional_client_identifiers'], $this->options['facebook']['pixel_id'], true ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=facebook-capi-token#/pixels/facebook?id=user-transparency-settings' ) ;
        echo  $this->html_pro_feature() ;
        
        if ( $this->options['facebook']['capi']['user_transparency']['send_additional_client_identifiers'] && !$this->options['facebook']['pixel_id'] ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate the Facebook pixel', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_setting_html_facebook_microdata()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[facebook][microdata]'>
            <input type='checkbox' id='wgact_setting_facebook_microdata_active'
                   name='wgact_plugin_options[facebook][microdata]'
                   value='1'
                <?php 
        checked( $this->options['facebook']['microdata'] );
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Enable Facebook product microdata output', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['facebook']['microdata'], $this->options['facebook']['pixel_id'], true ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=facebook-microdata#/pixels/facebook?id=microdata-tags-for-catalogues' ) ;
        echo  $this->html_pro_feature() ;
        
        if ( $this->options['facebook']['microdata'] && !$this->options['facebook']['pixel_id'] ) {
            echo  '<p></p><span class="dashicons dashicons-info"></span>' ;
            esc_html_e( 'You need to activate the Facebook pixel', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '</p><br>' ;
        }
    
    }
    
    public function wgact_option_html_google_ads_add_cart_data()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][ads][add_cart_data]'>
            <input type='checkbox' id='wgact_plugin_option_gads_add_cart_data'
                   name='wgact_plugin_options[google][ads][add_cart_data]'
                   value='1' <?php 
        checked( $this->options['google']['ads']['add_cart_data'] );
        ?> />
            <?php 
        esc_html_e( 'Add the cart data to the conversion event', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['ads']['add_cart_data'], $this->add_to_cart_requirements_fulfilled() ) ;
        
        if ( !$this->add_to_cart_requirements_fulfilled() ) {
            ?>
            <p><span class="dashicons dashicons-info"></span>
                <?php 
            esc_html_e( 'Requires an active Google Ads Conversion ID, an active Conversion Label and an active Google Merchant Center ID (aw_merchant_id)', 'woocommerce-google-adwords-conversion-tracking-tag' );
            ?>
            </p>
            <?php 
        }
    
    }
    
    public function wgact_setting_html_order_duplication_prevention()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[shop][order_deduplication]'>
            <input type='checkbox' id='wgact_setting_order_duplication_prevention'
                   name='wgact_plugin_options[shop][order_deduplication]'
                   value='1' <?php 
        checked( $this->options['shop']['order_deduplication'] );
        ?> />
            <?php 
        $this->get_order_duplication_prevention_text();
        ?></label>
        <?php 
        echo  $this->get_status_icon( $this->options['shop']['order_deduplication'] ) ;
        ?>
        <br>
        <p>
            <span class="dashicons dashicons-info"></span>
            <?php 
        esc_html_e( 'Only disable order duplication prevention for testing. Remember to re-enable the setting once done.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </p>
        <?php 
    }
    
    public function wgact_setting_html_maximum_compatibility_mode()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[general][maximum_compatibility_mode]'>
            <input type='checkbox' id='wgact_setting_maximum_compatibility_mode'
                   name='wgact_plugin_options[general][maximum_compatibility_mode]'
                   value='1' <?php 
        checked( $this->options['general']['maximum_compatibility_mode'] );
        ?> />
            <?php 
        esc_html_e( 'Enable the maximum compatibility mode', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['general']['maximum_compatibility_mode'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=maximum-compatibility-mode#/general?id=maximum-compatibility-mode' ) ;
    }
    
    private function get_order_duplication_prevention_text()
    {
        esc_html_e( 'Basic order duplication prevention is ', 'woocommerce-google-adwords-conversion-tracking-tag' );
    }
    
    private function add_to_cart_requirements_fulfilled() : bool
    {
        
        if ( $this->options['google']['ads']['conversion_id'] && $this->options['google']['ads']['conversion_label'] && $this->options['google']['ads']['aw_merchant_id'] ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    public function wgact_option_html_google_ads_dynamic_remarketing()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[google][ads][dynamic_remarketing]'>
            <input type='checkbox' id='wgact_plugin_option_gads_dynamic_remarketing'
                   name='wgact_plugin_options[google][ads][dynamic_remarketing]'
                   value='1' <?php 
        checked( $this->options['google']['ads']['dynamic_remarketing'] );
        ?> />

            <?php 
        esc_html_e( 'Enable dynamic remarketing audience collection', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['google']['ads']['dynamic_remarketing'], $this->options['google']['ads']['conversion_id'] ) ;
        ?>
        <?php 
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=dynamic-remarketing#/dynamic-remarketing' ) ;
        ?>
        <p><?php 
        
        if ( !$this->options['google']['ads']['conversion_id'] ) {
            ?>
                <span class="dashicons dashicons-info"></span>
                <?php 
            esc_html_e( 'Requires an active Google Ads Conversion ID', 'woocommerce-google-adwords-conversion-tracking-tag' );
            echo  '<br>' ;
        }
        
        ?><span class="dashicons dashicons-info"></span>
            <?php 
        esc_html_e( 'You need to choose the correct product identifier setting in order to match the product identifiers in the product feeds.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </p>
        <?php 
    }
    
    public function wgact_option_html_variations_output()
    {
        // adding the hidden input is a hack to make WordPress save the option with the value zero,
        // instead of not saving it and remove that array key entirely
        // https://stackoverflow.com/a/1992745/4688612
        ?>
        <label>
            <input type='hidden' value='0' name='wgact_plugin_options[general][variations_output]'>
            <input type='checkbox' id='wgact_plugin_option_variations_output'
                   name='wgact_plugin_options[general][variations_output]'
                   value='1' <?php 
        checked( $this->options['general']['variations_output'] );
        ?> />

            <?php 
        esc_html_e( 'Enable variations output', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <?php 
        echo  $this->get_status_icon( $this->options['general']['variations_output'], $this->options['google']['ads']['dynamic_remarketing'], true ) ;
        ?>
        <?php 
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=dynamic-remarketing#/dynamic-remarketing' ) ;
        ?>
        <p><span class="dashicons dashicons-info"></span>
            <?php 
        esc_html_e( 'In order for this to work you need to upload your product feed including product variations and the item_group_id. Disable it, if you choose only to upload the parent product for variable products.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </p>
        <?php 
    }
    
    public function wgact_plugin_option_google_business_vertical()
    {
        ?>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_0'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='0'
                <?php 
        echo  checked( 0, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Retail', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_1'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='1'
                <?php 
        echo  checked( 1, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Education', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_3'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='3'
                <?php 
        echo  checked( 3, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Hotels and rentals', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_4'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='4'
                <?php 
        echo  checked( 4, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Jobs', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_5'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='5'
                <?php 
        echo  checked( 5, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Local deals', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_6'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='6'
                <?php 
        echo  checked( 6, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Real estate', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_google_business_vertical_8'
                   name='wgact_plugin_options[google][ads][google_business_vertical]'
                   value='8'
                <?php 
        echo  checked( 8, $this->options['google']['ads']['google_business_vertical'], false ) ;
        ?>
                <?php 
        echo  $this->disable_if_demo() ;
        ?>
            />
            <?php 
        esc_html_e( 'Custom', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <?php 
    }
    
    public function wgact_plugin_setting_aw_merchant_id()
    {
        echo  "<input type='text' id='wgact_plugin_aw_merchant_id' name='wgact_plugin_options[google][ads][aw_merchant_id]' size='40' value='{$this->options['google']['ads']['aw_merchant_id']}' />" ;
        echo  $this->get_status_icon( $this->options['google']['ads']['aw_merchant_id'] ) ;
        echo  $this->get_documentation_html( '/wgact/?utm_source=woocommerce-plugin&utm_medium=documentation-link&utm_campaign=woopt-pixel-manager-docs&utm_content=conversion-cart-data#/pixels/google-ads?id=conversion-cart-data' ) ;
        echo  '<br>' ;
        esc_html_e( 'ID of your Google Merchant Center account. It looks like this: 12345678', 'woocommerce-google-adwords-conversion-tracking-tag' );
    }
    
    public function wgact_plugin_setting_aw_feed_country()
    {
        ?><b><?php 
        echo  $this->get_visitor_country() ;
        ?></b><?php 
        //		echo '<br>' . 'get_external_ip_address: ' . WC_Geolocation::get_external_ip_address();
        //		echo '<br>' . 'get_ip_address: ' . WC_Geolocation::get_ip_address();
        //		echo '<p>' . 'geolocate_ip: ' . '<br>';
        //		echo print_r(WC_Geolocation::geolocate_ip());
        //		echo '<p>' . 'WC_Geolocation::geolocate_ip(WC_Geolocation::get_external_ip_address()): ' . '<br>';
        //		echo print_r(WC_Geolocation::geolocate_ip(WC_Geolocation::get_external_ip_address()));
        ?>
        <div style="margin-top:10px">
            <?php 
        esc_html_e( 'Currently the plugin automatically detects the location of the visitor for this setting. In most, if not all, cases this will work fine. Please let us know if you have a use case where you need another output:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
            <a href="mailto:support@woopt.com">support@woopt.com</a>
        </div>
        <?php 
    }
    
    // dupe in pixel
    public function get_visitor_country()
    {
        
        if ( $this->isLocalhost() ) {
            //	        error_log('check external ip');
            $this->ip = WC_Geolocation::get_external_ip_address();
        } else {
            //		    error_log('check regular ip');
            $this->ip = WC_Geolocation::get_ip_address();
        }
        
        $location = WC_Geolocation::geolocate_ip( $this->ip );
        //	    error_log ('ip: ' . $this->>$ip);
        //	    error_log ('country: ' . $location['country']);
        return $location['country'];
    }
    
    // dupe in pixel
    public function isLocalhost() : bool
    {
        return in_array( $_SERVER['REMOTE_ADDR'], [ '127.0.0.1', '::1' ] );
    }
    
    public function wgact_plugin_setting_aw_feed_language()
    {
        ?><b><?php 
        echo  $this->get_gmc_language() ;
        ?></b>
        <div style="margin-top:10px">
            <?php 
        esc_html_e( 'The plugin will use the WordPress default language for this setting. If the shop uses translations, in theory we could also use the visitors locale. But, if that language is  not set up in the Google Merchant Center we might run into issues. If you need more options here let us know:', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
            <a href=\"mailto:support@woopt.com\">support@woopt.com</a>
        </div>
        <?php 
    }
    
    // dupe in pixel
    public function get_gmc_language() : string
    {
        return strtoupper( substr( get_locale(), 0, 2 ) );
    }
    
    public function wgact_plugin_option_product_identifier()
    {
        ?>
        <label>
            <input type='radio' id='wgact_plugin_option_product_identifier_0'
                   name='wgact_plugin_options[google][ads][product_identifier]'
                   value='0' <?php 
        echo  checked( 0, $this->options['google']['ads']['product_identifier'], false ) ;
        ?>/><?php 
        esc_html_e( 'post ID (default)', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_option_product_identifier_1'
                   name='wgact_plugin_options[google][ads][product_identifier]'
                   value='1' <?php 
        echo  checked( 1, $this->options['google']['ads']['product_identifier'], false ) ;
        ?>/><?php 
        esc_html_e( 'post ID with woocommerce_gpf_ prefix *', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        </label>
        <br>
        <label>
            <input type='radio' id='wgact_plugin_option_product_identifier_2'
                   name='wgact_plugin_options[google][ads][product_identifier]'
                   value='2' <?php 
        echo  checked( 2, $this->options['google']['ads']['product_identifier'], false ) ;
        ?>/><?php 
        esc_html_e( 'SKU', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?></label>

        <br><br>

        <?php 
        esc_html_e( 'Choose a product identifier.', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        <br><br>
        <?php 
        esc_html_e( '* This is for users of the WooCommerce Google Product Feed Plugin', 'woocommerce-google-adwords-conversion-tracking-tag' );
        ?>
        <a href="https://woocommerce.com/products/google-product-feed/" target="_blank">WooCommerce Google Product Feed
            Plugin</a>


        <?php 
    }
    
    private function html_beta() : string
    {
        return '<div class="status-icon beta">' . esc_html__( 'beta', 'woocommerce-google-adwords-conversion-tracking-tag' ) . '</div>';
    }
    
    private function html_active() : string
    {
        return '<div class="status-icon active">' . esc_html__( 'active', 'woocommerce-google-adwords-conversion-tracking-tag' ) . '</div>';
    }
    
    private function html_inactive() : string
    {
        return '<div class="status-icon inactive">' . esc_html__( 'inactive', 'woocommerce-google-adwords-conversion-tracking-tag' ) . '</div>';
    }
    
    private function html_partially_active() : string
    {
        return '<div class="status-icon partially-active">' . esc_html__( 'partially active', 'woocommerce-google-adwords-conversion-tracking-tag' ) . '</div>';
    }
    
    private function html_pro_feature() : string
    {
        
        if ( !wga_fs()->is__premium_only() && $this->options['general']['pro_version_demo'] ) {
            //            if (1===1) {
            return '<div class="pro-feature">' . esc_html__( 'Pro Feature', 'woocommerce-google-adwords-conversion-tracking-tag' ) . '</div>';
        } else {
            return '';
        }
    
    }
    
    private function get_status_icon( $status, $requirements = true, $inactive_silent = false ) : string
    {
        
        if ( $status && $requirements ) {
            return $this->html_active();
        } elseif ( $status && !$requirements ) {
            return $this->html_partially_active();
        } elseif ( $inactive_silent == false ) {
            return $this->html_inactive();
        }
        
        return '';
    }
    
    private function disable_if_demo() : string
    {
        
        if ( !wga_fs()->is__premium_only() && $this->options['general']['pro_version_demo'] ) {
            return 'disabled';
        } else {
            return '';
        }
    
    }
    
    // validate the options
    public function wgact_options_validate( $input ) : array
    {
        //        error_log(print_r($input, true));
        // validate Google Analytics Universal property ID
        if ( isset( $input['google']['analytics']['universal']['property_id'] ) ) {
            
            if ( !$this->is_google_analytics_universal_property_id( $input['google']['analytics']['universal']['property_id'] ) ) {
                $input['google']['analytics']['universal']['property_id'] = ( isset( $this->options['google']['analytics']['universal']['property_id'] ) ? $this->options['google']['analytics']['universal']['property_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-google-analytics-universal-property-id', esc_html__( 'You have entered an invalid Google Analytics Universal property ID.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Google Analytics 4 measurement ID
        if ( isset( $input['google']['analytics']['ga4']['measurement_id'] ) ) {
            
            if ( !$this->is_google_analytics_4_measurement_id( $input['google']['analytics']['ga4']['measurement_id'] ) ) {
                $input['google']['analytics']['ga4']['measurement_id'] = ( isset( $this->options['google']['analytics']['ga4']['measurement_id'] ) ? $this->options['google']['analytics']['ga4']['measurement_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-google-analytics-4-measurement-id', esc_html__( 'You have entered an invalid Google Analytics 4 measurement ID.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Google Analytics 4 API key
        if ( isset( $input['google']['analytics']['ga4']['api_secret'] ) ) {
            
            if ( !$this->is_google_analytics_4_api_secret( $input['google']['analytics']['ga4']['api_secret'] ) ) {
                $input['google']['analytics']['ga4']['api_secret'] = ( isset( $this->options['google']['analytics']['ga4']['api_secret'] ) ? $this->options['google']['analytics']['ga4']['api_secret'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-google-analytics-4-measurement-id', esc_html__( 'You have entered an invalid Google Analytics 4 API key.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate ['google]['ads']['conversion_id']
        if ( isset( $input['google']['ads']['conversion_id'] ) ) {
            
            if ( !$this->is_gads_conversion_id( $input['google']['ads']['conversion_id'] ) ) {
                $input['google']['ads']['conversion_id'] = ( isset( $this->options['google']['ads']['conversion_id'] ) ? $this->options['google']['ads']['conversion_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-conversion-id', esc_html__( 'You have entered an invalid conversion ID. It only contains 8 to 10 digits.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate ['google]['ads']['conversion_label']
        if ( isset( $input['google']['ads']['conversion_label'] ) ) {
            
            if ( !$this->is_gads_conversion_label( $input['google']['ads']['conversion_label'] ) ) {
                $input['google']['ads']['conversion_label'] = ( isset( $this->options['google']['ads']['conversion_label'] ) ? $this->options['google']['ads']['conversion_label'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-conversion-label', esc_html__( 'You have entered an invalid conversion label.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate ['google]['ads']['phone_conversion_label']
        if ( isset( $input['google']['ads']['phone_conversion_label'] ) ) {
            
            if ( !$this->is_gads_conversion_label( $input['google']['ads']['phone_conversion_label'] ) ) {
                $input['google']['ads']['phone_conversion_label'] = ( isset( $this->options['google']['ads']['phone_conversion_label'] ) ? $this->options['google']['ads']['phone_conversion_label'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-conversion-label', esc_html__( 'You have entered an invalid conversion label.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate ['google]['ads']['aw_merchant_id']
        if ( isset( $input['google']['ads']['aw_merchant_id'] ) ) {
            
            if ( !$this->is_gads_aw_merchant_id( $input['google']['ads']['aw_merchant_id'] ) ) {
                $input['google']['ads']['aw_merchant_id'] = ( isset( $this->options['google']['ads']['aw_merchant_id'] ) ? $this->options['google']['ads']['aw_merchant_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-aw-merchant-id', esc_html__( 'You have entered an invalid merchant ID. It only contains 7 to 10 digits.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Google Optimize container ID
        if ( isset( $input['google']['optimize']['container_id'] ) ) {
            
            if ( !$this->is_google_optimize_measurement_id( $input['google']['optimize']['container_id'] ) ) {
                $input['google']['optimize']['container_id'] = ( isset( $this->options['google']['optimize']['container_id'] ) ? $this->options['google']['optimize']['container_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-google-optimize-container-id', esc_html__( 'You have entered an invalid Google Optimize container ID.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate ['facebook']['pixel_id']
        if ( isset( $input['facebook']['pixel_id'] ) ) {
            
            if ( !$this->is_facebook_pixel_id( $input['facebook']['pixel_id'] ) ) {
                $input['facebook']['pixel_id'] = ( isset( $this->options['facebook']['pixel_id'] ) ? $this->options['facebook']['pixel_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-facebook-pixel-id', esc_html__( 'You have entered an invalid Facebook pixel ID. It only contains 14 to 16 digits.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate ['facebook']['capi']['token']
        if ( isset( $input['facebook']['capi']['token'] ) ) {
            
            if ( !$this->is_facebook_capi_token( $input['facebook']['capi']['token'] ) ) {
                $input['facebook']['capi']['token'] = ( isset( $this->options['facebook']['capi']['token'] ) ? $this->options['facebook']['capi']['token'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-facebook-pixel-id', esc_html__( 'You have entered an invalid Facebook CAPI token.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Bing Ads UET tag ID
        if ( isset( $input['bing']['uet_tag_id'] ) ) {
            
            if ( !$this->is_bing_uet_tag_id( $input['bing']['uet_tag_id'] ) ) {
                $input['bing']['uet_tag_id'] = ( isset( $this->options['bing']['uet_tag_id'] ) ? $this->options['bing']['uet_tag_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-bing-ads-uet-tag-id', esc_html__( 'You have entered an invalid Bing Ads UET tag ID. It only contains 7 to 9 digits.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Twitter pixel ID
        if ( isset( $input['twitter']['pixel_id'] ) ) {
            
            if ( !$this->is_twitter_pixel_id( $input['twitter']['pixel_id'] ) ) {
                $input['twitter']['pixel_id'] = ( isset( $this->options['twitter']['pixel_id'] ) ? $this->options['twitter']['pixel_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-twitter-pixel-id', esc_html__( 'You have entered an invalid Twitter pixel ID. It only contains 5 to 7 lowercase letters and numbers.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Pinterest pixel ID
        if ( isset( $input['pinterest']['pixel_id'] ) ) {
            
            if ( !$this->is_pinterest_pixel_id( $input['pinterest']['pixel_id'] ) ) {
                $input['pinterest']['pixel_id'] = ( isset( $this->options['pinterest']['pixel_id'] ) ? $this->options['pinterest']['pixel_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-pinterest-pixel-id', esc_html__( 'You have entered an invalid Pinterest pixel ID. It only contains 13 digits.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Snapchat pixel ID
        if ( isset( $input['snapchat']['pixel_id'] ) ) {
            
            if ( !$this->is_snapchat_pixel_id( $input['snapchat']['pixel_id'] ) ) {
                $input['snapchat']['pixel_id'] = ( isset( $this->options['snapchat']['pixel_id'] ) ? $this->options['snapchat']['pixel_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-snapchat-pixel-id', esc_html__( 'You have entered an invalid Snapchat pixel ID.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate TikTok pixel ID
        if ( isset( $input['tiktok']['pixel_id'] ) ) {
            
            if ( !$this->is_tiktok_pixel_id( $input['tiktok']['pixel_id'] ) ) {
                $input['tiktok']['pixel_id'] = ( isset( $this->options['tiktok']['pixel_id'] ) ? $this->options['tiktok']['pixel_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-tiktok-pixel-id', esc_html__( 'You have entered an invalid TikTok pixel ID.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // validate Hotjar site ID
        if ( isset( $input['hotjar']['site_id'] ) ) {
            
            if ( !$this->is_hotjar_site_id( $input['hotjar']['site_id'] ) ) {
                $input['hotjar']['site_id'] = ( isset( $this->options['hotjar']['site_id'] ) ? $this->options['hotjar']['site_id'] : '' );
                add_settings_error( 'wgact_plugin_options', 'invalid-hotjar-site-id', esc_html__( 'You have entered an invalid Hotjar site ID. It only contains 7 to 9 digits.', 'woocommerce-google-adwords-conversion-tracking-tag' ) );
            }
        
        }
        // merging with the existing options
        // and overwriting old values
        // since disabling a checkbox doesn't send a value,
        // we need to set one to overwrite the old value
        $input = array_replace_recursive( $this->non_form_keys( $input ), $input );
        //        error_log(print_r($input, true));
        //        $input = $this->merge_options($this->options, $input);
        //		error_log('input merged');
        //		error_log(print_r($input, true));
        return $input;
    }
    
    // Recursively go through the array and merge (overwrite old values with new ones
    // if a value is missing in the input array, set it to value zero in the options array
    // Omit key like 'db_version' since they would be overwritten with zero.
    protected function merge_options( $array_existing, $array_input ) : array
    {
        $array_output = [];
        foreach ( $array_existing as $key => $value ) {
            
            if ( array_key_exists( $key, $array_input ) ) {
                
                if ( is_array( $value ) ) {
                    $array_output[$key] = $this->merge_options( $value, $array_input[$key] );
                } else {
                    $array_output[$key] = $array_input[$key];
                }
            
            } else {
                
                if ( is_array( $value ) ) {
                    $array_output[$key] = $this->set_array_value_to_zero( $value );
                } else {
                    $array_output[$key] = 0;
                }
            
            }
        
        }
        return $array_output;
    }
    
    protected function non_form_keys( $input ) : array
    {
        // place here what could be overwritten when a form field is missing
        // and what should not be re-set to the default value
        // but should be preserved
        $non_form_keys = [
            'db_version' => $this->options['db_version'],
        ];
        // in case the form field input is missing
        //        if (!array_key_exists('google_business_vertical', $input['google']['ads'])) {
        //            $non_form_keys['google']['ads']['google_business_vertical'] = $this->options['google']['ads']['google_business_vertical'];
        //        }
        return $non_form_keys;
    }
    
    function set_array_value_to_zero( $array )
    {
        array_walk_recursive( $array, function ( &$leafnode ) {
            $leafnode = 0;
        } );
        return $array;
    }
    
    public function is_gads_conversion_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^\\d{8,11}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_gads_conversion_label( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^[-a-zA-Z_0-9]{17,20}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_gads_aw_merchant_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^\\d{7,10}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    public function is_google_optimize_measurement_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^(GTM|OPT)-[A-Z0-9]{6,8}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    public function is_google_analytics_universal_property_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^UA-\\d{6,10}-\\d{1,2}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    public function is_google_analytics_4_measurement_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^G-[A-Z0-9]{10,12}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    public function is_google_analytics_4_api_secret( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^[a-zA-Z\\d_-]{18,26}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_facebook_pixel_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^\\d{14,16}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_facebook_capi_token( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^[a-zA-Z\\d_-]{150,250}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_bing_uet_tag_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^\\d{7,9}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_twitter_pixel_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^[a-z0-9]{5,7}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_pinterest_pixel_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^\\d{13}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_snapchat_pixel_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^[a-z0-9\\-]*$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_tiktok_pixel_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^[A-Z0-9]{20,20}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function is_hotjar_site_id( $string ) : bool
    {
        if ( empty($string) ) {
            return true;
        }
        $re = '/^\\d{7,9}$/m';
        return $this->validate_with_regex( $re, $string );
    }
    
    protected function validate_with_regex( string $re, $string ) : bool
    {
        preg_match_all(
            $re,
            $string,
            $matches,
            PREG_SET_ORDER,
            0
        );
        
        if ( isset( $matches[0] ) ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    private function get_consent_mode_regions() : array
    {
        return [
            'AF'    => 'Afghanistan',
            'AX'    => 'Åland Islands',
            'AL'    => 'Albania',
            'DZ'    => 'Algeria',
            'AS'    => 'American Samoa',
            'AD'    => 'Andorra',
            'AO'    => 'Angola',
            'AI'    => 'Anguilla',
            'AQ'    => 'Antarctica',
            'AG'    => 'Antigua and Barbuda',
            'AR'    => 'Argentina',
            'AM'    => 'Armenia',
            'AW'    => 'Aruba',
            'AU'    => 'Australia',
            'AT'    => 'Austria',
            'AZ'    => 'Azerbaijan',
            'BS'    => 'Bahamas',
            'BH'    => 'Bahrain',
            'BD'    => 'Bangladesh',
            'BB'    => 'Barbados',
            'BY'    => 'Belarus',
            'BE'    => 'Belgium',
            'BZ'    => 'Belize',
            'BJ'    => 'Benin',
            'BM'    => 'Bermuda',
            'BT'    => 'Bhutan',
            'BO'    => 'Bolivia, Plurinational State of',
            'BQ'    => 'Bonaire, Sint Eustatius and Saba',
            'BA'    => 'Bosnia and Herzegovina',
            'BW'    => 'Botswana',
            'BV'    => 'Bouvet Island',
            'BR'    => 'Brazil',
            'IO'    => 'British Indian Ocean Territory',
            'BN'    => 'Brunei Darussalam',
            'BG'    => 'Bulgaria',
            'BF'    => 'Burkina Faso',
            'BI'    => 'Burundi',
            'KH'    => 'Cambodia',
            'CM'    => 'Cameroon',
            'CA'    => 'Canada',
            'CV'    => 'Cape Verde',
            'KY'    => 'Cayman Islands',
            'CF'    => 'Central African Republic',
            'TD'    => 'Chad',
            'CL'    => 'Chile',
            'CN'    => 'China',
            'CX'    => 'Christmas Island',
            'CC'    => 'Cocos (Keeling) Islands',
            'CO'    => 'Colombia',
            'KM'    => 'Comoros',
            'CG'    => 'Congo',
            'CD'    => 'Congo, the Democratic Republic of the',
            'CK'    => 'Cook Islands',
            'CR'    => 'Costa Rica',
            'CI'    => 'Côte d\'Ivoire',
            'HR'    => 'Croatia',
            'CU'    => 'Cuba',
            'CW'    => 'Curaçao',
            'CY'    => 'Cyprus',
            'CZ'    => 'Czech Republic',
            'DK'    => 'Denmark',
            'DJ'    => 'Djibouti',
            'DM'    => 'Dominica',
            'DO'    => 'Dominican Republic',
            'EC'    => 'Ecuador',
            'EG'    => 'Egypt',
            'SV'    => 'El Salvador',
            'GQ'    => 'Equatorial Guinea',
            'ER'    => 'Eritrea',
            'EE'    => 'Estonia',
            'ET'    => 'Ethiopia',
            'FK'    => 'Falkland Islands (Malvinas)',
            'FO'    => 'Faroe Islands',
            'FJ'    => 'Fiji',
            'FI'    => 'Finland',
            'FR'    => 'France',
            'GF'    => 'French Guiana',
            'PF'    => 'French Polynesia',
            'TF'    => 'French Southern Territories',
            'GA'    => 'Gabon',
            'GM'    => 'Gambia',
            'GE'    => 'Georgia',
            'DE'    => 'Germany',
            'GH'    => 'Ghana',
            'GI'    => 'Gibraltar',
            'GR'    => 'Greece',
            'GL'    => 'Greenland',
            'GD'    => 'Grenada',
            'GP'    => 'Guadeloupe',
            'GU'    => 'Guam',
            'GT'    => 'Guatemala',
            'GG'    => 'Guernsey',
            'GN'    => 'Guinea',
            'GW'    => 'Guinea-Bissau',
            'GY'    => 'Guyana',
            'HT'    => 'Haiti',
            'HM'    => 'Heard Island and McDonald Islands',
            'VA'    => 'Holy See (Vatican City State)',
            'HN'    => 'Honduras',
            'HK'    => 'Hong Kong',
            'HU'    => 'Hungary',
            'IS'    => 'Iceland',
            'IN'    => 'India',
            'ID'    => 'Indonesia',
            'IR'    => 'Iran, Islamic Republic of',
            'IQ'    => 'Iraq',
            'IE'    => 'Ireland',
            'IM'    => 'Isle of Man',
            'IL'    => 'Israel',
            'IT'    => 'Italy',
            'JM'    => 'Jamaica',
            'JP'    => 'Japan',
            'JE'    => 'Jersey',
            'JO'    => 'Jordan',
            'KZ'    => 'Kazakhstan',
            'KE'    => 'Kenya',
            'KI'    => 'Kiribati',
            'KP'    => 'Korea, Democratic People\'s Republic of',
            'KR'    => 'Korea, Republic of',
            'KW'    => 'Kuwait',
            'KG'    => 'Kyrgyzstan',
            'LA'    => 'Lao People\'s Democratic Republic',
            'LV'    => 'Latvia',
            'LB'    => 'Lebanon',
            'LS'    => 'Lesotho',
            'LR'    => 'Liberia',
            'LY'    => 'Libya',
            'LI'    => 'Liechtenstein',
            'LT'    => 'Lithuania',
            'LU'    => 'Luxembourg',
            'MO'    => 'Macao',
            'MK'    => 'North Macedonia',
            'MG'    => 'Madagascar',
            'MW'    => 'Malawi',
            'MY'    => 'Malaysia',
            'MV'    => 'Maldives',
            'ML'    => 'Mali',
            'MT'    => 'Malta',
            'MH'    => 'Marshall Islands',
            'MQ'    => 'Martinique',
            'MR'    => 'Mauritania',
            'MU'    => 'Mauritius',
            'YT'    => 'Mayotte',
            'MX'    => 'Mexico',
            'FM'    => 'Micronesia, Federated States of',
            'MD'    => 'Moldova, Republic of',
            'MC'    => 'Monaco',
            'MN'    => 'Mongolia',
            'ME'    => 'Montenegro',
            'MS'    => 'Montserrat',
            'MA'    => 'Morocco',
            'MZ'    => 'Mozambique',
            'MM'    => 'Myanmar',
            'NA'    => 'Namibia',
            'NR'    => 'Nauru',
            'NP'    => 'Nepal',
            'NL'    => 'Netherlands',
            'NC'    => 'New Caledonia',
            'NZ'    => 'New Zealand',
            'NI'    => 'Nicaragua',
            'NE'    => 'Niger',
            'NG'    => 'Nigeria',
            'NU'    => 'Niue',
            'NF'    => 'Norfolk Island',
            'MP'    => 'Northern Mariana Islands',
            'NO'    => 'Norway',
            'OM'    => 'Oman',
            'PK'    => 'Pakistan',
            'PW'    => 'Palau',
            'PS'    => 'Palestine, State of',
            'PA'    => 'Panama',
            'PG'    => 'Papua New Guinea',
            'PY'    => 'Paraguay',
            'PE'    => 'Peru',
            'PH'    => 'Philippines',
            'PN'    => 'Pitcairn',
            'PL'    => 'Poland',
            'PT'    => 'Portugal',
            'PR'    => 'Puerto Rico',
            'QA'    => 'Qatar',
            'RE'    => 'Réunion',
            'RO'    => 'Romania',
            'RU'    => 'Russian Federation',
            'RW'    => 'Rwanda',
            'BL'    => 'Saint Barthélemy',
            'SH'    => 'Saint Helena, Ascension and Tristan da Cunha',
            'KN'    => 'Saint Kitts and Nevis',
            'LC'    => 'Saint Lucia',
            'MF'    => 'Saint Martin (French part)',
            'PM'    => 'Saint Pierre and Miquelon',
            'VC'    => 'Saint Vincent and the Grenadines',
            'WS'    => 'Samoa',
            'SM'    => 'San Marino',
            'ST'    => 'Sao Tome and Principe',
            'SA'    => 'Saudi Arabia',
            'SN'    => 'Senegal',
            'RS'    => 'Serbia',
            'SC'    => 'Seychelles',
            'SL'    => 'Sierra Leone',
            'SG'    => 'Singapore',
            'SX'    => 'Sint Maarten (Dutch part)',
            'SK'    => 'Slovakia',
            'SI'    => 'Slovenia',
            'SB'    => 'Solomon Islands',
            'SO'    => 'Somalia',
            'ZA'    => 'South Africa',
            'GS'    => 'South Georgia and the South Sandwich Islands',
            'SS'    => 'South Sudan',
            'ES'    => 'Spain',
            'LK'    => 'Sri Lanka',
            'SD'    => 'Sudan',
            'SR'    => 'Suriname',
            'SJ'    => 'Svalbard and Jan Mayen',
            'SZ'    => 'Swaziland',
            'SE'    => 'Sweden',
            'CH'    => 'Switzerland',
            'SY'    => 'Syrian Arab Republic',
            'TW'    => 'Taiwan, Province of China',
            'TJ'    => 'Tajikistan',
            'TZ'    => 'Tanzania, United Republic of',
            'TH'    => 'Thailand',
            'TL'    => 'Timor-Leste',
            'TG'    => 'Togo',
            'TK'    => 'Tokelau',
            'TO'    => 'Tonga',
            'TT'    => 'Trinidad and Tobago',
            'TN'    => 'Tunisia',
            'TR'    => 'Turkey',
            'TM'    => 'Turkmenistan',
            'TC'    => 'Turks and Caicos Islands',
            'TV'    => 'Tuvalu',
            'UG'    => 'Uganda',
            'UA'    => 'Ukraine',
            'AE'    => 'United Arab Emirates',
            'GB'    => 'United Kingdom',
            'US'    => 'United States',
            'US-CA' => 'United States - California',
            'UM'    => 'United States Minor Outlying Islands',
            'UY'    => 'Uruguay',
            'UZ'    => 'Uzbekistan',
            'VU'    => 'Vanuatu',
            'VE'    => 'Venezuela, Bolivarian Republic of',
            'VN'    => 'Viet Nam',
            'VG'    => 'Virgin Islands, British',
            'VI'    => 'Virgin Islands, U.S.',
            'WF'    => 'Wallis and Futuna',
            'EH'    => 'Western Sahara',
            'YE'    => 'Yemen',
            'ZM'    => 'Zambia',
            'ZW'    => 'Zimbabwe',
        ];
    }
    
    private function pro_version_demo_active() : bool
    {
        
        if ( $this->options['general']['pro_version_demo'] ) {
            return true;
        } else {
            return false;
        }
    
    }

}
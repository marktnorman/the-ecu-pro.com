<?php

namespace WGACT\Classes\Pixels\Google;

use  libphonenumber\PhoneNumberFormat ;
use  libphonenumber\PhoneNumberUtil ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class Google_Ads extends Google
{
    use  Trait_Google ;
    protected  $google_business_vertical ;
    public function __construct( $options )
    {
        parent::__construct( $options );
        add_action( 'wp_enqueue_scripts', [ $this, 'wooptpm_google_ads_front_end_scripts' ] );
        $this->pixel_name = 'google_ads';
    }
    
    public function wooptpm_google_ads_front_end_scripts()
    {
        //        wp_enqueue_script('wooptpm-google-ads', plugin_dir_url(__DIR__) . '../../js/public/google-ads.js', ['jquery', 'wooptpm'], WGACT_CURRENT_VERSION, true);
        wp_enqueue_script(
            'wooptpm-google-ads',
            WOOPTPM_PLUGIN_DIR_PATH . 'js/public/google-ads.js',
            [ 'jquery', 'wooptpm' ],
            WGACT_CURRENT_VERSION,
            true
        );
    }
    
    public function inject_product_list( $list_name )
    {
        // handled on front-end
    }
    
    public function inject_product( $product, $product_attributes )
    {
        // handled on front-end
    }
    
    public function inject_cart( $cart, $cart_total )
    {
        // handled on front-end
    }
    
    public function inject_order_received_page_dedupe( $order, $order_total, $is_new_customer )
    {
        $order_currency = $this->get_order_currency( $order );
        // if cart data beta is off and conversion id has been set
        
        if ( $this->add_cart_data == false && $this->options_obj->google->ads->conversion_id ) {
            // no deduper needed here
            // Google does this server side
            
            if ( $this->options_obj->google->ads->conversion_label ) {
                ?>

                gtag('event', 'conversion', {
                'send_to'       : <?php 
                echo  json_encode( $this->get_google_ads_conversion_ids( true ) ) ;
                ?>,
                'value'         : <?php 
                echo  $order_total ;
                ?>,
                'currency'      : '<?php 
                echo  $order_currency ;
                ?>',
                'transaction_id': '<?php 
                echo  $order->get_order_number() ;
                ?>',
                });
            <?php 
            }
            
            ?>

            <?php 
            if ( $this->is_dynamic_remarketing_active() ) {
                $this->get_dyn_remarketing_purchase_script( $order, $order_total );
            }
            ?>

            <?php 
        }
        
        if ( $this->add_cart_data == true && $this->conversion_id && $this->conversion_label ) {
            if ( $this->is_dynamic_remarketing_active() ) {
                $this->get_dyn_remarketing_purchase_script( $order, $order_total );
            }
        }
    }
    
    public function inject_order_received_page_no_dedupe( $order, $order_total, $is_new_customer )
    {
        $order_currency = $this->get_order_currency( $order );
        
        if ( $this->add_cart_data == true && $this->conversion_id && $this->conversion_label ) {
            ?>

            gtag('event', 'purchase', <?php 
            echo  json_encode( $this->get_google_ads_formatted_purchase_json(
                $order,
                $order_total,
                $order_currency,
                $is_new_customer
            ) ) ;
            ?>);

            <?php 
        }
    
    }
    
    private function are_billing_and_shipping_addresses_equal( $order ) : bool
    {
        
        if ( $order->get_billing_address_1() === $order->get_shipping_address_1() && $order->get_billing_city() === $order->get_shipping_city() && $order->get_billing_state() === $order->get_shipping_state() && $order->get_billing_postcode() === $order->get_shipping_postcode() && $order->get_billing_country() === $order->get_shipping_country() ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    private function get_e164_formatted_phone_number( $number, $country ) : string
    {
        try {
            $phone_util = PhoneNumberUtil::getInstance();
            $number_parsed = $phone_util->parse( $number, $country );
            return $phone_util->format( $number_parsed, PhoneNumberFormat::E164 );
        } catch ( \libphonenumber\NumberParseException $e ) {
            error_log( $e );
            return $number;
        }
    }
    
    // https://support.google.com/google-ads/answer/9028614
    private function get_google_ads_formatted_purchase_json(
        $order,
        $order_total,
        $order_currency,
        $is_new_customer
    ) : array
    {
        return [
            'send_to'          => $this->get_google_ads_conversion_ids( true ),
            'transaction_id'   => (string) $order->get_order_number(),
            'value'            => (double) $order_total,
            'currency'         => (string) $order_currency,
            'discount'         => (double) $order->get_total_discount(),
            'aw_merchant_id'   => (int) $this->aw_merchant_id,
            'aw_feed_country'  => (string) $this->get_visitor_country(),
            'aw_feed_language' => $this->get_gmc_language(),
            'new_customer'     => (string) $is_new_customer,
            'items'            => $this->get_order_items_for_google_ads_purchase_script( $order, false ),
        ];
    }
    
    protected function get_dyn_remarketing_purchase_script( $order, $order_total )
    {
        echo  "\n            wooptpmExists().then(function(){\n                if (!wooptpm.isOrderIdStored('" . $order->get_id() . "')) {\n                    gtag('event', 'purchase', {\n                        'send_to': " . json_encode( $this->get_google_ads_conversion_ids() ) . ",\n                        'value'  : " . $order_total . ",\n                        'items'  : " . json_encode( $this->get_order_items_for_google_ads_purchase_script( $order, true ) ) . "\n                    });\n                }\n            });\n        " ;
    }
    
    private function get_order_items_for_google_ads_purchase_script( $order, $dyn_r = false ) : array
    {
        $order_items = (array) $this->wooptpm_get_order_items( $order );
        $order_items_array = [];
        foreach ( $order_items as $order_item ) {
            $product_id = $this->get_variation_or_product_id( $order_item->get_data(), $this->options_obj->general->variations_output );
            $product = wc_get_product( $product_id );
            $order_items_array = [];
            
            if ( !is_object( $product ) ) {
                $this->log_problematic_product_id( $product_id );
                continue;
            }
            
            $dyn_r_ids = $this->get_dyn_r_ids( $product );
            $item_details_array['id'] = (string) $dyn_r_ids[$this->get_dyn_r_id_type()];
            $item_details_array['quantity'] = (int) $order_item['quantity'];
            $item_details_array['price'] = (double) $product->get_price();
            if ( $dyn_r === true ) {
                $item_details_array['google_business_vertical'] = (string) $this->google_business_vertical;
            }
            array_push( $order_items_array, $item_details_array );
        }
        return $order_items_array;
    }

}
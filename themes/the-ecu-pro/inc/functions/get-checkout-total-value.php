<?php
add_action('wp_ajax_woocommerce_get_cart_total', 'woocommerce_get_cart_total');
add_action('wp_ajax_nopriv_woocommerce_get_cart_total', 'woocommerce_get_cart_total');

/**
 *
 */
function woocommerce_get_cart_total()
{
    $data = '';
    if (isset($_POST['form_data'])) {
        $data = json_decode($_POST['form_data']);
        WC()->session->set('chosen_shipping_methods', array( $data->selected_shipping ) );

        global $woocommerce;
        echo $woocommerce->cart->total;
        die();
    }
}

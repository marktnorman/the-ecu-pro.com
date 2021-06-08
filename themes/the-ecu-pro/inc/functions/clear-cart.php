<?php

add_action('wp_ajax_clear_cart', 'clear_cart');
add_action('wp_ajax_nopriv_clear_cart', 'clear_cart');

/**
 * clear_cart function
 */
function clear_cart()
{

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    echo 'Cart cleared!';
    die();
}
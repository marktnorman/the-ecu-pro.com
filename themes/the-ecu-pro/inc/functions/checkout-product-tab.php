<?php

add_action('woocommerce_multistep_checkout_before', 'initial_product_tab');

function initial_product_tab($checkout)
{
    wc_get_template( 'checkout/initial-product-tab.php', array( 'checkout' => $checkout ) );
}
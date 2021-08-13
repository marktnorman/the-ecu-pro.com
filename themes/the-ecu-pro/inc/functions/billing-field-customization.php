<?php

add_filter(  'woocommerce_billing_fields', 'custom_billing_fields', 9999, 1 );
function custom_billing_fields( $fields ) {
    $fields['billing_email']['priority'] = 1;
    $fields['billing_email']['class'] = array('form-row-first');
    $fields['billing_email']['placeholder'] = 'Email Address';

    $fields['billing_phone']['priority'] = 2;
    $fields['billing_phone']['class'] = array('form-row-last');
    $fields['billing_phone']['placeholder'] = 'Mobile Phone';

    $fields['billing_address_1']['class'] = array('form-row-first');
    $fields['billing_country']['class'] = array('form-row-last');
    $fields['billing_postcode']['class'] = array('form-row-postcode');
    $fields['billing_city']['class'] = array('form-row-city');
    $fields['billing_state']['class'] = array('form-row-state');
    $fields['billing_state']['autocomplete'] = array('false');
    $fields['billing_state']['autocomplete'] = array('off');

    return $fields;
}

add_filter('woocommerce_default_address_fields', 'override_default_address_checkout_fields', 9998, 1);
function override_default_address_checkout_fields( $address_fields ) {
    $address_fields['first_name']['placeholder'] = 'First Name';
    $address_fields['first_name']['priority'] = 3;
    $address_fields['last_name']['placeholder'] = 'Last Name';
    $address_fields['last_name']['priority'] = 4;
    $address_fields['address_1']['placeholder'] = 'Shipping Address';
    $address_fields['address_1']['priority'] = 5;
    $address_fields['state']['placeholder'] = 'State';
    $address_fields['state']['autocomplete'] = array('false');
    $address_fields['state']['autocomplete'] = array('off');
    $address_fields['state']['priority'] = 9;
    $address_fields['postcode']['placeholder'] = 'Zip Code';
    $address_fields['postcode']['priority'] = 7;
    $address_fields['country']['placeholder'] = 'Country';
    $address_fields['country']['priority'] = 6;
    $address_fields['city']['placeholder'] = 'City';
    $address_fields['city']['priority'] = 8;

    unset($address_fields['company']);
    unset($address_fields['address_2']);

    return $address_fields;
}
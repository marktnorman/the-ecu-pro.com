<?php

add_action('wp_ajax_updating_shipping', 'updating_shipping');
add_action('wp_ajax_nopriv_updating_shipping', 'updating_shipping');

/**
 *
 */
function updating_shipping()
{
    $data = '';
    if (isset($_POST['form_data'])) {
        $data = json_decode($_POST['form_data']);
        WC()->customer->set_billing_email($data->billing_email);
        WC()->customer->set_billing_phone($data->billing_phone);

        WC()->customer->set_billing_first_name($data->billing_first_name);
        WC()->customer->set_shipping_first_name($data->billing_first_name);

        WC()->customer->set_billing_last_name($data->billing_last_name);
        WC()->customer->set_shipping_last_name($data->billing_last_name);

        WC()->customer->set_billing_address_1($data->billing_address_1);
        WC()->customer->set_shipping_address_1($data->billing_address_1);

        WC()->customer->set_billing_country($data->billing_country);
        WC()->customer->set_shipping_country($data->billing_country);

        WC()->customer->set_billing_postcode($data->billing_postcode);
        WC()->customer->set_shipping_postcode($data->billing_postcode);

        WC()->customer->set_billing_city($data->billing_city);
        WC()->customer->set_shipping_city($data->billing_city);

        WC()->customer->set_billing_state($data->billing_state);
        WC()->customer->set_shipping_state($data->billing_state);
    }

    $shipping_options = [];

    foreach (WC()->cart->get_shipping_packages() as $package_id => $package) {
        // Check if a shipping for the current package exist
        if (WC()->session->__isset('shipping_for_package_' . $package_id)) {
            // Loop through shipping rates for the current package
            foreach (WC()->session->get(
                'shipping_for_package_' . $package_id
            )['rates'] as $shipping_rate_id => $shipping_rate) {
                $rate_id     = $shipping_rate->get_id(
                ); // same thing that $shipping_rate_id variable (combination of the shipping method and instance ID)
                $method_id   = $shipping_rate->get_method_id(); // The shipping method slug
                $instance_id = $shipping_rate->get_instance_id(); // The instance ID
                $label_name  = $shipping_rate->get_label(); // The label name of the method
                $cost        = $shipping_rate->get_cost(); // The cost without tax
                $tax_cost    = $shipping_rate->get_shipping_tax(); // The tax cost
                $taxes       = $shipping_rate->get_taxes(); // The taxes details (array)

                $shipping_options[] = [
                    'rate_id'     => $rate_id,
                    'method_id'   => $method_id,
                    'instance_id' => $instance_id,
                    'label_name'  => $label_name,
                    'cost'        => $cost,
                    'tax_cost'    => $tax_cost,
                    'taxes'       => $taxes,
                ];

                echo "<li>";
                echo "<input type='radio' name='shipping_method[0]' data-index='0' id='shipping_method_0_flat_rate".$instance_id."' value='flat_rate:".$instance_id."' class='shipping_method'>";
                echo "<label class='radio-button-label' for='shipping_method_0_flat_rate".$instance_id."'>".$label_name.": <span class='woocommerce-Price-amount amount'><bdi><span class='woocommerce-Price-currencySymbol'>$</span>".$cost."</bdi></span></label>";
                echo "<div class='check'></div>";
                echo "</li>";

            }
        }
    }

    die();

}
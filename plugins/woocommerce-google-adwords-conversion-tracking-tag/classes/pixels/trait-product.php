<?php

namespace WGACT\Classes\Pixels;

use WGACT\Classes\Admin\Environment_Check;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

trait Trait_Product
{
    protected function get_formatted_variant_text($product): string
    {
        $variant_text_array = [];

        $attributes = $product->get_attributes();
        if ($attributes) {
            foreach ($attributes as $key => $value) {

                $key_name             = str_replace('pa_', '', $key);
                $variant_text_array[] = ucfirst($key_name) . ': ' . strtolower($value);
            }
        }

        return implode(' | ', $variant_text_array);
    }

    protected function get_variation_or_product_id($item, $variations_output = true)
    {
        if ($item['variation_id'] <> 0 && $variations_output == true) {
            return $item['variation_id'];
        } else {
            return $item['product_id'];
        }
    }

    // https://stackoverflow.com/a/56278308/4688612
    // https://stackoverflow.com/a/39034036/4688612
    public function get_brand_name($product_id): string
    {
//        return $this->get_brand_by_taxonomy($product_id, 'product_brand') ?: // for Woocommerce Brands plugin
//            $this->get_brand_by_taxonomy($product_id, 'yith_product_brand') ?: // for YITH WooCommerce Brands plugin
//                $this->get_brand_by_taxonomy($product_id, 'pa_brand') ?: // for a custom product attribute
//                    '';

        $brand_taxonomy = 'pa_brand';

        if ((new Environment_Check())->is_yith_wc_brands_active()) {
            $brand_taxonomy = 'yith_product_brand';
        } else if ((new Environment_Check())->is_woocommerce_brands_active()) {
            $brand_taxonomy = 'product_brand';
        }

        $brand_taxonomy = apply_filters('wooptpm_custom_brand_taxonomy', $brand_taxonomy);

        return $this->get_brand_by_taxonomy($product_id, $brand_taxonomy) ?:
            $this->get_brand_by_taxonomy($product_id, 'pa_' . $brand_taxonomy) ?:
                '';
    }

    public function get_brand_by_taxonomy($product_id, $taxonomy): string
    {
        if (taxonomy_exists($taxonomy)) {
            $brand_names = wp_get_post_terms($product_id, $taxonomy, ['fields' => 'names']);
            return reset($brand_names);
        } else {
            return '';
        }
    }

    // get an array with all product categories
    public function get_product_category($product_id): array
    {
        $prod_cats        = get_the_terms($product_id, 'product_cat');
        $prod_cats_output = [];

        // only continue with the loop if one or more product categories have been set for the product
        if (!empty($prod_cats)) {
            foreach ((array)$prod_cats as $key) {
                array_push($prod_cats_output, $key->name);
            }

            // apply filter to the $prod_cats_output array
            $prod_cats_output = apply_filters_deprecated('wgact_filter', [$prod_cats_output], '1.10.2', '', 'This filter has been deprecated without replacement.');
        }

        return $prod_cats_output;
    }

    protected function get_compiled_product_id($product_id, $product_sku, $options, $channel = ''): string
    {
        // depending on setting use product IDs or SKUs
        if (0 == $this->options['google']['ads']['product_identifier'] || $channel === 'ga_ua' || $channel === 'ga_4') {
            return (string)$product_id;
        } else if (1 == $this->options['google']['ads']['product_identifier']) {
            return (string)'woocommerce_gpf_' . $product_id;
        } else {
            if ($product_sku) {
                return (string)$product_sku;
            } else {
                return (string)$product_id;
            }
        }
    }

    protected function get_dyn_r_ids($product): array
    {
        $dyn_r_ids = [
            'post_id' => (string)$product->get_id(),
            'sku'     => (string)$product->get_sku() ? $product->get_sku() : $product->get_id(),
            'gpf'     => 'woocommerce_gpf_' . (string)$product->get_id(),
        ];

        // if you want to add a custom dyn_r_id for each product
        $dyn_r_ids = apply_filters('wooptpm_product_ids', $dyn_r_ids, $product);

        return $dyn_r_ids;
    }

    protected function log_problematic_product_id($product_id = 0)
    {
        wc_get_logger()->debug(
            'WooCommerce detects the page ID ' . $product_id . ' as product, but when invoked by wc_get_product( ' . $product_id . ' ) it returns no product object',
            ['source' => 'wooptpm']
        );
    }

    protected function get_order_item_ids($order): array
    {
        $order_items       = $this->wooptpm_get_order_items($order);
        $order_items_array = [];

        foreach ((array)$order_items as $order_item) {

            $product_id = $this->get_variation_or_product_id($order_item->get_data(), $this->options_obj->general->variations_output);

            $product = wc_get_product($product_id);

            // only continue if WC retrieves a valid product
            if (is_object($product)) {

                $dyn_r_ids           = $this->get_dyn_r_ids($product);
                $product_id_compiled = $dyn_r_ids[$this->get_dyn_r_id_type()];
//                $product_id_compiled = $this->get_compiled_product_id($product_id, $product->get_sku(), $this->options, '');
                array_push($order_items_array, $product_id_compiled);
            } else {

                $this->log_problematic_product_id($product_id);
            }
        }

        return $order_items_array;
    }

    protected function get_order_items_formatted_for_purchase_event($order): array
    {
        $order_items           = $this->wooptpm_get_order_items($order);
        $order_items_formatted = [];

        foreach ((array)$order_items as $order_item) {

            $product_id = $this->get_variation_or_product_id($order_item->get_data(), $this->options_obj->general->variations_output);

            $product         = wc_get_product($product_id);
            $product_details = [];

            // only continue if WC retrieves a valid product
            if (is_object($product)) {

                $dyn_r_ids           = $this->get_dyn_r_ids($product);
                $product_id_compiled = $dyn_r_ids[$this->get_dyn_r_id_type()];
//                $product_id_compiled = $this->get_compiled_product_id($product_id, $product->get_sku(), $this->options, '');

                $product_details['id']       = $product_id_compiled;
                $product_details['name']     = $product->get_name();
                $product_details['quantity'] = $order_item->get_quantity();
                $product_details['price']    = $product->get_price();
                $product_details['brand']    = $this->get_brand_name($product_id);
                $product_details['category'] = implode(',', $this->get_product_category($product_id));

//                error_log('type: ' . $product->get_type());

                if ($product->is_type('variation')) {
                    $product_details['variant'] = $this->get_formatted_variant_text($product);

                    $parent_id                  = $product->get_parent_id();
                    $parent_product             = wc_get_product($parent_id);

                    $dyn_r_ids_parent             = $this->get_dyn_r_ids($parent_product);
                    $parent_product_id_compiled   = $dyn_r_ids_parent[$this->get_dyn_r_id_type()];
                    $product_details['parent_id'] = $parent_product_id_compiled;
                }

                $order_items_formatted[] = $product_details;
//                array_push($order_items_formatted, $product_details);
            } else {

                $this->log_problematic_product_id($product_id);
            }
        }

        return $order_items_formatted;
    }


    protected function get_dyn_r_id_type(): string
    {
//        $dyn_r_id_type = '';

        if ($this->options_obj->google->ads->product_identifier == 0) {
            $this->dyn_r_id_type = 'post_id';
        } elseif ($this->options_obj->google->ads->product_identifier == 1) {
            $this->dyn_r_id_type = 'gpf';
        } elseif ($this->options_obj->google->ads->product_identifier == 2) {
            $this->dyn_r_id_type = 'sku';
        }

        // if you want to change the dyn_r_id type for Google programmatically
        $this->dyn_r_id_type = apply_filters('wooptpm_product_id_type_for_' . $this->pixel_name, $this->dyn_r_id_type);

        return $this->dyn_r_id_type;
    }

    protected function wooptpm_get_order_items($order)
    {
        return apply_filters('wooptpm_order_items', $order->get_items(), $order);
    }

    public function get_product_data_layer_script($product, $set_position = true, $meta_tag = false): string
    {
        if (!is_object($product)) {

//            $this->log_problematic_product_id();
            wc_get_logger()->debug('get_product_data_layer_script received an invalid product', ['source' => 'wooptpm']);

            return '';
        }

        $data = $this->get_product_details_for_datalayer($product);

        // if placed in <head> it must be a <meta> tag else, it can be an <input> tag
        $tag = $meta_tag ? "meta" : "input";

        $html = "
            <$tag type='hidden' class='wooptpmProductId' data-id='" . $product->get_id() . "'>
            <script type=\"text/javascript\" data-cfasync=\"false\">
                window.wooptpmDataLayer.products = window.wooptpmDataLayer.products || {};
                window.wooptpmDataLayer.products[" . $product->get_id() . "] = " . json_encode($data) . ";";

        if ($set_position === true) {

            $html .= "
                window.wooptpmDataLayer.products[{$product->get_id()}]['position'] = window.wooptpmDataLayer.position++;";
        }

        $html .= "</script>";

        return $html;
    }

    public function get_product_details_for_datalayer($product): array
    {
        global $woocommerce_wpml;

        $dyn_r_ids = $this->get_dyn_r_ids($product);

        if ((new Environment_Check())->is_wpml_woocommerce_multi_currency_active()) {
            $price = $woocommerce_wpml->multi_currency->prices->get_product_price_in_currency($product->get_id(), get_woocommerce_currency());
        } else {
            $price = $product->get_price();
        }

        $product_details = [
            'id'          => (string)$product->get_id(),
            'sku'         => (string)$product->get_sku(),
            'name'        => (string)$product->get_name(),
            'price'       => (float)$price,
            'brand'       => $this->get_brand_name($product->get_id()),
            'category'    => $this->get_product_category($product->get_id()),
            'quantity'    => 1,
            'dyn_r_ids'   => $dyn_r_ids,
            'isVariable'  => $product->get_type() == 'variable',
            'isVariation' => false,
        ];

        if ($product->get_type() == 'variation') {

            $parent_product = wc_get_product($product->get_parent_id());
            if ($parent_product) {
                $product_details['name']               = $parent_product->get_name();
                $product_details['parentId_dyn_r_ids'] = $this->get_dyn_r_ids($parent_product);
                $product_details['parentId']           = $parent_product->get_id();
            } else {
                wc_get_logger()->debug('Variation ' . $product->get_id() . ' doesn\'t link to a valid parent product.', ['source' => 'wooptpm']);
            }

            $product_details['isVariation'] = true;
            $product_details['variant']     = $this->get_formatted_variant_text($product);
        }

        return $product_details;
    }
}
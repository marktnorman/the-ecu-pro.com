<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $post, $product;

// Get product description option
$product_description_option = get_field("product_associated_description", $product->get_id());
$product_description_option = !empty($product_description_option) ? $product_description_option : 'option-1';

// Get product vehicle date
$product_vehicle_date = get_field("vehicle_date", $product->get_id());
$product_vehicle_date = !empty($product_vehicle_date) ? $product_vehicle_date : 'No date set';

// Get product ECU
$product_ecu = get_field("ecu", $product->get_id());
$product_ecu = !empty($product_ecu) ? $product_ecu : 'No ECU set';

// Get product security system
$product_security_system = get_field("security_system", $product->get_id());
$product_security_system = !empty($product_security_system) ? $product_security_system : 'No security system set';

//Get our description taxonomy terms
$description_attribute = $product->get_attribute('description');

// Get video URL
$product_video_url = get_field("video_embed_url", $product->get_id());
$formatted_video_output = '<a href="#" class="video-informational-popup-trigger">[i]</a>';
$product_video_url = !empty($product_video_url) ? $formatted_video_output : '';

$attribute_taxonomies = wc_get_attribute_taxonomies();
$taxonomy             = array();

if ($attribute_taxonomies) :
    foreach ($attribute_taxonomies as $tax) :
        if ($tax->attribute_name === 'description') :
            if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) :
                $taxonomy[$tax->attribute_name] = get_terms(
                    wc_attribute_taxonomy_name($tax->attribute_name),
                    'orderby=name&hide_empty=0'
                );
            endif;
        endif;
    endforeach;
endif;

foreach ($taxonomy as $taxonomy_terms) :
    foreach ($taxonomy_terms as $taxonomy_term) :
        if ($taxonomy_term->slug === $product_description_option) :
            $product_short_description = get_field(
                "top_description",
                $taxonomy_term->taxonomy . '_' . $taxonomy_term->term_id
            );
        endif;
    endforeach;
endforeach;

$product_short_description = !empty($product_short_description) ? $product_short_description : apply_filters(
    'woocommerce_short_description',
    $post->post_excerpt
);

// Find and replace vehicle date with the set value
$product_short_description = str_replace('[vehicle-date]', $product_vehicle_date, $product_short_description);

// Find and replace ECU with the set value
$product_short_description = str_replace('[ECU]', $product_ecu, $product_short_description);

// Find and replace security system with the set value
$product_short_description = str_replace('[security-system]', $product_security_system, $product_short_description);

// Find and replace information CTA to call to action for video popup
$product_short_description = str_replace('[i]', $product_video_url, $product_short_description);
?>
<div class="woocommerce-product-details__short-description">
    <?php echo $product_short_description; ?>
</div>

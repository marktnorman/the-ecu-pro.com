<?php

add_filter('wp_get_attachment_image_attributes', 'change_attachment_image_attributes', 20, 2);

/**
 * @param $attr
 * @param $attachment
 *
 * @return mixed
 */
function change_attachment_image_attributes($attr, $attachment)
{

    // Get post parent
    $parent = get_post_field('post_parent', $attachment);

    // Get post type to check if it's product
    $type = get_post_field('post_type', $parent);
    if ($type != 'product') {
        return $attr;
    }

    $product_ID = get_post_field('ID', $parent);

    $product_image1_id = get_post_thumbnail_id($product_ID);

    if ($product_image1_id == $attachment->ID) {
        $product_image1_alt         = get_post_meta($product_ID, 'product_image1_alt_text', true);
        $product_image1_title       = get_post_meta($product_ID, 'product_image1_title', true);
        $product_image1_caption     = get_post_meta($product_ID, 'product_image1_caption', true);
        $product_image1_description = get_post_meta($product_ID, 'product_image1_description', true);

        $attr['alt']         = $product_image1_alt;
        $attr['title']       = $product_image1_title;
        $attr['caption']     = $product_image1_caption;
        $attr['description'] = $product_image1_description;

        return $attr;
    }

    global $product;
    if (!isset($product)) {
        return $attr;
    }

    $attachment_ids = $product->get_gallery_image_ids();

    if (!empty($attachment_ids)) {
        $counter = 2;
        foreach ($attachment_ids as $attachment_id) {
            if ($attachment_id == $attachment->ID) {
                $product_image_alt         = get_post_meta($product_ID, 'product_image' . $counter . '_alt_text', true);
                $product_image_title       = get_post_meta($product_ID, 'product_image' . $counter . '_title', true);
                $product_image_caption     = get_post_meta($product_ID, 'product_image' . $counter . '_caption', true);
                $product_image_description = get_post_meta(
                    $product_ID,
                    'product_image' . $counter . '_description',
                    true
                );

                $attr['alt']         = $product_image_alt;
                $attr['title']       = $product_image_title;
                $attr['caption']     = $product_image_caption;
                $attr['description'] = $product_image_description;
            }
            $counter++;
        }
    }

    return $attr;
}

add_filter('woocommerce_structured_data_product', 'update_product_description_meta_yoast', 9, 1);

/**
 * @param $data
 *
 * @return mixed
 * @throws Exception
 */
function update_product_description_meta_yoast($data)
{
    if (!is_product()) {
        return $data;
    }

    global $woocommerce, $product;
    $variation_product = new WC_Product_Variation($product->id());

    // generate our description from short and bottom
    $full_copy = strip_tags(generate_product_short_description());
    $full_copy .= strip_tags(generate_product_bottom_description());
    $full_copy .= str_replace('[i]', '', $full_copy);

    // Update the description
    $data['description'] = $full_copy;

    // Update URL to variation
    $data['@id'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $data['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Update price to variation price
    $data['offers'][0]['price'] = $variation_product->get_price();

    return $data;
}
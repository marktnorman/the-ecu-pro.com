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
 */
function update_product_description_meta_yoast($data)
{
    if (!is_product()) {
        return $data;
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

    $attribute_taxonomies      = wc_get_attribute_taxonomies();
    $taxonomy                  = array();
    $product_short_description = '';

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

    // Find and replace vehicle date with the set value
    $product_short_description = str_replace('[vehicle-date]', $product_vehicle_date, $product_short_description);

    // Find and replace ECU with the set value
    $product_short_description = str_replace('[ECU]', $product_ecu, $product_short_description);

    // Find and replace security system with the set value
    $product_short_description = str_replace('[security-system]', $product_security_system, $product_short_description);

    // Find and replace information CTA to call to action for video popup
    $product_short_description = str_replace('[i]', '', $product_short_description);

    $data['description'] = strip_tags($product_short_description);

    return $data;
}
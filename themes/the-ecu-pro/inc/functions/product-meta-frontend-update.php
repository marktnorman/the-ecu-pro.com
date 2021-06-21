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

add_filter( 'wpseo_json_ld_output', '__return_false' );

//add_filter('wpseo_schema_graph_pieces', 'update_product_description_meta_yoast', 11, 2);

/**
 * Updates the description schema graph for Yoast
 *
 * @param array  $pieces  The current graph pieces.
 * @param string $context The current context.
 *
 * @return array The remaining graph pieces.
 */
function update_product_description_meta_yoast($pieces, $context): array
{
    if (!is_product()) {
        return $pieces;
    }

    global $post;

    $product = wc_get_product($post->ID);
    // $product->get_short_description()

    $pieces['product'] = array(
        'url'         => get_permalink($post->ID),
        'description' => 'This is the supercvustom desc'
    );

    return $pieces;
}
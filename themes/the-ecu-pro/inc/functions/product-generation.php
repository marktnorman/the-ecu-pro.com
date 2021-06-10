<?php

//Schedule an action if it's not already scheduled
//if (!wp_next_scheduled('generate_products_hook')) {
    //wp_schedule_event(time(), 'every_3_minutes', 'generate_products_hook');
    //wp_schedule_event(time(), 'daily', 'generate_products_hook');
//}

//add_action('generate_products_hook', 'generateProductContent');

/**
 * generateProductContent
 *
 * @throws WC_Data_Exception
 */
function generateProductContent()
{
    global $wpdb;

    $products_table = 'wp_ungenerated_products';

    $product_results = $wpdb->get_results(
        "
    SELECT * 
    FROM  $products_table
        WHERE product_created is NULL LIMIT 60"
    );

    // No more products die
    if (empty($product_results)) {
        return;
    }

    foreach ($product_results as $product) {

        $featured_image_id = get_image_id($product->product_image_1);
        if (!empty($featured_image_id)) {
            $featured_image_id = reset($featured_image_id);
            if (isset($featured_image_id->ID)) {
                $featured_image_id = $featured_image_id->ID ?? '';
            }
        }

        if (isset($image_gallery_ids)) {
            unset($image_gallery_ids);
        }

        $image_gallery_ids = [];

        $image_gallery_first_id = get_image_id($product->product_image_2);
        if (!empty($image_gallery_first_id)) {
            $image_gallery_first_id = reset($image_gallery_first_id);
            if (isset($image_gallery_first_id->ID)) {
                $image_gallery_ids[] = $image_gallery_first_id->ID;
            }
        } else {
            $wpdb->insert(
                'wp_missing_image_names',
                array(
                    'row_id'     => $product->row_id,
                    'image_name' => $product->product_image_2
                )
            );
        }

        $image_gallery_second_id = get_image_id($product->product_image_3);
        if (!empty($image_gallery_second_id)) {
            $image_gallery_second_id = reset($image_gallery_second_id);
            if (isset($image_gallery_second_id->ID)) {
                $image_gallery_ids[] = $image_gallery_second_id->ID;
            }
        } else {
            $wpdb->insert(
                'wp_missing_image_names',
                array(
                    'row_id'     => $product->row_id,
                    'image_name' => $product->product_image_3
                )
            );
        }

        // Tag the product category but create if doesnt exist
        $taxonomy = 'product_cat';

        // Parent
        $parent_term = insert_term($product->product_parent_category, $taxonomy);

        // 1st Child
        $first_child_term = insert_term(
            $product->product_child1_category,
            $taxonomy,
            ['parent' => $parent_term['term_id']]
        );

        // 2nd Child (1st child becomes the parent)
        $second_child_term = insert_term(
            $product->product_child2_category,
            $taxonomy,
            ['parent' => $first_child_term['term_id']]
        );

        // 3de Child (2nd child becomes the parent)
        $third_child_term = insert_term(
            $product->product_child3_category,
            $taxonomy,
            ['parent' => $second_child_term['term_id']]
        );

        // Determine which ECU type we working with
        if ($product->product_ecu == 'FRM') {
            $variation_terms        = array('FRM Testing (Recommended)', 'FRM Repair', 'FRM Clone');
            $default_variation_term = 'frm-testing-recommended';
            $type_title             = 'FRM';
            $type_slug              = 'frm';
        } else {
            $variation_terms        = array('ECU Testing (Recommended)', 'ECU Repair', 'ECU Clone');
            $default_variation_term = 'ecu-testing-recommended';
            $type_title             = 'ECU';
            $type_slug              = 'ecu';
        }

        $args = array(
            'name'               => $product->product_title,
            'type'               => 'variable',
            'description'        => '',
            'short_description'  => '',
            'status'             => 'publish',
            'visibility'         => 'visible',
            'regular_price'      => '1',
            'tax_class'          => 'standard',
            'virtual'            => false,
            'sku'                => $product->product_sku,
            'category_ids'       => [
                $parent_term['term_id'],
                $first_child_term['term_id'],
                $second_child_term['term_id'],
                $third_child_term['term_id']
            ],
            'attributes'         => array(
                // Taxonomy and term name values
                'pa_variation' => array(
                    'term_names'    => $variation_terms,
                    'is_visible'    => true,
                    'for_variation' => true,
                )
            ),
            'default_attributes' => array(
                'pa_variation' => $default_variation_term
            )
        );

        // Create our product!
        $post_id = create_product($args);

        // Set our featured image
        $image_result = 'Failed, no image';
        if (!empty($featured_image_id)) {
            $image_result = set_post_thumbnail($post_id, $featured_image_id);
        } else {
            $wpdb->insert(
                'wp_missing_image_names',
                array(
                    'row_id'     => $product->row_id,
                    'image_name' => $product->product_image_1
                )
            );
        }

        // Set our gallery images
        $image_gallery_result = 'Failed, no gallery images';
        if (isset($image_gallery_ids)) {
            update_post_meta($post_id, '_product_image_gallery', implode(',', $image_gallery_ids));
            $image_gallery_result = 'Success - ' . implode(',', $image_gallery_ids);

        }

        // If fail log!
        if (!$post_id) {
            $logData = array(
                'wp_error'  => $post_id,
                'post_data' => $product
            );

            write_log($logData);
            exit;
        }

        $parent_id = $post_id; // Or get the variable product id dynamically

        // The first variation data
        $variation_data = array(
            'attributes'    => array(
                'variation' => $type_title . ' Testing (Recommended)'
            ),
            'sku'           => $product->product_sku . '-1',
            'regular_price' => $product->product_variation_testing_price,
            'sale_price'    => '',
        );

        // Lets create and tag variations
        create_product_variation(
            $parent_id,
            $variation_data,
            $type_slug . ' testing $' . $product->product_variation_testing_price
        );

        // The second variation data
        $variation_data = array(
            'attributes'    => array(
                'variation' => $type_title . ' Repair'
            ),
            'sku'           => $product->product_sku . '-2',
            'regular_price' => $product->product_variation_repair_price,
            'sale_price'    => '',
        );

        // Lets third and tag variations
        create_product_variation(
            $parent_id,
            $variation_data,
            $type_slug . ' repair $' . $product->product_variation_repair_price
        );

        // The third variation data
        $variation_data = array(
            'attributes'    => array(
                'variation' => $type_title . ' Clone'
            ),
            'sku'           => $product->product_sku . '-3',
            'regular_price' => $product->product_variation_clone_price,
            'sale_price'    => '',
        );

        // Lets create and tag variations
        create_product_variation(
            $parent_id,
            $variation_data,
            $type_slug . ' clone $' . $product->product_variation_clone_price
        );

        // Set our custom data
        update_post_meta($post_id, 'ecu', $product->product_ecu);

        if ($product->product_ecu == 'FRM') {
            update_post_meta($post_id, 'product_type', 'FRMs');
            update_post_meta($post_id, '_product_type', 'field_609d99fadef5a');
        } else {
            update_post_meta($post_id, 'product_type', 'ECUs');
            update_post_meta($post_id, '_product_type', 'field_609d99fadef5a');
        }

        // Set ECU condition meta based on Security system and required meta for ACF fields
        switch ($product->product_security_system) {
            case "EWS":
                update_post_meta($post_id, 'ecu_condition', '1');
                update_post_meta($post_id, '_ecu_condition', 'field_60abad652080e');
                break;
            case "CAS":
                update_post_meta($post_id, 'ecu_condition', '2');
                update_post_meta($post_id, '_ecu_condition', 'field_60abad652080e');
                break;
            case "FEM":
                update_post_meta($post_id, 'ecu_condition', '3');
                update_post_meta($post_id, '_ecu_condition', 'field_60abad652080e');
                break;
            case "FRM":
                update_post_meta($post_id, 'ecu_condition', '4');
                update_post_meta($post_id, '_ecu_condition', 'field_60abad652080e');
                break;
        }

        update_post_meta($post_id, 'vehicle', $product->product_vehicle);
        update_post_meta($post_id, '_vehicle', 'field_60ae6c5fe248b');

        update_post_meta($post_id, 'vehicle_date', $product->product_vehicle_date);
        update_post_meta($post_id, '_vehicle_date', 'field_60ae6cca2ac32');

        update_post_meta($post_id, 'security_system', $product->product_security_system);
        update_post_meta($post_id, '_security_system', 'field_60ae6d14a6a56');

        update_post_meta($post_id, 'ecu_faults', $product->product_ecu);
        update_post_meta($post_id, '_ecu_faults', 'field_60ae6e03133ba');

        update_post_meta($post_id, 'product_associated_description', $product->product_associated_description);
        update_post_meta($post_id, '_product_associated_description', 'field_60aeb5bc71a5f');

        if (!empty($product->yoast_wpseo_metadesc)) {
            update_post_meta($post_id, '_yoast_wpseo_metadesc', $product->yoast_wpseo_metadesc);
        }

        if (!empty($product->product_video_url)) {
            update_post_meta($post_id, 'video_embed_url', $product->product_video_url);
            update_post_meta($post_id, '_video_embed_url', 'field_609803896cd32');
        }
        update_post_meta($post_id, '_wcopc', 'yes');

        // Meta data for product images
        if (!empty($product->product_image1_alt_text)) {
            update_post_meta($post_id, 'product_image1_alt_text', $product->product_image1_alt_text);
            update_post_meta($post_id, 'product_image1_title', $product->product_image1_title);
            update_post_meta($post_id, 'product_image1_caption', $product->product_image1_caption);
            update_post_meta($post_id, 'product_image1_description', $product->product_image1_description);
        }

        if (!empty($product->product_image2_alt_text)) {
            update_post_meta($post_id, 'product_image2_alt_text', $product->product_image2_alt_text);
            update_post_meta($post_id, 'product_image2_title', $product->product_image2_title);
            update_post_meta($post_id, 'product_image2_caption', $product->product_image2_caption);
            update_post_meta($post_id, 'product_image2_description', $product->product_image2_description);
        }

        if (!empty($product->product_image3_alt_text)) {
            update_post_meta($post_id, 'product_image3_alt_text', $product->product_image3_alt_text);
            update_post_meta($post_id, 'product_image3_title', $product->product_image3_title);
            update_post_meta($post_id, 'product_image3_caption', $product->product_image3_caption);
            update_post_meta($post_id, 'product_image3_description', $product->product_image3_description);
        }

        // Update that the product has been created
        $product_insert_status = $wpdb->update(
            $products_table,
            array('product_created' => 1),
            array('product_sku' => $product->product_sku)
        );

        if ($product_insert_status) {
            $logData = array(
                'product_data'                  => $product,
                'status'                        => 'Completed!',
                'product_status_return'         => $product_insert_status,
                'product image result'          => $image_result,
                'product gallery images result' => $image_gallery_result,
            );

            write_log($logData);
        } else {
            $logData = array(
                'product_data'          => $product,
                'status'                => 'Failed!',
                'product_status_return' => $product_insert_status,
            );

            write_log($logData);
        }

    }
}
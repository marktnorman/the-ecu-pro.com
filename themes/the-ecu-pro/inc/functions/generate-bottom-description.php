<?php

/**
 * @throws Exception
 */
function generate_product_bottom_description()
{
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

    //Get our ecu faults taxonomy terms
    $ecu_faults_attribute = $product->get_attribute('ecu-faults');

    // Get video URL

    $product_type = get_post_meta($product->get_id(), 'product_type', true);
    $term_object  = get_term_by('name', $product_type, 'pa_product-type-data');

    $how_it_works_product_video_url = '';

    if (have_rows('how_ti_works_product_data', $term_object)):
        while (have_rows('how_ti_works_product_data', $term_object)) : the_row();

            $how_it_works_product_video_url = get_sub_field(
                'how_it_works_product_video_url'
            );

        endwhile;
    endif;

    $how_it_works_product_video_url = !empty($how_it_works_product_video_url) ? $how_it_works_product_video_url : 'https://www.youtube.com/embed/JESCRzDYdqE';

    $formatted_video_output = '<a href="#" data-source-attr="'.$how_it_works_product_video_url.'" class="video-informational-popup-trigger">[i]</a>';
    $product_video_url      = !empty($how_it_works_product_video_url) ? $formatted_video_output : '';

    $attribute_taxonomies   = wc_get_attribute_taxonomies();
    $taxonomy_ecu_faults    = array();
    $ecu_fault_items_output = '';
    $taxonomy_description   = array();

    if ($attribute_taxonomies) {
        foreach ($attribute_taxonomies as $tax) {
            if ($tax->attribute_name === 'ecu-faults') {
                if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) {
                    $taxonomy_ecu_faults[$tax->attribute_name] = get_terms(
                        wc_attribute_taxonomy_name($tax->attribute_name),
                        'orderby=name&hide_empty=0'
                    );
                }
            }

            if ($tax->attribute_name === 'description') {
                if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) {
                    $taxonomy_description[$tax->attribute_name] = get_terms(
                        wc_attribute_taxonomy_name($tax->attribute_name),
                        'orderby=name&hide_empty=0'
                    );
                }
            }
        }
    }

    foreach ($taxonomy_ecu_faults as $taxonomy_ecu_fault_terms) {
        foreach ($taxonomy_ecu_fault_terms as $taxonomy_ecu_fault_term) {
            if ($taxonomy_ecu_fault_term->name === $product_ecu) {

                $ecu_fault_items_output = '<ul>';

                $rows = get_field(
                    'ecu_fault',
                    $taxonomy_ecu_fault_term->taxonomy . '_' . $taxonomy_ecu_fault_term->term_id
                );
                foreach ($rows as $key => $ecu_fault_item) {
                    $ecu_fault_items_output .= '<li>';
                    $ecu_fault_items_output .= $ecu_fault_item['ecu_fault_item'];
                    $ecu_fault_items_output .= '</li>';
                }

                $ecu_fault_items_output .= '</ul>';

            }
        }
    }

    foreach ($taxonomy_description as $taxonomy_description_terms) {
        foreach ($taxonomy_description_terms as $taxonomy_description_term) {
            if ($taxonomy_description_term->slug === $product_description_option) {
                $product_description = get_field(
                    "bottom_description",
                    $taxonomy_description_term->taxonomy . '_' . $taxonomy_description_term->term_id
                );
            }
        }
    }

    $product_description = !empty($product_description) ? $product_description : apply_filters(
        'woocommerce_full_description',
        $post->post_content
    );

    $product_ecu_faults = !empty($ecu_fault_items_output) ? $ecu_fault_items_output : 'No faults';

    // Find and replace vehicle date with the set value
    $product_description = str_replace(
        '[vehicle-date]',
        $product_vehicle_date,
        $product_description
    );

    // Find and replace ECU with the set value
    $product_description = str_replace('[ECU]', $product_ecu, $product_description);

    // Find and replace ecu faults with the set value
    $product_description = str_replace(
        '[ecu-faults]',
        $product_ecu_faults,
        $product_description
    );

    // Find and replace information CTA to call to action for video popup
    $product_description = str_replace('[i]', $product_video_url, $product_description);

    return $product_description;
}
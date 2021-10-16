<?php

global $product;
$id = $product->get_id();
$product_type = get_post_meta($id, 'product_type', true);
$term_object = get_term_by('name', $product_type, 'pa_product-type-data');

$product_video_url = get_field(
    'video_embed_url',
    $id
);
$product_video_url = !empty($product_video_url) ? $product_video_url : 'https://www.youtube.com/embed/JESCRzDYdqE';

if (have_rows('how_ti_works_product_data', $term_object)):

    while (have_rows('how_ti_works_product_data', $term_object)) : the_row();

        // Lets generate the unique title
        $product_vehicle_date = get_field("vehicle_date", $id);
        $product_ecu = get_field("ecu", $id);
        $how_it_works_product_title = "";

        if (!empty($product_vehicle_date) && !empty($product_ecu)) {
            $how_it_works_product_title = "How " . $product_vehicle_date . " " . $product_ecu . " DME / ECU repairs works";
        }

        if (empty($how_it_works_product_title)) {
            $how_it_works_product_title = get_sub_field(
                'how_it_works_product_title'
            );
        }

        $how_it_works_product_copy = get_sub_field(
            'how_it_works_product_copy'
        );
        $how_it_works_product_copy = !empty($how_it_works_product_copy) ? $how_it_works_product_copy : '';

        $accordian_product_items = get_sub_field('how_it_works_accordian_data');
        $accordian_product_html = '';
        $counter = 1;
        foreach ($accordian_product_items as $accordian_product_item) {

            $accordian_title = $accordian_product_item['product_accordian_title'];
            $accordian_copy = $accordian_product_item['product_accordian_body'];

            $accordian_product_html .= '<div class="wrap-' . $counter . '">';
            $accordian_product_html .= '<input type="radio" id="tab-' . $counter . '" name="tabs">';
            $accordian_product_html .= '<label for="tab-' . $counter . '">';
            $accordian_product_html .= '<div class="accordian-title">' . $accordian_title . '</div>';
            $accordian_product_html .= '<div class="cross"></div>';
            $accordian_product_html .= '</label>';
            $accordian_product_html .= '<div class="content">' . $accordian_copy . '</div>';
            $accordian_product_html .= '</div>';

            $counter++;
        } ?>

        <div class="how-it-works-outer-container homepage-section-outer">
            <h2 class="homepage-section-title"><?php echo $how_it_works_product_title; ?></h2>
            <div class="col-lg-12">
                <div class="col-lg-6 first">
                    <?php echo $how_it_works_product_copy; ?>
                    <div class="accordion">
                        <?php echo $accordian_product_html; ?>
                    </div>
                </div>
                <div class="col-lg-6 second">
                    <div class="youtube-video-container">
                        <iframe width="560" height="315"
                                src="<?php echo $product_video_url; ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endwhile;
endif;
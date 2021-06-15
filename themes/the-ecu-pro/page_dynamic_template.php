<?php
/**
 * Template Name: Dynamic content page template
 * Template which can be used on pages utilising ACF fields
 */

get_header('pages');

?>

    <div id="main" class="column1 boxed no-breadcrumbs"><!-- main -->
        <div class="container">
            <div class="row main-content-wrap">

                <!-- main content -->
                <div class="main-content col-lg-12">
                    <div id="content" role="main">
                        <article class="page type-page status-publish">
                            <div class="page-content">
                                <div class="process-container">
                                    <img src="<?php echo get_stylesheet_directory_uri(
                                        ) . '/assets/images/3-step-process-large.png' ?>"/>
                                </div>
                                <div class="steps-outer-container">
                                    <ul>
                                        <li class="one">
                                            <img src="<?php echo get_stylesheet_directory_uri(
                                                ) . '/assets/images/awesome-phone.png' ?>"/>
                                            <p>Call us - our experts will tell you exactly what we need to fix your
                                                problem.</p>
                                        </li>
                                        <li class="two">
                                            <img src="<?php echo get_stylesheet_directory_uri(
                                                ) . '/assets/images/Icon-awesome-bookmark.png' ?>"/>
                                            <p>Book our express collection service</p>
                                        </li>
                                        <li class="three">
                                            <img src="<?php echo get_stylesheet_directory_uri(
                                                ) . '/assets/images/material-directions-car.png' ?>"/>
                                            <p>Plug it back into the car - no additional programming required</p>
                                        </li>
                                    </ul>
                                </div>
                                <?php

                                if (have_rows('dynamic_flexible_content')):

                                    $loop_count = 1;

                                    while (have_rows('dynamic_flexible_content')) : the_row();

                                        switch ($loop_count) {
                                            case 1:
                                                $how_it_works_title = get_sub_field(
                                                    'how_it_works_title'
                                                );
                                                $how_it_works_title = !empty($how_it_works_title) ? $how_it_works_title : '';

                                                $how_it_works_body = get_sub_field(
                                                    'how_it_works_body'
                                                );
                                                $how_it_works_body = !empty($how_it_works_body) ? $how_it_works_body : '';

                                                $how_it_works_video_url = get_sub_field(
                                                    'how_it_works_video_url'
                                                );
                                                $how_it_works_video_url = !empty($how_it_works_video_url) ? $how_it_works_video_url : 'https://www.youtube.com/embed/JESCRzDYdqE';

                                                $accordian_items = get_sub_field('how_it_works_accordian');
                                                $counter         = 1;
                                                $accordian_html  = '';
                                                foreach ($accordian_items as $accordian_item) {

                                                    $accordian_title = $accordian_item['accordian_group_object']['accordian_title'];
                                                    $accordian_copy  = $accordian_item['accordian_group_object']['accordian_copy'];

                                                    $accordian_html .= '<div class="wrap-' . $counter . '">';
                                                    $accordian_html .= '<input type="radio" id="tab-' . $counter . '" name="tabs">';
                                                    $accordian_html .= '<label for="tab-' . $counter . '">';
                                                    $accordian_html .= '<div class="accordian-title">' . $accordian_title . '</div>';
                                                    $accordian_html .= '<div class="cross"></div>';
                                                    $accordian_html .= '</label>';
                                                    $accordian_html .= '<div class="content">' . $accordian_copy . '</div>';
                                                    $accordian_html .= '</div>';

                                                    $counter++;
                                                } ?>

                                                <div class="how-it-works-outer-container homepage-section-outer">
                                                    <h2 class="homepage-section-title"><?php echo $how_it_works_title; ?></h2>
                                                    <div class="col-lg-12">
                                                        <div class="col-lg-6 first">
                                                            <p><?php echo $how_it_works_body; ?></p>
                                                            <div class="accordion">
                                                                <?php echo $accordian_html; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 second">
                                                            <div class="youtube-video-container">
                                                                <iframe width="560" height="315"
                                                                        src="<?php echo $how_it_works_video_url; ?>"
                                                                        frameborder="0"
                                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                        allowfullscreen></iframe>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php break;
                                            case 2:
                                                $recommended_title = get_sub_field(
                                                    'recommended_service_title'
                                                );
                                                $recommended_title = !empty($recommended_title) ? $recommended_title : '';

                                                $recommended_body = get_sub_field(
                                                    'recommended_service_body'
                                                );
                                                $recommended_body = !empty($recommended_body) ? $recommended_body : '';

                                                $recommended_product_object = get_sub_field(
                                                    'recommended_service_product'
                                                );

                                                $product   = wc_get_product($recommended_product_object->ID);
                                                $image_id  = $product->get_image_id();
                                                $image_url = wp_get_attachment_image_url($image_id, 'medium');

                                                ?>

                                                <div class="rec-service-outer-container homepage-section-outer">
                                                    <h2 class="homepage-section-title"><?php echo $recommended_title; ?></h2>
                                                    <div class="col-lg-12">
                                                        <div class="col-lg-6 first">
                                                            <p><?php echo $recommended_body; ?></p>
                                                        </div>
                                                        <div class="col-lg-6 second">
                                                            <div class="featured-product-container">
                                                                <img src="<?php echo $image_url; ?>"
                                                                     data-spai="1"
                                                                     class="attachment-medium size-medium wp-post-image"
                                                                     alt="ECU Testing Service"/><span
                                                                        class="tag"><?php echo 'Testing Service'; ?></span>
                                                                <h3><?php echo $recommended_product_object->post_title; ?></h3>
                                                                <span class="price">$99</span>
                                                                <a href="<?php echo $recommended_product_object->guid; ?>">View
                                                                    service</a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="our-service-outer-container homepage-section-outer">
                                                    <div class="our-service-outer-container-inner">
                                                        <h2 class="homepage-section-title">Our service</h2>
                                                        <div class="col-lg-12 white-bg">
                                                            <div class="products-outer">
                                                                <?php

                                                                $products = wc_get_products(
                                                                    [
                                                                        'category'    => array('bmw'),
                                                                        'numberposts' => '4'
                                                                    ]
                                                                );

                                                                echo '<ul class="products-inner">';

                                                                foreach ($products as $product) {

                                                                    echo '<li>';

                                                                    $post_id      = $product->get_id();
                                                                    $content_post = wc_get_product($post_id); ?>
                                                                    <a class="products-image-container"
                                                                       href="<?php echo get_the_permalink(
                                                                           $post_id
                                                                       ); ?>"><?php echo get_the_post_thumbnail(
                                                                            $post_id,
                                                                            'medium'
                                                                        ); ?></a>
                                                                    <?php echo '<span class="tag">DME Testing Service DME Testing Service</span>';
                                                                    echo '<h3>' . $content_post->get_title() . '</h3>';
                                                                    echo '<span class="price">$' . $content_post->get_regular_price(
                                                                        ) . '</span>';

                                                                    ?>

                                                                    <a class="products-permalink"
                                                                       href="<?php echo get_the_permalink(
                                                                           $post_id
                                                                       ); ?>">View
                                                                        service</a>

                                                                    <?php

                                                                    echo '</li>';

                                                                }

                                                                echo '</ul>';

                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php break;
                                            case 3:

                                                $info_title = get_sub_field(
                                                    'more_info_title'
                                                );
                                                $info_title = !empty($info_title) ? $info_title : '';

                                                $info_upper_body = get_sub_field(
                                                    'more_info_upper_body'
                                                );
                                                $info_upper_body = !empty($info_upper_body) ? $info_upper_body : '';

                                                $info_bottom_body = get_sub_field(
                                                    'more_info_bottom_body'
                                                );
                                                $info_bottom_body = !empty($info_bottom_body) ? $info_bottom_body : '';

                                                $info_image_url = get_sub_field(
                                                    'more_info_faults_image'
                                                );
                                                $info_image_url = !empty($info_image_url) ? $info_image_url : '';

                                                if (!empty($info_title) && !empty($info_upper_body) && !empty($info_image_url) && !empty($info_bottom_body)) {

                                                    ?>
                                                    <div class="more-info-outer-container homepage-section-outer">
                                                        <h2 class="homepage-section-title"><?php echo $info_title; ?></h2>
                                                        <div class="col-lg-12">
                                                            <p><?php echo $info_upper_body; ?></p>
                                                            <div class="col-4">
                                                                <img src="<?php echo $info_image_url; ?>"/>
                                                            </div>
                                                            <div class="col-8">
                                                                <?php echo $info_bottom_body; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php

                                                }

                                                break;
                                        }

                                        $loop_count++;

                                    endwhile;
                                endif;

                                ?>


                            </div>
                        </article>
                    </div>
                </div><!-- end main content -->

                <div class="sidebar-overlay"></div>

            </div>
        </div>
    </div>

<?php get_footer(); ?>
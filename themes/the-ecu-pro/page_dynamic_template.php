<?php
/**
 * Template Name: Dynamic content page template
 * Template which can be used on pages utilising ACF fields
 */

get_header();

global $post;

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
                                $how_it_works_title = get_field(
                                    'how_it_works_title',
                                    $post->ID
                                );
                                $how_it_works_title = !empty($how_it_works_title) ? $how_it_works_title : '';

                                $how_it_works_body = get_field(
                                    'how_it_works_body',
                                    $post->ID
                                );
                                $how_it_works_body = !empty($how_it_works_body) ? $how_it_works_body : '';

                                $how_it_works_video_url = get_field(
                                    'how_it_works_video_url',
                                    $post->ID
                                );
                                $how_it_works_video_url = !empty($how_it_works_video_url) ? $how_it_works_video_url : 'https://www.youtube.com/embed/JESCRzDYdqE';
                                ?>
                                <div class="how-it-works-outer-container homepage-section-outer">
                                    <h2 class="homepage-section-title"><?php echo $how_it_works_title; ?></h2>
                                    <div class="col-lg-12">
                                        <div class="col-lg-6 first">
                                            <p><?php echo $how_it_works_body; ?></p>
                                            <div class="accordion">
                                                <div class="wrap-1">
                                                    <input type="radio" id="tab-1" name="tabs">
                                                    <label for="tab-1">
                                                        <div class="accordian-title">Shipping within the US</div>
                                                        <div class="cross"></div>
                                                    </label>
                                                    <div class="content">Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing
                                                        elit. Mollitia autem quasi inventore unde nobis voluptatibus
                                                        illum
                                                        quae rerum laudantium minima, excepturi quis maiores. Eaque
                                                        quae,
                                                        nam delectus explicabo, deserunt ipsum!
                                                    </div>
                                                </div>
                                                <div class="wrap-2">
                                                    <input type="radio" id="tab-2" name="tabs">
                                                    <label for="tab-2">
                                                        <div class="accordian-title">International Shipping</div>
                                                        <div class="cross"></div>
                                                    </label>
                                                    <div class="content">Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing
                                                        elit. Mollitia autem quasi inventore unde nobis voluptatibus
                                                        illum
                                                        quae rerum laudantium minima, excepturi quis maiores. Eaque
                                                        quae,
                                                        nam delectus explicabo, deserunt ipsum!
                                                    </div>
                                                </div>
                                                <div class="wrap-3">
                                                    <input type="radio" id="tab-3" name="tabs">
                                                    <label for="tab-3">
                                                        <div class="accordian-title">Returns</div>
                                                        <div class="cross"></div>
                                                    </label>
                                                    <div class="content">Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing
                                                        elit. Mollitia autem quasi inventore unde nobis voluptatibus
                                                        illum
                                                        quae rerum laudantium minima, excepturi quis maiores. Eaque
                                                        quae,
                                                        nam delectus explicabo, deserunt ipsum!
                                                    </div>
                                                </div>
                                                <div class="wrap-4">
                                                    <input type="radio" id="tab-4" name="tabs">
                                                    <label for="tab-4">
                                                        <div class="accordian-title">Warranty</div>
                                                        <div class="cross"></div>
                                                    </label>
                                                    <div class="content">Lorem ipsum dolor sit amet, consectetur
                                                        adipisicing
                                                        elit. Mollitia autem quasi inventore unde nobis voluptatibus
                                                        illum
                                                        quae rerum laudantium minima, excepturi quis maiores. Eaque
                                                        quae,
                                                        nam delectus explicabo, deserunt ipsum!
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 second">
                                            <div class="youtube-video-container">
                                                <iframe width="560" height="315"
                                                        src="<?php echo $how_it_works_video_url; ?>" frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rec-service-outer-container homepage-section-outer">
                                    <h2 class="homepage-section-title">Recommended Service</h2>
                                    <div class="col-lg-12">
                                        <div class="col-lg-6 first">
                                            <p>We recommend first sending your X5 DME for a testing service. Once we
                                                have
                                                tested the DME we will know if the fault lies with the DME or the
                                                vehicle
                                                itself. If the DME is faulty and we know what parts have failed, we will
                                                be
                                                able to advise you on the repair process. If you need an estimated
                                                repair
                                                price, please call us.</p>
                                        </div>
                                        <div class="col-lg-6 second">
                                            <div class="featured-product-container">
                                                <img src="/wp-content/uploads/2019/11/DSC_0561-1-MEVD-1-1140x595.jpg"
                                                     data-spai="1" class="attachment-medium size-medium wp-post-image"
                                                     alt="ECU Testing Service" loading="lazy"><span
                                                        class="tag">DME Testing Service DME Testing Service</span>
                                                <h3>X5 ECU / DME Testing Service</h3><span class="price">$99</span>
                                                <a href="/product/x5-ecu-dme-testing-service/">View service</a>

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
                                                       href="<?php echo get_the_permalink($post_id); ?>">View
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
                                <div class="more-info-outer-container homepage-section-outer">
                                    <h2 class="homepage-section-title">More Info</h2>
                                    <div class="col-lg-12">
                                        <p>Footwell module (FRM) failure is a common problem on all E series BMW cars.
                                            The
                                            FRM module is a unit that control all the vehicle’s lights. It is used to
                                            send
                                            signals to the vehicle’s lights and window functions. Normally on BMW
                                            vhicles
                                            the FRM will fail when you jump start your vehicle or when the vehicle’s
                                            battery
                                            goes flat. Symptoms of failed FRM units include:</p>
                                        <div class="col-4">
                                            <img src="<?php echo get_stylesheet_directory_uri(
                                                ) . '/assets/images/more-info-black.png' ?>"/>
                                        </div>
                                        <div class="col-8">
                                            <p>If you experience any of these symptoms you can send us your BMW Footwell
                                                module (FRM) for
                                                a repair. <br><br>
                                                If your FRM is faulty as a result of a flat battery, it is highly
                                                recommended that you replace your
                                                vehicle battery. FRM units have an extremely high failure rate, so to
                                                avoid
                                                future problems the
                                                battery must be replaced. <br><br>
                                                Our turnaround time on FRM repairs is only 1 day. Once the repaired FRM
                                                unit
                                                repaired and
                                                reinstalled into the vehicle no additional programming is required. All
                                                the
                                                lights will work, and
                                                the vehicle will be 100% back to normal.</p>
                                            <span class="info-blue">All our BMW FRM repairs comes with a 6-month warranty and free 30-day technical support! </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div><!-- end main content -->

                <div class="sidebar-overlay"></div>

            </div>
        </div>
    </div>

<?php get_footer(); ?>
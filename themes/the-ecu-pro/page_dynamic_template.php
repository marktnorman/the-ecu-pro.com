<?php
/**
 * Template Name: Dynamic content page template
 * Template which can be used on pages utilising ACF fields
 */

?>

    <!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <!--[if IE]>
        <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
        <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
        <link rel="profile" href="http://gmpg.org/xfn/11"/>
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
              rel="stylesheet">

        <?php wp_head(); ?>
    </head>
<body <?php body_class(); ?>>

<?php $queried_object = get_queried_object_id(); ?>

    <div class="header-wrapper">
        <header id="header" class="header-separate header-1 sticky-menu-header">

            <div class="header-top">
                <div class="container">
                    <div class="header-left">
                        <div class="switcher-wrap">
                            <ul class="view-switcher porto-view-switcher mega-menu show-arrow">
                                <li class="menu-item has-sub narrow sub-ready">
                                    <a class="nolink" href="#"><i class="flag-us"></i>Eng</a>
                                    <div class="popup" style="display: block;">
                                        <div class="inner">
                                            <ul class="sub-menu">
                                                <li class="menu-item"><a href="#"><i class="flag-us"></i>Eng</a></li>
                                                <li class="menu-item"><a href="#"><i class="flag-fr"></i>Frh</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <span class="gap switcher-gap">|</span>
                            <ul id="menu-currency-switcher"
                                class="currency-switcher porto-view-switcher mega-menu show-arrow">
                                <li class="menu-item has-sub narrow sub-ready">
                                    <a class="nolink" href="#">USD</a>
                                    <div class="popup" style="display: block;">
                                        <div class="inner">
                                            <ul class="sub-menu wcml-switcher">
                                                <li class="menu-item"><a href="#">USD</a></li>
                                                <li class="menu-item"><a href="#">EUR</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="header-right">
                        <span class="welcome-msg">Professional ECU Repair Services </span><span class="gap">|</span>
                        <ul id="menu-top-menu" class="top-links mega-menu show-arrow">
                            <li id="nav-menu-item-2039"
                                class="menu-item menu-item-type-post_type menu-item-object-page narrow"><a
                                        href="/my-account/">My Account</a></li>
                            <li id="nav-menu-item-2040"
                                class="menu-item menu-item-type-post_type menu-item-object-page narrow"><a
                                        href="/wishlist/">Wishlist</a></li>
                            <li id="nav-menu-item-2041"
                                class="menu-item menu-item-type-post_type menu-item-object-page narrow"><a
                                        href="/cart/">Cart</a></li>
                            <?php if (!is_user_logged_in()) { ?>
                            <li class="menu-item"><a class="porto-link-login"
                                                     href="/my-account/">Log In</a>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="header-main">
                <div class="container header-row">
                    <div class="header-left">
                        <a class="mobile-toggle"><i class="fas fa-bars"></i></a>
                        <h1 class="logo">
                            <a href="https://development.the-ecu-pro.com/"
                               title="The ECU Pro - Professional ECU repair and exchange services" rel="home">
                                <img
                                        class="img-responsive standard-logo d-block"
                                        src="https://the-ecu-pro.com/wp-content/uploads/2019/11/TheECUPro-Logo-B-Highly-Compressed.png"
                                        data-spai="1" alt="The ECU Pro" loading="lazy" data-spai-upd="150"></a>
                        </h1>
                    </div>
                    <div class="header-center">
                        <div class="searchform-popup">
                            <a class="search-toggle"><i class="fas fa-search"></i><span
                                        class="search-text">Search</span></a>
                            <form action="<?php echo esc_url(home_url('/')); ?>" method="get"
                                  class="searchform searchform-cats" role="search"
                                  wtx-context="33BF3DF1-8256-4006-9B74-F0D143CC8FF7">
                                <div class="searchform-fields">
                                <span class="text"><input name="s" type="text" value=""
                                                          placeholder="Search ECU By Part Number" autocomplete="off"
                                                          wtx-context="2D496482-E9B3-4959-8883-094627C44992"></span>
                                    <input type="hidden" name="post_type" value="product"
                                           wtx-context="DEBF0DCA-B3BF-45F4-872B-219449C3CBB3">
                                    <select name="category" id="product_cat" class="cat" tabindex="0"
                                            wtx-context="E8339C9E-E4A6-4809-B265-7B9E247209D2">
                                        <option value="0">All Categories</option>
                                        <option class="level-0" value="bmw">BMW</option>
                                        <option class="level-0" value="uncategorized">Uncategorized</option>
                                        <option class="level-0" value="shop-by-ecu">Shop by ECU</option>
                                        <option class="level-0" value="bmw-ecu-service">BMW ECU Service</option>
                                        <option class="level-0" value="mini-ecu-service">MINI ECU Service</option>
                                        <option class="level-0" value="mini">MINI</option>
                                    </select>
                                    <span class="button-wrap"><button class="btn btn-special" title="Search"
                                                                      type="submit"><i
                                                    class="fas fa-search"></i></button></span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="header-minicart">
                            <?php
                            get_template_part('templates/header-click-to-view-number');
                            $_cart_qty = WC()->cart->cart_contents_count;
                            $_cart_qty = ($_cart_qty > 0 ? $_cart_qty : '0');
                            ?>
                            <div id="mini-cart" class="mini-cart minicart-arrow-alt">
                                <div class="cart-head">
                            <span class="cart-icon"><i class="minicart-icon"></i><span
                                        class="cart-items"><?php echo $_cart_qty; ?></span></span><span
                                            class="cart-items-text"><?php echo $_cart_qty; ?> items</span></div>
                                <div class="cart-popup widget_shopping_cart">
                                    <div class="widget_shopping_cart_content">

                                        <div class="total-count"><span>0 ITEMS</span><a class="pull-right"
                                                                                        href="https://development.the-ecu-pro.com/cart/">VIEW
                                                CART</a></div>
                                        <div class="scroll-wrapper cart_list product_list_widget scrollbar-inner"
                                             style="position: relative;">
                                            <ul class="cart_list product_list_widget scrollbar-inner scroll-content"
                                                style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">

                                                <li class="woocommerce-mini-cart__empty-message empty">
                                                    No products in the cart.
                                                </li>


                                            </ul>
                                            <div class="scroll-element scroll-x">
                                                <div class="scroll-element_outer">
                                                    <div class="scroll-element_size"></div>
                                                    <div class="scroll-element_track"></div>
                                                    <div class="scroll-bar"></div>
                                                </div>
                                            </div>
                                            <div class="scroll-element scroll-y">
                                                <div class="scroll-element_outer">
                                                    <div class="scroll-element_size"></div>
                                                    <div class="scroll-element_track"></div>
                                                    <div class="scroll-bar"></div>
                                                </div>
                                            </div>
                                        </div><!-- end product list -->


                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="main-menu-wrap">
                <div id="main-menu" class="container  hide-sticky-content">
                    <div class="menu-left">
                        <div class="logo">
                            <a href="https://development.the-ecu-pro.com/"
                               title="The ECU Pro - Professional ECU repair and exchange services">
                                <img class="img-responsive standard-logo retina-logo"
                                     src="/wp-content/uploads/2019/11/TheECUPro-Logo-B-Highly-Compressed.png"
                                     data-spai="1" alt="The ECU Pro" loading="lazy" data-spai-upd="0"> </a>
                        </div>
                    </div>
                    <?php
                    wp_nav_menu(
                        array(
                            'menu'            => 102,
                            'menu_class'      => 'main-menu mega-menu show-arrow',
                            'menu_id'         => 'menu-menu',
                            'container'       => 'div',
                            'container_class' => 'menu-center',
                            'container_id'    => 'main-header-menu',
                        )
                    );
                    ?>
                    <div class="menu-right">
                        <div class="searchform-popup"><a class="search-toggle"><i class="fas fa-search"></i><span
                                        class="search-text">Search</span></a>
                            <form action="<?php echo esc_url(home_url('/')); ?>" method="get"
                                  class="searchform searchform-cats" role="search"
                                  wtx-context="33BF3DF1-8256-4006-9B74-F0D143CC8FF7">
                                <div class="searchform-fields">
                            <span class="text"><input name="s" type="text" value=""
                                                      placeholder="Search ECU By Part Number " autocomplete="off"
                                                      wtx-context="2D496482-E9B3-4959-8883-094627C44992"></span>
                                    <input type="hidden" name="post_type" value="product"
                                           wtx-context="DEBF0DCA-B3BF-45F4-872B-219449C3CBB3">
                                    <select name="category" id="product_cat"
                                            class="cat" tabindex="0"
                                            wtx-context="E8339C9E-E4A6-4809-B265-7B9E247209D2">
                                        <option value="0">All Categories</option>
                                        <option class="level-0" value="bmw">BMW</option>
                                        <option class="level-0" value="uncategorized">Uncategorized</option>
                                        <option class="level-0" value="shop-by-ecu">Shop by ECU</option>
                                        <option class="level-0" value="bmw-ecu-service">BMW ECU Service</option>
                                        <option class="level-0" value="mini-ecu-service">MINI ECU Service</option>
                                        <option class="level-0" value="mini">MINI</option>
                                    </select>
                                    <span class="button-wrap">
          <button class="btn btn-special" title="Search" type="submit"><i
                      class="fas fa-search"></i></button>
        </span>
                                </div>
                            </form>
                        </div>
                        <div id="mini-cart" class="mini-cart minicart-arrow-alt">
                            <div class="cart-head">
                        <span class="cart-icon"><i class="minicart-icon"></i><span
                                    class="cart-items">0</span></span><span class="cart-items-text">0 items</span>
                            </div>
                            <div class="cart-popup widget_shopping_cart">
                                <div class="widget_shopping_cart_content">

                                    <div class="total-count"><span>0 ITEMS</span><a class="pull-right"
                                                                                    href="https://development.the-ecu-pro.com/cart/">VIEW
                                            CART</a></div>
                                    <div class="scroll-wrapper cart_list product_list_widget scrollbar-inner"
                                         style="position: relative;">
                                        <ul class="cart_list product_list_widget scrollbar-inner scroll-content"
                                            style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 0px;">

                                            <li class="woocommerce-mini-cart__empty-message empty">
                                                No products in the cart.
                                            </li>


                                        </ul>
                                        <div class="scroll-element scroll-x">
                                            <div class="scroll-element_outer">
                                                <div class="scroll-element_size"></div>
                                                <div class="scroll-element_track"></div>
                                                <div class="scroll-bar"></div>
                                            </div>
                                        </div>
                                        <div class="scroll-element scroll-y">
                                            <div class="scroll-element_outer">
                                                <div class="scroll-element_size"></div>
                                                <div class="scroll-element_track"></div>
                                                <div class="scroll-bar"></div>
                                            </div>
                                        </div>
                                    </div><!-- end product list -->


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </div>

<div class="page-wrapper">

<?php if (!is_front_page()) { ?>
    <section class="page-top page-header-6">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 clearfix">
                    <div class="breadcrumbs-wrap pt-left">
                        <?php

                        // use yoast breadcrumbs if enabled
                        if (function_exists('yoast_breadcrumb')) {
                            $yoast_breadcrumbs = yoast_breadcrumb('', '', false);
                            if ($yoast_breadcrumbs) {
                                return $yoast_breadcrumbs;
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (is_product()) { ?>
    <div class="align-items-center">
        <ul>
            <li>
                <div class="services-icon">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/car.png' ?>"/>
                </div>
                <h3>FREE FEDEX<br/>SHIPPING</h3>
            </li>
            <li>
                <div class="services-icon">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/warranty.png' ?>"/>
                </div>
                <h3>6 MONTH<br/>WARRANTY</h3>
            </li>
            <li>
                <div class="services-icon">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/secure.png' ?>"/>
                </div>
                <h3>SAFE AND SECURE<br/>SHOPPING</h3>
            </li>
        </ul>
    </div>
<?php } ?>

    <div id="main" class="column1 boxed no-breadcrumbs"><!-- main -->

<?php

$hero_top_element = get_field(
    "page_top_hero_element",
    $queried_object
);

$services_top_element = get_field(
    "page_top_services_element",
    $queried_object
);

if (wp_is_mobile()) {
    $hero_url = get_field(
        "mobile_hero_bg_image",
        $queried_object
    );
    $hero_url = !empty($hero_url) ? $hero_url : '/wp-content/uploads/2021/06/hero-bg-mobile.png';
} else {
    $hero_url = wp_get_attachment_url(get_post_thumbnail_id($queried_object));
    $hero_url = !empty($hero_url) ? $hero_url : '/wp-content/uploads/2021/06/hero-bg.png';
}

if ($hero_top_element) { ?>
    <div id="home-background-image" style="background: url('<?php echo $hero_url; ?>') no-repeat center center;" class="">
        <div class="ecu-filter-wrapper">
            <div id="ecu-mmy-filter-home-decorator"></div>
            <?php

            $ecu_title = get_field("ecu_mmy_title", $queried_object);
            $ecu_title = !empty($ecu_title) ? $ecu_title : 'X5 DME Repairs';

            $ecu_desc = get_field("ecu_mmy_desc", $queried_object);
            $ecu_desc = !empty($ecu_desc) ? $ecu_desc : 'Select your vehicle';

            echo do_shortcode(
                '[ecu-mmy-filter where="home" title="' . $ecu_title . '" desc="' . $ecu_desc . '"]'
            ); ?>
        </div>
    </div>
<?php }

if ($services_top_element) { ?>
    <div class="align-items-center">
        <ul>
            <li>
                <div class="services-icon">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/car.png' ?>"/>
                </div>
                <h3>FREE FEDEX<br/>SHIPPING</h3>
            </li>
            <li>
                <div class="services-icon">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/warranty.png' ?>"/>
                </div>
                <h3>6 MONTH<br/>WARRANTY</h3>
            </li>
            <li>
                <div class="services-icon">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/secure.png' ?>"/>
                </div>
                <h3>SAFE AND SECURE<br/>SHOPPING</h3>
            </li>
        </ul>
    </div>
<?php } ?>

    <div class="outer-parent-container">
    <div class="container">
    <div class="row main-content-wrap">
<?php if (is_product_category() || is_shop() || is_archive()) { ?>
    <div class="sidebar col-lg-3 left-sidebar">
        <div class="sidebar-content">
            <div id="ecu-mmy-filter-footer-wrapper"
                 class="ecu-mmy-filter-wrapper ecu-mmy-filter-footer">
                <div class="ecu-wrapper">
                    <div class="ecu-mmy-filter-title">
                        <h3>Select Your</h3>
                    </div>
                    <div class="ecu-mmy-filter-desc">
                    </div>
                    <p>To view all our repair services</p>
                    <div class="ecu-select-wrapper">
                        <select class="ecu-mmy-filter-selector ecu-make" name="ecu-make"
                                parent="ecu-mmy-filter-footer-wrapper"
                                wtx-context="32DC183F-BEAC-4556-AD5C-BD9EC7D0ACB6">
                            <option slug="" value="0">Select Make</option>
                            <option slug="bmw" value="3920">BMW</option>
                            <option slug="bmw-ecu-service" value="7886" style="display: none;">BMW ECU
                                Service
                            </option>
                            <option slug="mini" value="8066">MINI</option>
                            <option slug="mini-ecu-service" value="7929" style="display: none;">MINI ECU
                                Service
                            </option>
                            <option slug="shop-by-ecu" value="7516">Shop by ECU</option>
                        </select>
                    </div>
                    <div class="ecu-select-wrapper">
                        <select class="ecu-mmy-filter-selector ecu-model" placeholder="Select Model"
                                name="ecu-model" parent="ecu-mmy-filter-footer-wrapper"
                                wtx-context="A561CBD8-C420-40AE-BF96-93463C26410D">
                            <option slug="" value="0">Select Model</option>

                        </select>
                    </div>
                    <div class="ecu-select-wrapper">
                        <select class="ecu-mmy-filter-selector ecu-engine" name="ecu-engine"
                                parent="ecu-mmy-filter-footer-wrapper"
                                wtx-context="8245EF62-20C8-4306-86AF-DBE9848C3998">
                            <option slug="" value="0">Select Engine</option>
                        </select>
                    </div>
                    <div class="ecu-select-wrapper">
                        <select class="ecu-mmy-filter-selector ecu-year" name="ecu-year"
                                parent="ecu-mmy-filter-footer-wrapper"
                                wtx-context="FA44D98E-E547-4097-9943-47CAAB761C44">
                            <option slug="" value="0">Select Year</option>
                        </select>
                    </div>
                    <div class="ecu-mmy-filter-apply">
                        <button parent="ecu-mmy-filter-footer-wrapper"
                                class="ecu-filter-button btn btn-danger red">SHOW REPAIR SERVICES
                        </button>
                    </div>
                    <a class="bottom-cta" href="/ecu-repair-request">Can't find your vehicle?</a>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content col-lg-9">
    <?php } else { ?>
    <div class="main-content col-lg-12">
<?php } ?>


    <div id="main" class="column1 boxed no-breadcrumbs"><!-- main -->
        <div class="container">
            <div class="row main-content-wrap">

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

                                while (have_rows('dynamic_flexible_content')) : the_row();

                                    // Grab the current layout in loop
                                    $layout                     = get_row_layout();

                                    switch ($layout) {
                                        case 'how_it_works_section':
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
                                        case 'recommended_service_section':
                                            $recommended_title = get_sub_field(
                                                'recommended_service_title'
                                            );
                                            $recommended_title  = !empty($recommended_title) ? $recommended_title : '';

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

                                            <?php break;
                                        case 'service_products_section':

                                            $services_title = get_sub_field(
                                                'services_title'
                                            );
                                            $services_title     = !empty($services_title) ? $services_title : '';

                                            $services_product_term_id = get_sub_field(
                                                'product_category'
                                            );
                                            $services_product_term_id = !empty($services_product_term_id) ? $services_product_term_id : '';

                                            if (!empty($services_title) && !empty($services_product_term_id)) {

                                                ?>
                                                <div class="our-service-outer-container homepage-section-outer">
                                                    <div class="our-service-outer-container-inner">
                                                        <h2 class="homepage-section-title"><?php echo $services_title; ?></h2>
                                                        <div class="col-lg-12 white-bg">
                                                            <div class="products-outer">
                                                                <?php

                                                                $term = get_term(
                                                                    $services_product_term_id,
                                                                    'product_cat'
                                                                );
                                                                $slug = $term->slug;

                                                                $products = wc_get_products(
                                                                    [
                                                                        'category'    => array($slug),
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
                                                                    if (!empty($content_post->get_price())) {
                                                                        echo '<span class="price">$' . $content_post->get_price(
                                                                            ) . '</span>';
                                                                    }
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
                                                <?php

                                            }

                                            break;
                                        case 'more_info_section':

                                            $info_title = get_sub_field(
                                                'more_info_title'
                                            );
                                            $info_title         = !empty($info_title) ? $info_title : '';

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

                                endwhile;
                            endif;

                            ?>

                        </div>
                    </article>
                </div>

                <div class="sidebar-overlay"></div>

            </div>
        </div>
    </div>

<?php get_footer(); ?>
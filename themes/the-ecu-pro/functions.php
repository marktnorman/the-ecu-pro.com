<?php

include_once 'inc/functions/forms.php';
include_once 'inc/functions/operating-hours.php';

// THEME SETUP
if (!function_exists('theecupro_setup')) {

    function theecupro_setup()
    {

        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');

        #=======
        # MENUS
        #=======
        register_nav_menus(
            array(
                'main_menu'         => __('Main Menu', 'theecupro'),
                'secondary_menu'    => __('Secondary Menu', 'theecupro'),
                'sidebar_menu'      => __('Sidebar Menu', 'theecupro'),
                'top_nav'           => __('Top Navigation', 'theecupro'),
                'view_switcher'     => __('View Switcher', 'theecupro'),
                'currency_switcher' => __('Currency Switcher', 'theecupro'),
            )
        );

        #===================
        # REGISTER SIDEBARS
        #===================
        register_sidebar(
            array(
                'name'          => __('Blog Sidebar', 'theecupro'),
                'id'            => 'blog-sidebar',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Home Sidebar', 'theecupro'),
                'id'            => 'home-sidebar',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Secondary Sidebar', 'theecupro'),
                'id'            => 'secondary-sidebar',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        if (class_exists('Woocommerce')) {

            register_sidebar(
                array(
                    'name'          => __('Woo Category Sidebar', 'theecupro'),
                    'id'            => 'woo-category-sidebar',
                    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                )
            );

            register_sidebar(
                array(
                    'name'          => __('Woo Category Filter', 'theecupro'),
                    'id'            => 'woo-category-filter-sidebar',
                    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                )
            );

            register_sidebar(
                array(
                    'name'          => __('Woo Product Sidebar', 'theecupro'),
                    'id'            => 'woo-product-sidebar',
                    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                )
            );

        }

        register_sidebar(
            array(
                'name'          => __('Content Bottom Widget 1', 'theecupro'),
                'id'            => 'content-bottom-1',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Content Bottom Widget 2', 'theecupro'),
                'id'            => 'content-bottom-2',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Content Bottom Widget 3', 'theecupro'),
                'id'            => 'content-bottom-3',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Content Bottom Widget 4', 'theecupro'),
                'id'            => 'content-bottom-4',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Footer Top Widget', 'theecupro'),
                'id'            => 'footer-top',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Footer Widget 1', 'theecupro'),
                'id'            => 'footer-column-1',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Footer Widget 2', 'theecupro'),
                'id'            => 'footer-column-2',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Footer Widget 3', 'theecupro'),
                'id'            => 'footer-column-3',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Footer Widget 4', 'theecupro'),
                'id'            => 'footer-column-4',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );

        register_sidebar(
            array(
                'name'          => __('Footer Bottom Widget', 'theecupro'),
                'id'            => 'footer-bottom',
                'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                'after_widget'  => '</aside>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            )
        );
    }
}

add_action('after_setup_theme', 'theecupro_setup');

// Update CSS within admin area
add_action('admin_enqueue_scripts', 'child_theme_admin_styles');
function child_theme_admin_styles()
{
    wp_enqueue_style('admin-styles', get_stylesheet_directory_uri() . '/css/admin-style.css', '', '5.1.9');
}


add_action('wp_enqueue_scripts', 'theecupro_enqueue_assets');
function theecupro_enqueue_assets()
{
    $version = '11.3.3';

    // CARRY ON
    wp_enqueue_style('theecupro-default-style', get_stylesheet_uri());

    wp_enqueue_style(
        'theecupro-custom-style',
        get_stylesheet_directory_uri() . '/css/style-custom.css',
        [],
        $version
    );

    wp_enqueue_style(
        'theecupro-responsive-style',
        get_stylesheet_directory_uri() . '/css/style-responsive.css',
        ['theecupro-custom-style', 'theecupro-theme-style'],
        $version
    );

    wp_enqueue_style(
        'theecupro-theme-style',
        get_stylesheet_directory_uri() . '/css/theme.css',
        [],
        $version
    );

    wp_enqueue_style(
        'theecupro-bootstrap-style',
        get_stylesheet_directory_uri() . '/css/bootstrap.css',
        [],
        $version
    );

    wp_enqueue_script(
        'theecupro-lapform-js',
        get_template_directory_uri() . '/js/lapForm.js',
        [],
        $version,
        true
    );
    wp_enqueue_script(
        'theecupro-main-js',
        get_stylesheet_directory_uri() . '/js/scripts.js',
        array('jquery', 'theecupro-magnific-js'),
        $version,
        true
    );
    wp_enqueue_script(
        'theecupro-magnific-js',
        get_stylesheet_directory_uri() . '/js/jquery.magnific-popup.min.js',
        array('jquery'),
        $version,
        true
    );

    //data for ajax request
    wp_localize_script(
        'theecupro-main-js',
        'ecu_ajax_object',
        array(
            'nonce'     => wp_create_nonce('ajax-nonce'),
            'child_url' => get_stylesheet_directory_uri(),
            'site_url'  => site_url(),
            'ajax_url'  => admin_url('admin-ajax.php')
        )
    );

    wp_enqueue_script(
        'jquery-validate-min',
        get_stylesheet_directory_uri() . '/js/jquery.validate.min.js',
        array('jquery')
    );

    wp_enqueue_script(
        'ecu_select2_js',
        esc_url(get_stylesheet_directory_uri() . '/js/select2.min.js'),
        array('jquery'),
        null,
        true
    );

    if (is_page('frm-repair-request')) {
        wp_enqueue_script(
            'sales-integration',
            get_stylesheet_directory_uri() . '/js/salesiqintegration.js',
            array('jquery')
        );

        wp_enqueue_script(
            'sales-validation',
            get_stylesheet_directory_uri() . '/js/validation.js',
            array('jquery')
        );

        wp_enqueue_style(
            'zf-styles',
            get_stylesheet_directory_uri() . '/css/zf-styles.css',
            ['theecupro-custom-style', 'theecupro-theme-style'],
            $version
        );

        // Old FRM backup
        //<iframe style="height: 1030px; width: 99%; border: none;" src="https://forms.zohopublic.com/theecupro/form/FRMRepairNeworder/formperma/quq2ae6TdZGo3g94TBVPueElGeCce-X5j2d0lH8YEQU" frameborder="0"></iframe>
    }

    if (is_page('new-ecu-order')) {
        wp_enqueue_script(
            'dme-sales-integration',
            get_stylesheet_directory_uri() . '/js/dme-salesiqintegration.js',
            array('jquery')
        );

        wp_enqueue_script(
            'dme-sales-validation',
            get_stylesheet_directory_uri() . '/js/dme-validation.js',
            array('jquery')
        );

        wp_enqueue_style(
            'zf-dme-styles',
            get_stylesheet_directory_uri() . '/css/zf-dme-styles.css',
            ['theecupro-custom-style', 'theecupro-theme-style'],
            $version
        );

        // Old DME backup
        // <iframe frameborder="0" style="height:2040px;width:99%;border:none;" src='https://forms.zohopublic.com/theecupro/form/DMERepairNeworder/formperma/5WeBn72nrEr45AnXDVHTWf7cUHi7-e_zAJe_a3gsaAQ'></iframe>

    }

}

add_action('init', 'include_dependencies');
function include_dependencies()
{
    require_once get_stylesheet_directory() . '/inc/shortcodes/ecu-mmy-filter.php';
    require_once get_stylesheet_directory() . '/inc/functions/add-product-to-cart.php';
    require_once get_stylesheet_directory() . '/inc/functions/clear-cart.php';
    require_once get_stylesheet_directory() . '/inc/functions/shipping-selection-product.php';
    require_once get_stylesheet_directory() . '/inc/functions/billing-field-customization.php';
    require_once get_stylesheet_directory() . '/inc/functions/shipping-initiator.php';
    require_once get_stylesheet_directory() . '/inc/functions/get-checkout-total-value.php';
    require_once get_stylesheet_directory() . '/inc/functions/auto-user-auth.php';
    //require_once get_stylesheet_directory() . '/inc/functions/product-generation.php';
    require_once get_stylesheet_directory() . '/inc/functions/product-meta-frontend-update.php';
    require_once get_stylesheet_directory() . '/inc/functions/generate-short-description.php';
    require_once get_stylesheet_directory() . '/inc/functions/generate-bottom-description.php';
}

/**
 * @param $id
 *
 * @return string
 */
function getHomepageProduct($id): string
{
    if (!$id) {
        exit;
    }

    $product = wc_get_product($id);
    if (file_exists(get_attached_file($product->get_image_id()))) {
        $image = '<img src="' . wp_get_attachment_image_url($product->get_image_id(), 'full') . '" />';
    } else {
        $image = "<img src='" . wp_get_upload_dir()['baseurl'] . "/woocommerce-placeholder-200x150.png' />";
    }

    $html = '
        <ul class="products">
            <li class="product-col product-default">
            <div class="product-inner">
                <div class="product-image">
                    <a href="' . $product->get_permalink() . '">
                        ' . $image . '
                    </a>
                </div>
                <div class="product-content">
                    <a class="product-loop-title" href="' . $product->get_permalink() . '">
                        <h3 class="woocommerce-loop-product__title">' . $product->get_name() . '</h3>
                    </a>
                    <span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>' . $product->get_price(
        ) . '</span></span>
                    <div class="add-links-wrap">
                        <div class="add-links clearfix">
                            <a href="' . $product->get_permalink(
        ) . '" data-quantity="1" class="viewcart-style-2 button product_type_simple add_to_cart_button" data-product_id="' . $id . '" rel="nofollow">Select options</a>
                            <div class="quickview" data-id="' . $id . '" title="Quick View">Quick View</div>
                        </div>
                    </div>
                </div>
            </div>
            </li>
        </ul>
    ';

    return $html;
}

// get categories: Model/Make/Year/Engine
function getFilterCategories($parent)
{
    $args = array(
        'taxonomy'     => 'product_cat',
        'orderby'      => 'name',
        'order'        => 'asc',
        'hide_empty'   => false,
        'hierarchical' => false,
        'parent'       => (int) $parent,
    );
    return get_terms($args);
}

add_action('wp_ajax_get_ajax_products', 'get_ajax_products');
add_action('wp_ajax_nopriv_get_ajax_products', 'get_ajax_products');

function get_ajax_products($category)
{
    $categoryPost = $_POST['category'];
    $args         = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page'      => 5,
        'tax_query'           => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug', //This is optional, as it defaults to 'term_id'
                'terms'    => getHierarchicalTermTree($categoryPost),
                'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
            ),
        )
    );
    $products     = new WP_Query($args);

    $html = '
        <div class="col-12 col-md-6">
        <div class="row">
            <div class="col-6">' . getHomepageProduct($products->posts[0]->ID) . '</div>
            <div class="col-6">' . getHomepageProduct($products->posts[1]->ID) . '</div>
        </div>
        </div>
        <div class="col-12 col-md-6">
        <div class="row">
            <div class="col-6">' . getHomepageProduct($products->posts[2]->ID) . '</div>
            <div class="col-6">' . getHomepageProduct($products->posts[3]->ID) . '</div>
        </div>
        </div>
    ';

    if (count($products->posts) > 4) {
        $topCatTerm = get_term_by('slug', $categoryPost, 'product_cat');
        $topCatID   = $topCatTerm->term_id;
        $html       .= '<div class="col-12 text-center"><h4><a href="' . get_term_link(
                $topCatID,
                'product_cat'
            ) . '">All products...</a></h4></div>';
    }

    wp_send_json($html);
    wp_die();
}

function getHierarchicalTermTree($categorySlug)
{
    $topCatTerm = get_term_by('slug', $categorySlug, 'product_cat');
    $topCatID   = $topCatTerm->term_id;
    $categories = [];
    array_push($categories, $categorySlug);
    if ($topCatTerm) {
        $next = getChildCategories($topCatID);
        if ($next) {
            $count = 0;
            foreach ($next as $cat) {
                $count++;
                if ($count > 40) {
                    break;
                }
                array_push($categories, $cat->slug);

                if (getChildCategories($cat->term_id)) {
                    foreach (getChildCategories($cat->term_id) as $cat1) {
                        $count++;
                        if ($count > 40) {
                            break;
                        }
                        array_push($categories, $cat1->slug);

                        if (getChildCategories($cat1->term_id)) {
                            foreach (getChildCategories($cat1->term_id) as $cat2) {
                                $count++;
                                if ($count > 40) {
                                    break;
                                }
                                array_push($categories, $cat2->slug);
                            }
                        }
                    }
                }
            }
        }
    }

    return $categories;
}

function getChildCategories($parentId)
{
    $childrens = get_terms('product_cat', ['parent' => $parentId]);
    if ($childrens) {
        return $childrens;
    } else {
        return false;
    }
}

if (!function_exists('write_log')) {

    function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/**
 * descriptionDataCallback
 *
 * @throws Exception
 */
function descriptionDataCallback()
{
    echo generate_product_bottom_description();
}

function technicalDataCallback()
{ ?>
    <table class="table tech-data-table">
        <tbody>
        <tr class="s-row">
            <td class="name s-grid-4">Part</td>
            <td class="value">Engine Control Unit (ECU) or Digital Motor Electronics Control Unit (DME)</td>
        </tr>
        <tr class="s-row">
            <td class="name s-grid-4">Supported Part Numbers</td>
            <td class="value">All ECU Part numbers</td>
        </tr>
        <tr class="s-row">
            <td class="name s-grid-4">Warranty</td>
            <td class="value">1-Year</td>
        </tr>
        <tr class="s-row">
            <td class="name s-grid-4">Supported ECU Numbers</td>
            <td class="value">All</td>
        </tr>
        <tr class="s-row">
            <td class="name s-grid-4">Supported Brands</td>
            <td class="value">Siemens, Bosch, Continental</td>
        </tr>
        <tr class="s-row">
            <td class="name s-grid-4">Options Available</td>
            <td class="value">Testing, Repaired, Virgin or Exchange</td>
        </tr>
        <tr class="s-row">
            <td class="name s-grid-4">Condition</td>
            <td class="value">Refurbished</td>
        </tr>
        </tbody>
    </table>
    <?php
}

function contactUsCallback()
{
    //echo do_shortcode('[contact-form-7 id="1846"]');
    global $tecup_form_error, $tecup_form_success;
    $which_form = 'contact-form';
    formMsg($which_form, false);
    ?>
    <form id="<?= $which_form ?>" name="form" class="label-placeholder-form" data-js-ref="lap-form" method="post">
        <input type="hidden" name="which-form" value="<?= $which_form ?>">
        <input type="hidden" name="tecup-action" value="save_contact_message">
        <div class="field <?php field_error_class($tecup_form_error, $which_form, 'cname', 'required'); ?>">
            <label id="contact-label-name" for="contact-name">Your name</label>
            <input id="contact-name" type="text" maxlength="255" name="cname" data-js-ref="lap-form-input"
                   data-js-label-id="contact-label-name" value="<?= $_POST['cname'] ?? '' ?>">
        </div>
        <div class="field <?php field_error_class($tecup_form_error, $which_form, 'email', 'email'); ?>">
            <label id="contact-label-email" for="contact-email">Your email</label>
            <input id="contact-email" data-js-ref="lap-form-input" data-js-label-id="contact-label-email" type="email"
                   maxlength="255" name="email" value="<?= $_POST['email'] ?? '' ?>">
        </div>
        <div class="field">
            <label id="ctvnf-label-phone" for="ctvnf-phone">Your phone number</label>
            <input id="ctvnf-phone" data-js-ref="lap-form-input" data-js-label-id="ctvnf-label-phone" type="text"
                   name="phone" value="<?= $_POST['phone'] ?? '' ?>">
        </div>
        <div class="field <?php field_error_class($tecup_form_error, $which_form, 'subject', 'required'); ?>">
            <label id="contact-label-vehicle" for="contact-vehicle">Make, Model, Engine, Year</label>
            <input id="contact-vehicle" type="text" maxlength="255" name="vehicle" data-js-ref="lap-form-input"
                   data-js-label-id="contact-label-vehicle" value="<?= $_POST['vehicle'] ?? '' ?>">
        </div>
        <div class="field">
            <textarea id="contact-msg" name="message"
                      class="p-3 <?php field_error_class($tecup_form_error, $which_form, 'message', 'required'); ?>"
                      placeholder="Message"><?= $_POST["message"] ?? '' ?></textarea>
        </div>
        <div class="field">
            <button type="submit" class="button lap-form-button text-center" data-js-ref="lap-form-submit">Send
                message
            </button>
        </div>
    </form>
<?php }

function ShippingReturnsCallback()
{ ?>
    <div class="shipping-block"><h2><strong>Shipping within the US</strong></h2>
        <p>All items come with free 48-72 hour FedEx shipping. Additional FedEx shipping options are available on
            the
            checkout page should you require faster shipping.</p>

        <table class="table tech-data-table" style="height: 83px;" width="627">
            <tbody>
            <tr class="s-row">
                <td class="name s-grid-4">48-72 hour deliver</td>
                <td class="value">Free</td>
            </tr>
            <tr class="s-row">
                <td class="name s-grid-4">24-48 hour delivery</td>
                <td class="value">$50</td>
            </tr>
            <tr class="s-row">
                <td class="name s-grid-4">Overnight delivery</td>
                <td class="value">$75</td>
            </tr>
            </tbody>
        </table>
        <h2><strong>International Shipping</strong></h2>

        <p>International shipments are either shipped with FedEx or DHL. Shipping options for international
            shipments
            are
            available on the checkout page.</p>

        <table class="table tech-data-table" style="height: 83px;" width="627">
            <tbody>
            <tr class="s-row">
                <td class="name s-grid-4">DHL 2-3 day shipping</td>
                <td class="value">&nbsp;$125</td>
            </tr>
            <tr class="s-row">
                <td class="name s-grid-4">DHL 4-6 day shipping</td>
                <td class="value">$70</td>
            </tr>
            <tr class="s-row">
                <td class="name s-grid-4">FedEx 2-3 day shipping</td>
                <td class="value">$150</td>
            </tr>
            <tr class="s-row">
                <td class="name s-grid-4">FedEx 4-6 day shipping</td>
                <td class="value">$100</td>
            </tr>
            </tbody>
        </table>
        <h2><strong>Returns</strong></h2>
        <p>All items come with a warranty. Warranty does not include any labor associated with installation and/or
            removal
            of parts, key and/or locksmith fees. Buyer will not be reimbursed for any such fees--NO EXCEPTIONS.
            Buyer
            hereby
            acknowledges and agrees that the Seller's liability is limited to the price of the item sold and Seller
            is
            not
            liable for any damage and/or injury sustained that results from any item(s) sold by any entity operated
            by
            The
            ECU Pro, LLC and Buyer hereby now and forever relinquish seller from any such liability.</p>

        <p>Electrical parts are tested prior to purchase and if returned, all units will be inspected for burnt
            components,
            physical damage and water damage. Returns will be processed in the order received and may have a greater
            handling time than order processing. The warranty shall be void if an item is returned with any signs of
            (a)
            burnt components (b)physical and/or water damage (c)misuse, abuse, modifications, opened, tampered with,
            and/or
            used for any purpose not originally intended (d)vehicle is involved in a collision or (e)security seal
            is
            removed, broken and/or damaged.</p>

        <p>To issue a return, please follow the<a href="https://www.the-ecu-pro.com/return-policy/"> instructions in
                our
                official return policy.&nbsp;</a></p>
    </div>
<?php }

function WarrantyCallback()
{ ?>
    <div class="warranty-block"><h2><strong>Warranty&nbsp;</strong></h2>
        <p>All items come with a warranty. Warranty does not include any labor associated with installation and/or
            removal
            of parts, key and/or locksmith fees. Buyer will not be reimbursed for any such fees--NO EXCEPTIONS.
            Buyer
            hereby
            acknowledges and agrees that the Seller's liability is limited to the price of the item sold and Seller
            is
            not
            liable for any damage and/or injury sustained that results from any item(s) sold by any entity operated
            by
            The
            ECU Pro, LLC and Buyer hereby now and forever relinquish seller from any such liability.</p>


        <h2><strong>Returns</strong></h2>
        <p>Electrical parts are tested prior to purchase and if returned, all units will be inspected for burnt
            components,
            physical damage and water damage. Returns will be processed in the order received and may have a greater
            handling time than order processing. The warranty shall be void if an item is returned with any signs of
            (a)
            burnt components (b)physical and/or water damage (c)misuse, abuse, modifications, opened, tampered with,
            and/or
            used for any purpose not originally intended (d)vehicle is involved in a collision or (e)security seal
            is
            removed, broken and/or damaged.</p>

        <p>To issue a return, please follow the<a href="https://www.the-ecu-pro.com/return-policy/"> instructions in
                our
                official return policy.&nbsp;</a></p>
    </div>
<?php }

add_filter('woocommerce_product_tabs', 'additional_product_tabs');
/**
 * Add 2 custom product data tabs
 */
function additional_product_tabs($tabs)
{
    global $tecup_form_error, $tecup_form_success;
    $contact_tab_priority = $tecup_form_error || $tecup_form_success ? 35 : 55;
    unset($tabs['additional_information']);
    unset($tabs['technical_data']);

    $tabs['description'] = array(
        'title'    => __('Description', 'woocommerce'),
        'priority' => 45,
        'callback' => 'descriptionDataCallback'
    );

    $tabs['contact_us'] = array(
        'title'    => __('Contact us', 'woocommerce'),
        'priority' => $contact_tab_priority,
        'callback' => 'contactUsCallback'
    );

    $tabs['shipping_returns'] = array(
        'title'    => __('Shipping & Returns', 'woocommerce'),
        'priority' => 60,
        'callback' => 'ShippingReturnsCallback'
    );

    $tabs['warranty'] = array(
        'title'    => __('Warranty', 'woocommerce'),
        'priority' => 65,
        'callback' => 'WarrantyCallback'
    );

    return $tabs;

}

// Remove add to cart message
add_filter('wc_add_to_cart_message_html', '__return_false');

// Remove sku check
add_filter('wc_product_has_unique_sku', '__return_false');

/**
 * @param $query
 *
 * @return mixed
 */
function advanced_search_query($query)
{

    if ($query->is_search()) {
        // category terms search.
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $query->set(
                'tax_query',
                array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => array($_GET['category'])
                    )
                )
            );
        }
    }
    return $query;
}

//add_action('pre_get_posts', 'advanced_search_query', 1000);

// Replace add to cart button by a linked button to the product in Shop and archives pages
add_filter('woocommerce_loop_add_to_cart_link', 'replace_loop_add_to_cart_button', 10, 2);

/**
 * @param $button
 * @param $product
 *
 * @return mixed|string
 */
function replace_loop_add_to_cart_button($button, $product)
{
    if (is_archive()) {
        // Not needed for variable products
        if ($product->is_type('variable')) {
            return $button;
        }

        // Button text here
        $button_text = __("View product", "woocommerce");

        return '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
    }
}

add_filter('site_transient_update_plugins', 'remove_update_notification');
function remove_update_notification($value)
{
    unset($value->response["woocommerce-email-template-customizer/woocommerce-email-template-customizer.php"]);
    return $value;
}

// Custom email message to order email
add_action('woocommerce_email_before_order_table', 'woocommerce_email_before_order_table_func');
function woocommerce_email_before_order_table_func($order)
{

    $order       = wc_get_order($order->get_id());
    $items       = $order->get_items();
    $productType = '';
    $prodCnt     = 0;
    foreach ($items as $item) {
        $product_id  = $item->get_product_id();
        $productType .= ($prodCnt <= 0) ? get_field("product_type", $product_id) : ',' . get_field(
                "product_type",
                $product_id
            );
        $prodCnt++;
    }

    if ($productType == 'FRMs') {
        $orderLink = '/frm-repair-request/';
    } else {
        $orderLink = '/new-ecu-order/';
    }

    $custom_message = '<div style="margin-bottom: 20px;"><table class="td order-form-table" cellspacing="0" cellpadding="4" style="background-color: #e2e2e2; margin: 20px 0px 30px 0; border: 0;">
				<tr>
					<th class="td" scope="col" style="text-align:center; line-height: 40px;">
					<p style="color: red;">To complete your order please complete the online form:<br />
					<a class="thank-you-form-button" href="' . $orderLink . '" target="_blank" style="margin: 20px; color: #fff !important; padding: 5px 20px; background: #d21f1f; border-radius: 7px; font-weight: bold; text-transform: uppercase; width: auto; display: inline-block; text-align: center;">Complete online work order</a><br />
					Please complete the form before sending your parts.</p>
					</th>
				</tr>
			</table></div>';

    add_post_meta($order->get_id(), 'custom_message', $custom_message, true);

}

/**
 * @param $woocommerce_get_sidebar
 * @param $int
 */
function action_woocommerce_sidebar($woocommerce_get_sidebar, $int)
{
    dynamic_sidebar('woo-category-sidebar');
}

// add the action
//add_action('woocommerce_sidebar', 'action_woocommerce_sidebar', 10, 2);

/**
 * retrieves the attachment ID from the title
 *
 * @param $title
 *
 * @return array
 */
function get_image_id($title): array
{
    global $wpdb;

    // Lets remove the image extension
    $image_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $title);

    // Lets check the first table postmeta
    $image_id = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT post_id AS ID FROM $wpdb->postmeta WHERE meta_value LIKE '%s' AND meta_key = '_wp_attached_file' LIMIT 1;",
            '%' . $image_name . '%'
        )
    );

    // If we didn't find anything the image might have meta attached to it in the posts table
    if (empty($image_id)) {
        $image_id = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID FROM $wpdb->posts WHERE post_title LIKE '%s' AND post_type = 'attachment' LIMIT 1;",
                '%' . $image_name . '%'
            )
        );
    }

    return $image_id;
}

/**
 * Create a product variation for a defined variable product ID.
 *
 * @param $product_id
 * @param $variation_data
 * @param $variation_type
 *
 * @throws WC_Data_Exception
 */

function create_product_variation($product_id, $variation_data, $variation_type)
{
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post_args = array(
        'post_title'  => $product->get_name() . ' ' . $variation_type,
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post($variation_post_args);

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation($variation_id);

    $logData = array(
        'variation_object' => $variation
    );

    write_log($logData);

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name) {
        $taxonomy = 'pa_' . $attribute; // The attribute taxonomy

        $term = get_term_by('name', $term_name, $taxonomy);

        // Get the post Terms names from the parent variable product.
        $post_term_names = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'names'));

        // Check if the post term exist and if not we set it in the parent variable product.
        if (!in_array($term_name, $post_term_names)) {
            wp_set_post_terms($product_id, $term->term_id, $taxonomy, true);
        }

        // Set/save the attribute data in the product variation
        update_post_meta($variation_id, 'attribute_' . $taxonomy, $term->slug);
    }

    // Set/save all other data

    // SKU
    if (!empty($variation_data['sku'])) {
        $variation->set_sku($variation_data['sku']);
    }

    // Prices
    if (empty($variation_data['sale_price'])) {
        $variation->set_price($variation_data['regular_price']);
    } else {
        $variation->set_price($variation_data['sale_price']);
        $variation->set_sale_price($variation_data['sale_price']);
    }
    $variation->set_regular_price($variation_data['regular_price']);

    // Stock
    if (!empty($variation_data['stock_qty'])) {
        $variation->set_stock_quantity($variation_data['stock_qty']);
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }

    update_post_meta($variation_id, 'variation_title', $product->get_name() . ' ' . $variation_type);

    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}

/**
 * Custom function for product creation
 *
 * @param $args
 *
 * @return false|int
 * @throws WC_Data_Exception
 */
function create_product($args)
{

    if (!function_exists('wc_get_product_object_type') && !function_exists('wc_prepare_product_attributes')) {
        return false;
    }

    // Get an empty instance of the product object (defining it's type)
    $product = wc_get_product_object_type($args['type']);
    if (!$product) {
        return false;
    }

    // Product name (Title) and slug
    $product->set_name($args['name']); // Name (title).
    if (isset($args['slug'])) {
        $product->set_name($args['slug']);
    }

    // Description and short description:
    $product->set_description($args['description']);
    $product->set_short_description($args['short_description']);

    // Status ('publish', 'pending', 'draft' or 'trash')
    $product->set_status(isset($args['status']) ? $args['status'] : 'publish');

    // Visibility ('hidden', 'visible', 'search' or 'catalog')
    $product->set_catalog_visibility(isset($args['visibility']) ? $args['visibility'] : 'visible');

    // Featured (boolean)
    $product->set_featured(isset($args['featured']) ? $args['featured'] : false);

    // Virtual (boolean)
    $product->set_virtual(isset($args['virtual']) ? $args['virtual'] : false);

    // Prices
    $product->set_regular_price($args['regular_price']);
    $product->set_sale_price(isset($args['sale_price']) ? $args['sale_price'] : '');
    $product->set_price(isset($args['sale_price']) ? $args['sale_price'] : $args['regular_price']);
    if (isset($args['sale_price'])) {
        $product->set_date_on_sale_from(isset($args['sale_from']) ? $args['sale_from'] : '');
        $product->set_date_on_sale_to(isset($args['sale_to']) ? $args['sale_to'] : '');
    }

    // Downloadable (boolean)
    $product->set_downloadable(isset($args['downloadable']) ? $args['downloadable'] : false);
    if (isset($args['downloadable']) && $args['downloadable']) {
        $product->set_downloads(isset($args['downloads']) ? $args['downloads'] : array());
        $product->set_download_limit(isset($args['download_limit']) ? $args['download_limit'] : '-1');
        $product->set_download_expiry(isset($args['download_expiry']) ? $args['download_expiry'] : '-1');
    }

    // Taxes
    if (get_option('woocommerce_calc_taxes') === 'yes') {
        $product->set_tax_status(isset($args['tax_status']) ? $args['tax_status'] : 'taxable');
        $product->set_tax_class(isset($args['tax_class']) ? $args['tax_class'] : '');
    }

    // SKU and Stock (Not a virtual product)
    if (isset($args['virtual']) && !$args['virtual']) {
        $product->set_sku(isset($args['sku']) ? $args['sku'] : '');
        $product->set_manage_stock(isset($args['manage_stock']) ? $args['manage_stock'] : false);
        $product->set_stock_status(isset($args['stock_status']) ? $args['stock_status'] : 'instock');
        if (isset($args['manage_stock']) && $args['manage_stock']) {
            $product->set_stock_status($args['stock_qty']);
            $product->set_backorders(
                isset($args['backorders']) ? $args['backorders'] : 'no'
            ); // 'yes', 'no' or 'notify'
        }
    }

    // Sold Individually
    $product->set_sold_individually(isset($args['sold_individually']) ? $args['sold_individually'] : false);

    // Weight, dimensions and shipping class
    $product->set_weight(isset($args['weight']) ? $args['weight'] : '');
    $product->set_length(isset($args['length']) ? $args['length'] : '');
    $product->set_width(isset($args['width']) ? $args['width'] : '');
    $product->set_height(isset($args['height']) ? $args['height'] : '');
    if (isset($args['shipping_class_id'])) {
        $product->set_shipping_class_id($args['shipping_class_id']);
    }

    // Upsell and Cross sell (IDs)
    $product->set_upsell_ids(isset($args['upsells']) ? $args['upsells'] : '');
    $product->set_cross_sell_ids(isset($args['cross_sells']) ? $args['upsells'] : '');

    // Attributes et default attributes
    if (isset($args['attributes'])) {
        $product->set_attributes(wc_prepare_product_attributes($args['attributes']));
    }
    if (isset($args['default_attributes'])) {
        $product->set_default_attributes($args['default_attributes']);
    } // Needs a special formatting

    // Reviews, purchase note and menu order
    $product->set_reviews_allowed(isset($args['reviews']) ? $args['reviews'] : false);
    $product->set_purchase_note(isset($args['note']) ? $args['note'] : '');
    if (isset($args['menu_order'])) {
        $product->set_menu_order($args['menu_order']);
    }

    // Product categories and Tags
    if (isset($args['category_ids'])) {
        $product->set_category_ids($args['category_ids']);
    }
    if (isset($args['tag_ids'])) {
        $product->set_tag_ids($args['tag_ids']);
    }

    return $product->save();
}

/**
 *
 * Utility function that returns the correct product object instance
 *
 * @param $type
 *
 * @return false|WC_Product|WC_Product_External|WC_Product_Grouped|WC_Product_Simple|WC_Product_Variable
 */
function wc_get_product_object_type($type)
{
    // Get an instance of the WC_Product object (depending on his type)
    if (isset($type) && $type === 'variable') {
        $product = new WC_Product_Variable();
    } elseif (isset($type) && $type === 'grouped') {
        $product = new WC_Product_Grouped();
    } elseif (isset($type) && $type === 'external') {
        $product = new WC_Product_External();
    } else {
        $product = new WC_Product_Simple(); // "simple" By default
    }

    if (!is_a($product, 'WC_Product')) {
        return false;
    } else {
        return $product;
    }
}

/**
 * insert_term
 *
 * @param       $term
 * @param       $taxonomy
 * @param array $args
 *
 * @return array|int[]|WP_Error
 */
function insert_term($term, $taxonomy, $args = array())
{
    if (isset($args['parent'])) {
        $parent = $args['parent'];
    } else {
        $parent = 0;
    }
    $result = term_exists($term, $taxonomy, $parent);
    if ($result == false || $result == 0) {
        return wp_insert_term($term, $taxonomy, $args);
    } else {
        return (array) $result;
    }
}

add_filter('pre_get_posts', 'remove_variations_pre_get_posts_query');

/**
 * @param $query
 */
function remove_variations_pre_get_posts_query($query)
{
    if (!is_admin() && is_archive() && $query->is_main_query()) {
        // Not a query for an admin page.
        // It's the main query for a front end page of your site.

        if (is_archive()) {
            $meta_query   = $query->get('meta_query');
            $meta_query[] = array(
                'key'     => 'attribute_pa_variation',
                'compare' => 'NOT EXISTS',
            );

            $query->set('meta_query', $meta_query);
        }
    }
}

add_action('init', 'work_order_form_redirection');
function work_order_form_redirection()
{
    if (isset($_GET['work-order-initiation']) && isset($_GET['order-id'])) {

        $order = wc_get_order($_GET['order-id']);

        if (!empty($order)) {
            $items       = $order->get_items();
            $productType = '';

            foreach ($items as $item) {
                $product_id  = $item->get_product_id();
                $productType = get_field("product_type", $product_id);
            }

            if ($productType == 'FRMs') {
                wp_redirect('https://the-ecu-pro.com/frm-repair-request/');
                exit;
            } else {
                wp_redirect('https://the-ecu-pro.com/new-ecu-order/');
                exit;
            }
        }
    }
}

//add_action('template_redirect', 'redirect_host_correction');
/**
 * Lets redirect if a user was taking to dev site from live
 */
function redirect_host_correction()
{
    $referred_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

    // If we're on the same site do nothing
    if ($_SERVER['HTTP_HOST'] == $referred_host || $referred_host == 'NULL') {
        return;
    }

    $live_allowed_host     = 'the-ecu-pro.com';
    $referred_allowed_host = 'development.the-ecu-pro.com';
    $referred_host         = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    $sub_str               = substr($referred_host, 0 - strlen($live_allowed_host));

    // If the referred host is development redirect to live
    if (($sub_str == $live_allowed_host) && ($referred_host != $referred_allowed_host)) {
        $updated_location = str_replace(
            $referred_host,
            $live_allowed_host,
            $_SERVER['HTTP_REFERER'] . $_SERVER['REQUEST_URI']
        );

        wp_redirect($updated_location);
        exit;
    }
}

// Disabling Glutenberg
add_filter('use_block_editor_for_post', '__return_false');


add_filter('body_class', 'dummy_checkout_woo_body_class');
/**
 * @param $classes
 * Custom classes for styling
 *
 * @return mixed
 */
function dummy_checkout_woo_body_class($classes)
{
    if (is_page('frm-tag-creation') || is_page('dme-tag-creation')) {
        $classes[] = 'woocommerce-checkout';
        $classes[] = 'woocommerce-page';
        $classes[] = 'woocommerce-order-received';
        $classes[] = 'woocommerce-js';
    }

    if (is_page('frm-repair-request') || is_page('new-ecu-order')) {
        $classes[] = 'zf-backgroundBg';
    }

    return $classes;
}

//add_action('wp_footer', 'redirect_after_work_order_creation');

/**
 * redirect_after_work_order_creation
 */
function redirect_after_work_order_creation()
{
    ?>
    <script type="text/javascript">
        document.addEventListener('wpcf7mailsent', function (event) {
            if ('97196' == event.detail.contactFormId) {
                var inputs = event.detail.inputs;

                for (var i = 0; i < inputs.length; i++) {
                    if ('your-email' == inputs[i].name) {
                        var user_email = inputs[i].value;
                        break;
                    }
                }
                location = 'https://the-ecu-pro.com/frm-tag-creation?success=FRM&associated-email=' + user_email;
            } else if ('97296' == event.detail.contactFormId) {
                var inputs = event.detail.inputs;

                for (var i = 0; i < inputs.length; i++) {
                    if ('your-email' == inputs[i].name) {
                        var user_email = inputs[i].value;
                        break;
                    }
                }
                location = 'https://the-ecu-pro.com/dme-tag-creation?success=DME&associated-email=' + user_email;
            }
        }, false);
    </script>
    <?php
}

add_filter('woocommerce_add_to_cart_validation', 'add_the_date_validation', 15, 5);

/**
 * @param $passed
 *
 * @return false|mixed
 */
function add_the_date_validation($passed)
{
    if (empty($_REQUEST['ecu_send_validate'])) {
        wc_add_notice(__('Please confirm the condition below.', 'woocommerce'), 'error');
        $passed = false;
    }
    return $passed;
}


add_filter('add_to_cart_redirect', 'redirect_to_checkout');

/**
 * @return mixed
 */
function redirect_to_checkout()
{
    global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();
    return $checkout_url;
}

//add_action('template_redirect', 'pass_on_query_vars');

/**
 * pass_on_query_vars
 */
function pass_on_query_vars()
{
    if (isset($_GET)) {
        if (isset($_GET['developer'])) {

            //get_query_var();

        }
    }
}
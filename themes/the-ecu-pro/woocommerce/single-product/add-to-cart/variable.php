<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.5
 */

defined('ABSPATH') || exit;

global $product;

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars(
    $variations_json,
    ENT_QUOTES,
    'UTF-8',
    true
);

$product_type = get_post_meta($product->get_id(), 'product_type', true);
$term_object  = get_term_by('name', $product_type, 'pa_product-type-data');

$product_video_url = get_field(
    'video_embed_url',
    $product->get_id()
);
$product_video_url = !empty($product_video_url) ? $product_video_url : 'https://www.youtube.com/embed/JESCRzDYdqE';

do_action('woocommerce_before_add_to_cart_form'); ?>

<?php if (wp_is_mobile()) { ?>
    <ul class="cta-container">
        <?php

        $date = new DateTime("now", new DateTimeZone('America/New_York'));

        $currentTime = $date->format('H:i:s');
        $currentTime = strtotime($currentTime);
        $closingTime = strtotime('17:00:00');

        if ($currentTime >= $closingTime) { ?>
            <li class="contact-button"><a class="message-product-button" href="#">Message</a></li>
        <?php } else { ?>
            <li class="contact-button"><a href="tel:+18887232080">Call us</a></li>
        <?php } ?>

        <li class="service-button"><a href="#">Select service</a></li>
    </ul>
<?php } ?>

    <div class="messages-container"></div>
    <div class="tabs" <?php if (wp_is_mobile()) { ?>style="display: none;"<?php } ?> id="tabs-main-container">
        <div class="tab-2">
            <label for="tab2-1" class="tab-container-label">1. Conditions</label>
            <input id="tab2-1" name="tabs-two" type="radio" class="radio-tab-selector first-tab" checked="checked">
            <div class="first outer-container active">
                <form class="variations_form cart" action="<?php echo esc_url(
                    apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())
                ); ?>" method="post" enctype='multipart/form-data'
                      data-product_id="<?php echo absint($product->get_id()); ?>"
                      data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
                    <?php do_action('woocommerce_before_variations_form'); ?>

                    <?php

                    if (empty($available_variations) && false !== $available_variations) : ?>
                        <p class="stock out-of-stock"><?php echo esc_html(
                                apply_filters(
                                    'woocommerce_out_of_stock_message',
                                    __('This product is currently out of stock and unavailable.', 'woocommerce')
                                )
                            ); ?></p>
                    <?php else : ?>
                        <table class="variations" cellspacing="0">
                            <tbody>
                            <?php foreach ($attributes as $attribute_name => $options) :

                                $selected = isset(
                                    $_REQUEST['attribute_' . sanitize_title(
                                        $attribute_name
                                    )]
                                ) ? wc_clean(
                                    $_REQUEST['attribute_' . sanitize_title($attribute_name)]
                                ) : $product->get_variation_default_attribute($attribute_name);

                                ?>
                                <tr>
                                    <td class="label"><label for="<?php echo esc_attr(
                                            sanitize_title($attribute_name)
                                        ); ?>"><?php echo wc_attribute_label(
                                                $attribute_name
                                            ); // WPCS: XSS ok.
                                            ?></label></td>
                                    <td class="value">
                                        <?php
                                        wc_dropdown_variation_attribute_options(
                                            array(
                                                'options'   => $options,
                                                'attribute' => $attribute_name,
                                                'product'   => $product,
                                                'selected'  => $selected
                                            )
                                        );
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="single_variation_wrap">
                            <?php
                            /**
                             * Hook: woocommerce_before_single_variation.
                             */
                            do_action('woocommerce_before_single_variation');

                            /**
                             * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
                             *
                             * @since  2.4.0
                             * @hooked woocommerce_single_variation - 10 Empty div for variation data.
                             * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
                             */
                            do_action('woocommerce_single_variation');

                            /**
                             * Hook: woocommerce_after_single_variation.
                             */
                            do_action('woocommerce_after_single_variation');
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php do_action('woocommerce_after_variations_form');

                    $ecu_condition_value  = get_field("ecu_condition", $product->get_id());
                    $ecu_condition_object = get_field_object("ecu_condition", $product->get_id(), true, false);

                    if (!empty($ecu_condition_value)) { ?>
                        <input type="checkbox" id="validation-checkbox" name="ecu_send_validate"
                               class="validation-checkbox checkbox"
                               value="<?php echo $ecu_condition_value; ?>">
                        <label class="validation-checkbox-label"
                               for="validation-checkbox"><?php echo $ecu_condition_object['value']; ?></label>
                    <?php } ?>
                </form>
            </div>
        </div>
        <div class="tab-2">
            <label for="tab2-2" class="tab-container-label">2. Checkout</label>
            <input id="tab2-2" name="tabs-two" type="radio" class="radio-tab-selector last-tab">
            <div class="outer-container checkout-container">
            </div>
        </div>

        <div class="cta-buttons-product">
            <?php

            if (wp_is_mobile()) {
                $date = new DateTime("now", new DateTimeZone('America/New_York'));

                $currentTime = $date->format('H:i:s');
                $currentTime = strtotime($currentTime);
                $closingTime = strtotime('17:00:00');

                if ($currentTime >= $closingTime) { ?>
                    <a class="contact-button message-product-button" href="#">Message</a>
                <?php } else { ?>
                    <a class="contact-button" href="tel:+18887232080">Call us</a>
                <?php }
            }
            ?>
            <a class="continue-button initial" href="#">Continue to checkout</a>
        </div>

    </div>
    <div id="below-tabs-container"></div>
    </form>

    <div id="video-popup-container-overlay">
        <div id="video-popup-container">
            <div id="close">x</div>
            <iframe width="560" height="315" src="<?php echo $product_video_url; ?>" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen=""></iframe>
        </div>
    </div>

<?php
do_action('woocommerce_after_add_to_cart_form');

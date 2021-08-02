<?php
/**
 * Template Name: Checkout completed (Dummy)
 */

get_header(); ?>

<div id="main" class="column1 boxed no-breadcrumbs"><!-- main -->

    <div class="outer-parent-container">
        <div class="container">
            <div class="row main-content-wrap">
                <div class="main-content col-lg-12">
                    <div class="woocommerce">
                        <div class="woocommerce-order">
                            <div style="margin-bottom: 20px;">
                                <?php

                                if (have_posts()) {
                                    while (have_posts()) {
                                        the_post();
                                        the_content();
                                    }
                                }

                                ?>
                            </div>
                            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                                Thank you. Your order has been received.</p>

                            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

                                <li class="woocommerce-order-overview__order order">
                                    Order number: <strong>97237</strong>
                                </li>

                                <li class="woocommerce-order-overview__date date">
                                    Date: <strong>07/31/2021</strong>
                                </li>

                                <li class="woocommerce-order-overview__email email">
                                    Email: <strong>marktnorman@gmail.com</strong>
                                </li>

                                <li class="woocommerce-order-overview__total total">
                                    Total: <strong><span class="woocommerce-Price-amount amount"><bdi><span
                                                        class="woocommerce-Price-currencySymbol">$</span>0.00</bdi></span></strong>
                                </li>


                            </ul>


                            <section class="woocommerce-order-details">

                                <h2 class="woocommerce-order-details__title">Order details</h2>

                                <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

                                    <thead>
                                    <tr>
                                        <th class="woocommerce-table__product-name product-name">Product</th>
                                        <th class="woocommerce-table__product-table product-total">Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr class="woocommerce-table__line-item order_item">

                                        <td class="woocommerce-table__product-name product-name">
                                            <a href="https://the-ecu-pro.com/product/bmw-and-mini-frm-repair-plug-and-play-2/?attribute_pa_variation=frm-repair">BMW
                                                and MINI FRM repair – Plug and Play - FRM Repair</a> <strong
                                                    class="product-quantity">×&nbsp;1</strong></td>

                                        <td class="woocommerce-table__product-total product-total">
                                            <span class="woocommerce-Price-amount amount"><bdi><span
                                                            class="woocommerce-Price-currencySymbol">$</span>199.00</bdi></span>
                                        </td>

                                    </tr>

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th scope="row">Subtotal:</th>
                                        <td><span class="woocommerce-Price-amount amount"><span
                                                        class="woocommerce-Price-currencySymbol">$</span>199.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Discount:</th>
                                        <td>-<span class="woocommerce-Price-amount amount"><span
                                                        class="woocommerce-Price-currencySymbol">$</span>199.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Shipping:</th>
                                        <td>Free shipping</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Total:</th>
                                        <td><span class="woocommerce-Price-amount amount"><span
                                                        class="woocommerce-Price-currencySymbol">$</span>0.00</span>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>

                            </section>

                            <section class="woocommerce-customer-details">


                                <h2 class="woocommerce-column__title">Billing address</h2>

                                <address>
                                    Mark Norman<br>706 Onyx Hotel and Apartments, 57 Heerengracht street<br>Cape
                                    Town<br>Western Cape<br>8001<br>South Africa
                                    <p class="woocommerce-customer-details--phone">0609918373</p>

                                    <p class="woocommerce-customer-details--email">marktnorman@gmail.com</p>
                                </address>


                            </section>


                        </div>
                    </div>
                    <div class="sidebar">

                    </div>
                </div><!-- end outer-parent-container -->
            </div><!-- end container -->
        </div><!-- end main-content-wrap -->
    </div>

</div>

<?php get_footer(); ?>

</div><!-- end outer-parent-container -->
</div><!-- end container -->
</div><!-- end main-content-wrap -->
</div><!-- end main-content -->

<?php if (is_product()) {

    global $product;
    $id           = $product->get_id();
    $product_type = get_post_meta($id, 'product_type', true);
    $term_object  = get_term_by('name', $product_type, 'pa_product-type-data');

    $product_video_url = get_field(
        'video_embed_url',
        $id
    );
    $product_video_url = !empty($product_video_url) ? $product_video_url : 'https://www.youtube.com/embed/JESCRzDYdqE';

    if (have_rows('how_ti_works_product_data', $term_object)):

        while (have_rows('how_ti_works_product_data', $term_object)) : the_row();

            $how_it_works_product_title = get_sub_field(
                'how_it_works_product_title'
            );
            $how_it_works_product_title = !empty($how_it_works_product_title) ? $how_it_works_product_title : '';

            $how_it_works_product_copy = get_sub_field(
                'how_it_works_product_copy'
            );
            $how_it_works_product_copy = !empty($how_it_works_product_copy) ? $how_it_works_product_copy : '';

            $accordian_product_items = get_sub_field('how_it_works_accordian_data');
            $accordian_product_html  = '';
            $counter                 = 1;
            foreach ($accordian_product_items as $accordian_product_item) {

                $accordian_title = $accordian_product_item['product_accordian_title'];
                $accordian_copy  = $accordian_product_item['product_accordian_body'];

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
    endif; ?>
<?php } ?>

<?php if (is_front_page()) { ?>

    <div class="pre-footer-container" style="display: none;">
        <div class="col-lg-12">
            <ul>
                <li class="phone-container">
                    <h3>Call us</h3>
                    <?php
                    global $tecup_form_show_number;
                    if ($tecup_form_show_number) { ?>
                        <b>+1-888-723-2080</b>
                    <?php } else { ?>
                        <a class="revealed-number" data-js-ref="ctvnf-open"><span
                                    data-js-ref="ctvnf-number">+1 888-723-</span></a>
                        <a class="reveal-action" href="#" data-js-ref="ctvnf-open"> <span data-js-ref="ctvnf-click-msg">CLICK <span
                                        class="helper-text">to show number</span></span></a>
                    <?php } ?>
                </li>
                <li class="chat-container">
                    <h3>Online Chat</h3>
                    <a href="/" class="pre-footer-button">Chat Now</a>
                </li>
                <li class="book-container">
                    <h3>Book Express</h3>
                    <span>Collection Service</span>
                    <a href="/product/bmw-ecu-testing-service/" class="pre-footer-button">Book Now</a>
                </li>
            </ul>
        </div>
    </div>

<?php } ?>


</div><!-- end main -->

<div class="footer-wrapper">
    <div id="footer" class="footer-1">
        <div class="footer-main">
            <div class="container">

                <div class="row">
                    <div class="col-lg-3">
                        <aside id="block-widget-18" class="widget widget-block">
                            <div class="block">
                                <div class="porto-block">
                                    <div class="porto-u-heading" data-hspacer="line_only" data-halign="left"
                                         style="text-align:left">
                                        <div class="porto-u-main-heading"><h1
                                                    style="font-weight:600;color:#ffffff;font-size:22px;line-height:60px;">
                                                MY ACCOUNT</h1></div>
                                        <div class="porto-u-heading-spacer line_only" style="height:1px;"><span
                                                    class="porto-u-headings-line"
                                                    style="border-style: solid; border-bottom-width: 1px; border-color: rgb(255, 255, 255); width: 40px; margin-left: 0px; float: left;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                        <aside id="nav_menu-4" class="widget widget_nav_menu">
                            <div class="menu-footer-my-account-container">
                                <ul id="menu-footer-my-account" class="menu">
                                    <li id="menu-item-19146"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-19146">
                                        <a href="https://the-ecu-pro.com/my-account/">Dashboard</a></li>
                                    <li id="menu-item-19148"
                                        class="menu-item menu-item-type-custom menu-item-object-custom menu-item-19148">
                                        <a href="https://www.the-ecu-pro.com/my-account/orders/">Orders</a></li>
                                    <li id="menu-item-19149"
                                        class="menu-item menu-item-type-custom menu-item-object-custom menu-item-19149">
                                        <a href="https://www.the-ecu-pro.com/my-account/edit-address/">Addresses</a>
                                    </li>
                                    <li id="menu-item-19150"
                                        class="menu-item menu-item-type-custom menu-item-object-custom menu-item-19150">
                                        <a href="https://www.the-ecu-pro.com/my-account/edit-account/">Account
                                            Details</a></li>
                                    <li id="menu-item-19145"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-19145">
                                        <a href="https://the-ecu-pro.com/cart/">Cart</a></li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                    <div class="col-lg-3">
                        <aside id="block-widget-15" class="widget widget-block">
                            <div class="block">
                                <div class="porto-block">
                                    <div class="porto-u-heading footer footer-policy" data-hspacer="line_only"
                                         data-halign="left" style="text-align:left">
                                        <div class="porto-u-main-heading"><h1
                                                    style="font-weight:600;color:#ffffff;font-size:22px;line-height:60px;">
                                                POLICIES</h1></div>
                                        <div class="porto-u-heading-spacer line_only" style="height:1px;"><span
                                                    class="porto-u-headings-line"
                                                    style="border-style: solid; border-bottom-width: 1px; border-color: rgb(255, 255, 255); width: 40px; margin-left: 0px; float: left;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                        <aside id="nav_menu-3" class="widget widget_nav_menu">
                            <div class="menu-footer-policy-container">
                                <ul id="menu-footer-policy" class="menu">
                                    <li id="menu-item-19154"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-privacy-policy menu-item-19154">
                                        <a href="https://the-ecu-pro.com/privacy-policy/">Privacy Policy</a>
                                    </li>
                                    <li id="menu-item-19152"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-19152">
                                        <a href="https://the-ecu-pro.com/terms-and-conditions/">Terms of
                                            Service</a></li>
                                    <li id="menu-item-19153"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-19153">
                                        <a href="https://the-ecu-pro.com/return-policy/">Return Policy</a>
                                    </li>
                                    <li id="menu-item-19155"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-19155">
                                        <a href="https://the-ecu-pro.com/disclaimer/">Disclaimer</a></li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                    <div class="col-lg-3">
                        <aside id="block-widget-14" class="widget widget-block">
                            <div class="block">
                                <div class="porto-block">
                                    <div class="porto-u-heading footer contact-info" data-hspacer="line_only"
                                         data-halign="left" style="text-align:left">
                                        <div class="porto-u-main-heading"><h1
                                                    style="font-weight:600;color:#ffffff;font-size:22px;line-height:60px;">
                                                CONTACT INFO</h1></div>
                                        <div class="porto-u-heading-spacer line_only" style="height:1px;"><span
                                                    class="porto-u-headings-line"
                                                    style="border-style: solid; border-bottom-width: 1px; border-color: rgb(255, 255, 255); width: 40px; margin-left: 0px; float: left;"></span>
                                        </div>
                                        <div class="porto-u-sub-heading"
                                             style="color: #afafaf;font-size:14px;line-height:30px;">
                                            <ul class="contact-info">
                                                <li><i class="fas fa-map-marker-alt"
                                                       style="font-size: 18px; margin-right: 10px;"></i>
                                                    181 Hills Creek Rd,
                                                    <div style="margin-left: 30px;">Wellsboro, PA 16901, USA</div>
                                                </li>
                                                <li class="text-white"><i class="porto-icon-phone"
                                                                          style="font-size: 20px; margin-right: 5px; margin-left: -3px;"></i>
                                                    <?php global $tecup_form_show_number;
                                                    if ($tecup_form_show_number) { ?>
                                                        <b>+1-888-723-2080</b>
                                                    <?php } else { ?>
                                                        <span data-js-ref="ctvnf-number">+1 888-723-<a href="#"
                                                                                                       data-js-ref="ctvnf-open"> CLICK to show number</a></span>
                                                    <?php } ?>
                                                </li>
                                                <li><i class="fab fa-skype"
                                                       style="font-size: 18px; margin-right: 10px;"></i>
                                                    <a href="skype:info@the-ecu-pro.com">info@the-ecu-pro.com</a></li>
                                                <li><i class="porto-icon-mail-alt"
                                                       style="font-size: 16px; margin-right: 7px;"></i>
                                                    <a href="mailto:info@the-ecu-pro.com">info@the-ecu-pro.com</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <div class="col-lg-3">
                        <aside id="block-widget-17" class="widget widget-block">
                            <div class="block">
                                <div class="porto-block">
                                    <div class="porto-u-heading" data-hspacer="line_only" data-halign="left"
                                         style="text-align:left">
                                        <div class="porto-u-main-heading"><h1
                                                    style="font-weight:600;color:#ffffff;font-size:22px;line-height:60px;">
                                                REPAIR SERVICES OFFERED</h1></div>
                                        <div class="porto-u-heading-spacer line_only" style="height:1px;"><span
                                                    class="porto-u-headings-line"
                                                    style="border-style: solid; border-bottom-width: 1px; border-color: rgb(255, 255, 255); width: 40px; margin-left: 0px; float: left;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                        <div id="ecu-mmy-filter-footer-wrapper" class="ecu-mmy-filter-wrapper ecu-mmy-filter-footer">
                            <div class="ecu-wrapper">
                                <div class="ecu-mmy-filter-title">
                                    <h3></h3>
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
                                <a class="bottom-cta" href="/contact-us">Can't find your vehicle?</a>
                            </div>
                        </div>
                        <aside id="text-9" class="widget widget_text">
                            <div class="textwidget">
                            </div>
                        </aside>
                    </div>
                </div>

            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">

                <div class="footer-center">
                    <img class="img-responsive footer-payment-img"
                         src="<?php echo get_stylesheet_directory_uri() . '/assets/images/payments-footer.png' ?>"
                         data-spai="1" alt="Payment Gateways" loading="lazy" data-spai-upd="516">
                    <span class="footer - copyright">The ECU Pro @ 2021 All Rights Reserved.</span></div>

            </div>
        </div>
    </div>
</div>

</div><!-- end page wrapper -->

<?php
get_template_part('templates/click-to-view-number-form');

wp_footer();

if (is_page('frm-repair-request')) { ?>
    <script type="text/javascript">
        var zf_DateRegex = new RegExp("^(([0][1-9])|([1-2][0-9])|([3][0-1]))[-](Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-](?:(?:19|20)[0-9]{2})$");
        var zf_MandArray = ["Name_First", "Name_Last", "Email", "PhoneNumber_countrycode", "SingleLine"];
        var zf_FieldArray = ["Name_First", "Name_Last", "Email", "PhoneNumber_countrycode", "Address_AddressLine1", "Address_AddressLine2", "Address_City", "Address_Region", "Address_ZipCode", "Address_Country", "SingleLine", "SingleLine1"];
        var isSalesIQIntegrationEnabled = true;
        var salesIQFieldsArray = [{
            "formFieldName": "Email",
            "formFieldType": 9,
            "salesIQFieldName": "Email"
        }, {
            "formFieldName": "Name",
            "formFieldType": 7,
            "salesIQFieldName": "Name",
            "fieldCompLinkName": "Name_First"
        }, {"formFieldName": "PhoneNumber", "formFieldType": 11, "salesIQFieldName": "Phone"}];
    </script>
<?php }

if (is_page('new-ecu-order')) { ?>
    <script type="text/javascript">var zf_DateRegex = new RegExp("^(([0][1-9])|([1-2][0-9])|([3][0-1]))[-](Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-](?:(?:19|20)[0-9]{2})$");
        var zf_MandArray = ["Name_First", "Name_Last", "Email", "SingleLine", "SingleLine1", "Radio1", "Radio", "MultiLine2", "Radio2", "MultiLine", "MultiLine1", "Dropdown"];
        var zf_FieldArray = ["Name_First", "Name_Last", "Email", "PhoneNumber_countrycode", "Address_AddressLine1", "Address_AddressLine2", "Address_City", "Address_Region", "Address_ZipCode", "Address_Country", "SingleLine", "SingleLine1", "Radio1", "Radio", "MultiLine2", "Radio2", "MultiLine", "MultiLine1", "FileUpload", "Dropdown", "SingleLine2"];
        var isSalesIQIntegrationEnabled = true;
        var salesIQFieldsArray = [{
            "formFieldName": "Email",
            "formFieldType": 9,
            "salesIQFieldName": "Email"
        }, {
            "formFieldName": "Name",
            "formFieldType": 7,
            "salesIQFieldName": "Name",
            "fieldCompLinkName": "Name_First"
        }, {"formFieldName": "PhoneNumber", "formFieldType": 11, "salesIQFieldName": "Phone"}];</script>
<?php }


?>

</body>
</html>

(function ($) {
    "use strict";

    function waitingUI(target) {
        target.block({message: '<img src="' + ecu_ajax_object.child_url + '/assets/images/loading.gif" />'});
    }

    function hidewaitingUI(target) {
        target.unblock();
    }

    $(document).ready(function () {
        //Hide a option in the selector
        $("select.ecu-mmy-filter-selector").find("[slug=bmw-ecu-service]").css("display", "none");
        $("select.ecu-mmy-filter-selector").find("[slug=mini-ecu-service]").css("display", "none");

        //Search button clicked
        $(".ecu-filter-button").on("click", function () {
            var parent = $("#" + $(this).attr("parent"));
            var make = parent.find(".ecu-make").children("option:selected").attr("slug");
            var model = parent.find(".ecu-model").children("option:selected").attr("slug");
            var engine = parent.find(".ecu-engine").children("option:selected").attr("slug");
            var year = parent.find(".ecu-year").children("option:selected").attr("slug");
            var new_link = ecu_ajax_object.site_url + "/product-category";
            if (make !== "") {
                new_link += "/" + make;
                if (model !== "") {
                    new_link += "/" + model;
                    if (engine !== "") {
                        new_link += "/" + engine;
                        if (year !== "") {
                            new_link += "/" + year;
                        }
                    }
                }
                window.location.href = new_link;
            }

        });
        //Make/Model/Engine/Year change
        $(".ecu-make, .ecu-model, .ecu-year, .ecu-engine").on("change", function () {
            var parent = $("#" + $(this).attr("parent"));
            var selector_model = parent.find(".ecu-model");
            var selector_make = parent.find(".ecu-make");
            var selector_year = parent.find(".ecu-year");
            var selector_engine = parent.find(".ecu-engine");
            var id = parseInt($(this).val());
            var selector = null;

            var default_value = '';
            switch ($(this).attr("name")) {
                case 'ecu-make':
                    default_value = "Select Model";
                    selector = selector_model;
                    selector_engine.html("<option slug='' value='0'>Select Engine</option>\n");
                    selector_year.html("<option slug='' value='0'>Select Year</option>\n");
                    if (id === 0) {
                        selector_model.html("<option slug='' value='0'>" + default_value + "</option>\n");
                        return;
                    }
                    break;
                case 'ecu-model':
                    default_value = "Select Engine";
                    selector = selector_engine;
                    selector_year.html("<option slug='' value='0'>Select Year</option>\n");
                    if (id === 0) {
                        selector_engine.html("<option slug='' value='0'>" + default_value + "</option>\n");
                        selector_engine.trigger("change");
                        return;
                    }
                    break;
                case 'ecu-engine':
                    default_value = "Select Year";
                    selector = selector_year;
                    if (id === 0) {
                        selector_year.html("<option slug='' value='0'>" + default_value + "</option>\n");
                        selector_year.trigger("change");
                        return;
                    }
                    break;
                case 'ecu-year':
                    selector_year.removeClass("selected");
                    parent.find('.ecu-mmy-filter-apply button').focus();
                    return;
                    break;
                default:
            }
            if (id === 0) {
                return; //do nothing when nothing selected
            }
            waitingUI(selector.parent());
            $.ajax({
                url: ecu_ajax_object.ajax_url,
                type: "POST",
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                data: {
                    action: "change_selector",
                    parent: id,
                },
                success: function (data) {
                    var info = JSON.parse(data);
                    var elements = "<option slug='' value='0'>" + default_value + "</option>\n";
                    info.forEach(function (element) {
                        elements += `<option slug=${element.slug} value=${element.term_id}>${element.name}</option>\n`;
                    });
                    selector.html(elements);
                    $(".ecu-mmy-filter-selector").each(function () {
                        $(this).removeClass("selected");
                    });
                    selector.addClass("selected");
                    hidewaitingUI(selector.parent());
                },
                error: function (jqXHR, exception) {
                    console.log("Failed");
                    hidewaitingUI(selector.parent());
                }
            });
        });
    });

    $("a.woocommerce-terms-and-conditions-link").unbind("click");

    $("body").on('click', 'a.woocommerce-terms-and-conditions-link', function (event) {
        $(".woocommerce-terms-and-conditions").remove();
        $(".woocommerce-terms-and-conditions").empty();
        $(this).attr("target", "_blank");
        window.open($(this).attr("href"));

        return false;
    });

    $(".woocommerce-terms-and-conditions").remove();
    $(".woocommerce-terms-and-conditions").empty();

    function changeBackgroundPosition(winWidth) {
        if (winWidth >= 1460) {
            var offset = -(455 - (winWidth - 1420) / 2) + "px";
            if (document.getElementById("home-background-image") != null) {
                document.getElementById("home-background-image").style.backgroundPositionX = offset;
                document.getElementById("home-background-image").style.backgroundSize = "inherit";
            }
        } else if (winWidth < 1460 && winWidth >= 1300) {
            var offset = -(455 - (winWidth - 1240) / 2) + "px";
            if (document.getElementById("home-background-image") != null) {
                document.getElementById("home-background-image").style.backgroundPositionX = offset;
                document.getElementById("home-background-image").style.backgroundSize = "inherit";
            }
        } else if (winWidth < 1300 && winWidth >= 1200) {
            var offset = -(170 - (winWidth - 1100) / 2) + "px";
            if (document.getElementById("home-background-image") != null) {
                document.getElementById("home-background-image").style.backgroundPositionX = offset;
                document.getElementById("home-background-image").style.backgroundSize = "inherit";
            }
        } else if (winWidth < 1200 && winWidth >= 992) {
            var offset = -(170 - (winWidth - 850) / 2) + "px";
            if (document.getElementById("home-background-image") != null) {
                document.getElementById("home-background-image").style.backgroundPositionX = offset;
                document.getElementById("home-background-image").style.backgroundSize = "inherit";
            }
        } else {
            if (document.getElementById("home-background-image") != null) {
                document.getElementById("home-background-image").style.backgroundPositionX = "22%";
                document.getElementById("home-background-image").style.backgroundSize = "cover";

            }
        }
    }

    if ($.blockUI !== undefined) {
        $.blockUI.defaults.message = "";
    }

    $(document).ready(function () {

        $('#footer .widget_wysija .wysija-submit').removeClass('btn-default').addClass('btn-primary');
        $('.sidebar .widget_wysija .wysija-submit').addClass('btn btn-quaternary');

        // Single product page customizaton
        $('ul.resp-tabs-list li[aria-controls="tab_item-3"]').attr('id', 'product-contact-us');

        $('p.move-contact-us-section').click(function () {
            $('ul.resp-tabs-list li').removeClass('resp-tab-active');
            $('div.resp-tabs-container div').removeClass('resp-tab-content-active');
            $('div#tab-description').css("display", "none");
            $('ul.resp-tabs-list li[aria-controls="tab_item-3"]').addClass('resp-tab-active');
            $('div#tab-custom_tab2').addClass('resp-tab-content-active');
        });

        $("p.move-contact-us-section").click(function () {
            $('html, body').animate({
                scrollTop: $("#tab-custom_tab2").offset().top - 150
            }, 1000);
        });

        $(".ppom-col .ppom-c-hide").each(function () {
            if ($(this).css("display") != "none")
                $(this).css("display", "flex");
        });

        $("select#ecu_select").on("change", function (e) {
            $(".ppom-col .ppom-c-hide").each(function () {
                if ($(this).css("display") != "none")
                    $(this).css("display", "flex");
            });
        });

        $(".trigger-chat").on("click", function (e) {
            e.preventDefault();
            $("#zsiq_float").trigger("click");
        });

        $('.product-video').magnificPopup({
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,

            fixedContentPos: false
        });

        $(window).on('resize', function () {
            changeBackgroundPosition($(document).width());
        });

        $("#trigger-chat").on("click", function (e) {
            e.preventDefault();
            $("#zsiq_float").trigger("click");
        });

        $("#sticky-add-to-cart").on("click", function (e) {
            e.preventDefault();
            var isChecked = false;
            $('input[type="checkbox"]').each(function () {
                if ($(this).prop("checked") == true) {
                    isChecked = true;
                }
            })
            $("form.cart button").submit();
            if (!isChecked) {
                $('html, body').animate({
                    scrollTop: $(".ppom-input-ecu_select").offset().top - 80
                }, 1000);
            }
        });

        $("#save-opening-hours").on("click", function () {
            console.log("saving....");
            var from_01 = $("#from-01").val();
            var to_01 = $("#to-01").val();
            var from_02 = $("#from-02").val();
            var to_02 = $("#to-02").val();

            $.ajax({
                url: ecu_ajax_object.ajax_url,
                type: "POST",
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                data: {
                    action: "save_opening_hours",
                    from_01: from_01,
                    to_01: to_01,
                    from_02: from_02,
                    to_02: to_02,
                    nonce: ecu_ajax_object.nonce
                },
                success: function (result, status, xhr) {
                    console.log(result);
                },
                error: function (jqXHR, exception) {
                    console.log("Failed");
                }
            });
        });

        $('.chat-container a').on("click", function (e) {
            e.preventDefault();
            $(".zsiq_chatbtn").trigger("click");
        });

        // Function to update query params
        // function updateUrlParameter(param, value) {
        //     const regExp = new RegExp(param + "(.+?)(&|$)", "g");
        //     const newUrl = window.location.href.replace(regExp, param + "=" + value + "$2");
        //     window.history.pushState("", "", newUrl);
        // }

        function updateUrlParameter(key, value) {
            let baseUrl = [location.protocol, '//', location.host, location.pathname].join(''),
                urlQueryString = document.location.search,
                newParam = key + '=' + value,
                params = '?' + newParam;

            // If the "search" string exists, then build params from it
            if (urlQueryString) {
                let keyRegex = new RegExp('([\?&])' + key + '[^&]*');

                // If param exists already, update it
                if (urlQueryString.match(keyRegex) !== null) {
                    params = urlQueryString.replace(keyRegex, "$1" + newParam);
                } else { // Otherwise, add it to end of query string
                    params = urlQueryString + '&' + newParam;
                }
            }
            window.history.replaceState({}, "", baseUrl + params);
        }

        //$('.variations_form').on('change', 'select', function (e) {

        $('.variations_form select').change(function (e) {
            let val = $(e.target).val();
            let text = $(e.target).find("option:selected").text();

            console.log(val);
            console.log(text);

            $('.entry-summary h4.product-single-secondary-title').text(text);

            $('.entry-summary h4.product-single-secondary-title').removeClass('hide');
            $('.entry-summary h4.product-single-secondary-title').addClass('show');

            updateUrlParameter('attribute_pa_variation', val);
        });

        $(document).on('click', '.cta-buttons-product .continue-button.initial', function (e) {
            e.preventDefault();

            var $thisbutton = $(this),
                $form = $('form.cart'),
                id = $($form).attr('data-product_id'),
                product_qty = $form.find('input[name=quantity]').val() || 1,
                product_id = $form.find('input[name=product_id]').val() || id,
                variation_id = $form.find('input[name=variation_id]').val() || 0;

            if (variation_id == '0') {
                if ($('.error-no-selection').length > 0) {
                    return false;
                } else {
                    $("<span class='error-no-selection'>Please select an option from the above</span>").insertAfter("form.variations_form");
                    return false;
                }
            } else {
                if ($('.error-no-selection').length > 0) {
                    $('.error-no-selection').addClass('hide');
                }
            }

            if ($("#validation-checkbox").prop('checked') != true) {
                $("<span class='error-no-selection'>This field is required</span>").insertAfter(".validation-checkbox-label");
                return false;
            }

            // Stop if no variation has been chosen
            if ($('.woocommerce-variation-add-to-cart-disabled').length > 0) {
                return false;
            }

            $.ajax({
                url: ecu_ajax_object.ajax_url,
                type: 'POST',
                cache: false,
                data: {
                    'action': 'clear_cart'
                },
                success: function (response) {
                    console.log(response);

                    //Validation done and success continue
                    $('button.single_add_to_cart_button').trigger('click');

                    $('.cta-buttons-product .continue-button').removeClass('initial');

                    $('#tab2-2').trigger('click');

                    $('div.outer-container').removeClass('active');

                    $('#tab2-2').next('div.outer-container').addClass('active');

                    //$('.cta-buttons-product .continue-button').addClass('next-tab');
                    $('.cta-buttons-product .continue-button').addClass('payment-trigger');
                    $('.cta-buttons-product .continue-button').text('Pay now');

                    $([document.documentElement, document.body]).animate({
                        scrollTop: $('.entry-summary .tabs').offset().top
                    }, 2000);

                    return false;
                },
                error: function (request, status, error) {
                    console.log(request.responseText);
                    console.log(error);
                }
            });
        });

        function navigate_next_tab() {
            $(document).on('click', '.cta-buttons-product .continue-button.next-tab', function (e) {
                e.preventDefault();

                if ($('.cta-buttons-product .continue-button.payment-trigger').length > 0) {
                    return false;
                }

                var current_tab = $('div.outer-container.active').prev().attr('id');
                var lastChar = current_tab.substr(current_tab.length - 1);
                var incremented = parseInt(lastChar) + 1;

                $('#tab2-' + incremented).trigger('click');

                $('div.outer-container').removeClass('active');
                $('#tab2-' + incremented).next('div.outer-container').addClass('active');
            });
        }

        $('form.woocommerce-checkout').appendTo($('.outer-container.checkout-container'));
        $('div#opc-messages').appendTo($('div.messages-container'));

        $(document).on('click', '.cta-buttons-product .continue-button.payment-trigger', function (e) {
            e.preventDefault();

            // let user_data = {
            //     action: 'user_auth_checkout',
            //     form_data: $.trim($("input[name='billing_email']").val())
            // };
            //
            // // Lets try log the user in if he exists
            // $.ajax({
            //     url: ecu_ajax_object.ajax_url,
            //     type: "POST",
            //     data: user_data,
            //     success: function (response) {
            //         console.log(response);
            //
            //
            //     },
            //     error: function (request, status, error) {
            //         console.log(request.responseText);
            //         console.log(error);
            //     },
            // });

            $('.woocommerce #payment #place_order').trigger('click');

        });

        function shipping_radio_buttons_actions() {
            $('.radio-button-label').on("click", function () {
                $("input.shipping_method").attr('checked', false);
                $(this).prev('.shipping_method').attr('checked', true);

                $('input[type=radio][name="shipping_method"]').on('change', function () {
                    $('body').trigger('update_checkout');
                });

            });
        }

        function shipping_submission_handler() {
            $('.cta-buttons-product .continue-button.added').click(function (e) {
                e.preventDefault();

                let form_identifier = "form#woocommerce-checkout";

                $(form_identifier).validate({
                    rules: {
                        billing_first_name: "required",
                        billing_last_name: "required",
                        billing_phone: "required",
                        billing_address_1: "required",
                        billing_country: "required",
                        billing_postcode: "required",
                        billing_city: "required",
                        billing_email: {
                            required: true,
                            email: true
                        },
                    },
                    messages: {
                        billing_first_name: "Please enter your firstname",
                        billing_last_name: "Please enter your lastname",
                        email: "Please enter a valid email address"
                    },
                    submitHandler: function (form) {

                        let data = {
                            action: 'updating_shipping',
                            form_data: $(form).serialize(),
                        };

                        $.ajax({
                            url: ecu_ajax_object.ajax_url,
                            type: "POST",
                            data: data,
                            success: function (response) {
                                $('div.outer-container').removeClass('active');
                                $('#tab2-3').next('div.outer-container').addClass('active');

                                $('.outer-container.active ul.shipping-options-container').html(response);

                                $('#tab2-3').trigger('click');

                                $('.cta-buttons-product .continue-button').removeClass('added');
                                $('.cta-buttons-product .continue-button').addClass('shipping');
                            },
                            error: function (request, status, error) {
                                console.log(request.responseText);
                                console.log(error);
                            },
                            done: function () {
                                return false;
                            }
                        });
                    },
                    // other options
                });

                if ($(form_identifier).valid()) {
                    $(form_identifier).submit();
                }

            });
        }

        function shipping_update_final_total() {
            $('.cta-buttons-product .continue-button.shipping').click(function (e) {
                e.preventDefault();

                let form_id = "form#woocommerce-checkout-shipping";

                $(form_id).validate({
                    rules: {
                        shipping_method: "required",
                    },
                    messages: {},
                    submitHandler: function (form) {

                        let shipping_data = {
                            action: 'woocommerce_get_cart_total',
                            form_data: $(form).serialize(),
                        };

                        $.ajax({
                            url: ecu_ajax_object.ajax_url,
                            type: "POST",
                            data: shipping_data,
                            success: function (response) {
                                $('#tab2-4').trigger('click');

                                $('div.outer-container').removeClass('active');
                                $('#tab2-4').next('div.outer-container').addClass('active');

                                $('.cta-buttons-product .continue-button').removeClass('shipping');
                                $('.cta-buttons-product .continue-button').addClass('payment');

                                $('.outer-container.active').html(response);
                            },
                            error: function (request, status, error) {
                                console.log(request.responseText);
                                console.log(error);
                            },
                        });

                        return false;

                    },
                    // other options
                });

                if ($(form_id).valid()) {
                    $(form_id).submit();
                }
            });
        }

        // Rename variation text
        if ($('.single-product').length > 0) {
            if ($('.product-type-variable').length > 0) {
                $("label[for='pa_variation']").html('SELECT YOUR PREFERRED SERVICE:');
            }

            setTimeout(
                function () {
                    let primary_image_title = $('.woocommerce-product-gallery__image:first img').attr('data-o_title');
                    console.log(primary_image_title);
                    $('.woocommerce-product-gallery__image:first img').attr('title', primary_image_title);
                    console.log($('.woocommerce-product-gallery__image:first img').attr('title'));
                }, 2000);

        }

        // Prevent user from just clicking on the checkout tab
        $('input.radio-tab-selector.last-tab').on("click", function () {
            if ($('.initial').length > 0) {
                return false;
            }
        });

        $('input.radio-tab-selector.first-tab').on("click", function () {

            // If the user goes back, add relative classes and functionality
            if ($('.payment-trigger').length > 0) {
                $('.cta-buttons-product .continue-button').removeClass('payment-trigger');
                $('.cta-buttons-product .continue-button').text('Continue to checkout');

                $('#tab2-1').trigger('click');

                $('div.outer-container').removeClass('active');
                $('#tab2-1').next('div.outer-container').addClass('active');

                $('.cta-buttons-product .continue-button').addClass('initial');
            }

            let $form = $('form.cart'),
                variation_id = $form.find('input[name=variation_id]').val() || 0;

            if (variation_id == '0') {
                if ($('.error-no-selection').length > 0) {
                    return false;
                } else {
                    $("<span class='error-no-selection'>Please select an option from the above</span>").insertAfter("form.variations_form");
                    return false;
                }
            }

            // Stop if no variation has been chosen
            if ($('.woocommerce-variation-add-to-cart-disabled').length > 0) {
                return false;
            }

            if ($("#validation-checkbox").prop('checked') != true) {
                $("<span class='error-no-selection'>This field is required</span>").insertAfter(".validation-checkbox-label");
                return false;
            }

        });

        $(document).on('click', '.button-container-helper, button.filter-button', function (e) {
            e.preventDefault();
            if ($('#ecu-mmy-filter-home-wrapper.ecu-mmy-filter-home').hasClass('open')) {
                $('#ecu-mmy-filter-home-wrapper.ecu-mmy-filter-home').removeClass('open');
            } else {
                $('#ecu-mmy-filter-home-wrapper.ecu-mmy-filter-home').addClass('open');
            }
        });

        $(document).on('click', 'a.message-button', function (e) {
            e.preventDefault();
            $('.contact-form-container').removeClass('d-none')
            $('ul.cta-container').addClass('d-none')
            $('html, body').animate({
                scrollTop: $('.contact-form-container').offset().top
            }, 1000);
        });

        if ($(window).width() > 997) {

            if ($('.single-product').length > 0) {
                // Woocommerce tabs
                let $window = $(window);
                let window_width = $window.width();
                let target = '.woocommerce-page div.product .woocommerce-tabs';

                let of = $('div.type-product').offset(), // this will return the left and top
                    left = of.left, // this will return left
                    right = $(window).width() - left - $('.woocommerce-tabs').width() // you can get right by calculate

                $(target).css('width', window_width + 'px');
                $(target).css('left', '-' + left + 'px');
                $(target).css('max-width', 'unset');
            }

            if ($('.single-product').length > 0) {
                $('form.woocommerce-form-login').insertBefore($('.messages-container'));

                $('.video-informational-popup-trigger').click(function () {
                    $('#video-popup-container-overlay').fadeIn(300);
                });
                $('#close').click(function () {
                    $('#video-popup-container-overlay').fadeOut(300);
                });
            }

            if ($('.home').length > 0) {

                // Our service homepage container
                let $window = $(window);
                let window_width = $window.width();
                let target = 'div.our-service-outer-container';

                let of = $(target).offset(),
                    left = of.left;

                $(target).css('width', window_width + 'px');
                $(target).css('left', '-' + left + 'px');
                $(target).css('max-width', 'unset');
            }
        }

        if ($(window).width() < 997) {
            $('.header-main .header-center').insertAfter($('.header-main .header-right'));

            // Header phone toggle only mobile
            $(document).on('click', '.phone-toggle', function (e) {
                e.preventDefault();
                if ($('.zf-templateWidth').css('display') == 'block') {
                    $('.zf-templateWidth').css('display', 'none');
                } else {
                    $('.zf-templateWidth').css('display', 'block');
                }
            });

            // Show services
            $(document).on('click', '.service-button a', function (e) {
                e.preventDefault();
                if ($('.entry-summary .tabs').css('display') == 'flex') {
                    $('.entry-summary .tabs').css('display', 'none');
                } else {
                    $('.entry-summary .tabs').css('display', 'flex');
                    $('ul.cta-container').css('display', 'none');

                    $('html, body').animate({
                        scrollTop: $('#below-tabs-container').offset().top - $(window).height()
                    }, 2000);
                }
            });

            $(document).on('click', 'a.message-product-button', function (e) {
                e.preventDefault();

                $('ul.wc-tabs li').removeClass('active');
                $('ul.wc-tabs li.contact_us_tab').addClass('active');

                $('#tab-description').css('display', 'none');
                $('#tab-contact_us').css('display', 'block');

                $('ul.cta-container').css('display', 'none');

                $('html, body').animate({
                    scrollTop: $('#tab-title-contact_us').offset().top
                }, 2000);

            });
        }

        if ($(window).width() < 790) {
            $(document).on('click', '.search-toggle', function (e) {
                e.preventDefault();
                if ($('.searchform-popup form.searchform').css('display') == 'block') {
                    $('.searchform-popup form.searchform').css('display', 'none');
                } else {
                    $('.searchform-popup form.searchform').css('display', 'block');
                }
            });
        }

    });
})(jQuery);

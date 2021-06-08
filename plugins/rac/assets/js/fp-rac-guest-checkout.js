/* global rac_guest_params */

jQuery(document).ready(function () {
    if (rac_guest_params.is_checkout) {
        jQuery("#billing_email").val(rac_guest_params.fp_rac_popup_email);
        var request = null;
        jQuery("#billing_email").on("focusout", function () {
            fp_rac_common_function_for_checkout_fields();
        });
        jQuery("#billing_first_name").on("change", function () {
            fp_rac_common_function_for_checkout_fields();
        });
        jQuery("#billing_last_name").on("change", function () {
            fp_rac_common_function_for_checkout_fields();
        });
        jQuery("#billing_phone").on("change", function () {
            fp_rac_common_function_for_checkout_fields();
        });
        window.onbeforeunload = function () {
            fp_rac_common_function_for_checkout_fields();
        };
        function fp_rac_common_function_for_checkout_fields() {
            var fp_rac_mail = jQuery("#billing_email").val();
            var atpos = fp_rac_mail.indexOf("@");
            var dotpos = fp_rac_mail.lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= fp_rac_mail.length)
            {
                console.log(rac_guest_params.console_error);
            } else {
                console.log(fp_rac_mail);
                var fp_rac_first_name = jQuery("#billing_first_name").val();
                var fp_rac_last_name = jQuery("#billing_last_name").val();
                var fp_rac_phone = jQuery("#billing_phone").val();
                var data = {
                    action: "rac_preadd_guest",
                    rac_email: fp_rac_mail,
                    rac_first_name: fp_rac_first_name,
                    rac_last_name: fp_rac_last_name,
                    rac_phone: fp_rac_phone,
                    rac_security: rac_guest_params.guest_entry,
                    rac_lang: rac_guest_params.current_lang_code
                }
                if (request == null) {
                    request = jQuery.post(rac_guest_params.ajax_url, data,
                            function (response) {
                                request = null;
                                console.log(response);
                            });
                }
            }
        }
    }
    var Email = '';
    var proceed_add_to_cart = false;
    var force_guest_email = rac_guest_params.force_guest == 'yes' ? true : false;
    var check = force_guest_email ? true : rac_guest_params.popup_already_displayed != 'yes';
    jQuery(".product_type_simple").on("click", function () {
        jQuery('.product_type_simple').removeClass('fp_rac_currently_clicked_atc');
        jQuery('.single_add_to_cart_button').removeClass('fp_rac_currently_clicked_atc');
        jQuery(this).addClass('fp_rac_currently_clicked_atc');
        var object_clicked = jQuery(this);
        if (jQuery(this).hasClass('ajax_add_to_cart') && !proceed_add_to_cart && (!rac_guest_params.is_cookie_already_set) && (rac_guest_params.enable_popup == 'yes')) {
            if ((!jQuery(this).hasClass('rac_hide_guest_poup')) && check) {
                common_function_get_guest_email_address_in_cookie(object_clicked);
                return false;
            }
        }
    });
    jQuery(".single_add_to_cart_button").on("click", function () {
        var object_clicked = jQuery(this);
        jQuery('.product_type_simple').removeClass('fp_rac_currently_clicked_atc');
        jQuery('.single_add_to_cart_button').removeClass('fp_rac_currently_clicked_atc');
        jQuery(this).addClass('fp_rac_currently_clicked_atc');
        if (!jQuery(this).hasClass('wc-variation-selection-needed') && !proceed_add_to_cart && !jQuery(this).hasClass('disabled') && (!rac_guest_params.is_cookie_already_set) && (rac_guest_params.enable_popup == 'yes')) {
            if ((!jQuery(this).hasClass('rac_hide_guest_poup')) && check) {
                common_function_get_guest_email_address_in_cookie(object_clicked);
                return false;
            }
        }
    });
    function common_function_get_guest_email_address_in_cookie(object_clicked) {
        var force_guest_email = rac_guest_params.force_guest == 'yes' ? false : true;
        if (force_guest_email) {
            jQuery('.single_add_to_cart_button').addClass('rac_hide_guest_poup');
            jQuery('.product_type_simple').addClass('rac_hide_guest_poup');
        }
        var data = {
            action: 'fp_rac_already_popup_displayed',
            already_displayed: 'yes',
        };
        jQuery.post(rac_guest_params.ajax_url, data, function () {});
        Email = '<input type="text" name="fp_rac_guest_email_in_cookie" id="fp_rac_guest_email_in_cookie" value="">';
        swal({
            title: rac_guest_params.email_label,
            html: Email,
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-thumbs-up"></i>' + rac_guest_params.add_to_cart_label,
            cancelButtonText: '<i class="fa fa-thumbs-down"></i>' + rac_guest_params.cancel_label
        }).then(function (isConfirm) {
            if (isConfirm) {
                var email_id = jQuery('#fp_rac_guest_email_in_cookie').val();
                if (email_id !== "") {
                    var atpos = email_id.indexOf("@");
                    var dotpos = email_id.lastIndexOf(".");
                    if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email_id.length) {
                        swal({
                            title: rac_guest_params.email_address_not_valid,
                            type: "error"
                        });
                        return false;
                    } else {
                        var data1 = {
                            action: 'fp_rac_set_guest_email_in_cookie',
                            cookie_guest_email: email_id,
                        };
                        jQuery.post(rac_guest_params.ajax_url, data1, function (response) {
                            if (response == 'success') {
                                proceed_add_to_cart = true;
                                if (rac_guest_params.is_shop && rac_guest_params.ajax_add_to_cart != 'yes') {
                                    var href = object_clicked.attr('href');
                                    window.location = href;
                                } else {
//                                    object_clicked.trigger('click');
                                    jQuery('.fp_rac_currently_clicked_atc').trigger('click');
                                }
                            }
                        });
                    }
                } else {
                    swal({
                        title: rac_guest_params.enter_email_address,
                        type: "error"
                    });
                    return false;
                }
            }
        });
        return false;
    }
});
<?php


add_action("wpcf7_before_send_mail", "wpcf7_generate_work_order_id");

/**
 * @param $cf7
 *
 * @return void|null
 */
function wpcf7_generate_work_order_id($cf7)
{
    // get the contact form object
    $wpcf = WPCF7_ContactForm::get_current();

    if ($wpcf->id == '97196') {
        global $wpdb;



        return $wpcf;

    }

    return $wpcf;
}

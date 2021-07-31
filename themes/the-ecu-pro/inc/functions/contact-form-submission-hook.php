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

        $work_order_table = 'wp_work_order_identifier';

        $last_work_order_row = $wpdb->get_results("SELECT * FROM  $work_order_table WHERE work_order_type = '' ORDER BY row_order_id DESC LIMIT 1;");

        // No more products die
        if (empty($last_work_order_row)) {
            return;
        }
    }

    return $wpcf;
}

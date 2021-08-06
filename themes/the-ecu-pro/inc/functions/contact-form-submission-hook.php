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

        $last_work_order_row = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $work_order_table ORDER BY row_order_id DESC LIMIT 1;"
            )
        );

        // No more products die
        if (empty($last_work_order_row)) {
            return $wpcf;
        } else {
            // We found the last row
            $last_saved_order     = substr($last_work_order_row[0]->work_order_name, -1);
            $last_saved_order_int = (int) $last_saved_order;
            $last_saved_order_int = $last_saved_order_int + 1;

            $work_order_updated_name = 'FRM' . $last_saved_order_int;

            $wpdb->insert(
                $work_order_table,
                array(
                    'work_order_name' => $work_order_updated_name,
                    'work_order_type' => 'FRM'
                )
            );

            wp_redirect( esc_url( add_query_arg( 'work-order', $work_order_updated_name, '/work-order-tag-creation/' ) ) );

        }

        return $wpcf;

    }

    return $wpcf;
}

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

            $version = '1.0.0';

            wp_enqueue_script(
                'theecupro-work-order',
                get_template_directory_uri() . '/js/work-order-config.js',
                [],
                $version,
                true
            );

            wp_localize_script(
                'theecupro-work-order',
                'ecu_object',
                array(
                    'id' => $work_order_updated_name
                )
            );

        }

        //return $wpcf;

    }

    return $wpcf;
}

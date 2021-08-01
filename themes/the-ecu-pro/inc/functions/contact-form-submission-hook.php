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

        // Hardcoded ID - 97237
        $order = wc_get_order( '97237' );

        // Lets get the product type
        $items = $order->get_items();
        $productType = '';
        $prodCnt = 0;
        foreach ($items as $item) {
            $product_id  = $item->get_product_id();
            $productType .= ($prodCnt <= 0) ? get_field("product_type", $product_id) : ',' . get_field(
                    "product_type",
                    $product_id
                );
            $prodCnt++;
        }

        if ($productType == 'FRMs') {
            $work_order_type = 'FRM';
        } else {
            $work_order_type = 'ECU';
        }

        $last_work_order_row = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $work_order_table WHERE work_order_type = '%s' ORDER BY row_order_id DESC LIMIT 1;",
                $work_order_type
            )
        );

        // No more products die
        if (empty($last_work_order_row)) {
            return $wpcf;
        } else {
            // We found the last row
            $last_saved_order = substr($last_work_order_row[0]->work_order_name, -1);
            $last_saved_order_int = (int)$last_saved_order;
            $last_saved_order_int = $last_saved_order_int + 1;

            $work_order_updated_name = $work_order_type . $last_saved_order_int;

            $wpdb->insert(
                $work_order_table,
                array(
                    'work_order_name'     => $work_order_updated_name,
                    'work_order_type' => $work_order_type,
                    'order_id' => $order->get_id(),
                    'user_id' => $order->get_user_id()
                )
            );
        }

        return $wpcf;

    }

    return $wpcf;
}

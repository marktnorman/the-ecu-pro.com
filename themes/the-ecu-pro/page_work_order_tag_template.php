<?php
/**
 * Template Name: Work order tag template
 */

get_header(); ?>

<div id="main" class="column1 boxed no-breadcrumbs"><!-- main -->

    <div class="outer-parent-container">
        <div class="container">
            <div class="row main-content-wrap">
                <div class="main-content col-lg-12">
                    <div class="woocommerce">
                        <div id="work-order-tag-container" class="woocommerce-order">
                            <div style="margin-top: 20px;">

                                <?php

                                if (isset($_GET) && !empty($_GET['success'])) {

                                    global $wpdb;

                                    $work_order_table = 'wp_work_order_identifier';

                                    $last_work_order_row = $wpdb->get_results(
                                        $wpdb->prepare(
                                            "SELECT * FROM $work_order_table WHERE work_order_type = '%s' ORDER BY row_order_id DESC LIMIT 1;",
                                            $_GET['success']
                                        )
                                    );

                                    // No more products die
                                    if (!empty($last_work_order_row)) {
                                        // We found the last row
                                        $last_saved_order     = substr($last_work_order_row[0]->work_order_name, -1);
                                        $last_saved_order_int = (int) $last_saved_order;
                                        $last_saved_order_int = $last_saved_order_int + 1;

                                        $work_order_updated_name = $_GET['success'] . $last_saved_order_int;

                                        $wpdb->insert(
                                            $work_order_table,
                                            array(
                                                'work_order_name' => $work_order_updated_name,
                                                'work_order_type' => $_GET['success'],
                                            )
                                        );
                                    } else {

                                        $work_order_updated_name = $_GET['success'] . '1';

                                        $wpdb->insert(
                                            $work_order_table,
                                            array(
                                                'work_order_name' => $work_order_updated_name,
                                                'work_order_type' => $_GET['success'],
                                            )
                                        );
                                    }

                                    ?>

                                    <div class="success-container">
                                        <h3>Thank you!</h3>
                                        <p>Please write the following order number on your box with the parts that you
                                            send in:</p>
                                        <span class="order-id-message">Important - </span><span class="order-id"><?php echo $work_order_updated_name; ?></span>
                                    </div>

                                <?php } else {
                                    if (have_posts()) {
                                        while (have_posts()) {
                                            the_post();
                                            the_content();
                                        }
                                    }
                                }
                                ?>
                            </div>
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

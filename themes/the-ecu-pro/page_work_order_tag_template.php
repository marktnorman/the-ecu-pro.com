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

                                if (isset($_GET) && !empty($_GET['success']) && !empty($_GET['associated-email'])) {

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

                                    // API mail request
                                    require_once(__DIR__ . 'vendor/autoload.php');

                                    // Configure API key authorization: api-key
                                    $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey(
                                        'api-key',
                                        SENDINBLUE_API_KEY
                                    );

                                    // Uncomment below line to configure authorization using: partner-key
                                    // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('partner-key', 'YOUR_API_KEY');
                                    $apiInstance                 = new SendinBlue\Client\Api\TransactionalEmailsApi(
                                    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
                                    // This is optional, `GuzzleHttp\Client` will be used as default.
                                        new GuzzleHttp\Client(),
                                        $config
                                    );
                                    $sendSmtpEmail               = new \SendinBlue\Client\Model\SendSmtpEmail(
                                    ); // \SendinBlue\Client\Model\SendSmtpEmail | Values to send a transactional email
                                    $sendSmtpEmail['to']         = array(
                                        array(
                                            //'email' => 'theecupro001@gmail.com',
                                            'email' => 'marktnorman@gmail.com',
                                            'name'  => 'The ECU Pro'
                                        )
                                    );
                                    $sendSmtpEmail['templateId'] = 1;
                                    $sendSmtpEmail['params']     = array('name' => 'Uys', 'surname' => 'Cloete');
                                    $sendSmtpEmail['headers']    = array('X-Mailin-custom' => 'content-type:application/json|accept:application/json');
                                    $sendSmtpEmail['tags'] = array('work-order-id' => $work_order_updated_name, 'associated-work-order-email' => $_GET['associated-email']);

                                    try {
                                        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
                                        print_r($result);
                                    } catch (Exception $e) {
                                        echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(
                                        ), PHP_EOL;
                                    }

                                    ?>

                                    <div class="success-container">
                                        <h3>Thank you!</h3>
                                        <p>Please write the following order number on your box with the parts that you
                                            send in:</p>
                                        <span class="order-id-message">Important - </span><span
                                                class="order-id"><?php echo $work_order_updated_name; ?></span>
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

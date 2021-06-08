<?php

add_action('woocommerce_thankyou', 'stop_redirect', 99, 1);

function stop_redirect($order_id)
{
    $order        = wc_get_order($order_id);
    $order_status = $order->get_status();

    if ($order->has_status(array('processing', 'completed'))) {
        $user = $order->get_user();

        $html = '<div class="thank-you-container">';
        $html .= '<h2>Thank You</h2>';
        $html .= '<span class="order-number">' . $order->get_order_number() . '</span>';
        $html .= '<div class="thank-you-inner">';
        $html .= '<span class="confirmed">You order is confirmed</span>';
        $html .= '<p class="confirmed-paragraph">You`ll receive an email when your order is on its way.</p>';
        $html .= '</div>';
        $html .= '</div>';
        ?>

        <script type="text/javascript">
            $(document).ready(function (e) {
                e.preventDefault();

                $('.outer-container.checkout-container.active').empty();
                $('.outer-container.checkout-container.active').html(<?php echo $html; ?>);

                return false;
            });
        </script>
    <?php }
}

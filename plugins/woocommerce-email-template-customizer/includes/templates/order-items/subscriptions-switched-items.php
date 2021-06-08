<?php

defined( 'ABSPATH' ) || exit;

$text_align  = is_rtl() ? 'right' : 'left';
$margin_side = is_rtl() ? 'left' : 'right';

$trans_product  = $props['content']['product'] ?? esc_html__( 'Product', 'viwec-email-template-customizer' );
$trans_quantity = $props['content']['quantity'] ?? esc_html__( 'Quantity', 'viwec-email-template-customizer' );
$trans_price    = $props['content']['price'] ?? esc_html__( 'Price', 'viwec-email-template-customizer' );
$show_sku       = ! empty( $props['attrs']['show_sku'] ) && $props['attrs']['show_sku'] == 'true' ? true : false;

$border       = $render->get_style( $props, 'childStyle', '.viwec-subscription-border' );
$style        = "text-align:left;padding:6px;" . $border;
$header_style = $render->get_style( $props, 'childStyle', '.viwec-subscription-header' );
$body_style   = $render->get_style( $props, 'childStyle', '.viwec-subscription-body' );

$odd_row  = $render->get_style( $props, 'childStyle', '.viwec-subscription-body-odd' );
$even_row = $render->get_style( $props, 'childStyle', '.viwec-subscription-body-even' );


do_action( 'woocommerce_email_before_subscription_table', $order, $sent_to_admin, $plain_text, $email );

?>

    <table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' style='border-collapse:collapse;line-height: 1'>
        <thead>
        <tr>
            <th style='<?php echo esc_attr( $style . $header_style ) ?>'><?php echo esc_html( $trans_product ) ?></th>
            <th style='<?php echo esc_attr( $style . $header_style ) ?>'><?php echo esc_html( $trans_quantity ) ?></th>
            <th style='<?php echo esc_attr( $style . $header_style ) ?>'><?php echo esc_html( $trans_price ) ?></th>
        </tr>
        </thead>

        <tbody>
		<?php
		$i = 0;
		foreach ( $items as $item_id => $item ) {
			$product = $item->get_product();
			$sku     = $purchase_note = $image = '';
			$p_url   = $product->get_permalink();

			if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
				continue;
			}

			if ( is_object( $product ) ) {
				$sku           = $product->get_sku();
				$purchase_note = $product->get_purchase_note();
			}

			$row_style = $i % 2 ? $even_row : $odd_row;

			?>
            <tr>
                <td style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
					<?php
					$name = "<a style='color: inherit' href='{$p_url}'>{$item->get_name()}</a>";
					echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $name, $item, false ) );

					if ( $show_sku && $sku ) {
						echo '<small>' . wp_kses_post( ' (#' . $sku . ')' ) . '</small>';
					}

					do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

					wc_display_item_meta(
						$item,
						array(
							'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
						)
					);

					// allow other plugins to add additional product information here.
					do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
					do_action( 'viwec_order_item_parts', $item_id, $item, $order, false );
					?>
                </td>

                <td style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
					<?php
					$qty          = $item->get_quantity();
					$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

					if ( $refunded_qty ) {
						$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * - 1 ) ) . '</ins>';
					} else {
						$qty_display = esc_html( $qty );
					}
					echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
					?>
                </td>

                <td style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
					<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                </td>
            </tr>
			<?php
			$i ++;

			if ( $show_purchase_note && $purchase_note ) {
				$row_style = $i % 2 ? $even_row : $odd_row;
				?>
                <tr>
                    <td colspan="3" style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
						<?php
						echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
						?>
                    </td>
                </tr>
				<?php
				$i ++;
			}
		}

		?>
        </tbody>

        <tfoot>
		<?php
		if ( $totals = $order->get_order_item_totals() ) {

			foreach ( $totals as $type => $total ) {
				switch ( $type ) {
					case 'cart_subtotal':
						$label = ! empty( $props['content']['subtotal'] ) ? $props['content']['subtotal'] : $total['label'];
						break;
					case 'shipping':
						$label = ! empty( $props['content']['shipping'] ) ? $props['content']['shipping'] : $total['label'];
						break;
					case 'payment_method':
						$label = ! empty( $props['content']['payment_method'] ) ? $props['content']['payment_method'] : $total['label'];
						break;
					case 'order_total':
						$label = ! empty( $props['content']['total'] ) ? $props['content']['total'] : $total['label'];
						break;
					case 'discount':
						$label = ! empty( $props['content']['discount'] ) ? $props['content']['discount'] : $total['label'];
						break;
					default:
						$label = $total['label'];
				}

				$row_style = $i % 2 ? $even_row : $odd_row;

				?>
                <tr>
                    <th class="td" scope="row" colspan="2" style="<?php echo esc_attr( $style . $body_style . $row_style ) ?>">
						<?php echo esc_html( $label ); ?>
                    </th>
                    <td class="td" style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
						<?php echo wp_kses_post( $total['value'] ); ?>
                    </td>
                </tr>
				<?php
				$i ++;
			}
		}
		if ( $order->get_customer_note() ) {
			$row_style = $i % 2 ? $even_row : $odd_row;
			?>
            <tr>
                <th class="td" scope="row" colspan="2" style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
					<?php esc_html_e( 'Note:', 'woocommerce-subscriptions' ); ?>
                </th>
                <td class="td" style='<?php echo esc_attr( $style . $body_style . $row_style ) ?>'>
					<?php echo wp_kses_post( wptexturize( $order->get_customer_note() ) ); ?>
                </td>
            </tr>
			<?php
		}
		?>
        </tfoot>

    </table>

<?php
do_action( 'woocommerce_email_after_subscription_table', $order, $sent_to_admin, $plain_text, $email );



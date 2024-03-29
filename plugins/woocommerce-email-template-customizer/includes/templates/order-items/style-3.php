<?php
defined( 'ABSPATH' ) || exit;

$text_align     = is_rtl() ? 'right' : 'left';
$margin_side    = is_rtl() ? 'left' : 'right';
$item_style     = ! empty( $props['childStyle']['.viwec-item-row'] ) ? $render->parse_styles( $props['childStyle']['.viwec-item-row'] ) : '';
$img_size       = ! empty( $props['childStyle']['.viwec-product-img'] ) ? $render->parse_styles( $props['childStyle']['.viwec-product-img'] ) : '';
$name_size      = ! empty( $props['childStyle']['.viwec-product-name'] ) ? $render->parse_styles( $props['childStyle']['.viwec-product-name'] ) : '';
$quantity_size  = ! empty( $props['childStyle']['.viwec-product-quantity'] ) ? $render->parse_styles( $props['childStyle']['.viwec-product-quantity'] ) : '';
$price_size     = ! empty( $props['childStyle']['.viwec-product-price'] ) ? $render->parse_styles( $props['childStyle']['.viwec-product-price'] ) : '';
$items_distance = ! empty( $props['childStyle']['.viwec-product-distance'] ) ? $render->parse_styles( $props['childStyle']['.viwec-product-distance'] ) : '';
$show_sku       = ! empty( $props['attrs']['show_sku'] ) && $props['attrs']['show_sku'] == 'true' ? true : false;
$trans_quantity = $props['content']['quantity'] ?? 'x';
$font_size      = '15px';
$list_items_key = array_keys( $items );
$end_id         = end( $list_items_key );

foreach ( $items as $item_id => $item ) {

	if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		continue;
	}

	$product = $item->get_product();
	$sku     = $purchase_note = $image = '';

	if ( ! is_object( $product ) ) {
		continue;
	}
	$sku           = $product->get_sku();
	$purchase_note = $product->get_purchase_note();
	$pid           = $product->get_id();
	$image         = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
	$image         = $image ? $image : wc_placeholder_img_src( 'woocommerce_thumbnail' );
	$image         = apply_filters( 'viwec_custom_placeholder_img_src', $image );
	$p_url         = $product->get_permalink();
	?>
    <table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'
           style='<?php echo esc_attr( $item_style ) ?> border-collapse:collapse;font-size: 0;'>
        <tr>
            <td valign='middle'>
                <!--[if mso | IE]>
                <table width="100%" role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="" style="vertical-align:middle;width: 30%"><![endif]-->
                <div class='viwec-responsive ' style='vertical-align:middle;display:inline-block;width: 30%'>
                    <table align="left" width="100%" border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url( $p_url ) ?>">
									<?php
									if ( function_exists( 'fpd_get_option' ) && fpd_get_option( 'fpd_order_product_thumbnail' ) ) {
										ob_start();
										do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
										$img = ob_get_clean();
										$img = str_replace( [ 'border: 1px solid #ccc; float: left; margin-right: 5px; margin-bottom: 5px; max-width: 30%;' ], '', trim( $img ) );
										echo( $img );
									} else {
										?>
                                        <img width='100%' src='<?php echo esc_url( $image ) ?>' style='vertical-align: middle'>
										<?php
									}
									?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if mso | IE]></td>
                <td class="" style="vertical-align:middle;width: 50%;">
                <![endif]-->
                <div class='viwec-responsive'
                     style='width:49.5%;vertical-align:middle;display:inline-block;line-height: 150%;font-size: <?php echo esc_attr( $font_size ) ?>'>
                    <table align="left" width="100%" border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td class="viwec-mobile-hidden" style="padding: 0;width: 15px;"></td>
                            <td style="" class="viwec-responsive-center">
                                <a href="<?php echo esc_url( $p_url ) ?>" class="viwec-responsive-center">
                                    <p style="<?php echo esc_attr( $name_size ) ?>" class="viwec-responsive-center">
										<?php
										echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
										if ( $show_sku && $sku ) {
											echo '<small>' . wp_kses_post( ' (#' . $sku . ')' ) . '</small>';
										}
										?>
                                    </p>
                                </a>
                                <p style="<?php echo esc_attr( $quantity_size ) ?>">
									<?php
									echo esc_html( $trans_quantity ) . ' ';
									$qty = $item->get_quantity();

									$refunded_qty = $order->get_qty_refunded_for_item( $item_id );
									if ( $refunded_qty ) {
										$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * - 1 ) ) . '</ins>';
									} else {
										$qty_display = esc_html( $qty );
									}
									echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
									echo '<br>';

									?>
                                </p>
								<?php
								if ( ! ( function_exists( 'fpd_get_option' ) && fpd_get_option( 'fpd_order_product_thumbnail' ) ) ) {
									do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
								}

								wc_display_item_meta(
									$item,
									array(
										'before'       => '<div class=""><div>',
										'after'        => '</div></div>',
										'separator'    => '</div><div>',
										'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
									)
								);

								do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );

								do_action( 'viwec_order_item_parts', $item_id, $item, $order, false );

								if ( $show_purchase_note && $purchase_note ) {
									echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
								}
								?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if mso | IE]></td>
                <td class="" style="vertical-align:middle;width: 20%;">
                <![endif]-->
                <div class='viwec-responsive'
                     style='text-align:right;width: 20%;vertical-align:middle;display:inline-block;line-height: 150%;font-size: <?php echo esc_attr( $font_size ) ?>'>
                    <table align="left" width="100%" border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td class="viwec-mobile-hidden" style="padding: 0;width: 15px;"></td>
                            <td class="viwec-responsive-center" style="vertical-align: middle">
                                <p style="white-space: nowrap;min-width: fit-content;<?php echo esc_attr( $price_size ) ?>"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
	<?php
	if ( $end_id !== $item_id ) {
		?>
        <div style='width: 100%; <?php echo esc_attr( $items_distance ); ?>'></div>
		<?php
	}
}



<?php

namespace VIWEC\INC;

defined( 'ABSPATH' ) || exit;

class View_Product {
	protected static $instance = null;
	protected $wpdb;

	public static function init() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		add_action( 'woocommerce_init', [ $this, 'save_clicked' ] );
		add_action( 'admin_menu', [ $this, 'add_report_menu' ] );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'mark_order' ] );
//		add_action( 'wp_head', [ $this, 'test' ] );
	}

	public function test() {
//		echo '<pre>' . print_r( wc()->session, true ) . '</pre>';
	}

	public function insert_query( $pid ) {
		$this->wpdb->insert( VIWEC_VIEW_PRODUCT_TB, [ 'clicked_date' => current_time( 'U' ), 'product' => $pid ], '%d' );
	}

	public function save_clicked() {
		if ( ! empty( $_REQUEST['viwec_rt'] ) ) {
			$param = sanitize_text_field( $_REQUEST['viwec_rt'] );

			if ( substr( NONCE_SALT, 0, 10 ) === substr( $param, 0, 10 ) ) {

				if ( ! is_user_logged_in() && isset( WC()->session ) && ! WC()->session->has_session() ) {
					WC()->session->set_customer_session_cookie( true );
				}

				$pid = substr( $param, 10 );
				$this->insert_query( $pid );
				wc()->session->set( 'viwec_come_from_email', $pid );
			}
		}
	}

	public function mark_order( $order_id ) {
		$pid = wc()->session->get( 'viwec_come_from_email' );
		if ( $pid ) {
			update_post_meta( $order_id, '_viwec_from_suggestion_product', $pid );
			wc()->session->__unset( 'viwec_come_from_email' );
		}
	}

	public function add_report_menu() {
		$hook = add_submenu_page( 'edit.php?post_type=viwec_template', esc_html__( 'Report', 'viwec-email-template-customizer' ),
			esc_html__( 'Report', 'viwec-email-template-customizer' ), 'manage_options', 'viwec_report', [ $this, 'report_page' ] );

		add_action( "admin_print_scripts-{$hook}", [ $this, 'get_last_month_data' ] );
	}

	public function get_last_month_data() {
		$to   = current_time( 'U' );
		$from = $to - MONTH_IN_SECONDS;

		if ( isset( $_POST['viwec_nonce'] ) && wp_verify_nonce( $_POST['viwec_nonce'], 'viwec_view_product' ) ) {
			$_to   = ! empty( $_POST['viwec_to'] ) ? sanitize_text_field( $_POST['viwec_to'] ) : '';
			$_from = ! empty( $_POST['viwec_from'] ) ? sanitize_text_field( $_POST['viwec_from'] ) : '';
			if ( $_to ) {
				$to = strtotime( $_to ) + DAY_IN_SECONDS - 1;
			}
			if ( $_from ) {
				$from = strtotime( $_from );
			}
		}
		$table = VIWEC_VIEW_PRODUCT_TB;

		$q    = "SELECT * FROM {$table} WHERE clicked_date >= {$from} AND clicked_date <= {$to}";
		$data = $this->wpdb->get_results( $q );

		$data = wp_list_pluck( $data, 'product' );

		$data = array_count_values( $data );

		$args = [
			'numberposts'  => '-1',
			'date_before'  => date_i18n( 'Y-m-d', $to ),
			'date_after'   => date_i18n( 'Y-m-d', $from ),
			'meta_key'     => '_viwec_from_suggestion_product',
			'meta_compare' => 'EXISTS'
		];

		$orders = count( wc_get_orders( $args ) );

		$chart_label = [ esc_html__( 'Ordered', 'viwec-email-template-customizer' ), '' ];
		$chart_data  = [ $orders, '' ];

		foreach ( $data as $pid => $quantity ) {
			$product = wc_get_product( $pid );
			if ( ! $product ) {
				continue;
			}

			$chart_label[] = $product->get_formatted_name();
			$chart_data[]  = $quantity;
		}

		wp_localize_script( VIWEC_SLUG . '-report', 'viwecParams',
			[ 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'chartLabel' => $chart_label, 'chartData' => $chart_data ] );
	}

	public function report_page() {
		$today      = ! empty( $_POST['viwec_to'] ) ? sanitize_text_field( $_POST['viwec_to'] ) : date_i18n( 'yy-m-d', current_time( 'U' ) );
		$last_month = ! empty( $_POST['viwec_from'] ) ? sanitize_text_field( $_POST['viwec_from'] ) : date_i18n( 'yy-m-d', current_time( 'U' ) - MONTH_IN_SECONDS );
		?>
        <h1><?php esc_html_e( 'Report', 'viwec-email-template-customizer' ); ?></h1>
        <div class="vi-ui segments">
            <div class="vi-ui segment">
                <form method="post">
					<?php wp_nonce_field( 'viwec_view_product', 'viwec_nonce' ) ?>
                    <input type="date" class="viwec-from" name="viwec_from" value="<?php echo esc_attr( $last_month ) ?>">
                    <input type="date" class="viwec-to" name="viwec_to" value="<?php echo esc_attr( $today ) ?>">
                    <button type="submit" name="viwec_view_product_report" class="button-primary">
						<?php esc_html_e( 'View', 'viwec-email-template-customizer' ); ?>
                    </button>
                </form>
            </div>

            <div class="vi-ui segment">
                <div class="viwec-chart-title">
                    <h2><?php esc_html_e( 'Ordered & clicked from suggestion product in email', 'viwec-email-template-customizer' ); ?></h2>
                </div>
                <div class="viwec-canvas-chart">
                    <canvas id="viwec-report-chart"></canvas>
                </div>
            </div>
        </div>
		<?php
	}
}


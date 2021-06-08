<?php

if( ! defined('ABSPATH') )	exit;		// Exit if accessed directly

if( ! class_exists('Ph_Multicarrier_Product_Settings') ) {
	class Ph_Multicarrier_Product_Settings {
		public function __construct(){
			// Add a custome field in product shipping page
			add_action( 'woocommerce_product_options_shipping', array($this,'add_settings_to_product_shipping')  );
			// Saving the values
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_settings' ) );
		}

		public function add_settings_to_product_shipping() {
			//Pre packed
            woocommerce_wp_checkbox(
				array(
					'id'				=> '_ph_multi_carrier_pre_packed',
					'label'				=> __('Pre Packed Product (Multi-Carrier)','eha_multi_carrier_shipping'),
					'description'		=> __('Check this if the item comes in boxes. It will consider as a separate package and ship in its own box.', 'eha_multi_carrier_shipping'),
					'desc_tip'			=> 'true',
					)
				);
		}

		/**
		 * Save Settings
		 */
		public function save_product_settings($post_id) {
			// Save Prepack option
			$prepacked = ! empty($_POST['_ph_multi_carrier_pre_packed']) ? $_POST['_ph_multi_carrier_pre_packed'] : null;
			update_post_meta( $post_id, '_ph_multi_carrier_pre_packed', $prepacked );
		}
	}
}

new Ph_Multicarrier_Product_Settings();
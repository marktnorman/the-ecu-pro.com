<?php
/*
 * Plugin Name: ECU Product Exporter for WooCommerce 
 * Description: Expert ECU proudcts with Excel
 * Version: 1.0
 * Author: Shao
 *
 * WC requires at least: 2.2
 * WC tested up to: 3.6.5
 *  
 * License: GPL2
 * Created On: 12-09-2019
 * Updated On: 18-09-2019
 */


if (!defined('ABSPATH') || !is_admin()) {
	return;
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
//ADD MENU LINK AND PAGE FOR WOOCOMMERCE exportER
add_action('admin_menu', 'ecu_product_exporter_menu');

function ecu_product_exporter_menu()
{
	add_menu_page('ECU Product Exporter', 'ECU Product Exporter', 'administrator', 'ecu-product-exporter', 'ecu_product_exporter_init', 'dashicons-products', '50');
}
//load css and js
function ecu_product_exporter_enqueue_scripts()
{
}
add_action('admin_enqueue_scripts', 'ecu_product_exporter_enqueue_scripts');

//init admin page
function ecu_hf_user_permission()
{
	// Check if user has rights to export
	$current_user		 = wp_get_current_user();
	$current_user->roles = apply_filters('hf_add_user_roles', $current_user->roles);
	$current_user->roles = array_unique($current_user->roles);
	$user_ok			 = false;
	$wf_roles			 = apply_filters('hf_user_permission_roles', array('administrator', 'shop_manager'));
	if ($current_user instanceof WP_User) {
		$can_users = array_intersect($wf_roles, $current_user->roles);
		if (!empty($can_users) || is_super_admin($current_user->ID)) {
			$user_ok = true;
		}
	}
	return $user_ok;
}
function ecu_product_exporter_init()
{

	if (!empty($_GET['action']) && !empty($_GET['page']) && $_GET['page'] == 'ecu-product-exporter') {
		switch ($_GET['action']) {
			case "export":
				$user_ok = ecu_hf_user_permission();
				if ($user_ok) {
					include_once(plugin_dir_path(__FILE__) . './ecu-product-export-class.php');
					ECU_ProdImpExpCsv_Exporter::do_export('product');
				} else {
					wp_redirect(wp_login_url());
				}
				break;
		}
	}
	$post_columns = include(plugin_dir_path(__FILE__) . './ecu-product-columns.php');
	$post_special_columns = include(plugin_dir_path(__FILE__) . './ecu-product-special-columns.php');
?>
	<form action="<?php echo admin_url('admin.php?page=ecu-product-exporter&action=export'); ?>" method="post">
		<div class="col-md-12">
		<?php foreach ($post_special_columns as $pkey => $pcolumn) {
			?>
				<tr>
					<td>
						<?php
						$tmpkey = $pkey;
						?>
						<input hidden type="text" name="columns_name[<?php echo $pkey; ?>]" value="<?php echo $tmpkey; ?>" class="input-text" />
					</td>
				</tr>
			<?php } ?>
			<?php foreach ($post_columns as $pkey => $pcolumn) {
			?>
				<tr>
					<td>
						<?php
						$tmpkey = $pkey;
						?>
						<input hidden type="text" name="columns_name[<?php echo $pkey; ?>]" value="<?php echo $tmpkey; ?>" class="input-text" />
					</td>
				</tr>
			<?php } ?>
		</div>
		<div class="col-md-12" style="display:flex;justify-content:center">
			<input hidden type="text" name="limit" value="100" class="input-text" />
			<input type="submit" id="btn-product-export" style="margin-top:100px" class="btn btn-lg btn-primary" value="Export Products" />
		</div>
	</form>
<?php
}

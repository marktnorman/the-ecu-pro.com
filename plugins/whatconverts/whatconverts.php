<?php
/*
Plugin Name: WhatConverts
Plugin URI: http://wordpress.org/extend/plugins/whatconverts/
Description: Enables <a href="https://www.whatconverts.com/">WhatConverts</a> on all pages. To setup, 1) Navigate to the plugin settings, 'Settings' > 'WhatConverts', 2) Add your Profile ID from <a href="http://app.whatconverts.com/">WhatConverts</a>.
Version: 1.0.4
Author: WhatConverts
Author URI: https://www.whatconverts.com/
*/
	  
if (!defined('WP_PLUGIN_DIR'))
	define('WP_PLUGIN_DIR',plugins_url());
	  
function activate_whatconverts() {
    $whatconverts_profile_id = get_option('whatconverts_profile_id');
	if (empty($whatconverts_profile_id))
        add_option('whatconverts_profile_id', '00000');
}

function deactive_whatconverts() {
    //delete_option('whatconverts_profile_id');
}

function admin_init_whatconverts() {
	register_setting('whatconverts', 'whatconverts_profile_id');
}

function admin_menu_whatconverts() {
	add_options_page('WhatConverts', 'WhatConverts', 'manage_options', 'whatconverts', 'options_page_whatconverts');
}

function options_page_whatconverts() {
	include(WP_PLUGIN_DIR.'/whatconverts/options.php');  
}

function whatconverts() {
	$whatconverts_profile_id = get_option('whatconverts_profile_id');
	if ($whatconverts_profile_id != '00000') 
		wp_enqueue_script( 'whatconverts-tracking-script', '//scripts.iconnode.com/' . $whatconverts_profile_id . '.js', array(), '', true );
}

register_activation_hook(__FILE__, 'activate_whatconverts');
register_deactivation_hook(__FILE__, 'deactive_whatconverts');

if (is_admin()) {
	add_action('admin_init', 'admin_init_whatconverts');
	add_action('admin_menu', 'admin_menu_whatconverts');
}

if (!is_admin()) {
	add_action('wp_enqueue_scripts', 'whatconverts');
}
?>
<?php

if (!defined('WPINC')) {
    exit;
}

class WF_ProdImpExpCsv_Settings {

    /**
     * Product Exporter Tool
     */
    public static function save_settings() {
        $nonce = (isset($_POST['wt_nonce']) ? sanitize_text_field($_POST['wt_nonce']) : '');
        if (!wp_verify_nonce($nonce,WF_PROD_IMP_EXP_ID) || !WF_Product_Review_Import_Export_CSV::hf_user_permission()) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wf_csv_import_export'));
        }        
        global $wpdb;
        $pro_ftp_server = !empty($_POST['pro_ftp_server']) ? sanitize_text_field($_POST['pro_ftp_server']) : '';
        $pro_ftp_user = !empty($_POST['pro_ftp_user']) ? stripslashes(htmlentities($_POST['pro_ftp_user'])) : '';
        $pro_ftp_password = !empty($_POST['pro_ftp_password']) ? wp_unslash($_POST['pro_ftp_password']) : '';
        $pro_ftp_port = !empty($_POST['pro_ftp_port']) ? absint($_POST['pro_ftp_port']) : 21;
        $pro_use_ftps = !empty($_POST['pro_use_ftps']) ? true : false;
        $pro_use_pasv = !empty($_POST['pro_use_pasv']) ? true : false;
        $pro_enable_ftp_ie = !empty($_POST['pro_enable_ftp_ie']) ? true : false;
        $pro_use_sftp = !empty($_POST['pro_use_sftp']) ? true : false;
        $auto_pro_use_implicit_ftp = !empty($_POST['auto_pro_use_implicit_ftp']) ? true : false;

        $rev_ftp_server = !empty($_POST['rev_ftp_server']) ? sanitize_text_field($_POST['rev_ftp_server']) : '';
        $rev_ftp_user = !empty($_POST['rev_ftp_user']) ? stripslashes(htmlentities($_POST['rev_ftp_user'])) : '';
        $rev_ftp_password = !empty($_POST['rev_ftp_password']) ? wp_unslash($_POST['rev_ftp_password']) : '';
        $rev_ftp_port = !empty($_POST['rev_ftp_port']) ? absint($_POST['rev_ftp_port']) : 21;
        $rev_use_ftps = !empty($_POST['rev_use_ftps']) ? true : false;
        $rev_use_pasv = !empty($_POST['rev_use_pasv']) ? true : false;
        $rev_enable_ftp_ie = !empty($_POST['rev_enable_ftp_ie']) ? true : false;
        $rev_use_sftp = !empty($_POST['rev_use_sftp']) ? true : false;


        $pro_auto_export = !empty($_POST['pro_auto_export']) ? sanitize_text_field($_POST['pro_auto_export']) : 'Disabled';
        $pro_auto_export_ftp_path = !empty($_POST['pro_auto_export_ftp_path']) ? sanitize_text_field($_POST['pro_auto_export_ftp_path']) : '/';
        $pro_auto_export_do_shortcode = !empty($_POST['pro_auto_export_do_shortcode']) ? sanitize_text_field($_POST['pro_auto_export_do_shortcode']) : null;
        $pro_auto_export_ftp_file_name = !empty($_POST['pro_auto_export_ftp_file_name']) ? sanitize_text_field($_POST['pro_auto_export_ftp_file_name']) : null;
        $pro_auto_export_start_time = !empty($_POST['pro_auto_export_start_time']) ? sanitize_text_field($_POST['pro_auto_export_start_time']) : '';
        $pro_auto_export_interval = !empty($_POST['pro_auto_export_interval']) ? sanitize_text_field($_POST['pro_auto_export_interval']) : '';
        $pro_auto_export_delimiter = !empty($_POST['delimiter']) ? $_POST['delimiter'] : ''; // WPCS: CSRF ok, input var ok.
        $pro_auto_export_profile = !empty($_POST['pro_auto_export_profile']) ? sanitize_text_field($_POST['pro_auto_export_profile']) : '';
        $pro_auto_export_categories = !empty($_POST['pro_auto_export_categories']) ? wc_clean($_POST['pro_auto_export_categories']) : null;
        $pro_auto_export_prod_status= !empty($_POST['pro_auto_export_prod_status']) ? wc_clean($_POST['pro_auto_export_prod_status']) : null;
        $pro_auto_export_include_hidden_meta = !empty($_POST['pro_auto_export_include_hidden_meta']) ? sanitize_text_field($_POST['pro_auto_export_include_hidden_meta']) : null;

        $pro_auto_import = !empty($_POST['pro_auto_import']) ? sanitize_text_field($_POST['pro_auto_import']) : 'Disabled';
        $pro_auto_import_multiple_files_import = !empty($_POST['pro_auto_import_multiple_files_import']) ? true : false;
        $pro_auto_import_file = !empty($_POST['pro_auto_import_file']) ? sanitize_text_field($_POST['pro_auto_import_file']) : null;
        $pro_auto_import_delimiter = !empty($_POST['pro_auto_import_delimiter']) ? $_POST['pro_auto_import_delimiter'] : ','; // WPCS: CSRF ok, input var ok.
        $pro_auto_import_start_time = !empty($_POST['pro_auto_import_start_time']) ? sanitize_text_field($_POST['pro_auto_import_start_time']) : '';
        $pro_auto_import_interval = !empty($_POST['pro_auto_import_interval']) ? sanitize_text_field($_POST['pro_auto_import_interval']) : '';
        $pro_auto_import_profile = !empty($_POST['pro_auto_import_profile']) ? sanitize_text_field($_POST['pro_auto_import_profile']) : '';
        $pro_auto_import_merge = !empty($_POST['pro_auto_import_merge']) ? true : false;
        $pro_auto_import_skip = !empty($_POST['pro_auto_import_skip']) ? true : false;
        $pro_auto_delete_products = !empty($_POST['pro_auto_delete_products']) ? true : false;
        $pro_auto_new_prod_status = !empty($_POST['pro_auto_new_prod_status']) ? wc_clean($_POST['pro_auto_new_prod_status']) : '';
        $pro_auto_use_chidren_sku = !empty($_POST['pro_auto_use_chidren_sku']) ? true : false;
        $pro_auto_use_sku_upsell_crosssell = isset($_POST['pro_auto_use_sku_upsell_crosssell']) ? true : false;
        $pro_auto_merge_empty_cells = isset($_POST['pro_auto_merge_empty_cells']) ? true : false;
        $pro_auto_stop_thumbnail_regen = isset($_POST['pro_auto_stop_thumbnail_regen']) ? true : false;


        $rev_auto_export = !empty($_POST['rev_auto_export']) ? sanitize_text_field($_POST['rev_auto_export']) : 'Disabled';
        $rev_auto_export_start_time = !empty($_POST['rev_auto_export_start_time']) ? sanitize_text_field($_POST['rev_auto_export_start_time']) : '';
        $rev_auto_export_interval = !empty($_POST['rev_auto_export_interval']) ? sanitize_text_field($_POST['rev_auto_export_interval']) : '';
        $rev_auto_export_ftp_path = !empty($_POST['rev_auto_export_ftp_path']) ? sanitize_text_field($_POST['rev_auto_export_ftp_path']) : '/';
        $rev_auto_export_ftp_file_name = !empty($_POST['rev_auto_export_ftp_file_name']) ? sanitize_text_field($_POST['rev_auto_export_ftp_file_name']) : null;

        $rev_auto_import = !empty($_POST['rev_auto_import']) ?sanitize_text_field ($_POST['rev_auto_import']) : 'Disabled';
        $rev_auto_import_file = isset($_POST['rev_auto_import_file']) ? sanitize_text_field($_POST['rev_auto_import_file']) : null;
        $rev_auto_import_start_time = !empty($_POST['rev_auto_import_start_time']) ? sanitize_text_field($_POST['rev_auto_import_start_time']) : '';
        $rev_auto_import_interval = !empty($_POST['rev_auto_import_interval']) ? sanitize_text_field($_POST['rev_auto_import_interval']) : '';
        $rev_auto_import_profile = !empty($_POST['rev_auto_import_profile']) ? sanitize_text_field($_POST['rev_auto_import_profile']) : '';
        $rev_auto_import_merge = !empty($_POST['rev_auto_import_merge']) ? true : false;
        $rev_auto_replycolumn = !empty($_POST['rev_auto_replycolumn']) ? true : false;


        // Product ipmort from URL 
        $pro_enable_url_ie = isset($_POST['pro_enable_url_ie']) ? true : false;
        $pro_auto_import_url = isset($_POST['pro_auto_import_url']) ? sanitize_text_field($_POST['pro_auto_import_url']) : null;
        $pro_auto_import_url_delimiter = !empty($_POST['pro_auto_import_url_delimiter']) ? sanitize_text_field($_POST['pro_auto_import_url_delimiter']) : ',';
        $pro_auto_import_url_start_time = isset($_POST['pro_auto_import_url_start_time']) ? sanitize_text_field($_POST['pro_auto_import_url_start_time']) : '';
        $pro_auto_import_url_interval = isset($_POST['pro_auto_import_url_interval']) ? sanitize_text_field($_POST['pro_auto_import_url_interval']) : '';
        $pro_auto_import_url_profile = isset($_POST['pro_auto_import_url_profile']) ? sanitize_text_field($_POST['pro_auto_import_url_profile']) : '';
        $pro_auto_import_url_merge = isset($_POST['pro_auto_import_url_merge']) ? true : false;
        $pro_auto_import_url_skip = isset($_POST['pro_auto_import_url_skip']) ? true : false;
        $pro_auto_import_url_delete_products = !empty($_POST['pro_auto_import_url_delete_products']) ? true : false;
        $pro_auto_import_url_stop_thumbnail_regen = !empty($_POST['pro_auto_import_url_stop_thumbnail_regen']) ? true : false;
        $pro_auto_import_url_use_sku_upsell_crosssell = !empty($_POST['pro_auto_import_url_use_sku_upsell_crosssell']) ? true : false;        
        $pro_auto_import_url_new_prod_status = !empty($_POST['pro_auto_import_url_new_prod_status']) ? wc_clean($_POST['pro_auto_import_url_new_prod_status']) : null;
        $pro_auto_import_url_use_chidren_sku = !empty($_POST['pro_auto_import_url_use_chidren_sku'])  ? true : false;
        $pro_auto_import_url_merge_empty_cells = !empty($_POST['pro_auto_import_url_merge_empty_cells']) ? true : false;

        
        


        $settings = array();
        $settings['pro_ftp_server'] = $pro_ftp_server;
        $settings['pro_ftp_user'] = $pro_ftp_user;
        $settings['pro_ftp_password'] = $pro_ftp_password;
        $settings['pro_ftp_port'] = $pro_ftp_port;
        $settings['pro_use_ftps'] = $pro_use_ftps;
        $settings['pro_use_pasv'] = $pro_use_pasv; 
        $settings['pro_use_sftp'] = $pro_use_sftp;
        $settings['auto_pro_use_implicit_ftp'] = $auto_pro_use_implicit_ftp; 
        $settings['pro_enable_ftp_ie'] = $pro_enable_ftp_ie;


        $settings['pro_auto_export'] = $pro_auto_export;
        $settings['pro_auto_export_ftp_path'] = $pro_auto_export_ftp_path;
        $settings['pro_auto_export_ftp_file_name'] = $pro_auto_export_ftp_file_name;
        $settings['pro_auto_export_do_shortcode'] = $pro_auto_export_do_shortcode;
        $settings['pro_auto_export_start_time'] = $pro_auto_export_start_time;
        $settings['pro_auto_export_delimiter'] =$pro_auto_export_delimiter;
        $settings['pro_auto_export_interval'] = $pro_auto_export_interval;
        $settings['pro_auto_export_profile'] = $pro_auto_export_profile;
        $settings['pro_auto_export_categories'] = $pro_auto_export_categories;
        $settings['pro_auto_export_prod_status'] = $pro_auto_export_prod_status;
        $settings['pro_auto_export_include_hidden_meta'] = $pro_auto_export_include_hidden_meta;

        $settings['pro_auto_import'] = $pro_auto_import;
        $settings['pro_auto_import_multiple_files_import'] = $pro_auto_import_multiple_files_import;
        $settings['pro_auto_import_file'] = $pro_auto_import_file;
        $settings['pro_auto_import_delimiter'] = $pro_auto_import_delimiter;
        $settings['pro_auto_import_start_time'] = $pro_auto_import_start_time;
        $settings['pro_auto_import_interval'] = $pro_auto_import_interval;
        $settings['pro_auto_import_profile'] = $pro_auto_import_profile;
        $settings['pro_auto_import_merge'] = $pro_auto_import_merge;
        $settings['pro_auto_import_skip'] = $pro_auto_import_skip;
        $settings['pro_auto_delete_products'] = $pro_auto_delete_products;
        $settings['pro_auto_new_prod_status'] = $pro_auto_new_prod_status;
        $settings['pro_auto_use_chidren_sku'] = $pro_auto_use_chidren_sku;
        $settings['pro_auto_use_sku_upsell_crosssell'] = $pro_auto_use_sku_upsell_crosssell;
        $settings['pro_auto_merge_empty_cells'] = $pro_auto_merge_empty_cells;
        $settings['pro_auto_stop_thumbnail_regen'] = $pro_auto_stop_thumbnail_regen;




        $settings['rev_ftp_server'] = $rev_ftp_server;
        $settings['rev_ftp_user'] = $rev_ftp_user;
        $settings['rev_ftp_password'] = $rev_ftp_password;
        $settings['rev_ftp_port'] = $rev_ftp_port;
        $settings['rev_use_ftps'] = $rev_use_ftps;
        $settings['rev_use_pasv'] = $rev_use_pasv;        
        $settings['rev_use_sftp'] = $rev_use_sftp; 
        $settings['rev_enable_ftp_ie'] = $rev_enable_ftp_ie;

        $settings['rev_auto_export'] = $rev_auto_export;
        $settings['rev_auto_export_start_time'] = $rev_auto_export_start_time;
        $settings['rev_auto_export_interval'] = $rev_auto_export_interval;
        $settings['rev_auto_export_ftp_path'] = $rev_auto_export_ftp_path;
        $settings['rev_auto_export_ftp_file_name'] = $rev_auto_export_ftp_file_name;

        $settings['rev_auto_import'] = $rev_auto_import;
        $settings['rev_auto_import_file'] = $rev_auto_import_file;
        $settings['rev_auto_import_start_time'] = $rev_auto_import_start_time;
        $settings['rev_auto_import_interval'] = $rev_auto_import_interval;
        $settings['rev_auto_import_profile'] = $rev_auto_import_profile;
        $settings['rev_auto_import_merge'] = $rev_auto_import_merge;
        $settings['rev_auto_replycolumn'] = $rev_auto_replycolumn;


        // Product ipmort from URL 
        $settings['pro_enable_url_ie'] = $pro_enable_url_ie;
        $settings['pro_auto_import_url'] = $pro_auto_import_url;
        $settings['pro_auto_import_url_delimiter'] = $pro_auto_import_url_delimiter;
        $settings['pro_auto_import_url_start_time'] = $pro_auto_import_url_start_time;
        $settings['pro_auto_import_url_interval'] = $pro_auto_import_url_interval;
        $settings['pro_auto_import_url_profile'] = $pro_auto_import_url_profile;
        $settings['pro_auto_import_url_merge'] = $pro_auto_import_url_merge;
        $settings['pro_auto_import_url_skip'] = $pro_auto_import_url_skip;
        $settings['pro_auto_import_url_delete_products'] = $pro_auto_import_url_delete_products;
        $settings['pro_auto_import_url_stop_thumbnail_regen'] = $pro_auto_import_url_stop_thumbnail_regen;
        $settings['pro_auto_import_url_use_sku_upsell_crosssell'] = $pro_auto_import_url_use_sku_upsell_crosssell;        
        $settings['pro_auto_import_url_new_prod_status'] = $pro_auto_import_url_new_prod_status;
        $settings['pro_auto_import_url_use_chidren_sku'] = $pro_auto_import_url_use_chidren_sku;
        $settings['pro_auto_import_url_merge_empty_cells'] = $pro_auto_import_url_merge_empty_cells;
        
        $settings_db = get_option('woocommerce_' . WF_PROD_IMP_EXP_ID . '_settings', null);


        $pro_orig_export_start_inverval = '';
        if (isset($settings_db['pro_auto_export_start_time']) && isset($settings_db['pro_auto_export_interval'])) {
            $pro_orig_export_start_inverval = $settings_db['pro_auto_export_start_time'] . $settings_db['pro_auto_export_interval'];
        }

        $rev_orig_export_start_inverval = '';
        if (isset($settings_db['rev_auto_export_start_time']) && isset($settings_db['rev_auto_export_interval'])) {
            $rev_orig_export_start_inverval = $settings_db['rev_auto_export_start_time'] . $settings_db['rev_auto_export_interval'];
        }


        $pro_orig_import_start_inverval = '';
        if (isset($settings_db['pro_auto_import_start_time']) && isset($settings_db['pro_auto_import_interval'])) {
            $pro_orig_import_start_inverval = $settings_db['pro_auto_import_start_time'] . $settings_db['pro_auto_import_interval'];
        }


        $rev_orig_import_start_inverval = '';
        if (isset($settings_db['rev_auto_import_start_time']) && isset($settings_db['rev_auto_import_interval'])) {
            $rev_orig_import_start_inverval = $settings_db['rev_auto_import_start_time'] . $settings_db['rev_auto_import_interval'];
        }

        // Product ipmort from URL 
        $pro_orig_import_url_start_inverval = '';
        if (isset($settings_db['pro_auto_import_url_start_time']) && isset($settings_db['pro_auto_import_url_interval'])) {
            $pro_orig_import_url_start_inverval = $settings_db['pro_auto_import_url_start_time'] . $settings_db['pro_auto_import_url_interval'];
        }




        update_option('woocommerce_' . WF_PROD_IMP_EXP_ID . '_settings', $settings);
        // clear scheduled export event in case export interval was changed

        if (($pro_orig_export_start_inverval !== $settings['pro_auto_export_start_time'] . $settings['pro_auto_export_interval']) || (!$pro_enable_ftp_ie) || ($pro_auto_export === 'Disabled')) {
            // note this resets the next scheduled execution time to the time options were saved + the interval
            wp_clear_scheduled_hook('wf_woocommerce_csv_im_ex_auto_export_products');
        }

        if (($rev_orig_export_start_inverval !== $settings['rev_auto_export_start_time'] . $settings['rev_auto_export_interval']) || (!$rev_enable_ftp_ie) || ($rev_auto_export === 'Disabled')) {
            // note this resets the next scheduled execution time to the time options were saved + the interval
            wp_clear_scheduled_hook('wf_pr_rev_csv_im_ex_auto_export_products');
        }


        // clear scheduled import event in case import interval was changed
        if (($pro_orig_import_start_inverval !== $settings['pro_auto_import_start_time'] . $settings['pro_auto_import_interval']) || (!$pro_enable_ftp_ie) || ($pro_auto_import === 'Disabled')) {
            // note this resets the next scheduled execution time to the time options were saved + the interval
            wp_clear_scheduled_hook('wf_woocommerce_csv_im_ex_auto_import_products');
        }
        if (($rev_orig_import_start_inverval !== $settings['rev_auto_import_start_time'] . $settings['rev_auto_import_interval']) || (!$rev_enable_ftp_ie) || ($rev_auto_import === 'Disabled')) {
            // note this resets the next scheduled execution time to the time options were saved + the interval
            wp_clear_scheduled_hook('wf_pr_rev_csv_im_ex_auto_import_products');
        }

        // Product ipmort from URL 
        // clear scheduled url import event in case import interval was changed
        if ($pro_orig_import_url_start_inverval !== $settings['pro_auto_import_url_start_time'] . $settings['pro_auto_import_url_interval']) {
            // note this resets the next scheduled execution time to the time options were saved + the interval
            wp_clear_scheduled_hook('wf_woocommerce_csv_im_ex_auto_import_products_from_url');
        }


        wp_redirect(admin_url('/admin.php?page=' . WF_WOOCOMMERCE_CSV_IM_EX . '&tab=settings'));
        exit;
    }

}
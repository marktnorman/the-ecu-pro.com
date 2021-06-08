<?php
if (!defined('WPINC')) {
    exit;
}

class WF_ProdReviewImpExpCsv_AJAX_Handler {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_product_reviews_csv_import_request', array($this, 'csv_import_request'));
        add_action('wp_ajax_product_reviews_test_ftp_connection', array($this, 'test_ftp_credentials'));
    }

    /**
     * Ajax event for importing a CSV
     */
    public function csv_import_request() {
        define('WP_LOAD_IMPORTERS', true);
        WF_PrRevImpExpCsv_Importer::product_importer();
    }

    /**
     * Ajax event to test FTP details
     */
    public function test_ftp_credentials() {
                
        $nonce = (isset($_POST['wt_nonce']) ? sanitize_text_field($_POST['wt_nonce']) : '');
        if (!wp_verify_nonce($nonce,WF_PROD_IMP_EXP_ID) || !WF_Product_Review_Import_Export_CSV::hf_user_permission()) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wf_csv_import_export'));
        }       
        $wf_prod_rev_ftp_details = array();        
        $wf_prod_rev_ftp_details['host'] = !empty($_POST['ftp_host']) ?sanitize_text_field($_POST['ftp_host']) : '';
        $wf_prod_rev_ftp_details['port'] = !empty($_POST['ftp_port']) ? absint($_POST['ftp_port']) : 21;
        $wf_prod_rev_ftp_details['userid'] = !empty($_POST['ftp_userid']) ? wp_unslash($_POST['ftp_userid']) : '';
        $wf_prod_rev_ftp_details['password'] = !empty($_POST['ftp_password']) ? wp_unslash($_POST['ftp_password']) : '';
        $wf_prod_rev_ftp_details['use_ftps'] = !empty($_POST['use_ftps']) ? absint($_POST['use_ftps']) : 0;
        $wf_prod_rev_ftp_details['is_sftp'] = !empty($_POST['use_sftp']) ?absint($_POST['use_sftp']) : 0;
        if (class_exists('class_wf_sftp_import_export') && $wf_prod_rev_ftp_details['is_sftp'] == 1) {
            $sftp_import = new class_wf_sftp_import_export();
            $sftp_conn = $sftp_import->connect($wf_prod_rev_ftp_details['host'], $wf_prod_rev_ftp_details['userid'], $wf_prod_rev_ftp_details['password'], $wf_prod_rev_ftp_details['port']);
            if (!$sftp_conn) {
                die("<div id= 'prod_rev_ftp_test_msg' style = 'color : red'>". __('Could not connect to Host. Server host / IP or Port may be wrong.', 'wf_csv_import_export')."</div>");
            } else {
                die("<div id= 'prod_rev_ftp_test_msg' style = 'color : green'>". __('sFTP successfully logged in.', 'wf_csv_import_export')."</div>");
            }
        } else {
            $ftp_conn = (!empty($wf_prod_rev_ftp_details['use_ftps'])) ? @ftp_ssl_connect($wf_prod_rev_ftp_details['host'], $wf_prod_rev_ftp_details['port']) : @ftp_connect($wf_prod_rev_ftp_details['host'], $wf_prod_rev_ftp_details['port']);
            if ($ftp_conn == false) {
                die("<div id= 'prod_rev_ftp_test_msg' style = 'color : red'>". __('Could not connect to Host. Server host / IP or Port may be wrong.', 'wf_csv_import_export')."</div>");
            }
            if (@ftp_login($ftp_conn, $wf_prod_rev_ftp_details['userid'], $wf_prod_rev_ftp_details['password'])) {
                die("<div id= 'prod_rev_ftp_test_msg' style = 'color : green'>". __('Successfully logged in.', 'wf_csv_import_export')."</div");
            } else {
                die("<div id= 'prod_rev_ftp_test_msg' style = 'color : blue'>". __('Connected to host but could not login. Server UserID or Password may be wrong or Try with / without FTPS ..', 'wf_csv_import_export')."</div>");
            }
        }
    }

}

new WF_ProdReviewImpExpCsv_AJAX_Handler();

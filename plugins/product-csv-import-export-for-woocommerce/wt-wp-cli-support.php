<?php

function is_cli_running() {
  return defined( 'WP_CLI' ) && WP_CLI;
}

if ( is_cli_running() ) {
  
  class wt_cli_product_importer extends WP_CLI_Command {
     public $settings;

     //CLI command:- wp wt_cli_import test param1 param2 --assoc_array_key=assoc_array_val --assoc_array_key2=assoc_array_val2
    function test( $args, $assoc_args ) {    
        WP_CLI::line( 'Version of this plugin is 0.1-beta, and version of WordPress ' . get_bloginfo( 'version' ) . '.' );
    }
    
    //CLI command:- wp wt_cli_import run_cron
    public function run_cron() {
                        
        require_once  ABSPATH . "wp-content/plugins/product-csv-import-export-for-woocommerce/includes/class-wf-prodimpexpcsv-import-cron.php";

        $wt_cron =  new WF_ProdImpExpCsv_ImportCron;
        
        $wt_cron->wf_scheduled_import_products();
        
    }    
  }   
    
WP_CLI::add_command( 'wt_cli_import', 'wt_cli_product_importer');
  
}
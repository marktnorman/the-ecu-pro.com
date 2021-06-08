<?php
/*
 * Plugin Name: ECU Product Updater for WooCommerce 
 * Description: Import ECU proudcts with Excel
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $updater_allDataInSheet;
global $updater_fieldIDs;
require_once( ABSPATH . 'wp-admin/includes/image.php' );
//ADD MENU LINK AND PAGE FOR WOOCOMMERCE Updater
add_action('admin_menu', 'ecu_product_updater_menu');

function ecu_product_updater_menu() {
	add_menu_page('ECU Product Excel Updater', 'ECU Product Updater', 'administrator', 'ecu-product-updater', 'ecu_product_updater_init', 'dashicons-upload','50');
}
//load css and js
function ecu_product_updater_enqueue_scripts(){
	//plugin css
	wp_register_style( 'ecu_product_updater_enqueue_css', plugins_url( "/assets/css/style.css", __FILE__ ) );
	wp_enqueue_style( 'ecu_product_updater_enqueue_css');


    //plugin js
    wp_register_script( 'ecu_product_updater_enqueue_js', 
    	plugins_url( '/assets/js/function.js', __FILE__ ), 
    	array('jquery') , 
    	null, 
    	true
    );
    wp_enqueue_script( 'ecu_product_updater_enqueue_js');

    wp_localize_script('ecu_product_updater_enqueue_js', 
    	'ecu_update_ajax_object', 
    	array(
    		'plugin_url' => plugins_url( '', __FILE__ ),
    		'site_url' => site_url(),
    		'upload_url' => plugins_url( '/upload', __FILE__ ),
    		'nonce' => wp_create_nonce( 'ajax-nonce' ),
    		'ajax_url' => admin_url( 'admin-ajax.php' )
    	)
    );
}

//set global variables
function set_global_update_variable(){
	global $updater_allDataInSheet;
	global $updater_fieldIDs;
}
add_action( 'init', 'set_global_update_variable' );

add_action('admin_enqueue_scripts', 'ecu_product_updater_enqueue_scripts');
//get excel file information
add_action("wp_ajax_get_update_excel_info", "ecu_get_update_excel_file_info");
function ecu_get_update_excel_file_info(){
    $nonce = $_POST['nonce'];
    if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
        wp_die ( 'Busted!');
    $excel_file = dirname(__FILE__) . "/upload/" . $_POST["fileName"];
    require_once( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');

	try {
		$objPHPExcel = PHPExcel_IOFactory::load($excel_file);
	} catch(Exception $e) {
		die('Error loading file "'.pathinfo($excel_file,PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	global $updater_allDataInSheet;
	$updater_allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	$data = count($updater_allDataInSheet);  // Here get total count of row in that Excel sheet
		
	$rownumber=1;
	$row = $objPHPExcel->getActiveSheet()->getRowIterator($rownumber)->current();
	$cellIterator = $row->getCellIterator();
	$cellIterator->setIterateOnlyExistingCells(false);

	foreach ($cellIterator as $cell) {
		//getValue
		global $updater_fieldIDs;
		$updater_fieldIDs[sanitize_text_field($cell->getValue())] = sanitize_text_field($cell->getColumn());
	}
	if(!$updater_fieldIDs["ID"]){
		$result["status"] = "fail";
		$result["message"] = "Excel format is wrong!";
		$result["fields"] = $updater_fieldIDs["ID"];
		$result["count"] = 0;
		echo json_encode($result);
		wp_die();
	}

	// echo $data;
	$line_count = $data;
	$result["status"] = "success";
	$result["message"] = "Success";
	$result["fields"] = $updater_fieldIDs;
	$result["count"] = $line_count;
	echo json_encode($result);
	wp_die();
}
//delete file - ajax reqeust
add_action( "wp_ajax_update_deleteaction", "ecu_delete_update_excel_file" );
function ecu_delete_update_excel_file(){
    // Check for nonce security
    $nonce = $_POST['nonce'];
    if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
        wp_die ( 'Busted!');
	$excel_file = dirname(__FILE__) . "/upload/" . $_POST["fileName"];
	if( file_exists($excel_file)){
		unlink($excel_file);
		echo "File deleted";
		wp_die();
	}
	echo $excel_file;
	wp_die();
}
//product update logic
add_action( "wp_ajax_product_upload_action_update", "ecu_product_upload_update" );
function ecu_product_upload_update(){
	if($_SERVER['REQUEST_METHOD'] != 'POST' || !current_user_can('administrator') )
		wp_die("You don't have enough permission!");

	$excel_file = dirname(__FILE__) . "/upload/" . $_POST["fileName"];
	require_once( plugin_dir_path( __FILE__ ) .'/Classes/PHPExcel/IOFactory.php');

	try {
		$objPHPExcel = PHPExcel_IOFactory::load($excel_file);
	} catch(Exception $e) {
		die('Error loading file "'.pathinfo($excel_file,PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	$updater_allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	$data = count($updater_allDataInSheet);  // Here get total count of row in that Excel sheet
		
	$rownumber=1;
	$row = $objPHPExcel->getActiveSheet()->getRowIterator($rownumber)->current();
	$cellIterator = $row->getCellIterator();
	$cellIterator->setIterateOnlyExistingCells(false);

	$i = $_POST["idx"];
	$updater_fieldIDs = $_POST["fields"];

	$sku = $updater_allDataInSheet[$i][$updater_fieldIDs['sku']];
	if($updater_allDataInSheet[$i][$updater_fieldIDs['skip']] == "1"){
		echo "<div>{$sku} is skipped!</div>";
		wp_die();
	}

	if(!$updater_fieldIDs["ID"])
		wp_die("Excel is not matched");

	if( !isset($updater_allDataInSheet) )
		wp_die("Dataset is empty!");
	
	$post_title = sanitize_text_field($updater_allDataInSheet[$i][$updater_fieldIDs['post_title']]);
	$post_content = ($updater_allDataInSheet[$i][$updater_fieldIDs['post_content']]);
	$post_excerpt = ($updater_allDataInSheet[$i][$updater_fieldIDs['post_excerpt']]);
	$post_name = sanitize_title_with_dashes($updater_allDataInSheet[$i][$updater_fieldIDs['post_title']]);
	$post_type = 'product';

	$id = $updater_allDataInSheet[$i][$updater_fieldIDs['ID']];
	$post = array(
		'ID' 		   => $id,
		'post_title'   => $post_title,
		'post_content' => $post_content,
		'post_status'  => 'publish',
		'post_excerpt' => $post_excerpt,
		'post_name'    => $post_name,
		'post_type'    => $post_type
	);
	ob_start();
	echo "<div>Update " . ($i-1) ."th product</div>";
	// wp_die();
	wp_update_post($post);
	// print "<p><a href='".esc_url( get_permalink($id))."' target='_blank'>".$title."</a> already exists. Updated.</p>";
	// update product info
	if(isset($updater_allDataInSheet[$i][$updater_fieldIDs['_sale_price']])){
		$sale_price = sanitize_text_field($updater_allDataInSheet[$i][$updater_fieldIDs['_sale_price']]);					

		if ( strlen(trim($sale_price)) >=1 ) {
			update_post_meta( $id, '_sale_price', $sale_price );
		}
		else
			delete_post_meta($id,'_sale_price');
		
	}
	if(isset($updater_allDataInSheet[$i][$updater_fieldIDs['_regular_price']])){
		$regular_price = sanitize_text_field($updater_allDataInSheet[$i][$updater_fieldIDs['_regular_price']]);
		if ( !$regular_price  && !empty($updater_allDataInSheet[$i][$updater_fieldIDs['_regular_price']])) {
		  $regular_price = '';
		  // print "For regular price of {$post_title} you need numbers entered.<br/>";
		}else update_post_meta( $id, '_regular_price', $regular_price );						
	}
	//ADDITION : IF SALE PRICE IS EMPTY PRICE WILL BE EQUAL TO REGULAR PRICE
	if(strlen(trim($sale_price)) >=1){
		//echo ("sale price is not empty");
		update_post_meta( $id, '_price', $sale_price );			
	}elseif(isset($updater_allDataInSheet[$i][$updater_fieldIDs['_regular_price']])){
		// delete_post_meta($id,'_sale_price');
		//echo ("regular price is");
		update_post_meta( $id, '_price', $regular_price );
	}
	update_post_meta( $id, '_sku', $sku );
	wp_die(" Success!");
}
//init admin page
function ecu_product_updater_init(){
	?>
		<div class="container">
		    <div class="row h-center">
			    <div class="col-md-12">
			        <form method="post" 
			        	enctype="multipart/form-data" 
			        	action="<?php echo plugins_url('/upload-excel.php', __FILE__) ?>" 
			        	class="dropzone dropzone-file-area" 
			        	id="updater"
			        >
		            <div class="dz-message" data-dz-message><h3><span>Drop a file here or click to update</span></h3></div>
			        </form>

			    </div>
			</div>
			<div class="row h-center">
			    <div class="col-md-12">
			    	<button id="btn-product-update" type="button" class="btn btn-lg btn-primary" disabled>Update Products</button>
			    </div>
			</div>
			<div class="row h-center">
				<div id="excel-content" class="col-md-12">
				</div>
			</div>
		</div>;
	<?php
}
?>
<?php
if (!defined('WPINC')) {
    exit;
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-wf-prodimg-exporter
 *
 * @author Fasil
 */
class WF_ProdImg_Exporter {

    public static function do_export($post_type = 'product', $prod_ids = array()) {
        $nonce = (isset($_GET['wt_nonce']) ? sanitize_text_field($_GET['wt_nonce']) : '');
       if (!wp_verify_nonce($nonce,WF_PROD_IMP_EXP_ID) || !WF_Product_Import_Export_CSV::hf_user_permission()) {
            wp_die(__('Access Denied', 'wf_csv_import_export'));
        }
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        $upload_path = wp_upload_dir();
        $wf_export_images_path = $upload_path['basedir'] . '/wf_export_images/'; // wp content path - fixed WP violation
        if (!file_exists($wf_export_images_path)) {
            mkdir($wf_export_images_path, 0777, true);
        }
        if(!empty($_GET['prod_ids'])){ // introduced specific product from export page
            $filter_args['selected_product_ids'] = sanitize_text_field($_GET['prod_ids']);
            }
            $filter_args['prod_categories'] = !($_GET['prod_categories']== 'null') ? sanitize_text_field($_GET['prod_categories']) : array();
            $filter_args['prod_tags'] = !($_GET['prod_tags'] == 'null') ? sanitize_text_field($_GET['prod_tags']) : array();
            $filter_args['prod_types'] = !($_GET['prod_types'] == 'null') ? wc_clean($_GET['prod_types']) : array();
            $filter_args['prod_status'] = !($_GET['prod_status'] == 'null') ? wc_clean($_GET['prod_status']) : array('publish', 'private', 'draft', 'pending', 'future');
            $filter_args['limit'] = (!empty($_GET['limit']) ) ? intval($_GET['limit']) : 999999999;
            $filter_args['current_offset']= !($_GET['offset']== 'null') ? intval($_GET['offset']) : 0;
            $filter_args['sortcolumn'] = !($_GET['sortcolumn'] == 'null') ?wc_clean($_GET['sortcolumn']): 'post_parent, ID';
            $filter_args['prod_sort_order'] = !empty($_GET['wt_prod_sort_ord']) ? sanitize_text_field($_GET['wt_prod_sort_ord']): 'DESC';
        $destination = $wf_export_images_path . "images.zip";
        $wf_export_images = '';
        //$wf_export_images = WF_ProdImg_Exporter::recursive_file_search($upload_path['basedir']);
        //$wf_export_images = WF_ProdImg_Exporter::get_all_products_images();  this workflow have issue when site url comes http and https both 
        $wf_export_images = self::get_all_products_images_new($filter_args);

        $wf_export_images = array_unique($wf_export_images); // Avoid dublication.

        if ($wf_export_images) {
            if (!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {


                require_once plugin_dir_path(__FILE__) . "../../src/zip.php";

                $zip = new Zip();

                $zip1 = $zip->zip_start($destination);

                $zip2 = $zip->zip_add($wf_export_images);

                $zip_res = $zip->zip_end();
            } else {

                $zip_res = self::Zippp($wf_export_images, $destination);
            }

            //Then download the zipped file.
//             header('Content-Type: application/zip');
//             header('Content-disposition: attachment; filename=images.zip');
//             header('Content-Length: ' . filesize($destination));
//             readfile($destination);

            if ($zip_res !== FALSE) {
                $external_link = $upload_path['baseurl'] . '/wf_export_images/images.zip';
                echo "<script>  window.open('" . $external_link . "', '_blank'); </script>";
                die;
            }
        }
        wp_redirect(admin_url('/admin.php?page=wf_woocommerce_csv_im_ex&wf_product_ie_msg=3'));
        die;
    }

    public static function Zippp($source_array, $destination) {
        if (!extension_loaded('zip') || !is_array($source_array)) {
            return false;
        }
        $zip = new ZipArchive;
        if (!$zip->open($destination, ZipArchive::OVERWRITE | ZIPARCHIVE::CREATE)) {
            return false;
        }

        foreach ($source_array as $file) {
//            $new_filename = substr($file, strrpos($file, '/') + 1);
            $new_filename = basename($file);

            $zip->addFile($file, $new_filename);
        }
        $zip->close();
    }
    
    
    public static function get_all_products_images_new($filter_args) {
        $filter_args['prod_types'] = (!empty($filter_args['prod_types'])&& !is_array($filter_args['prod_types']))? explode(',', $filter_args['prod_types']):$filter_args['prod_types'];
        $filter_args['prod_categories'] =  (!empty($filter_args['prod_categories'])&& !is_array($filter_args['prod_categories']))? explode(',', $filter_args['prod_categories']):$filter_args['prod_categories'];
        $filter_args['prod_tags'] =  (!empty($filter_args['prod_tags'])&& !is_array($filter_args['prod_tags']))? explode(',', $filter_args['prod_tags']):$filter_args['prod_tags'];
        $filter_args['prod_status'] =  (!empty($filter_args['prod_status'])&& !is_array($filter_args['prod_status']))? explode(',', $filter_args['prod_status']):$filter_args['prod_status'];
        
        $product_args = apply_filters('woocommerce_csv_product_image_export_args', array(
            'numberposts' => $filter_args['limit'],
            'post_status' => $filter_args['prod_status'],
            'post_type' => array('product', 'product_variation'),
            'orderby' => $filter_args['sortcolumn'],
            'suppress_filters' => FALSE,
            'order' => $filter_args['prod_sort_order'],
            'offset' => $filter_args['current_offset']
        ));
    if ((!empty($filter_args['prod_categories']) ) || (!empty($filter_args['prod_types'])) || (!empty($filter_args['prod_tags']))) {

            //If only product type has been selected
            if (!empty($filter_args['prod_types'])) {
                $product_args['tax_query'][] = 
                    array(
                        'taxonomy' => 'product_type',
                        'field' => 'slug',
                        'terms' => $filter_args['prod_types'],
                        'operator' => 'IN',
                );
            }

            //If only product categories has been selected
            if (!empty($filter_args['prod_categories'])) {
                $product_args['tax_query'][] = 
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => $filter_args['prod_categories'],
                        'operator' => 'IN',
                );
            }

            if (!empty($filter_args['prod_tags'])) {
                $product_args['tax_query'][] = 
                    array(
                        'taxonomy' => 'product_tag',
                        'field' => 'id',
                        'terms' => $filter_args['prod_tags'],
                        'operator' => 'IN',
                );
            }

        }
     

        if ($filter_args['selected_product_ids']) {
            $parent_ids = array_map('intval', explode(',', $filter_args['selected_product_ids']));
            global $wpdb;
            $child_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_parent IN (" . implode(',', $parent_ids) . ");");
            $sel_ids = array_merge($parent_ids, $child_ids);
            $product_args['post__in'] = $sel_ids;
        }
        
    $products = get_posts($product_args);
    if(empty($filter_args['prod_types'])||((!empty($filter_args['prod_types']) && in_array('variable', $filter_args['prod_types']) )) || (!empty($filter_args['prod_types']) && in_array('variable-subscription', $filter_args['prod_types']) )){
        include_once( 'class-wf-prodimpexpcsv-exporter.php' );
        $exporter = new WF_ProdImpExpCsv_Exporter();    
        $products = $exporter->get_childs_of_selected_parents($products);
    }
        $image_array = $image_array_with_path = array();

        if ($products || !is_wp_error($products)) {
            foreach ($products as $key => $product) {
                $image_array[] = self::getProductImages_new($product);
            }
        }

        if (!empty($image_array)) {
            foreach ($image_array as $value) {
                if (empty($value))
                    continue;
                foreach ($value as $val) {
                    $image_array_with_path[] = $val;
                }
            }
        }
        return $image_array_with_path;
    }

    public static function getProductImages_new($product) {
        $image_file_names = array();
        $meta_data = get_post_custom($product->ID);
        
        // Featured image
        if (( $featured_image_id = get_post_thumbnail_id($product->ID))) {
            
           $attached_file_path= get_attached_file($featured_image_id);
            
            if (!empty($attached_file_path)) {
                $image_file_names[] = $attached_file_path;
            }
        }
        
        // Images
        $images = isset($meta_data['_product_image_gallery'][0]) ? explode(',', maybe_unserialize(maybe_unserialize($meta_data['_product_image_gallery'][0]))) : false;
        $results = array();
        if ($images) {
            foreach ($images as $image_id) {
                if ($featured_image_id == $image_id) {
                    continue;
                }
                $attached_file_path = get_attached_file($image_id);

                if (!empty($attached_file_path)) {
                    $image_file_names[] = $attached_file_path;
                }
            }
        }
        
        /* compatible with WooCommerce Additional Variation Images Gallery plugin */
        $woo_variation_gallery_images = isset($meta_data['woo_variation_gallery_images'][0]) ? maybe_unserialize($meta_data['woo_variation_gallery_images'][0]) : FALSE;
        if($woo_variation_gallery_images){
            foreach ($woo_variation_gallery_images as $image_id) {
                if ($featured_image_id == $image_id) {
                    continue;
                }
                $attached_file_path = get_attached_file($image_id);

                if (!empty($attached_file_path)) {
                    $image_file_names[] = $attached_file_path;
                }
            }
        }
        
        return $image_file_names;
        
    }
    
    public static function get_all_products_images() {

        $upload_path = wp_upload_dir();
        $product_args = apply_filters('woocommerce_csv_product_image_export_args', array(
            'numberposts' => -1,
            'post_status' => array('publish', 'pending', 'private', 'draft'),
            'post_type' => array('product', 'product_variation'),
            'order' => 'ASC',
        ));
        $products = get_posts($product_args);
        $image_array = array();

        if ($products || !is_wp_error($products)) {
            foreach ($products as $key => $product) {
                $attachments = self::getProductImages($product);
                if (!empty($attachments)) {

                    foreach ($attachments as $att_id => $attachment) {
                        if (strstr(basename($attachment), '.')) {
                            $image_array[] = str_replace($upload_path['baseurl'], $upload_path['basedir'], $attachment);
                        }
                    }
                }
            }
        }
        return $image_array;
    }

    public static function getProductImages($product) {
        $image_file_names = array();
        $meta_data = get_post_custom($product->ID);

        // Featured image
        if (( $featured_image_id = get_post_thumbnail_id($product->ID))) {
            $image_object = get_post($featured_image_id);

            if ($image_object && $image_object->guid) {
                $temp_images_export_to_csv = $image_object->guid;
            }
            if (!empty($temp_images_export_to_csv)) {
                $image_file_names[] = $temp_images_export_to_csv;
            }
        }

        // Images
        $images = isset($meta_data['_product_image_gallery'][0]) ? explode(',', maybe_unserialize(maybe_unserialize($meta_data['_product_image_gallery'][0]))) : false;
        $results = array();
        if ($images) {
            foreach ($images as $image_id) {
                if ($featured_image_id == $image_id) {
                    continue;
                }
                $temp_gallery_images_export_to_csv = '';

                $gallery_image_object = get_post($image_id);

                if ($gallery_image_object && $gallery_image_object->guid) {
                    $temp_gallery_images_export_to_csv = $gallery_image_object->guid;
                }
                if (!empty($temp_gallery_images_export_to_csv)) {
                    $image_file_names[] = $temp_gallery_images_export_to_csv;
                }
            }
        }
        return $image_file_names;
    }

    public static function recursive_file_search($directory, $display = Array('.jpeg', '.jpg')) { // not using now ,let it be for future reference and improvments
        $files = array();
        $it = new RecursiveDirectoryIterator($directory);
        foreach (new RecursiveIteratorIterator($it) as $file) {
            $file = str_replace('\\', '/', $file);
            if (in_array(strrchr($file, '.'), $display)) {
                $files[] = $file;
            }
        }
        return $files;
    }

    public static function get_all_products_images_old() { // not using now ,let it be for future reference and improvments
        $upload_path = wp_upload_dir();
        $product_args = apply_filters('woocommerce_csv_product_image_export_args', array(
            'numberposts' => -1,
            'post_status' => array('publish', 'pending', 'private', 'draft'),
            'post_type' => array('product', 'product_variation'),
            'order' => 'ASC',
        ));
        $products = get_posts($product_args);
        $image_array = array();

        if ($products || !is_wp_error($products)) {
            foreach ($products as $key => $product) {
                $attachments = get_children(array('post_parent' => $product->ID,
                    'post_status' => 'inherit',
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => 'ASC',
                    'orderby' => 'menu_order ID'));

                foreach ($attachments as $att_id => $attachment) {
                    $image_array[] = str_replace($upload_path['baseurl'], $upload_path['basedir'], $attachment->guid);
                }
            }
        }
        return $image_array;
    }

    function GetImageUrlsByProductId($productId) { // not using now ,let it be for future reference and improvments
        $product = new WC_product($productId);
        $attachmentIds = $product->get_gallery_image_ids();
        $imgUrls = array();
        foreach ($attachmentIds as $attachmentId) {
            $imgUrls[] = wp_get_attachment_url($attachmentId);
        }

        return $imgUrls;
    }

}

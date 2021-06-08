<?php

/*
 * Common Helper functions
 */

/**
 * Helper class which contains common functions for both import and export.
 */
if (!class_exists('wf_piep_helper')) {

    class wf_piep_helper {

        /**
         * Get File name by url
         * @param string $file_url URL of the file.
         * @return string the base name of the given URL (File name).
         */
        public static function xa_wc_get_filename_from_url($file_url) {
            $parts = parse_url($file_url);
            if (isset($parts['path'])) {
                return basename($parts['path']);
            }
        }

        /**
         * Get info like language code, parent product ID etc by product id.
         * @param int Product ID.
         * @return array/false.
         */
        public static function wt_get_wpml_original_post_language_info($element_id) {
            $get_language_args = array('element_id' => $element_id, 'element_type' => 'post_product');
            $original_post_language_info = apply_filters('wpml_element_language_details', null, $get_language_args);
            return $original_post_language_info;
        }

        public static function wt_get_product_id_by_sku($sku) {
            global $wpdb;
            $post_exists_sku = $wpdb->get_var($wpdb->prepare("
	    		SELECT $wpdb->posts.ID
	    		FROM $wpdb->posts
	    		LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
	    		WHERE $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
	    		AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
	    		", $sku));
            if ($post_exists_sku) {
                return $post_exists_sku;
            }
            return false;
        }

        /**
         * To strip the specific string from the array key as well as value.
         * @param array $array.
         * @param string $data.
         * @return array.
         */
        public static function wt_array_walk($array, $data) {
            $new_array = array();
            foreach ($array as $key => $value) {
                $new_array[str_replace($data, '', $key)] = str_replace($data, '', $value);
            }
            return $new_array;
        }

        public static function wt_get_product_ptaxonomies() {
            $product_ptaxonomies = get_object_taxonomies('product', 'name');
            $product_vtaxonomies = get_object_taxonomies('product_variation', 'name');
            $product_taxonomies = array_merge($product_ptaxonomies, $product_vtaxonomies);
            return $product_taxonomies;
        }

        public static function format_data($data) {
            if (!is_array($data)){
                $data = (string) urldecode($data);
            }
//        $enc = mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true);        
            $use_mb = function_exists('mb_detect_encoding');
            $enc = '';
            if ($use_mb) {
                $enc = mb_detect_encoding($data);
            }
            $data = ( $enc == 'UTF-8' ) ? $data : utf8_encode($data);

            return $data;
        }
        
        /*
        * Format data from the csv file
        * @param  string $value
        * @param  bool $use_mb
        * @return string
        */
        public static function format_data_from_csv($value, $use_mb) {
           //return ( $enc == 'UTF-8' ) ? trim($data) : utf8_encode(trim($data));
            if ($use_mb) {
                $encoding = mb_detect_encoding($value, mb_detect_order(), true);
                if ($encoding) {
                    $value = mb_convert_encoding($value, 'UTF-8', $encoding);
                } else {
                    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            } else {
                $value = wp_check_invalid_utf8($value, true);
            }
            return $value;
        }
        
           /**
	 * Clear any caches.
	 *
	 * @param Product ID.
	 * 
	 */
	public static function wt_clear_caches( $product_id ) {
            $product = wc_get_product($product_id);
            if(!empty($product)){
            wc_delete_product_transients( $product->get_id() );
            if ( $product->get_parent_id( 'edit' ) ) {
                    wc_delete_product_transients( $product->get_parent_id( 'edit' ) );
                   ( WC()->version < '3.9' ) ? self::wt_invalidate_cache_group( 'product_' . $product->get_parent_id( 'edit' ) ) : WC_Cache_Helper::invalidate_cache_group( 'product_' . $product->get_parent_id( 'edit' ) );                 
            }
            WC_Cache_Helper::invalidate_attribute_count( array_keys( $product->get_attributes() ) );
            (WC()->version < '3.9' ) ? self::wt_invalidate_cache_group( 'product_' . $product->get_id() ): WC_Cache_Helper::invalidate_cache_group( 'product_' . $product->get_id() );
            
          }  return;
	  }
          
        public static function wt_invalidate_cache_group( $group ) {
            wp_cache_set( 'wc_' . $group . '_cache_prefix', microtime(), $group );
         }
        
    }

}
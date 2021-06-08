<?php
if ( ! defined( 'WPINC' ) ) {
	exit;
}

class WF_ProdImpExpCsv_System_Status_Tools {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_debug_tools', array( $this, 'tools' ) );
	}

	/**
	 * Tools we add to WC
	 * @param  array $tools
	 * @return array
	 */
	public function tools( $tools ) {
		$tools['delete_products'] = array(
			'name'		=> __( 'Delete Products','wf_csv_import_export'),
			'button'	=> __( 'Delete ALL products','wf_csv_import_export' ),
			'desc'		=> __( 'This tool will delete all products allowing you to start fresh.', 'wf_csv_import_export' ),
			'callback'  => array( $this, 'delete_products' )
		);
		$tools['delete_variations'] = array(
			'name'		=> __( 'Delete Variations','wf_csv_import_export'),
			'button'	=> __( 'Delete ALL variations','wf_csv_import_export' ),
			'desc'		=> __( 'This tool will delete all variations allowing you to start fresh.', 'wf_csv_import_export' ),
			'callback'  => array( $this, 'delete_variations' )
		);
		$tools['delete_orphaned_variations'] = array(
			'name'		=> __( 'Delete Orphans','wf_csv_import_export'),
			'button'	=> __( 'Delete orphaned variations','wf_csv_import_export' ),
			'desc'		=> __( 'This tool will delete variations which have no parent.', 'wf_csv_import_export' ),
			'callback'  => array( $this, 'delete_orphaned_variations' )
		);
                $tools['wt_delete_existing_products'] = array(
                    'name' => __('Delete Existing Products', 'wf_csv_import_export'),
                    'button' => __('Delete Existing products', 'wf_csv_import_export'),
                    'desc' => __('This tool will delete all products that are not present in the last imported CSV.', 'wf_csv_import_export'),
                    'callback' => array( $this,'wt_delete_existing_products')
                );
                $tools['remove_all_product_categories'] = array(
                    'name' => __('Remove All Product Categories', 'wf_csv_import_export'),
                    'button' => __('Delete Categories', 'wf_csv_import_export'),
                    'desc' => __( 'This tool will forcefully delete all product categories.', 'wf_csv_import_export' ),
                    'callback' => array( $this,'wt_remove_all_product_categories')
                );
                $tools['remove_all_product_tags'] = array(
                    'name' => __('Remove All Product Tags', 'wf_csv_import_export'),
                    'button' => __('Delete  Tags', 'wf_csv_import_export'),
                    'desc' => __( 'This tool will forcefully delete all product tags.', 'wf_csv_import_export' ),
                    'callback' => array( $this,'wt_remove_all_product_tags')
                );
//                $tools['remove_all_product_attributes'] = array(
//                    'name' => __('Remove All Product Attributes', 'wf_csv_import_export'),
//                    'button' => __('Delete  Attributes', 'wf_csv_import_export'),
//                    'desc' => __( 'This tool will forcefully delete all product attributes.', 'wf_csv_import_export' ),
//                    'callback' => array( $this,'wt_remove_all_product_attributes')
//                );
                
                return $tools;
	}

	/**
	 * Delete products
	 */
	public function delete_products() {
		global $wpdb;

		// Delete products
		$result  = absint( $wpdb->delete( $wpdb->posts, array( 'post_type' => 'product' ) ) );
		$result2 = absint( $wpdb->delete( $wpdb->posts, array( 'post_type' => 'product_variation' ) ) );

		// Delete meta and term relationships with no post
		$wpdb->query( "DELETE pm
			FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = pm.post_id
			WHERE wp.ID IS NULL" );
		$wpdb->query( "DELETE tr
			FROM {$wpdb->term_relationships} tr
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
			WHERE wp.ID IS NULL" );

		echo '<div class="updated"><p>' . sprintf( __( '%d Products Deleted', 'wf_csv_import_export' ), ( $result + $result2 ) ) . '</p></div>';
	}

	/**
	 * Delete variations
	 */
	public function delete_variations() {
		global $wpdb;

		// Delete products
		$result = absint( $wpdb->delete( $wpdb->posts, array( 'post_type' => 'product_variation' ) ) );

		// Delete meta and term relationships with no post
		$wpdb->query( "DELETE pm
			FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = pm.post_id
			WHERE wp.ID IS NULL" );
		$wpdb->query( "DELETE tr
			FROM {$wpdb->term_relationships} tr
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
			WHERE wp.ID IS NULL" );

		echo '<div class="updated"><p>' . sprintf( __( '%d Variations Deleted', 'wf_csv_import_export' ), $result ) . '</p></div>';
	}

	/**
	 * Delete orphans
	 */
	public function delete_orphaned_variations() {
		global $wpdb;

		// Delete meta and term relationships with no post
		$result = absint( $wpdb->query( "DELETE products
			FROM {$wpdb->posts} products
			LEFT JOIN {$wpdb->posts} wp ON wp.ID = products.post_parent
			WHERE wp.ID IS NULL AND products.post_type = 'product_variation';" ) );

		echo '<div class="updated"><p>' . sprintf( __( '%d Variations Deleted', 'wf_csv_import_export' ), $result ) . '</p></div>';
	}
        
        
        
        function wt_remove_all_product_categories() {
            global $wpdb;
    
            $wpdb->query("DELETE a,c FROM {$wpdb->prefix}terms AS a
                      LEFT JOIN {$wpdb->prefix}term_taxonomy AS c ON a.term_id = c.term_id
                      LEFT JOIN {$wpdb->prefix}term_relationships AS b ON b.term_taxonomy_id = c.term_taxonomy_id
                      WHERE c.taxonomy = 'product_cat'");

//            $args = array(
//                "taxonomy" => 'product_cat',
//                "hide_empty" => 0,
//                "type" => "post",
//                "orderby" => "name",
//                "order" => "ASC"
//            );
//            $types = get_categories($args);
//
//            foreach ($types as $type) {
//                $res = wp_delete_category($type->cat_ID);
//            }

            echo '<div class="updated"><p>' . __('Product Categories Deleted', 'wf_csv_import_export') . '</p></div>';
        }


        function wt_remove_all_product_tags() {
            global $wpdb;

            $wpdb->query("DELETE a,c FROM {$wpdb->prefix}terms AS a
                LEFT JOIN {$wpdb->prefix}term_taxonomy AS c ON a.term_id = c.term_id
                LEFT JOIN {$wpdb->prefix}term_relationships AS b ON b.term_taxonomy_id = c.term_taxonomy_id
                WHERE c.taxonomy = 'product_tag'");
            
            echo '<div class="updated"><p>' . __('Product Tags Deleted', 'wf_csv_import_export') . '</p></div>';
        }
        
        function wt_remove_all_product_attributes() {
            global $wpdb;            
            $wpdb->query("DELETE FROM {$wpdb->prefix}terms WHERE term_id IN 
                (SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy LIKE 'pa_%');
                DELETE FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy LIKE 'pa_%';
                DELETE FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id not IN 
                (SELECT term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy)");
            
            echo '<div class="updated"><p>' . __('Product Attributes Deleted', 'wf_csv_import_export') . '</p></div>';
        }
        
        
        
        function wt_delete_existing_products() {
            $product_to_be_deleted_new_array = get_option('wf_prod_csv_imp_exp_product_to_be_deleted'); // get saved product ids 
            if (!empty($product_to_be_deleted_new_array)) {
                global $wpdb;
                ini_set('memory_limit', '-1');
                ini_set('max_input_vars', '2000');
                ini_set('max_execution_time', 0);
                $query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_status != 'trash'";
                $all_product_id = $wpdb->get_col($query);
                $product_to_be_deleted_final = array_diff($all_product_id, $product_to_be_deleted_new_array); //Product to be deleted 
                
                unset($all_product_id);
                if (!empty($product_to_be_deleted_final)) {
                    $product_to_be_deleted_final = apply_filters('wt_product_to_be_deleted_array',$product_to_be_deleted_final); // product ids which is going to delete
                    $result = count($product_to_be_deleted_final);
                    foreach ($product_to_be_deleted_final as $temp_product_key => $temp_product_id) {
                        $status = wp_trash_post($temp_product_id);
                    }
                    unset($product_to_be_deleted_final);
                }
                update_option('wf_prod_csv_imp_exp_product_to_be_deleted', array());
                echo '<div class="updated"><p>' . sprintf(__('%d Products Deleted', 'wf_csv_import_export'), ( $result)) . '</p></div>';
            } else {
                echo '<div class="updated"><p>' . __('No Products to be Delete', 'wf_csv_import_export') . '</p></div>';
            }
        }

}

new WF_ProdImpExpCsv_System_Status_Tools();
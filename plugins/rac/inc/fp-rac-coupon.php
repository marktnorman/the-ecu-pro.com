<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class FPRacCoupon {

    public static function rac_create_coupon($email, $timestamp) {
        $getdatas = self::get_all_coupon_array_for_email($email);
        if (rac_check_is_array($getdatas)) {
            if ($coupon_code = self::rac_check_coupon_already_exists_for_email($getdatas)) {
                return $coupon_code;
            } else {
                return self::create_new_coupon($email, $timestamp);
            }
        } else {
            return self::create_new_coupon($email, $timestamp);
        }
    }

    public static function rac_check_coupon_already_exists_for_email($getdatas) {
        foreach ($getdatas as $coupon_post) {
            $coupon_object = new WC_Coupon($coupon_post->ID);
            $expired_date = strtotime(fp_rac_get_coupon_obj_data($coupon_object, 'expiry_date'));
            $usage_limit = fp_rac_get_coupon_obj_data($coupon_object, 'usage_limit');
            $usage_count = fp_rac_get_coupon_obj_data($coupon_object, 'usage_count');
            if (($expired_date && current_time('timestamp') <= $expired_date) && ($usage_limit > 0 && $usage_count < $usage_limit)) {
                return get_the_title($coupon_post->ID);
            }
        }

        return false;
    }

    public static function get_all_coupon_array_for_email($email) {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'shop_coupon',
            'post_status' => 'publish',
            'meta_query' => array(
                'rac_cartlist_coupon_email' => array(
                    'key' => 'rac_cartlist_coupon_email',
                    'value' => $email,
                    'compare' => 'EXISTS',
                ),
            ),
        );
        $coupon_array = fp_rac_check_query_having_posts($args);

        return $coupon_array;
    }

    //coupon exist check
    public static function coupon_exist_check($coupon_code) {
        //coupon creation pre check
        $coupon_name = '';
        $args = array(
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'asc',
            'post_type' => 'shop_coupon',
            'post_status' => 'publish',
            's' => $coupon_code,
        );
        $coupon_array = fp_rac_check_query_having_posts($args);
        if (rac_check_is_array($coupon_array)) {
            $coupon_info = $coupon_array[0];
            $coupon_name = $coupon_info->post_title;
        }

        return $coupon_name;
    }

    public static function create_new_coupon($email, $timestamp) {

        if (get_option('rac_prefix_coupon') == '1') {
            $afterexplode = explode('@', $email);
            $email_letters = $afterexplode[0];
            $coupon_code = $email_letters . $timestamp;
        } else {
            $manual_prefix = get_option('rac_manual_prefix_coupon_code');
            $coupon_code = $manual_prefix . $timestamp;
        }

        $coupon_pre_check = self::coupon_exist_check($coupon_code);
        if ($coupon_pre_check == '') {
            $time_now = time();
            $validity_time = get_option('rac_coupon_validity') * 24 * 60 * 60;
            $expire_time = $time_now + $validity_time;
            $expire_date = date_i18n("Y-m-d", $expire_time); //formating expire date

            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'shop_coupon'
            );

            $new_coupon_id = wp_insert_post($coupon);
            $allowproducts = self::fp_rac_prepare_search_values(get_option('rac_include_products_in_coupon'));
            $excluded_products = self::fp_rac_prepare_search_values(get_option('rac_exclude_products_in_coupon'));
            $allowcategory = self::fp_rac_prepare_search_values(get_option('rac_select_category_to_enable_redeeming'));
            $excludecategory = self::fp_rac_prepare_search_values(get_option('rac_exclude_category_to_enable_redeeming'));

            update_post_meta($new_coupon_id, 'discount_type', get_option('rac_coupon_type'));
            update_post_meta($new_coupon_id, 'coupon_amount', get_option('rac_coupon_value'));
            update_post_meta($new_coupon_id, 'individual_use', get_option('rac_individual_use_only'));
            update_post_meta($new_coupon_id, 'free_shipping', get_option('rac_coupon_allow_free_shipping'));
            update_post_meta($new_coupon_id, 'product_ids', implode(',', $allowproducts));
            update_post_meta($new_coupon_id, 'exclude_product_ids', implode(',', $excluded_products));
            update_post_meta($new_coupon_id, 'product_categories', $allowcategory);
            update_post_meta($new_coupon_id, 'exclude_product_categories', $excludecategory);
            update_post_meta($new_coupon_id, 'usage_limit', '1'); //this is must to avoid multiple usage
            update_post_meta($new_coupon_id, 'expiry_date', $expire_date);
            update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
            update_post_meta($new_coupon_id, 'exclude_sale_items', get_option('rac_exclude_sale_items'));
            update_post_meta($new_coupon_id, 'minimum_amount', get_option('rac_minimum_spend'));
            update_post_meta($new_coupon_id, 'maximum_amount', get_option('rac_maximum_spend'));
            update_post_meta($new_coupon_id, 'rac_cartlist_coupon_email', $email);

            if (update_post_meta($new_coupon_id, 'coupon_by_rac', 'yes')) {
                return $coupon_code;
            }
        }
    }

    public static function fp_rac_prepare_search_values($array) {
        $prepare_array = fp_rac_check_is_array($array);
        $intval_array = array_map('intval', $prepare_array);
        $filter_array = array_filter($intval_array);
        return $filter_array;
    }

}

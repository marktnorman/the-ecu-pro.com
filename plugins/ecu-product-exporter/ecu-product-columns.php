<?php
if (!defined('ABSPATH')) {
    exit;
}
// 'product_image1_url'            => 'product_image1_url',
// 'product_image1_alt_text'       => 'product_image1_alt_text',
// 'product_image1_title'          => 'product_image1_title',
// 'product_image1_caption'        => 'product_image1_caption',
// 'product_image1_description'    => 'product_image1_description',

// 'product_image2_url'            => 'product_image2_url',
// 'product_image2_alt_text'       => 'product_image2_alt_text',
// 'product_image2_title'          => 'product_image2_title',
// 'product_image2_caption'        => 'product_image2_caption',
// 'product_image2_description'    => 'product_image2_description',

// 'product_image3_url'            => 'product_image3_url',
// 'product_image3_alt_text'       => 'product_image3_alt_text',
// 'product_image3_title'          => 'product_image3_title',
// 'product_image3_caption'        => 'product_image3_caption',
// 'product_image3_description'    => 'product_image3_description',

// 'product_image4_url'            => 'product_image4_url',
// 'product_image4_alt_text'       => 'product_image4_alt_text',
// 'product_image4_title'          => 'product_image4_title',
// 'product_image4_caption'        => 'product_image4_caption',
// 'product_image4_description'    => 'product_image4_description',

// 'product_image5_url'            => 'product_image5_url',
// 'product_image5_alt_text'       => 'product_image5_alt_text',
// 'product_image5_title'          => 'product_image5_title',
// 'product_image5_caption'        => 'product_image5_caption',
// 'product_image5_description'    => 'product_image5_description',

// 'category'            => 'category',
return apply_filters('woocommerce_csv_product_post_columns', array(
    'post_title'        => 'post_title',
    'post_excerpt'        => 'post_excerpt',
    'post_content'        => 'post_content',

    'custom_tab_title1'        => 'custom_tab_title1',
    'custom_tab_priority1'        => 'custom_tab_priority1',
    'custom_tab_content1'        => 'custom_tab_content1',
    'custom_tab_title2'        => 'custom_tab_title2',
    'custom_tab_content2'        => 'custom_tab_content2',
    'custom_tab_priority2'        => 'custom_tab_priority2',

    // Meta
    '_regular_price'    => '_regular_price',
    '_sale_price'        => '_sale_price',
    '_tax_status'        => '_tax_status',
    '_tax_class'        => '_tax_class',
    '_sku'                => '_sku',
    '_manage_stock'        => '_manage_stock',
    '_stock_status'        => '_stock_status',
    '_stock'            => '_stock',
    '_weight'            => '_weight',
    '_length'            => '_length',
    '_width'            => '_width',
    '_height'            => '_height',
    '_upsell_ids'        => '_upsell_ids',
    '_crosssell_ids'    => '_crosssell_ids',
    '_product_meta_id'    => '_product_meta_id',

    '_yoast_wpseo_focuskw'    => '_yoast_wpseo_focuskw',
    '_yoast_wpseo_metadesc'    => '_yoast_wpseo_metadesc',

    '_heading_2'            => '_heading_2',
    'heading_2'             => 'heading_2',
    '_heading_3'             => '_heading_3',
    'heading_3'             => 'heading_3',

    '_bmw-part-number'         => '_bmw-part-number',
    '_bmw-part_title'       => '_bmw-part_title',
    'bmw-part_title'        => 'bmw-part_title',
    'bmw-part-number'       => 'bmw-part-number',

    '_bosch-part_title'       => '_bosch-part_title',
    '_bosch-part_number'       => '_bosch-part_number',
    'bosch-part_title'       => 'bosch-part_title',
    '_bosch-part_number'       => '_bosch-part_number',

    '_backorders'        => '_backorders',
    '_sold_individually'    => '_sold_individually',
    '_virtual'            => '_virtual',
    '_downloadable'     => '_downloadable',
    '_download_limit'    => '_download_limit',
    '_download_expiry'    => '_download_expiry',

    '_wc_average_rating'    => '_wc_average_rating',
    '_wc_review_count'    => '_wc_review_count',

    '_product_attributes'    => '_product_attributes',
    '_product_version'    => '_product_version',

    'slide_template'    => 'slide_template',
    'swatch_options'    => 'swatch_options',
    'total_sales'    => 'total_sales',
    'layout'    => 'layout',
    'header_view'    => 'header_view',

    'product_image_on_hover'    => 'product_image_on_hover',
    '_visibility'        => '_visibility',

    '_visibility'        => '_visibility',
    '_purchase_note' => '_purchase_note',
    '_featured'            => '_featured',
    '_sale_price_dates_from' => '_sale_price_dates_from',
    '_sale_price_dates_to'      => '_sale_price_dates_to',

    '_default_attributes'        => '_default_attributes',
));

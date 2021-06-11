<?php

function igniteup_define_template_believe($templates) {
    $options = array(
	'name' => 'Believe',
	'folder_name' => 'believe',
	'options' => array(
	    'logo' => array(
		'type' => 'image',
		'label' => __('Logo (Transparent)', CSCS_TEXT_DOMAIN),
		'def' => plugins_url("believe/images/logo.png", __FILE__),
		'description' => __('Recommended size: 250px x 90px - (Keep it empty to hide logo)', CSCS_TEXT_DOMAIN),
	    ),
	    'bg_color' => array(
		'type' => 'color-picker',
		'label' => __('Background Color', CSCS_TEXT_DOMAIN),
		'def' => '#28BB9B',
		'placeholder' => '#28BB9B',
		'description' => __('This will be the background color.', CSCS_TEXT_DOMAIN),
	    ),
	    'bg_image' => array(
		'type' => 'image',
		'label' => __('Background Image', CSCS_TEXT_DOMAIN),
		'def' => '',
		'placeholder' => '',
		'description' => __('Page background image. (Recommended size: 1920px x 1080px)', CSCS_TEXT_DOMAIN),
	    ),
	    'font_color' => array(
		'type' => 'color-picker',
		'label' => __('Font Color', CSCS_TEXT_DOMAIN),
		'def' => '#fff',
		'placeholder' => '#FFFFFF',
		'description' => __('This will be the font color', CSCS_TEXT_DOMAIN),
	    ),
	    'plane' => array(
		'type' => 'checkbox',
		'label' => __('Show Paper Plane', CSCS_TEXT_DOMAIN),
		'def' => '1',
		'description' => __('Show/Hide Paper Plane', CSCS_TEXT_DOMAIN),
	    ),
	    'title_top' => array(
		'type' => 'text',
		'label' => __('Title Top', CSCS_TEXT_DOMAIN),
		'def' => __('Our Website is', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Our Website is', CSCS_TEXT_DOMAIN),
		'description' => __('Text above the main title', CSCS_TEXT_DOMAIN),
	    ),
	    'main_title' => array(
		'type' => 'text',
		'label' => __('Main title', CSCS_TEXT_DOMAIN),
		'def' => __('Coming Soon', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Coming Soon', CSCS_TEXT_DOMAIN),
		'description' => __('The bold title', CSCS_TEXT_DOMAIN),
	    ),
	    'paragraph' => array(
		'type' => 'textarea',
		'label' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'def' => __('Meanwhile feel free to interact with our social networks', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'description' => __('This will be the paragraph text', CSCS_TEXT_DOMAIN),
	    )
	)
    );

    $templates['believe'] = $options;
    return $templates;
}

add_filter('igniteup_get_templates', 'igniteup_define_template_believe');

function igniteup_believe_styles() {
    ?>
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/bootstrap.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-awesome.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-montserrat.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-biryani.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/templates/believe/css/main.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <?php
}

add_action('igniteup_styles_believe', 'igniteup_believe_styles');

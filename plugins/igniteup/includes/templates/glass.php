<?php

function igniteup_define_template_glass($templates) {
    $templates['glass'] = array(
	'name' => 'Glass',
	'folder_name' => 'glass',
	'options' => array(
	    'logo' => array(
		'type' => 'image',
		'label' => __('Logo (Transparent)', CSCS_TEXT_DOMAIN),
		'def' => plugins_url("glass/img/logo.png", __FILE__),
		'description' => __('Recommended size: 250px x 90px - (Keep it empty to hide logo)', CSCS_TEXT_DOMAIN),
	    ),
	    'bg_color' => array(
		'type' => 'color-picker',
		'label' => __('Background Color', CSCS_TEXT_DOMAIN),
		'def' => '#303030',
		'placeholder' => '#28BB9B',
		'description' => __('This will be the background color.', CSCS_TEXT_DOMAIN),
	    ),
	    'bg_image' => array(
		'type' => 'image',
		'label' => __('Background Image', CSCS_TEXT_DOMAIN),
		'def' => plugins_url("glass/img/bg_default.jpg", __FILE__),
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
	    'link_color' => array(
		'type' => 'color-picker',
		'label' => __('Link Color', CSCS_TEXT_DOMAIN),
		'def' => '#cbcbcb',
		'placeholder' => '#cbcbcb',
		'description' => __('This will be the hover color', CSCS_TEXT_DOMAIN),
	    ),
	    'title_top' => array(
		'type' => 'text',
		'label' => __('Title Top', CSCS_TEXT_DOMAIN),
		'def' => __('Under Maintenance', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Header Text', CSCS_TEXT_DOMAIN),
		'description' => __('This will be the main title', CSCS_TEXT_DOMAIN),
	    ),
	    'paragraph' => array(
		'type' => 'textarea',
		'label' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'def' => __('sorry for the inconvenience <br> we will come with a new experience.', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'description' => __('This will be the paragraph text, you can use html tags here.', CSCS_TEXT_DOMAIN),
	    ),
	    'subscribe' => array(
		'type' => 'checkbox',
		'label' => __('Show Subscribe Form', CSCS_TEXT_DOMAIN),
		'def' => '1',
		'description' => __('Show/Hide Email Subscribe Form', CSCS_TEXT_DOMAIN),
	    ),
	)
    );
    return $templates;
}

add_filter('igniteup_get_templates', 'igniteup_define_template_glass');

function glass_template_styles() {
    ?>
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/bootstrap.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/animate.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-awesome.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-montserrat.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-opensans.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/templates/glass/css/main.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <?php
}

add_action('igniteup_styles_glass', 'glass_template_styles');

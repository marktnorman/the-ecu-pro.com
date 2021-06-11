<?php

function igniteup_define_template_offline($templates) {
    $templates['offline'] = array(
	'name' => 'Offline',
	'folder_name' => 'offline',
	'options' => array(
	    'logo' => array(
		'type' => 'image',
		'label' => __('Logo (Transparent)', CSCS_TEXT_DOMAIN),
		'def' => plugins_url("offline/img/rockyton_color.png", __FILE__),
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
	    'link_color' => array(
		'type' => 'color-picker',
		'label' => __('Link Color', CSCS_TEXT_DOMAIN),
		'def' => '#f1c40f',
		'placeholder' => '#f1c40f',
		'description' => __('This will be the hover color', CSCS_TEXT_DOMAIN),
	    ),
	    'title_top' => array(
		'type' => 'text',
		'label' => __('Title Top', CSCS_TEXT_DOMAIN),
		'def' => __('Website is offline', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Website is offline', CSCS_TEXT_DOMAIN),
		'description' => __('Text above the main title', CSCS_TEXT_DOMAIN),
	    ),
	    'paragraph' => array(
		'type' => 'textarea',
		'label' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'def' => __('sorry for the inconvenience <br> we will come with new experience.', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'description' => __('This will be the paragraph text, you can use html tags here.', CSCS_TEXT_DOMAIN),
	    ),
	    'contact' => array(
		'type' => 'text',
		'label' => __('Contact text', CSCS_TEXT_DOMAIN),
		'def' => __('contact site admin:', CSCS_TEXT_DOMAIN),
		'placeholder' => __('contact site admin:', CSCS_TEXT_DOMAIN),
		'description' => __('Contact information label', CSCS_TEXT_DOMAIN),
	    ),
	    'email' => array(
		'type' => 'email',
		'label' => __('Contact email', CSCS_TEXT_DOMAIN),
		'def' => 'contact@email.com',
		'placeholder' => 'contact@email.com',
		'description' => __('Your email address', CSCS_TEXT_DOMAIN),
	    ),
	)
    );
    return $templates;
}

add_filter('igniteup_get_templates', 'igniteup_define_template_offline');

function igniteup_offline_styles() {
    ?>
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/bootstrap.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-montserrat.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-biryani.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet'  href='<?php echo plugins_url('includes/templates/offline/css/main.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <?php
}

add_action('igniteup_styles_offline', 'igniteup_offline_styles');
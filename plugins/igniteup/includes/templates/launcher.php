<?php

function igniteup_define_template_launcher($templates) {
    $templates['launcher'] = array(
	'name' => 'Launcher',
	'folder_name' => 'launcher',
	'options' => array(
	    'launch_date' => array(
		'type' => 'date',
		'label' => __('Launch Date', CSCS_TEXT_DOMAIN),
		'placeholder' => 'mm/dd/yyyy',
		'def' => date('m/d/Y', strtotime('Next Monday')),
		'description' => __('Add the date when you are going to launch the site', CSCS_TEXT_DOMAIN),
	    ),
	    'launch_time' => array(
		'type' => 'text',
		'label' => __('Launch Time', CSCS_TEXT_DOMAIN),
		'placeholder' => 'hh:mm:ss',
		'def' => '12:12:12',
		'description' => __('Note: Enter time in hh:mm:ss format.', CSCS_TEXT_DOMAIN),
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
	    'title_top' => array(
		'type' => 'text',
		'label' => __('Title Top Text', CSCS_TEXT_DOMAIN),
		'def' => __('Almost Ready', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Bold Title', CSCS_TEXT_DOMAIN),
		'description' => __('This will be the bold title', CSCS_TEXT_DOMAIN),
	    ),
	    'title' => array(
		'type' => 'text',
		'label' => __('Subtitle Text', CSCS_TEXT_DOMAIN),
		'def' => __('Website will launch in', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Subtitle', CSCS_TEXT_DOMAIN),
		'description' => __('Text below the title', CSCS_TEXT_DOMAIN),
	    ),
	    'paragraph' => array(
		'type' => 'textarea',
		'label' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'def' => __('This website is currently unavailable due to maintenance. Please visit again later. If you have any inquiries forward to the site admin. Please subscribe with our Newsletter.', CSCS_TEXT_DOMAIN),
		'placeholder' => __('Paragraph Text', CSCS_TEXT_DOMAIN),
		'description' => __('This will be the paragraph text', CSCS_TEXT_DOMAIN),
	    ),
	    'subscribe_text_color' => array(
		'type' => 'color-picker',
		'label' => __('Button Font Color', CSCS_TEXT_DOMAIN),
		'def' => '#fff',
		'placeholder' => '#FFFFFF',
		'description' => __('This will be the font color the button', CSCS_TEXT_DOMAIN),
	    ),
	    'subscribe_bg_color' => array(
		'type' => 'color-picker',
		'label' => __('Button Background Color', CSCS_TEXT_DOMAIN),
		'def' => '#DB4F4B',
		'placeholder' => '#FFFFFF',
		'description' => __('This will be the background color the button', CSCS_TEXT_DOMAIN),
		),
		'show_rocket' => array(
			'type' => 'checkbox',
			'label' => __('Show Rocket Animation', CSCS_TEXT_DOMAIN),
			'def' => '1',
			'description' => __('Show/Hide Rocket Image', CSCS_TEXT_DOMAIN),
			),
	)
    );
    return $templates;
}

add_filter('igniteup_get_templates', 'igniteup_define_template_launcher');

function cscs_launcher_theme_scripts() {
    ?>
	<link rel = 'stylesheet' href = '<?php echo plugins_url('includes/css/bootstrap.min.css', CSCS_FILE) ?>' type = 'text/css' media = 'all' />
	<link rel='stylesheet'  href='<?php echo plugins_url('includes/css/font-awesome.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel = 'stylesheet' href = '<?php echo plugins_url('includes/css/animate.css', CSCS_FILE) ?>' type = 'text/css' media = 'all' />
    <link rel = 'stylesheet' href = '<?php echo plugins_url('includes/css/font-montserrat.css', CSCS_FILE) ?>' type = 'text/css' media = 'all' />
    <link rel = 'stylesheet' href = '<?php echo plugins_url('includes/css/icons/styles.css', CSCS_FILE) ?>' type = 'text/css' media = 'all' />
    <link rel = 'stylesheet' href = '<?php echo plugins_url('includes/templates/launcher/css/main.css', CSCS_FILE) ?>' type = 'text/css' media = 'all' />
    <?php
}

add_action('igniteup_styles_launcher', 'cscs_launcher_theme_scripts');

<?php

function igniteup_define_template_cleaner($templates)
{
    $templates['cleaner'] = array(
        'name' => 'Cleaner',
        'folder_name' => 'cleaner',
        'options' => array(
            'logo' => array(
                'type' => 'image',
                'label' => __('Logo (Transparent)', CSCS_TEXT_DOMAIN),
                'def' => plugins_url("cleaner/img/logo.png", __FILE__),
                'description' => __('Recommended size: 250px x 90px - (Keep it empty to hide logo)', CSCS_TEXT_DOMAIN),
            ),
            'featured' => array(
                'type' => 'image',
                'label' => __('Featured Image (Transparent)', CSCS_TEXT_DOMAIN),
                'def' => plugins_url("cleaner/img/ftd.png", __FILE__),
                'description' => __('(Keep it empty to hide)', CSCS_TEXT_DOMAIN),
            ),
            'bg_color' => array(
                'type' => 'color-picker',
                'label' => __('Background Color', CSCS_TEXT_DOMAIN),
                'def' => '#ededed',
                'placeholder' => '#ededed',
                'description' => __('This will be the background color.', CSCS_TEXT_DOMAIN),
            ),
            'bg_image' => array(
                'type' => 'image',
                'label' => __('Background Image', CSCS_TEXT_DOMAIN),
                'def' => plugins_url("cleaner/img/background.jpg", __FILE__),
                'placeholder' => '',
                'description' => __('Page background image. (Recommended size: 1920px x 1080px)', CSCS_TEXT_DOMAIN),
            ),
            'main_title' => array(
                'type' => 'text',
                'label' => __('Main Title Text', CSCS_TEXT_DOMAIN),
                'def' => __('Lorem Ipsum', CSCS_TEXT_DOMAIN),
                'placeholder' => __('Bold Title', CSCS_TEXT_DOMAIN),
                'description' => __('This will be the bold title', CSCS_TEXT_DOMAIN),
            ),
            'secondary_title' => array(
                'type' => 'text',
                'label' => __('Sub Title Text', CSCS_TEXT_DOMAIN),
                'def' => __('Dolor sit amet', CSCS_TEXT_DOMAIN),
                'placeholder' => __('Secondary title text', CSCS_TEXT_DOMAIN),
                'description' => __('This will be the secondary text', CSCS_TEXT_DOMAIN),
            ),
            'paragraph_title' => array(
                'type' => 'textarea',
                'label' => __('Descriptive Text', CSCS_TEXT_DOMAIN),
                'def' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Risus commodo viverra maecenas accumsan lacus vel facilisis.', CSCS_TEXT_DOMAIN),
                'placeholder' => __('Paragraph text', CSCS_TEXT_DOMAIN),
                'description' => __('This will be the text on paragraph. (HTML Supported)', CSCS_TEXT_DOMAIN),
            ),
            'font_color' => array(
                'type' => 'color-picker',
                'label' => __('Font Color', CSCS_TEXT_DOMAIN),
                'def' => '#000',
                'placeholder' => '#28BB9B',
                'description' => __('This will be the base color of all texts.', CSCS_TEXT_DOMAIN),
            ),
            'icon_color' => array(
                'type' => 'color-picker',
                'label' => __('Social Icon Color', CSCS_TEXT_DOMAIN),
                'def' => '#399',
                'placeholder' => '#28BB9B',
                'description' => __('This will be the base color for social media icons.', CSCS_TEXT_DOMAIN),
            ),
            'icon_hovercolor' => array(
                'type' => 'color-picker',
                'label' => __('Social Icon Hover Color', CSCS_TEXT_DOMAIN),
                'def' => '#000',
                'placeholder' => '#28BB9B',
                'description' => __('This will be the hover color for social media icons.', CSCS_TEXT_DOMAIN),
            ),
        )
    );
    return $templates;
}

add_filter('igniteup_get_templates', 'igniteup_define_template_cleaner');

function cleaner_template_styles()
{
    ?>
    <!-- <link rel='stylesheet' href='<?php echo plugins_url('includes/css/font-opensans.min.css', CSCS_FILE) ?>' type='text/css' media='all' /> -->
    <link rel='stylesheet' href='<?php echo plugins_url('includes/css/font-awesome.min.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <link rel='stylesheet' href='<?php echo plugins_url('includes/templates/cleaner/css/main.css', CSCS_FILE) ?>' type='text/css' media='all' />
    <style>
        <?php
        global $the_cs_template_options;
        $css_arr = array(
            array(
                'el' => 'body',
                'styles' => array(
                    'backgroundColor' => $the_cs_template_options['bg_color'],
                    'color' => $the_cs_template_options['font_color']
                )
            ),
            array(
                'el' => '.social-connect a',
                'styles' => array(
                    'color' => $the_cs_template_options['icon_color'],
                )
            ),
            array(
                'el' => '.social-connect a:hover',
                'styles' => array(
                    'color' => $the_cs_template_options['icon_hovercolor']
                )
            ),
            array(
                'el' => 'body::after',
                'styles' => array(
                    'backgroundImage' => $the_cs_template_options['bg_image']
                )
            ),
        );
        render_dynamic_css($css_arr); ?>
    </style>
<?php
}

function cleaner_template_scripts()
{
    ?>
    <script src="<?php echo plugins_url('includes/js/jquery.min.js', CSCS_FILE) ?>"></script>
    <script src="<?php echo plugins_url('includes/js/subscribe.js', CSCS_FILE) ?>"></script>
<?php
}

add_action('igniteup_styles_cleaner', 'cleaner_template_styles');
add_action('igniteup_scripts_cleaner', 'cleaner_template_scripts');

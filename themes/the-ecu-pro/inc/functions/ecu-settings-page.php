<?php

add_action('admin_menu', 'ecu_admin_menu');
/**
 * ecu_admin_menu
 */
function ecu_admin_menu()
{
    add_options_page(
        __('ECU Options', 'textdomain'),
        __('ECU Options', 'textdomain'),
        'manage_options',
        'ecu-options',
        'ecu_options_page'
    );
}

add_action('admin_init', 'ecu_admin_init');
/**
 * ecu_admin_init
 */
function ecu_admin_init()
{

    register_setting('ecu-settings-group', 'ecu-page-settings');

    add_settings_section('section-1', __('Section One', 'textdomain'), 'section_1_callback', 'ecu-options-page');

    add_settings_field('field-1-1', __('Field One', 'textdomain'), 'field_1_1_callback', 'ecu-options-page', 'section-1');
}

/*
 * THE ACTUAL PAGE
 * */
function ecu_options_page()
{
    ?>
    <div class="wrap">
        <h2><?php _e('ECU Fault Options', 'textdomain'); ?></h2>
        <form action="options.php" method="POST">
            <?php settings_fields('ecu-settings-group'); ?>
            <?php do_settings_sections('ecu-options-page'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

function section_1_callback()
{
    _e('Some help text regarding Section One goes here.', 'textdomain');
}

/*
* THE FIELDS
* */
function field_1_1_callback()
{

    $settings = (array) get_option('ecu-page-settings');
    $field    = "field_1_1";
    $value    = esc_attr($settings[$field]);

    echo "<input type='text' name='ecu-page-settings[$field]' value='$value' />";
}

function my_settings_validate_and_sanitize($input)
{

    $settings = (array) get_option('ecu-page-settings');

    if ($some_condition == $input['field_1_1']) {
        $output['field_1_1'] = $input['field_1_1'];
    } else {
        add_settings_error(
            'ecu-page-settings',
            'invalid-field_1_1',
            'You have entered an invalid value into Field One.'
        );
    }

    // and so on for each field

    return $output;
}
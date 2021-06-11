<?php

class CSAdminOptions
{

    public static $gener_options = array('enable', 'cs_page_title', 'skipfor', 'skip_with_get_request', 'whitelisted_ips', 'powered_by', 'customcss', 'favicon_url', 'send_status');
    public static $common_options = array('subscribe_text', 'alert_thankyou', 'alert_error_invalid_email', 'alert_error_already_exists', 'get_email_on_subscribe', 'social_twitter', 'social_facebook', 'social_pinterest', 'social_youtube', 'social_behance', 'social_medium', 'social_viber', 'social_whatsapp', 'social_messenger', 'social_github', 'social_linkedin', 'social_instagram', 'receive_email_addr', 'success_notice');
    private static $integration_options = array('mailchimp_api', 'mailchimp_list', 'save_email_to', 'enable_integration', 'mailpoet_list');

    public static function registerGeneralOptions()
    {
        foreach (self::$gener_options as $val)
            register_setting('cscs_gener_options', CSCS_GENEROPTION_PREFIX . $val);

        foreach (self::$integration_options as $ival)
            register_setting('cscs_integrat_options', CSCS_GENEROPTION_PREFIX . $ival);

        foreach (self::$common_options as $xval)
            register_setting('cscs_common_options', CSCS_GENEROPTION_PREFIX . $xval);
    }

    public static function registerOptions()
    {
        global $cscs_templates;
        $template_options = $cscs_templates[CSCS_DEFAULT_TEMPLATE];
        if (!isset($template_options['options']) || count($template_options['options']) < 1)
            return;
        foreach ($template_options['options'] as $key => $val) {
            register_setting('cscs_temp_options', CSCS_TEMPLATEOPTION_PREFIX . CSCS_DEFAULT_TEMPLATE . '_' . $key);
        }
    }

    public static function optionsPage()
    {
        include 'views/admin-dashboard.php';
    }

    public static function templatePage()
    {
        include 'views/admin-templates.php';
    }

    public static function subscribersPage()
    {
        include 'views/admin-subscribers.php';
    }

    private function getNameFromFilePath($file)
    {
        $ss = preg_split('/', $file);
        $remove_ext = explode('.', end($ss));
        unset($remove_ext[(count($remove_ext) - 1)]);
        return implode('', $remove_ext);
    }

    public static function getDefTemplate()
    {
        $saved_ = get_option(CSCS_DEFTEMP_OPTION, 'launcher');
        $templates = CSAdminOptions::getTemplates();
        $template_data = $templates[$saved_];
        $file = dirname(__FILE__) . '/templates/' . $saved_ . '/' . $saved_ . '.php';
        if (isset($template_data['plugin_file']) && !empty($template_data['plugin_file']))
            $file = dirname($template_data['plugin_file']) . '/template/index.php';

        $files = glob($file);

        if (count($files) < 1) {
            update_option(CSCS_DEFTEMP_OPTION, 'launcher');
            return 'launcher';
        }
        return $saved_;
    }

    public static function getTemplates($onlyCheckIfThereAreIncompatibles = FALSE)
    {
        global $cscs_templates;
        $version = get_option(CSCS_GENEROPTION_PREFIX . 'version', '1.0');
        $templates = apply_filters('igniteup_get_templates', $cscs_templates);
        ksort($templates);

        /*
         * 
         * Check for incompatible templates.
         * 
         */
        foreach ($templates as $template => $option) {
            if (isset($option['igniteup_version']) && floatval($option['igniteup_version']) > floatval($version)) {
                if ($onlyCheckIfThereAreIncompatibles) {
                    return TRUE;
                } else {
                    unset($templates[$template]);
                }
            }
        }
        $cscs_templates = $templates;
        return $onlyCheckIfThereAreIncompatibles ? FALSE : $templates; //Return FALSE if all themes are compatible.
    }

    public static function setDefaultOptions()
    {
        update_option(CSCS_GENEROPTION_PREFIX . 'skip_with_get_request', rand(100000000, 9999999999999));
    }

    public static function selectOptionIsSelected($saved_val, $current_val)
    {
        if ($saved_val == $current_val)
            return 'selected';
        return '';
    }

    /**
     * Returns a requested default string.
     * Available Strings are listed below.
     * 
     * CountDown: days, hours, minutes, secs, mins                                                                  
     * 
     * Forms: subscribe_thankyou, contact_success, contact_failed
     * 
     * Common: invalid_email, existing_email, sending, error                                                             
     * 
     * @param string $key slug
     * @return string Requested translatable string 
     */
    public static function getDefaultStrings($key)
    {
        $option_name = CSCS_GENEROPTION_PREFIX . $key;
        $return = get_option($option_name);
        $array = self::messageBag();
        if (empty($return))
            $return = $array[$key];
        return $return;
    }

    /**
     * Array of default strings
     * 
     * @return array
     */
    static function messageBag()
    {
        return array(

            /*
             * 
             * Subscribe Form
             * 
             */
            'subscribe_text' => __('Subscribe', CSCS_TEXT_DOMAIN),
            'subscribe_done' => __('Subscribed', CSCS_TEXT_DOMAIN),
            'alert_thankyou' => __('<strong>Thank you!</strong> You are subscribed.', CSCS_TEXT_DOMAIN), //Old templates: before v3.1
            'alert_error_invalid_email' => __('<strong>Invalid Email!</strong> Please try again.', CSCS_TEXT_DOMAIN), //Old templates: before v3.1
            'alert_error_already_exists' => __('<strong>Email already exists!</strong> Please try again.', CSCS_TEXT_DOMAIN), //Old templates: before v3.1
            'subscribe_thankyou' => __('Thanks for subscribing!', CSCS_TEXT_DOMAIN), //Subscribe form on SUCCESS

            /*
             * 
             * Contact Form
             * 
             */
            'contact_success' => __("Thanks for making a sound!", CSCS_TEXT_DOMAIN), //Contact form on SUCCESS
            'contact_failed' => __("Message not sent.", CSCS_TEXT_DOMAIN), //Contact form on !SUCCESS

            /*
             * 
             * Common
             * 
             */
            'invalid_email' => __("Please enter a valid email.", CSCS_TEXT_DOMAIN),
            'existing_email' => __("That email is already with us.", CSCS_TEXT_DOMAIN),
            'sending' => __("Sending...", CSCS_TEXT_DOMAIN), //Button text till ajax completion
            'error' => __("Something Went Wrong.", CSCS_TEXT_DOMAIN),

            /*
             * 
             * Countdown
             * 
             */
            'days' => __('DAY<span id="day-s">S</span>', CSCS_TEXT_DOMAIN),
            'hours' => __('HOUR<span id="hrs-s">S</span>', CSCS_TEXT_DOMAIN),
            'minutes' => __('MINUTE<span id="min-s">S</span>', CSCS_TEXT_DOMAIN),
            'seconds' => __('SECOND<span id="sec-s">S</span>', CSCS_TEXT_DOMAIN),
            'secs' => __('SECS', CSCS_TEXT_DOMAIN),
            'mins' => __('MINS', CSCS_TEXT_DOMAIN),
        );
    }
}

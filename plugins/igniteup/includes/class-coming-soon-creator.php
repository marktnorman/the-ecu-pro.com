<?php

class CSComingSoonCreator
{

	public function __construct($file = NULL, $version = NULL)
	{
		$this->setConstants($file, $version);

		add_action('admin_menu', array($this, 'setMenus'));
		add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));
		add_action('wp_enqueue_scripts', array($this, 'dequeScripts'), 99998); //TODO: deprecate this
		add_action('wp_enqueue_scripts', array($this, 'loadThemeScripts'), 99999); //TODO: deprecate this
		add_action('wp_head', array($this, 'wpHeadForOldThemes')); //TODO: deprecate this
		add_action('admin_init', array('CSAdminOptions', 'registerOptions'));
		add_action('admin_init', array('CSAdminOptions', 'registerGeneralOptions'));
		add_action('admin_init', array($this, 'removeSubscribers'));
		add_action('igniteup_head', array($this, 'headStatics'));
		add_action('igniteup_footer', array($this, 'footerScripts'));
		add_action("template_include", array($this, 'myThemeRedirect'));
		add_action('upload_mimes', array($this, 'customMimes'));
		add_action('admin_init', array($this, 'activateTemplate'));
		add_action('admin_init', array($this, 'showWelcomeMessage'));
		add_action('init', array($this, 'makeTemplateOptionsGlobal'));
		register_activation_hook(CSCS_FILE, array('CSAdminOptions', 'setDefaultOptions'));
		register_deactivation_hook(CSCS_FILE, array($this, 'deactivationCleanp'));
		add_action('wp_ajax_nopriv_subscribe_email', array($this, 'subscribeEmail'));
		add_action('wp_ajax_subscribe_email', array($this, 'subscribeEmail'));
		add_action('wp_ajax_igniteup_admin_subscribe', array($this, 'adminSubscribe'));
		add_action('wp_ajax_nopriv_contact_form', array($this, 'sendContactForm'));
		add_action('wp_ajax_contact_form', array($this, 'sendContactForm'));
		add_action('admin_init', array($this, 'createCsvFile'));
		add_action('admin_init', array($this, 'createBccFile'));
		add_action('admin_bar_menu', array($this, 'showAdminBarMenu'), 1000);
		add_action('after_setup_theme', array($this, 'load_languages'));
		add_action('admin_notices', array($this, 'noncompatible_theme_notice'));
		new CSComingSoonDbMigrations();
	}

	/*
     *
     * Define constants required by the plugin
     *
     *
     */

	private function setConstants($file, $version = NULL)
	{
		global $wpdb;
		define('CSCS_TEXT_DOMAIN', 'igniteup');
		define('CSCS_DEFTEMP_OPTION', 'cscs_default_template');
		define('CSCS_TEMPLATEOPTION_PREFIX', 'cscs_tempoption_');
		define('CSCS_GENEROPTION_PREFIX', 'cscs_generpotion_');
		define('CSCS_CAPABILITY_PREFIX', 'cscs_cap_');
		define('CSCS_DBTABLE_PREFIX', $wpdb->prefix . 'cscs_db_');
		define('CSCS_DBTABLE_SUBSCRIPTS', 'subscriptions');
		define('CSCS_DEFAULT_TEMPLATE', CSAdminOptions::getDefTemplate());
		define('CSCS_DEFAULT_TEMPLATE_LIST', '["launcher", "believe","offline","glass","cleaner"]');

		if (!empty($file))
			define('CSCS_FILE', $file);

		if (!empty($version))
			define('CSCS_CURRENT_VERSION', $version);
	}

	/*
     *
     * Set menu items
     *
     */

	public function setMenus()
	{
		add_menu_page('IgniteUp', __('IgniteUp', CSCS_TEXT_DOMAIN), 'manage_options', 'cscs_templates', '', '', 39);
		add_submenu_page('cscs_templates', __('Templates', CSCS_TEXT_DOMAIN), __('Templates', CSCS_TEXT_DOMAIN), 'manage_options', 'cscs_templates', array('CSAdminOptions', 'templatePage'));
		add_submenu_page('cscs_templates', __('Subscribers', CSCS_TEXT_DOMAIN), __('Subscribers', CSCS_TEXT_DOMAIN), 'manage_options', 'cscs_subscribers', array('CSAdminOptions', 'subscribersPage'));
		add_submenu_page('cscs_templates', __('Options', CSCS_TEXT_DOMAIN), __('Options', CSCS_TEXT_DOMAIN), 'manage_options', 'cscs_options', array('CSAdminOptions', 'optionsPage'));
	}

	private function greenToPublishTheme($set_cookie = false)
	{
		if (isset($_REQUEST['igniteup']) && $_REQUEST['igniteup'] == 'force')
			return TRUE;
		if (!$this->checkIfEnabled())
			return FALSE;
		if ($this->checkForSkipping())
			return FALSE;
		if ($this->checkForWhiteListedIPs())
			return FALSE;
		if ($this->checkForGetRequest($set_cookie))
			return FALSE;
		return TRUE;
	}

	public function loadThemeScripts()
	{
		wp_enqueue_style('igniteup-front-compulsory', plugin_dir_url(CSCS_FILE) . 'includes/css/front-compulsory.css', array(), CSCS_CURRENT_VERSION);
		if (!$this->greenToPublishTheme())
			return;

		do_action('cscs_theme_scripts_' . CSCS_DEFAULT_TEMPLATE);
		wp_enqueue_style('igniteup-front', plugin_dir_url(CSCS_FILE) . 'includes/css/front.css', array(), CSCS_CURRENT_VERSION);

		$custom_css = get_option(CSCS_GENEROPTION_PREFIX . 'customcss', '');
		wp_add_inline_style('igniteup-front', $custom_css);
	}

	public function dequeScripts()
	{
		if (!$this->greenToPublishTheme())
			return;

		$skip_scr = array('colors', 'wp-admin', 'login', 'install', 'wp-color-picker', 'customize-controls', 'customize-widgets', 'press-this', 'ie', 'admin-bar');
		global $wp_styles;
		if (!is_a($wp_styles, 'WP_Styles'))
			return;
		$registered_array = $wp_styles->registered;
		if (!is_array($registered_array))
			$registered_array = array();

		foreach ($registered_array as $script) {
			if (isset($script->handle) && !in_array($script->handle, $skip_scr)) {
				wp_dequeue_style($script->handle);
			}
		}
	}

	public function myThemeRedirect($original_template)
	{
		if (!$this->greenToPublishTheme(true))
			return $original_template;

		/*
	 * If option is set, send the header 503.
	 */
		if (get_option(CSCS_GENEROPTION_PREFIX . 'send_status', '1') == '1') {
			$protocol = "HTTP/1.0";
			if ("HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"])
				$protocol = "HTTP/1.1";
			header("$protocol 503 Service Unavailable", true, 503);
			header("Retry-After: 3600");
		}

		global $wp;
		$file = dirname(__FILE__) . '/templates/' . CSCS_DEFAULT_TEMPLATE . '/' . CSCS_DEFAULT_TEMPLATE . '.php';
		$template_data = $this->getActiveTemplateObject();

		if (isset($template_data['plugin_file']) && !empty($template_data['plugin_file']))
			$file = dirname($template_data['plugin_file']) . '/template/index.php';

		if (file_exists($file))
			include $file;
		die();
	}

	public function loadAdminScripts()
	{
		wp_enqueue_style('rockyton-icon', plugin_dir_url(CSCS_FILE) . 'includes/css/icons/styles.css', array(), CSCS_CURRENT_VERSION);
		wp_enqueue_style('igniteup', plugin_dir_url(CSCS_FILE) . 'includes/css/main.css', array(), CSCS_CURRENT_VERSION);
		wp_enqueue_style('bootstrap-switch', plugin_dir_url(CSCS_FILE) . 'includes/css/bootstrap-switch.min.css', array(), CSCS_CURRENT_VERSION);

		if (isset($_GET['page']) && ($_GET['page'] == 'cscs_options' || $_GET['page'] == 'cscs_subscribers')) {
			wp_enqueue_style('wp-color-picker');

			wp_enqueue_style('codemirror-main', plugin_dir_url(CSCS_FILE) . 'includes/css/codemirror/codemirror.css');
			wp_enqueue_style('codemirror-theme', plugin_dir_url(CSCS_FILE) . 'includes/css/codemirror/ttcn.css');
			wp_enqueue_style('jquery-timepicker-css', plugin_dir_url(CSCS_FILE) . 'includes/css/jquery.timepicker.min.css');
			wp_enqueue_script('jquery');

			wp_enqueue_script('jquery-form', false, array('jquery'));
			wp_enqueue_script('jquery-timepicker', plugin_dir_url(CSCS_FILE) . 'includes/js/jquery.timepicker.min.js', array('jquery'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('jquery-ui-accordion', false, array('jquery'));
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_style('jquery-ui-theme', '//code.jquery.com/ui/1.11.4/themes/flick/jquery-ui.css');
			wp_enqueue_script('bootstrap-switch', plugin_dir_url(CSCS_FILE) . 'includes/js/bootstrap-switch.min.js', array('jquery'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('igniteup', plugin_dir_url(CSCS_FILE) . 'includes/js/main.js', array('jquery', 'wp-color-picker', 'jquery-timepicker'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('codemirror-main', plugin_dir_url(CSCS_FILE) . 'includes/js/codemirror/codemirror.js', array(), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('codemirror-helper-css', plugin_dir_url(CSCS_FILE) . 'includes/js/codemirror/css.js', array('codemirror-main'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('codemirror-helper-js', plugin_dir_url(CSCS_FILE) . 'includes/js/codemirror/javascript.js', array('codemirror-main'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('codemirror-helper-xml', plugin_dir_url(CSCS_FILE) . 'includes/js/codemirror/xml.js', array('codemirror-main'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_script('codemirror-helper-html', plugin_dir_url(CSCS_FILE) . 'includes/js/codemirror/htmlmixed.js', array('codemirror-main', 'codemirror-helper-css', 'codemirror-helper-js', 'codemirror-helper-xml'), CSCS_CURRENT_VERSION, true);
			wp_enqueue_media();
		}
	}

	public static function getDefaultTemplateList()
	{
		return json_decode(CSCS_DEFAULT_TEMPLATE_LIST, TRUE);
	}

	public function customMimes($existing_mimes = array())
	{
		$existing_mimes['zip'] = 'application/zip';
		$existing_mimes['gz'] = 'application/x-gzip';
		return $existing_mimes;
	}

	public function activateTemplate()
	{
		if (!isset($_POST['activate_template']) || empty($_POST['activate_template']) || !is_super_admin())
			return;
		update_option(CSCS_DEFTEMP_OPTION, $_POST['activate_template']);
		header('Location: ' . $_SERVER['REQUEST_URI'] . '&activated=yes');
	}

	private function getActiveTemplateObject()
	{
		$templates = CSAdminOptions::getTemplates();
		return $templates[CSCS_DEFAULT_TEMPLATE];
	}

	public function makeTemplateOptionsGlobal()
	{
		$temp = $this->getActiveTemplateObject();
		$arr = array();
		if (isset($temp['options'])) {
			foreach ($temp['options'] as $key => $field) {
				$option_key = CSCS_TEMPLATEOPTION_PREFIX . CSCS_DEFAULT_TEMPLATE . '_' . $key;
				$saved_value = get_option($option_key, isset($field['def']) ? $field['def'] : '');
				$arr[$key] = $saved_value;
			}
		}

		$general_options = CSAdminOptions::$gener_options;
		foreach ($general_options as $opt) {
			$arr['general_' . $opt] = get_option(CSCS_GENEROPTION_PREFIX . $opt, '');
		}

		$common_options = CSAdminOptions::$common_options;
		foreach ($common_options as $opt) {
			$arr['common_' . $opt] = get_option(CSCS_GENEROPTION_PREFIX . $opt, '');
		}

		$sm_array = array(
			'common_social_facebook' => 'facebook',
			'common_social_twitter' => 'twitter-square',
			'common_social_messenger' => 'facebook-messenger',
			'common_social_whatsapp' => 'whatsapp-square',
			'common_social_viber' => 'viber',
			'common_social_pinterest' => 'pinterest-square',
			'common_social_youtube' => 'youtube-square',
			'common_social_instagram' => 'instagram',
			'common_social_behance' => 'behance-square',
			'common_social_linkedin' => 'linkedin',
			'common_social_github' => 'github-square',
			'common_social_medium' => 'medium',

		);
		$arr['social_icon_map'] = $sm_array;

		global $the_cs_template_options;
		$the_cs_template_options = $arr;
	}

	private function checkIfEnabled()
	{
		$get = get_option(CSCS_GENEROPTION_PREFIX . 'enable', '');
		if ($get == '1')
			return TRUE;
		return FALSE;
	}

	private function checkForSkipping()
	{
		if (!is_user_logged_in())
			return FALSE;
		$skipfor = get_option(CSCS_GENEROPTION_PREFIX . 'skipfor');
		$skip_for_array = empty($skipfor) ? array() : json_decode($skipfor, TRUE);
		global $current_user;
		$user_roles = $current_user->roles;
		if (in_array($user_roles[0], $skip_for_array))
			return TRUE;
		return FALSE;
	}

	private function checkForWhiteListedIPs()
	{
		$text = get_option(CSCS_GENEROPTION_PREFIX . 'whitelisted_ips', '');
		if (empty($text))
			return FALSE;

		$text_br = nl2br(trim($text));
		$iplist = explode("<br />", $text_br);

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$trimmed_ips = array_map('trim', $iplist);
		if (in_array($ip, $trimmed_ips))
			return TRUE;
		return FALSE;
	}

	private function checkForGetRequest($set_cookie)
	{
		$slug = CSCS_GENEROPTION_PREFIX . 'skip_with_get_request';
		$cookie_slug = 'igniteup_skip';
		$savedGet = get_option($slug, '');

		if (
			isset($_GET['ign_skip']) && $_GET['ign_skip'] == $savedGet || //Check if GET has set
			isset($_COOKIE[$cookie_slug]) && $_COOKIE[$cookie_slug] == $savedGet
		) { //Check if COOKIE has set
			if ($set_cookie && !isset($_COOKIE[$cookie_slug])) //Create cookie if not set
				setcookie($cookie_slug, $savedGet);
			return TRUE; //Skip igniteup
		}
		return FALSE; //Show igniteup
	}

	public function showAdminBarMenu()
	{
		if (!$this->checkIfEnabled())
			return;

		global $wp_admin_bar;

		if (!is_super_admin() || !is_admin_bar_showing())
			return;

		$wp_admin_bar->add_menu(array('id' => 'igniteup_enabled', 'title' => __('IgniteUp: Enabled', CSCS_TEXT_DOMAIN), 'href' => admin_url('admin.php?page=cscs_options')));
	}

	public function subscribeEmail()
	{
		$email = isset($_REQUEST['cs_email']) ? trim($_REQUEST['cs_email']) : '';
		$errorInput = CSAdminOptions::getDefaultStrings('alert_error_invalid_email');
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('status' => FALSE, 'error' => TRUE, 'message' => $errorInput));
			wp_die();
		}
		$name = '';
		if (!empty($_REQUEST['cs_name']))
			$name = filter_var($_REQUEST['cs_name'], FILTER_SANITIZE_STRING);

		global $wpdb;
		$errorAlreadyExists = CSAdminOptions::getDefaultStrings('alert_error_already_exists');
		$wpdb->get_results("SELECT id FROM " . CSCS_DBTABLE_PREFIX . CSCS_DBTABLE_SUBSCRIPTS . " WHERE email = '$email'", 'ARRAY_A');
		if ($wpdb->num_rows > 0) {
			echo json_encode(array('status' => FALSE, 'error' => TRUE, 'message' => $errorAlreadyExists));
			wp_die();
		}

		$wpdb->insert(CSCS_DBTABLE_PREFIX . CSCS_DBTABLE_SUBSCRIPTS, array('name' => $name, 'email' => $email));
		$this->subscribeToMailingLists($name, $email);
		if (get_option(CSCS_GENEROPTION_PREFIX . 'get_email_on_subscribe') == 'on')
			$this->notifySubscriber($email, $name);
		echo json_encode(array('status' => TRUE, 'error' => FALSE));
		wp_die();
	}

	public function sendContactForm()
	{
		$name = trim($_REQUEST['contact_name']);
		$email = trim($_REQUEST['contact_email']);
		$subject = isset($_REQUEST['contact_subject']) ? trim($_REQUEST['contact_subject']) : NULL;
		$message = trim($_REQUEST['contact_message']);
		// Create the email and send the message
		$cs_receive_email_addr = CSCS_GENEROPTION_PREFIX . 'receive_email_addr';
		$admin_email = get_bloginfo('admin_email');
		$to = get_option($cs_receive_email_addr, $admin_email);
		$email_subject = sprintf(__('%1$s Sent you a Message via %2$s Contact Form', CSCS_TEXT_DOMAIN), $name, get_bloginfo('name'));
		$subjectHTML = isset($subject) ? "<h3>" . nl2br(str_replace("\'", "'", filter_var($subject, FILTER_SANITIZE_STRING))) . "</h3>" : '';
		$email_body = "<html><body>" .
			$subjectHTML .
			"<p>" . nl2br(str_replace("\'", "'", filter_var($message, FILTER_SANITIZE_STRING))) . "</p><hr>" .
			"<h4>" . __('This message was sent to you via IgniteUp Contact Form', CSCS_TEXT_DOMAIN) . "</h4>" .
			"<p>" . __('Name:', CSCS_TEXT_DOMAIN) . " $name<br>" .
			__('Email:', CSCS_TEXT_DOMAIN) . " $email</p>" .
			"<p>" . __('You can reply the sender directly by replying to this email.', CSCS_TEXT_DOMAIN) . "</p>" .
			"</body></html>";
		$url = get_bloginfo('url');
		$status = wp_mail($to, $email_subject, $email_body, array("Reply-To: $email", "MIME-Version: 1.0", "Content-Type: text/html; charset=UTF-8"));
		echo json_encode(array('success' => $status));
		wp_die();
	}

	private function subscribeToMailingLists($name, $email)
	{
		$enabled = get_option(CSCS_GENEROPTION_PREFIX . 'enable_integration');
		if ($enabled !== '1')
			return;

		$cs_name_int_save_to_val = get_option(CSCS_GENEROPTION_PREFIX . 'save_email_to', '');

		switch ($cs_name_int_save_to_val) {
			case 'mailchimp':
				$cs_mailchimp_api_key = get_option(CSCS_GENEROPTION_PREFIX . 'mailchimp_api', '');
				if (empty($cs_mailchimp_api_key))
					return;

				$cs_mailchimp_list = get_option(CSCS_GENEROPTION_PREFIX . 'mailchimp_list', '');
				if (empty($cs_mailchimp_list))
					return;

				$MailChimp = new IgniteUpMailChimp($cs_mailchimp_api_key);
				$return = $MailChimp->call('lists/subscribe', array(
					'id' => $cs_mailchimp_list,
					'email' => array('email' => $email),
					'merge_vars' => array('EMAIL' => $email, 'FNAME' => $name, 'LNAME' => '')
				));
				update_option(CSCS_GENEROPTION_PREFIX . 'integrat_return', serialize($return));
				break;
			case 'mailpoet':
				$cs_mailpet_list_val = get_option(CSCS_GENEROPTION_PREFIX . 'mailpoet_list', '');

				if (empty($cs_mailpet_list_val))
					return;

				$user_data = array(
					'email' => $email,
					'firstname' => $name
				);

				$data_subscriber = array(
					'user' => $user_data,
					'user_list' => array('list_ids' => array($cs_mailpet_list_val))
				);

				$helper_user = WYSIJA::get('user', 'helper');
				$helper_user->addSubscriber($data_subscriber);
				break;
			default:
				break;
		}
	}

	private function convertToCsv($input_array, $output_file_name, $delimiter)
	{
		$temp_memory = fopen('php://memory', 'w');
		foreach ($input_array as $line) {
			fputcsv($temp_memory, $line, $delimiter);
		}
		fseek($temp_memory, 0);
		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
		fpassthru($temp_memory);
	}

	public function createCsvFile()
	{
		if (!isset($_GET['rockython_createcsv']) || !isset($_GET['sub']) || !is_super_admin())
			return;

		check_admin_referer('download-csv');

		global $wpdb;
		$subs = $wpdb->get_results("SELECT * FROM " . CSCS_DBTABLE_PREFIX . CSCS_DBTABLE_SUBSCRIPTS);
		$csv_array = array();
		$csv_array[] = array('Name', 'Email');

		foreach ($subs as $sub) :
			$csv_array[] = array(!empty($sub->name) ? $sub->email : '', !empty($sub->email) ? $sub->email : '');
		endforeach;
		$this->convertToCsv($csv_array, 'igniteup_subscribers_' . time() . '.csv', ',');
		exit();
	}

	public function createBccFile()
	{
		if (!isset($_GET['rockython_createbcc']) || !isset($_GET['sub']) || !is_super_admin())
			return;

		check_admin_referer('download-bcc');

		$textTitle = 'igniteup_subscribers_' . time() . '.txt';
		global $wpdb;
		$subs = $wpdb->get_results("SELECT * FROM " . CSCS_DBTABLE_PREFIX . CSCS_DBTABLE_SUBSCRIPTS);

		$bccArray = array();
		foreach ($subs as $reg) :
			$bccArray[] = $reg->name . ' <' . $reg->email . '>';
		endforeach;
		header('Content-type: text/plain; charset=utf-8');
		header('Content-Disposition: attachement; filename="' . $textTitle . '";');
		echo implode(", ", $bccArray);
		exit();
	}

	public function removeSubscribers()
	{
		if (!isset($_REQUEST['igniteup_action']) || $_REQUEST['igniteup_action'] !== 'trash' || !is_super_admin())
			return;

		check_admin_referer('delete-subscribers');

		global $wpdb;
		$subs = $wpdb->get_results("SELECT * FROM " . CSCS_DBTABLE_PREFIX . CSCS_DBTABLE_SUBSCRIPTS . ' ORDER BY id DESC');
		for ($i = 1; $i <= $subs[0]->id; $i++) {
			if (isset($_REQUEST['subscriber']) && in_array($i . '', $_REQUEST['subscriber']))
				$wpdb->delete(CSCS_DBTABLE_PREFIX . CSCS_DBTABLE_SUBSCRIPTS, array('id' => $i));
		}
	}

	public function load_languages()
	{
		load_plugin_textdomain(CSCS_TEXT_DOMAIN, false, dirname(plugin_basename(CSCS_FILE)) . '/localization/');
	}

	public function showWelcomeMessage()
	{
		if (defined('DOING_AJAX') && DOING_AJAX)
			return;

		// set options for showing welcome message.
		$prev_version = get_option(CSCS_GENEROPTION_PREFIX . 'version', '1.0');
		if (floatval(CSCS_CURRENT_VERSION) !== floatval($prev_version) && floatval(CSCS_CURRENT_VERSION) > floatval($prev_version)) {
			update_option(CSCS_GENEROPTION_PREFIX . 'version', '' . CSCS_CURRENT_VERSION);
			wp_redirect(admin_url('admin.php?page=cscs_options&section=help'));
			exit;
		}
		//-------------
	}

	public function headStatics()
	{
		/*
	 * Favicon
	 */
		$favicon_url = get_option(CSCS_GENEROPTION_PREFIX . 'favicon_url');
		if (!empty($favicon_url))
			echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';

		/*
	 * Custom script required by IgniteUp
	 */
		echo '<script type="text/javascript">igniteup_ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';

		/*
	 * Do action for template styles
	 */

		do_action('igniteup_styles_' . CSCS_DEFAULT_TEMPLATE);

		/*
	 * Custom CSS and Header scritps field
	 */
		$custom_css = get_option(CSCS_GENEROPTION_PREFIX . 'customcss', '');
		?>
			<style>
				#wp-admin-bar-igniteup_enabled {
					background-color: #C70000 !important;
				}

				#wp-admin-bar-igniteup_enabled:hover a {
					background: #A20A0A !important;
					color: #FFFFFF !important;
				}

				body::before {
					background: none !important;
				}

				<?php echo $custom_css;
						?>
			</style>
		<?php
			}

			/*
     * 
     * WP Head elements for older theme versions
     * 
     */

			public function wpHeadForOldThemes()
			{
				$favicon_url = get_option(CSCS_GENEROPTION_PREFIX . 'favicon_url');
				if (!empty($favicon_url))
					echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
				echo '<script type="text/javascript">igniteup_ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
			}

			/*
     * 
     * Publish footer scripts
     * 
     */

			public function footerScripts()
			{
				/*
	 * Do action for template scripts
	 */

				do_action('igniteup_scripts_' . CSCS_DEFAULT_TEMPLATE);
			}

			/*
     * 
     * Enable an admin notice if there are incompatible templates
     * 
     */

			public function noncompatible_theme_notice()
			{
				if (!CSAdminOptions::getTemplates(TRUE)) //Check if any non-compatible theme exists.
					return;
				?>
			<div class="error">
				<p><?php printf(__('There\'re themes not supporting current IgniteUp version (%1$s). To fix <a href="%2$s">Update IgniteUp</a> now!', CSCS_TEXT_DOMAIN), 'v' . CSCS_CURRENT_VERSION, admin_url('update-core.php')); ?></p>
			</div>
	<?php
		}

		/*
     * 
     * Help page subscription form action
     * 
     */

		public function adminSubscribe()
		{
			header('Content-type: application/json; charset=utf-8');
			$email = $_POST['admin_filled_email'];
			$error = FALSE;
			$message = '';
			if (empty($email)) {
				echo json_encode(array('error' => TRUE, 'message' => 'Please provide your email address!'));
				wp_die();
			}

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo json_encode(array('error' => TRUE, 'message' => 'Please enter a valid email address!'));
				wp_die();
			}

			$resp = wp_remote_post(
				'http://getigniteup.com/api/subscribe',
				array(
					'body' => array('ignitmailsubscr' => TRUE, 'ignitmailaddress' => $email),
					'timeout' => 20
				)
			);

			if (is_wp_error($resp)) {
				$error = TRUE;
				$message = 'Something went wrong. Please try again!';
			}

			$curr_user = get_current_user_id();
			if (!$error && !empty($curr_user)) {
				update_user_meta($curr_user, 'igniteup_admin_subscribed', '1');
			}

			echo json_encode(array('error' => $error, 'message' => $message));
			wp_die();
		}

		/*
     * 
     * Notify admin about a new subscriber
     * 
     */

		public function notifySubscriber($email, $name = NULL)
		{
			$cs_receive_email_addr = CSCS_GENEROPTION_PREFIX . 'receive_email_addr';
			$admin_email = get_bloginfo('admin_email');
			$to = get_option($cs_receive_email_addr, $admin_email);
			$email_subject = __("You have a new subscriber!", CSCS_TEXT_DOMAIN);
			$email_body = "<html><body>" .
				"<h4>" . __('You have a new subscriber to your website via IgniteUp subscribe form.', CSCS_TEXT_DOMAIN) . "</h4>" .
				"<p>" . __('Name:', CSCS_TEXT_DOMAIN) . " $name<br>" .
				__('Email:', CSCS_TEXT_DOMAIN) . " $email</p>" .
				"<p><a href='" . admin_url('admin.php?page=cscs_subscribers') . "'>View all subscribers</a></p>" .
				"</body></html>";

			$status = wp_mail($to, $email_subject, $email_body, array("MIME-Version: 1.0", "Content-Type: text/html; charset=UTF-8"));
			return $status;
		}

		/*
     * 
     * Igniteup_head action
     * 
     */

		public static function perfomIgniteUpHead()
		{
			do_action('igniteup_head');
		}

		/*
     * 
     * Igniteup_footer action
     * 
     */

		public static function perfomIgniteUpFooter()
		{
			do_action('igniteup_footer');
		}

		/*
     * 
     * Read theme options
     * 
     */

		public static function igniteUpGetOption($key, $default = NULL)
		{
			global $the_cs_template_options;
			$value = !empty($the_cs_template_options[$key]) ? $the_cs_template_options[$key] : $default;
			return $value;
		}

		/**
		 * 
		 * Clean all custom options saved in the database on plugin deactivation
		 * 
		 */

		public function deactivationCleanp()
		{ 
			$temps = CSAdminOptions::getTemplates();
			foreach($temps as $temp){
				foreach($temp['options'] as $key => $opt){
					delete_option(CSCS_TEMPLATEOPTION_PREFIX . $temp['folder_name'] . "_" . $key);
				}
			}

			foreach(CSAdminOptions::$gener_options as $opt){
				delete_option(CSCS_GENEROPTION_PREFIX . $opt);
			}

			foreach(CSAdminOptions::$common_options as $opt){
				delete_option(CSCS_GENEROPTION_PREFIX . $opt);
			}


		}
	}

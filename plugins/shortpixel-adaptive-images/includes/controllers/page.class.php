<?php

	namespace ShortPixel\AI;

	use ShortPixel\AI\Notice\Constants;
	use ShortPixel\AI\Options\Option;
	use ShortPixel\AI\Options\Category;
	use ShortPixel\AI\Options\Collection;

	class Page {
		/**
		 * View folder
		 * @var string
		 */
		const VIEWS_DIR = SHORTPIXEL_AI_PLUGIN_DIR . '/includes/views';

		/**
		 * Available page names
		 * @var array
		 */
		const NAMES = [
			'settings'    => 'shortpixel-ai-settings',
			'on-boarding' => 'shortpixel-ai-on-boarding',
		];

		/**
		 * @var \ShortPixel\AI\Page $instance
		 */
		private static $instance;

		/**
		 * @var \ShortPixelAI $ctrl AI Controller
		 */
		protected $ctrl;

		/**
		 * @var array $data AI plugin data
		 */
		protected $data;

		/**
		 * @var Collection|Category|Option
		 */
		protected $options;

		/**
		 * @var \ShortPixel\AI\Notice\Constants
		 */
		protected $noticeConstants;

		protected $domain_status = false;
		protected $domain_usage;

		/**
		 * Single ton implementation
		 *
		 * @param \ShortPixelAI $controller
		 *
		 * @return \ShortPixel\AI\Page
		 */
		public static function _( $controller ) {
			return self::$instance instanceof self ? self::$instance : new self( $controller );
		}

		/**
		 * Method verifies does specified page is user's current page
		 *
		 * @param string $page
		 *
		 * @return bool
		 */
		public static function isCurrent( $page ) {
			if ( !in_array( $page, array_keys( self::NAMES ) ) || !function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$screen = get_current_screen();

			return $screen && strpos( $screen->id, self::NAMES[ $page ] ) !== false;
		}

		/**
		 * Global init
		 */
		public function globalInit() {
			// add things here :)
		}

		/**
		 * Admin init
		 */
		public function adminInit() {
			$this->data = get_plugin_data( SHORTPIXEL_AI_PLUGIN_FILE );

			$this->checkForWizardRedirect();
		}

		/**
		 * Admin footer
		 */
		public function adminFooter() {
			if ( self::isCurrent( 'settings' ) ) {
				// Commented because of WordPress Plugins Reviewing team
				// echo '<div class="shortpixel-ai-beacon"></div>';
			}
		}

		/**
		 * Front-end styles & scripts for pages
		 */
		public function enqueueScripts() {
			$min    = ( !!SHORTPIXEL_AI_DEBUG ? '' : '.min' );
			$styles = [];

			$styles[ 'admin' ][ 'file' ]    = 'assets/css/admin' . $min . '.css';
			$styles[ 'admin' ][ 'url' ]     = $this->ctrl->plugin_url . $styles[ 'admin' ][ 'file' ];
			$styles[ 'admin' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->ctrl->plugin_dir . $styles[ 'admin' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

			if ( \ShortPixelAI::userCan( 'manage_options' ) ) {
				wp_enqueue_style( 'spai-admin-styles', $styles[ 'admin' ][ 'url' ], [], $styles[ 'admin' ][ 'version' ] );
			}
		}

		/**
		 * Admin styles & scripts for pages
		 */
		public function enqueueAdminScripts() {
			$min     = ( !!SHORTPIXEL_AI_DEBUG ? '' : '.min' );
			$scripts = [];

			$scripts[ 'settings' ][ 'file' ]    = 'assets/js/pages/settings' . $min . '.js';
			$scripts[ 'settings' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->ctrl->plugin_dir . $scripts[ 'settings' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

			$scripts[ 'on-boarding' ][ 'file' ]    = 'assets/js/pages/on-boarding' . $min . '.js';
			$scripts[ 'on-boarding' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->ctrl->plugin_dir . $scripts[ 'settings' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

			$scripts[ 'chart.js' ][ 'file' ]    = 'assets/js/libs/chart' . $min . '.js';
			$scripts[ 'chart.js' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->ctrl->plugin_dir . $scripts[ 'chart.js' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

			$scripts[ 'beacon' ][ 'file' ]    = 'assets/js/beacon' . $min . '.js';
			$scripts[ 'beacon' ][ 'version' ] = !!SHORTPIXEL_AI_DEBUG ? hash_file( 'crc32', $this->ctrl->plugin_dir . $scripts[ 'beacon' ][ 'file' ] ) : SHORTPIXEL_AI_VERSION;

			if ( self::isCurrent( 'settings' ) ) {
				$spai_key      = Options::_()->settings_general_apiKey;
                $this->domain_usage  = empty( $spai_key ) ? false : $this->ctrl->get_cdn_domain_usage( null, $spai_key );
				$this->domain_status = $this->ctrl->get_domain_status( true );

				// Registering scripts
				wp_register_script( 'chart.js', $this->ctrl->plugin_url . $scripts[ 'chart.js' ][ 'file' ], [], $scripts[ 'chart.js' ][ 'version' ], true );
				wp_register_script( 'spai-settings', $this->ctrl->plugin_url . $scripts[ 'settings' ][ 'file' ], [ 'jquery', 'chart.js' ], $scripts[ 'settings' ][ 'version' ], true );

				wp_localize_script( 'spai-settings', 'exclusionsL10n', [
					'add'      => __( 'Add', 'shortpixel-adaptive-images' ),
					'save'     => __( 'Save', 'shortpixel-adaptive-images' ),
					'messages' => [
						'selectors' => [
							'alreadyExists' => __( 'Selector(s) already present.', 'shortpixel-adaptive-images' ),
							'invalid'       => __( 'This doesn\'t look like a valid selector. <a href="https://vegibit.com/css-selectors-tutorial/" target="_blank">How to write a selector?</a>', 'shortpixel-adaptive-images' ),
						],
					],
				] );

				if ( @$this->domain_status->HasAccount && $this->domain_usage ) {
					wp_localize_script( 'spai-settings', 'statusBox', [
						'chart' => [
							'titles'      => [
								'cdn'     => __( 'CDN (Mb)', 'shortpixel-adaptive-images' ),
								'credits' => __( 'Credits (pcs)', 'shortpixel-adaptive-images' ),
							],
							'colors'      => [ 'cdn' => 'rgb(238, 44, 36)', 'credits' => 'rgb(75, 192, 192)' ],
							'backgrounds' => [ 'cdn' => 'rgba(238, 44, 36, 0.2)', 'credits' => 'rgba(75, 192, 192, 0.2)' ],
							'cdn'         => $this->domain_usage->cdn->chart,
							'credits'     => $this->domain_usage->credits->chart,
						],
					] );
				}

				/*
				 * Commented because of WordPress Plugins Reviewing team
				wp_register_script( 'spai-beacon', $this->ctrl->plugin_url . $scripts[ 'beacon' ][ 'file' ], [], $scripts[ 'beacon' ][ 'version' ], true );
				wp_register_script( 'spai-quriobot', 'https://quriobot.com/qb/widget/KoPqxmzqzjbg5eNl/5doqer3ZpnmR6ZL0?init=explicit&onScriptLoad=quriobotLoaded', null, null, true );
				*/

				$current_user = wp_get_current_user();
				$name_pieces  = [];

				if ( $current_user->first_name ) {
					$name_pieces[] = $current_user->first_name;
				}

				if ( $current_user->last_name ) {
					$name_pieces[] = $current_user->last_name;
				}

				/*
				 * Commented because of WordPress Plugins Reviewing team
				wp_localize_script( 'spai-beacon', 'beaconConstants', [
					'initID'      => 'e41d21e0-f3c4-4399-bcfe-358e59a860de',
					'identity'    => [
						'name'   => empty( $name_pieces ) ? $current_user->display_name : implode( ' ', $name_pieces ),
						'email'  => $current_user->user_email,
						'apiKey' => (string) Options::_()->settings_general_apiKey,
					],
					'suggestions' => [
						'compression' => [
							'5ce80e440428632d9eebe87c',
							'5bd2ef9c04286356f0a51b33',
							'5c310ddd2c7d3a31944fb5d5',
							'5a5de1c2042863193801047c',
							'5cf300f304286333a2640ec8',
							'5ec39099042863474d1afa97',
							'5cf63ef92c7d3a38371311ee',
							'5d15c25a04286305cb87d4d6',
							'5d000d1004286318cac423e2',
							'5dd2fa602c7d3a7e9ae41782',
						],
						'behaviour'   => [
							'5c6a4df9042863543ccd1b0c',
							'5c655af6042863543cccfc5d',
							'5e2eac8b2c7d3a7e9ae6c467',
							'5e1af9f42c7d3a7e9ae61359',
							'5cefd3800428637b2ee7d945',
							'5ce80e440428632d9eebe87c',
							'5bd2ef9c04286356f0a51b33',
							'5c310ddd2c7d3a31944fb5d5',
							'5a5de1c2042863193801047c',
							'5cf300f304286333a2640ec8',
						],
						'areas'       => [
							'5db6a05804286364bc90f5ce',
							'5c4eb96b2c7d3a66e32db163',
							'5de7c73004286364bc927a3d',
							'5bb5e1f32c7d3a04dd5b46de',
							'5ce80e440428632d9eebe87c',
							'5bd2ef9c04286356f0a51b33',
							'5c310ddd2c7d3a31944fb5d5',
							'5a5de1c2042863193801047c',
							'5cf300f304286333a2640ec8',
						],
						'exclusions'  => [
							'5ce57a000428634b8559942a',
							'5d1b73d604286305cb8811d4',
							'5c310ddd2c7d3a31944fb5d5',
							'5a5de1c2042863193801047c',
							'5cf300f304286333a2640ec8',
							'5ec39099042863474d1afa97',
							'5cf63ef92c7d3a38371311ee',
							'5d15c25a04286305cb87d4d6',
							'5d000d1004286318cac423e2',
							'5dd2fa602c7d3a7e9ae41782',
						],
					],
				] );
				*/

				// Enqueueing scripts
				wp_enqueue_script( 'chart.js' );
				/*
				 * Commented because of WordPress Plugins Reviewing team
				wp_enqueue_script( 'spai-beacon' );
				wp_enqueue_script( 'spai-quriobot' );
				*/
				wp_enqueue_script( 'spai-settings' );
			}

			if ( self::isCurrent( 'on-boarding' ) ) {
				// Registering scripts
				wp_register_script( 'spai-on-boarding', $this->ctrl->plugin_url . $scripts[ 'on-boarding' ][ 'file' ], [ 'jquery' ], $scripts[ 'on-boarding' ][ 'version' ] );

				// Enqueueing scripts
				wp_enqueue_script( 'spai-on-boarding' );
			}
		}

		/**
		 * Admin menu pages hook
		 */
		public function initAdminPages() {
			add_submenu_page(
				'admin.php',
				'ShortPixel AI On-Boarding',
				'ShortPixel AI On-Boarding',
				'manage_options',
				self::NAMES[ 'on-boarding' ],
				function() {
					$this->render( 'on-boarding.tpl.php' );
				} );

			add_submenu_page(
				'options-general.php',
				'ShortPixel AI',
				'ShortPixel AI',
				'manage_options',
				self::NAMES[ 'settings' ],
				function() {
					$this->render( 'settings.tpl.php' );
				} );
		}

		/**
		 * @param \WP_Admin_Bar $admin_bar
		 */
		public function initAdminBarItems( $admin_bar ) {
			if ( !!$this->ctrl->options->pages_onBoarding_displayAllowed && !$this->ctrl->options->pages_onBoarding_hasBeenPassed ) {
				if ( !is_admin() || !\ShortPixelAI::userCan( 'manage_options' ) ) {
					return;
				}

				$admin_bar->add_node( [
					'id'     => self::NAMES[ 'on-boarding' ],
					'parent' => null,
					'group'  => null,
					'title'  => '<span class="ab-icon"></span><span class="ab-label">' . __( 'ShortPixel AI Setup', 'shortpixel-adaptive-images' ) . '</span>',
					'href'   => admin_url( 'admin.php?page=' . self::NAMES[ 'on-boarding' ] ),
				] );
			}
		}

		public function render( $view ) {
			if ( file_exists( self::VIEWS_DIR . DIRECTORY_SEPARATOR . $view ) ) {
				echo '<div class="wrap">';
                    require_once( self::VIEWS_DIR . DIRECTORY_SEPARATOR . $view );
				echo '</div>';
			}
		}

		/**
		 * Page constructor.
		 *
		 * @param \ShortPixelAI $controller
		 */
		private function __construct( $controller ) {
			if ( !isset( self::$instance ) || !self::$instance instanceof self ) {
				self::$instance = $this;
			}

			$this->ctrl = $controller;

			$this->noticeConstants = Constants::_( $controller );

			$this->hooks();
		}

		/**
		 * Method adds hooks
		 */
		private function hooks() {
			add_action( 'init', [ $this, 'globalInit' ] );
			add_action( 'admin_footer', [ $this, 'adminFooter' ] );
			add_action( 'admin_init', [ $this, 'adminInit' ] );
			add_action( 'admin_menu', [ $this, 'initAdminPages' ], 10 );
			add_action( 'admin_bar_menu', [ $this, 'initAdminBarItems' ], 500 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminScripts' ] );
			add_action( 'wp_ajax_shortpixel_ai_handle_page_action', [ 'ShortPixel\AI\Page\Actions', 'handle' ] );

			if ( !is_admin() ) {
				add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
			}
		}

		private function checkForWizardRedirect() {
			if ( !empty( $this->ctrl->options->pages_onBoarding_redirect->allowed ) ) {
				$redirect_option          = $this->ctrl->options->get( 'redirect', [ 'pages', 'on_boarding' ], Option::_() );
				$redirect_option->allowed = false;

				$this->ctrl->options->set( $redirect_option, 'redirect', [ 'pages', 'on_boarding' ] );

				if ( !!$this->ctrl->options->flags_all_firstInstall && !$this->ctrl->options->pages_onBoarding_displayAllowed && empty( $this->ctrl->options->pages_onBoarding_step ) && !$this->ctrl->options->pages_onBoarding_hasBeenPassed ) {
					// Setting the flag that plugin has been installed
					$this->ctrl->options->flags_all_firstInstall = false;
					// Setting the flag that display of On-Boarding Wizard is allowed
					$this->ctrl->options->pages_onBoarding_displayAllowed = true;

					wp_redirect( admin_url( 'admin.php?page=' . self::NAMES[ 'on-boarding' ] ) );
					die;
				}
			}
		}
	}
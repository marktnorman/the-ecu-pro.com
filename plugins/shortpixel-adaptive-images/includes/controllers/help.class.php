<?php

	namespace ShortPixel\AI;

	class Help {
		/**
		 * Instance of the class (singleton)
		 * @var \ShortPixel\AI\Help $instance
		 */
		private static $instance;

		public static function _() {
			return self::$instance instanceof self ? self::$instance : new self;
		}

		public function addTabs() {
			$screen = get_current_screen();

			if ( !$screen || !$this->isAllowedScreen( $screen ) ) {
				return;
			}

			$screen->add_help_tab( $this->getTab( 'support' ) );

			if ( !!Options::_()->get( 'display_allowed', [ 'pages', 'on_boarding' ] ) ) {
				$screen->add_help_tab( $this->getTab( 'on-boarding' ) );
			}

			$screen->set_help_sidebar(
				'<p><strong>' . __( 'For more information:', 'shortpixel-adaptive-images' ) . '</strong></p>' .
				'<p><a href="https://help.shortpixel.com/category/307-shortpixel-adaptive-images" target="_blank">' . __( 'Documentation & Help', 'shortpixel-adaptive-images' ) . '</a></p>' .
				'<p><a href="https://shortpixel.com/contact/" target="_blank">' . __( 'Support', 'shortpixel-adaptive-images' ) . '</a></p>'
			);
		}

		/**
		 * Help real constructor.
		 */
		private function __construct() {
			$this->hooks();
		}

		private function hooks() {
			add_action( 'current_screen', [ $this, 'addTabs' ], 50 );
			add_action( 'wp_ajax_shortpixel_ai_handle_help_action', [ 'ShortPixel\AI\Help\Actions', 'handle' ] );
		}

		private function getTab( $id ) {
			switch ( $id ) {
				case 'support':
					return [
						'id'      => 'shortpixel-ai-' . $id . '-tab',
						'title'   => __( 'Help & Support', 'shortpixel-adaptive-images' ),
						'content' =>
							'<h2>' . __( 'Help & Support', 'shortpixel-adaptive-images' ) . '</h2>' .
							'<p>' . sprintf( __( 'Should you need help understanding or using ShortPixel Adaptive Images, <a href="%s" target="_blank">please read our documentation</a>. You will find all kinds of resources including snippets, tutorials and much more.', 'shortpixel-adaptive-images' ), 'https://help.shortpixel.com/category/307-shortpixel-adaptive-images' ) . '</p>' .
							'<p>' . sprintf( __( 'For help with ShortPixel Adaptive Images plugin, <a href="%s">open a support request at ShortPixel.com</a>.', 'shortpixel-adaptive-images' ), 'https://shortpixel.com/contact' ) . '</p>' .
							'<p>' . sprintf( __( 'Before asking for help to ShortPixel support team, we strongly recommend to <a href="%s" target="_blank">read articles on help.shortpixel.com</a>.', 'shortpixel-adaptive-images' ), 'https://help.shortpixel.com/category/307-shortpixel-adaptive-images' ) . '</p>' .
							'<p><a href="https://help.shortpixel.com/category/307-shortpixel-adaptive-images" class="button button-primary" target="_blank">' . __( 'Documentation & Help', 'shortpixel-adaptive-images' ) . '</a> <a href="https://shortpixel.com/contact" class="button button-secondary" target="_blank">' . __( 'ShortPixel Support', 'shortpixel-adaptive-images' ) . '</a></p>',
					];
				case 'on-boarding':
					return [
						'id'      => 'shortpixel-ai-' . $id . '-tab',
						'title'   => __( 'Setup Wizard', 'shortpixel-adaptive-images' ),
						'content' =>
							'<h2>' . __( 'Setup Wizard', 'shortpixel-adaptive-images' ) . '</h2>' .
							'<p>' . __( 'If you need to access the setup wizard again, please click on the button below.', 'shortpixel-adaptive-images' ) . '</p>' .
							'<p><button class="button button-primary" data-action="enable on boarding" data-plugin="shortpixel-adaptive-images">' . __( 'Setup Wizard', 'shortpixel-adaptive-images' ) . '</button></p>',
					];
			}

			return [];
		}

		private function isAllowedScreen( $screen ) {
			$screens = $this->getScreens();

			return !empty( array_filter( $screens, function( $id ) use ( $screen ) {
				return strpos( $screen->id, $id ) !== false;
			} ) );
		}

		private function getScreens() {
			$current_pages    = Page::NAMES;
			$disallowed_pages = [ 'on-boarding' ];

			$disallowed_pages = apply_filters( 'shortpixel/ai/help/disallowedPages', $disallowed_pages );

			return array_values( array_filter( $current_pages, function( $page_key ) use ( $disallowed_pages ) {
				return !in_array( $page_key, $disallowed_pages );
			}, ARRAY_FILTER_USE_KEY ) );
		}
	}
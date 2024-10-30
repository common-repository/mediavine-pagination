<?php
if ( ! class_exists( 'Validator' ) ) {
	require_once( 'includes/validator.php' );
}
/*
Plugin Name: Mediavine Pagination
Plugin URI: http://mediavine.com
Description: Allows for easy styling of pagination blocks.
Version: 1.0
Author: mediavine
Author URI: http://mediavine.com
License: GPL3
*/

if ( ! class_exists( 'Mediavine_Pagination' ) ) {
	class Mediavine_Pagination {
		// Constants & Settings
		public $SETTING_PREFIX = 'mediavinePagSetting_';
		public $SETTING_LIST = array(
			'teaser_text'             => 'string',
			'teaser_font_style'       => 'string',
			'teaser_font_mod'         => 'string',
			'teaser_font_color'       => 'color',
			'button_font_style'       => 'string',
			'button_font_mod'         => 'string',
			'button_font_color'       => 'color',
			'button_background_color' => 'color',
			'button_border_style'     => 'string',
			'button_border_color'     => 'color',
			'display_setting'         => 'string',
			'display_style'           => 'string',
			'page_number_text'        => 'string',
			'next_button_text'        => 'string'
		);
		public $SETTING_DEFAULTS = array(
			'teaser_text'             => "There's more {title} ahead! Just click \"Next\" below!",
			'teaser_font_style'       => 'inherit',
			'teaser_font_mod'         => '',
			'teaser_font_color'       => '#333',
			'button_font_style'       => 'Helvetica, Arial, Georgia, sans-serif',
			'button_font_mod'         => '',
			'button_font_color'       => '#fff',
			'button_background_color' => '#333',
			'button_border_style'     => 'solid',
			'button_border_color'     => '#333',
			'display_setting'         => 'next_and_number',
			'display_style'           => 'large',
			'page_number_text'        => 'Page {pagenumber}',
			'next_button_text'        => 'Next'
		);

		public function __construct() {
			$this->init_plugin_filters();
			$this->init_views();
		}

		/**
		 * Adds hooks for admin pages
		 */
		public function init_views() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
		}

		/**
		 * Called on admin pages
		 */
		public function admin_init() {
			$this->init_settings();
		}

		public function admin_print_footer_scripts() {
			if ( wp_script_is( 'quicktags' ) ) {
				echo '<script type="text/javascript">';
				require( 'scripts/addhtmleditorpagebutton.js' );
				echo '</script>';
			}
		}

		/**
		 * registers settings within the WP admin
		 */
		public function init_settings() {
			$group = $this->SETTING_PREFIX;

			foreach ( $this->SETTING_LIST as $key => $value ) {
				register_setting( $group, $group . $key, function ( $input ) use ( $group, $key, $value ) {
					$previous = get_option( $group . $key );

					$result = Validator::validate( $input, $value );
					if ( FALSE === $result && $value !== 'bool' ) {
						// Error found
						add_settings_error( $group, $key, 'Setting ' . $key . ' not a valid value: ' . $value, 'error' );

						return $previous;
					} else if ( '' === $result && isset( $previous ) ) {
						return $previous;
					}

					return $result;
				} );
			}
		}

		/**
		 * Adds link to settings page in the menu
		 */
		public function add_menu() {
			add_options_page( 'Mediavine Pagination Settings', 'Mediavine Pagination', 'manage_options', 'mediavine_pagination_pagination_settings', array(
				$this,
				'render_settings_page'
			) );
		}

		/**
		 * Initializes filters used by the plugin
		 * @return bool
		 */
		public function init_plugin_filters() {
			add_filter( 'wp_link_pages_args', array( $this, 'override_page_link_args' ) );

			add_filter( 'the_content', array( $this, 'add_pagination' ) );
			add_filter( 'mce_buttons', array( $this, 'enable_pagebreak' ) );

			return TRUE;
		}

		// Settings Accessors & Convenience Methods
		/**
		 * Accesses settings safely.
		 * If the setting is not set, the setting is initialized with the default value as defined in SETTING_DEFAULTS
		 *
		 * @param $name - Name of the setting
		 * @param $newValue (optional) - The value to set the setting to
		 *
		 * @return mixed
		 */
		public function option( $name, $newValue = null ) {
			if ( isset( $newValue ) ) {
				update_option( $this->SETTING_PREFIX . $name, $newValue );
			}

			$opt = get_option( $this->SETTING_PREFIX . $name );

			if ( FALSE === $opt && 'bool' !== $this->SETTING_LIST[ $name ] ) {
				return $this->option( $name, $this->SETTING_DEFAULTS[ $name ] );
			}

			return $opt;
		}

		/**
		 * Returns an array with all settings
		 * @return mixed
		 */
		public function get_settings() {
			$self          = $this;
			$base_settings = array_reduce( array_keys( $this->SETTING_LIST ), function ( $carry, $item ) use ( $self ) {
				$carry[ $item ] = $self->option( $item );

				return $carry;
			}, array() );

			return $this->add_computed_settings( $base_settings );
		}

		public function add_computed_settings( $settings ) {
			switch ( $settings[ 'display_style' ] ) {
				case 'small':
					$settings[ 'button_border_size' ] = '1';
					$settings[ 'button_font_size' ]   = '16';
					$settings[ 'teaser_font_size' ]   = '14';
					break;
				case 'medium':
					$settings[ 'button_border_size' ] = '2';
					$settings[ 'button_font_size' ]   = '20';
					$settings[ 'teaser_font_size' ]   = '18';
					break;
				case 'large':
					$settings[ 'button_border_size' ] = '3';
					$settings[ 'button_font_size' ]   = '24';
					$settings[ 'teaser_font_size' ]   = '24';
					break;
			}

			return $settings;
		}

		/**
		 * Returns the formatted string to be used by wordpress in page number display
		 * @return mixed
		 */
		public function get_page_button_format() {
			$raw_opt = $this->option( 'page_number_text' );

			return str_replace( '{pagenumber}', '%', $raw_opt );
		}

		/**
		 * Returns a formatted string to be used in the article teaser text
		 * @return mixed
		 */
		public function get_teaser_format() {
			$raw_opt = $this->option( 'teaser_text' );

			return str_replace( '{title}', get_the_title(), $raw_opt );
		}

		// HTML/UI
		function enable_pagebreak( $buttons ) {
			$buttons[] = 'wp_page';

			return $buttons;
		}

		/**
		 * Renders the settings management ui
		 */
		public function render_settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}

			wp_enqueue_style( 'mediavine_pagination_stylesheet_settings', plugins_url( 'styles/settings.css', __FILE__ ) );
			wp_enqueue_style( 'mediavine_pagination_stylesheet_grid', plugins_url( 'styles/flexboxgrid.min.css', __FILE__ ) );
			wp_enqueue_style( 'select2', plugins_url( 'styles/select2.min.css', __FILE__ ) );
			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( 'backbone' );
			wp_enqueue_script( 'jscolor', plugins_url( 'scripts/jscolor.min.js', __FILE__ ) );
			wp_enqueue_script( 'select2', plugins_url( 'scripts/select2.full.min.js', __FILE__ ) );

			wp_enqueue_script( 'mediavine_pagination_model_settings', plugins_url( 'ui/models/settings_model.js', __FILE__ ) );
			wp_localize_script( 'mediavine_pagination_model_settings', 'WPModel', $this->get_settings() );

			wp_enqueue_script( 'mediavine_pagination_ui_colorpicker', plugins_url( 'ui/components/mediavine-colorpicker.js', __FILE__ ) );
			wp_enqueue_script( 'polyfir_view', plugins_url( 'ui/components/polyview.js', __FILE__ ) );
			wp_enqueue_script( 'mediavine_pagination_ui_dropdown', plugins_url( 'ui/components/mediavine-dropdown.js', __FILE__ ) );
			wp_enqueue_script( 'mediavine_pagination_ui_fontpicker', plugins_url( 'ui/components/mediavine-fontpicker.js', __FILE__ ) );
			wp_enqueue_script( 'mediavine_pagination_ui_settings', plugins_url( 'ui/components/mediavine-settings.js', __FILE__ ) );

			include( sprintf( "%s/views/settings.php", dirname( __FILE__ ) ) );
		}

		/**
		 * Hooks into wp_link_pages_args to modify the page links generated by wordpress based on
		 * the settings provided by the blog owner.
		 *
		 * @param $args
		 *
		 * @return $args [
		 */
		public function override_page_link_args( $args ) {
			global $page, $numpages, $more;

			if ( ! is_single() && ! is_page() ) {
				return $args;
			}

			$display_option = $this->option( 'display_setting' );

			if ( 'next' !== $display_option ) {
				// Since the next button is easy to emulate, allow WP to do heavy lifting on numbers for us
				$args[ 'next_or_number' ] = 'number';
			} else {
				$args[ 'next_or_number' ] = 'next';
			}

			$args[ 'before' ] = $this->get_div_wrap_open();

			// Page list format
			$args[ 'pagelink' ] = $this->get_page_button_format();

			// Set display for "Next" button
			$args[ 'nextpagelink' ] = $this->option( 'next_button_text' );

			if ( $more ) {
				if ( $page < $numpages ) {
					$teaser_text = $this->option( 'teaser_text' );
					if ( ! empty( $teaser_text ) ) {
						$args[ 'before' ] .= $this->get_teaser_text();
					}

					if ( 'number' !== $display_option ) {
						$args[ 'before' ] .= $this->get_next_link_html( $args, $page );
					}
				}
			}

			$args[ 'after' ] = $this->get_div_wrap_close();

			return $args;
		}

		/**
		 * Returns the outer wrapper for the plugin display code
		 * @return string
		 */
		public function get_div_wrap_open() {
			return '<div class="mediavine-pagination">';
		}

		/**
		 * Closes the wrapper for plugin display
		 * @return string
		 */
		public function get_div_wrap_close() {
			return '</div>';
		}

		/**
		 * Returns the teaser text in its html formatted string
		 * @return string
		 */
		public function get_teaser_text() {
			return sprintf( '<h4 class="teaser-text">%s</h4>', $this->get_teaser_format() );
		}

		/**
		 * Provides a link to the previous page if called from The Loop
		 *
		 * @param $args
		 * @param int $current_page
		 *
		 * @return string
		 */
		public function get_next_link_html( $args, $current_page = 1 ) {
			// Unfortunately, wordpress doesn't provide a public method for us to use so we'll need this one for now
			return '<div class="nextpage">'
			       . _wp_link_page( $current_page + 1 )
			       . $args[ 'link_before' ]
			       . $args[ 'nextpagelink' ]
			       . $args[ 'link_after' ]
			       . '</a>'
			       . '</div>';
		}

		/**
		 * Adds styles to the content of the page if pagination is being used
		 *
		 * @return string
		 */
		public function add_pagination( $content ) {
			global $page, $numpages;

			if ( ! is_single() && ! is_page() ) {
				return $content;
			}
			$styles = $this->get_settings();

			include( sprintf( "%s/views/page-nav-style.php", dirname( __FILE__ ) ) );
			// $style_block set within included php file as style tag
			$content = $content . $style_block;

			return $content;
		}

		public function colorIsDark( $hex ) {
			$hex = str_replace( '#', '', $hex );
			if ( strlen( $hex ) == 3 ) {
				$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
			}

			// Split into three parts: R, G and B
			$color_parts = str_split( $hex, 2 );
			$r           = hexdec( $color_parts[ 0 ] );
			$g           = hexdec( $color_parts[ 1 ] );
			$b           = hexdec( $color_parts[ 2 ] );

			$contrast = sqrt(
				$r * $r * .241 +
				$g * $g * .691 +
				$b * $b * .068
			);

			return ( $contrast < 130 );
		}

		/**
		 * Adjust the brightness of the given $hex color by the number of $steps
		 *
		 * @param $hex
		 * @param $steps
		 *
		 * @return string
		 */
		public function adjustBrightness( $hex, $steps ) {
			// Steps should be between -255 and 255. Negative = darker, positive = lighter
			$steps = max( - 255, min( 255, $steps ) );

			// Normalize into a six character long hex string
			$hex = str_replace( '#', '', $hex );
			if ( strlen( $hex ) == 3 ) {
				$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
			}

			// Split into three parts: R, G and B
			$color_parts = str_split( $hex, 2 );
			$return      = '#';

			foreach ( $color_parts as $color ) {
				$color = hexdec( $color ); // Convert to decimal
				$color = max( 0, min( 255, $color + $steps ) ); // Adjust color
				$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
			}

			return $return;
		}

		/**
		 * Parses the given font mod string and turns it into css
		 *
		 * @param $mods
		 *
		 * @return string
		 */
		public function parse_font_mod( $mods ) {
			$splitted = explode( ',', $mods );
			$css      = array();
			foreach ( $splitted as $mod ) {
				if ( 'bold' === $mod ) {
					array_push( $css, 'font-weight: bold;' );
				} else if ( 'italic' === $mod ) {
					array_push( $css, 'font-style: italic;' );
				} else if ( 'underline' === $mod ) {
					array_push( $css, 'text-decoration: underline;' );
				}
			}

			// Two tabs to match the style tag below
			return implode( "\n\t\t", $css );
		}
	}
}

if ( class_exists( 'Mediavine_Pagination' ) ) {
	// Installation and uninstallation hooks
	register_activation_hook( __FILE__, array( 'Mediavine_Pagination', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Mediavine_Pagination', 'deactivate' ) );

	// instantiate the plugin class
	$mediavine_pagination = new Mediavine_Pagination();
}
?>

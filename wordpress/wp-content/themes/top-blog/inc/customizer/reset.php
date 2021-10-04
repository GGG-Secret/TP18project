<?php
/**
 * Reset Theme Options
 *
 * @package Top Blog
 */

if ( ! class_exists( 'Top_Blog_Customizer_Reset' ) ) {
	/**
	 * Adds Reset button to customizer
	 */
	final class Top_Blog_Customizer_Reset {
		/**
		 * @var Top_Blog_Customizer_Reset
		 */
		private static $instance = null;

		/**
		 * @var WP_Customize_Manager
		 */
		private $wp_customize;

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			add_action( 'customize_controls_print_scripts', array( $this, 'customize_controls_print_scripts' ) );
			add_action( 'wp_ajax_customizer_reset', array( $this, 'ajax_customizer_reset' ) );
			add_action( 'customize_register', array( $this, 'customize_register' ) );
		}

		public function customize_controls_print_scripts() {
			$min  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'top-blog-customizer-reset', get_template_directory_uri() . '/js/customizer-reset' . $min . '.js', array( 'customize-preview' ), top_blog_get_file_mod_date( '/js/customizer-reset' . $min . '.js' ), true );

			wp_localize_script( 'top-blog-customizer-reset', '_topBlogCustomizerReset', array(
				'reset'        => esc_html__( 'Reset', 'top-blog' ),
				'confirm'      => esc_html__( "Caution! This action is irreversible. Press OK to continue.", 'top-blog' ),
				'nonce'        => array(
					'reset' => wp_create_nonce( 'top-blog-customizer-reset' ),
				),
				'resetSection' => esc_html__( 'Reset Section', 'top-blog' ),
				'confirmSection' => esc_html__( "Caution! This action is irreversible. Press OK to reset the section.", 'top-blog' ),
			) );
		}

		/**
		 * Store a reference to `WP_Customize_Manager` instance
		 *
		 * @param $wp_customize
		 */
		public function customize_register( $wp_customize ) {
			$this->wp_customize = $wp_customize;
		}

		public function ajax_customizer_reset() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'top-blog-customizer-reset', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			if ( isset( $_POST['section'] ) && 'reset-all' === $_POST['section'] ) {
				$this->reset_customizer();
			}

			if ( isset( $_POST['section'] ) && 'fonts' === $_POST['section'] ) {
				$reset_options = Top_Blog_Theme_Options::get_font_options();

				foreach( $reset_options as $key => $value ) {
					remove_theme_mod( $key );
				}
			}

			if ( isset( $_POST['section'] ) && 'colors' === $_POST['section'] ) {
				$reset_options = Top_Blog_Basic_Colors_Options::get_colors();

				foreach( $reset_options as $key => $value ) {
					remove_theme_mod( $key );
				}
			}

			wp_send_json_success();
		}

		public function reset_customizer() {
			remove_theme_mods();
		}
	}
}

Top_Blog_Customizer_Reset::get_instance();

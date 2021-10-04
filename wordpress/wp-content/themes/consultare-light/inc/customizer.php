<?php
/**
 * Consultare Theme Customizer
 *
 * @package Consultare
 */

/**
 * Main Class for customizer
 */
class Consultare_Light_Customizer {
	public function __construct() {
		// Register Custozier Options.
		add_action( 'customize_register', array( $this, 'register_options' ), 5 );
		
		// Add default options.
		add_filter( 'consultare_customizer_defaults', array( $this, 'add_defaults' ), 99 );
	}

	/**
	 * Add options to defaults
	 */
	public function add_defaults( $default_options ) {
		$defaults = array(
			'consultare_header_style' => 'header-two',
		);

		$updated_defaults = wp_parse_args( $defaults, $default_options );

		return $updated_defaults;
	}

	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 * Other basic stuff for customizer initialization.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function register_options( $wp_customize ) {
		Consultare_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Consultare_Image_Radio_Button_Custom_Control',
				'settings'          => 'consultare_header_style',
				'sanitize_callback' => 'consultare_radio_sanitization',
				'label'             => esc_html__( 'Header Style', 'consultare-light' ),
				'section'           => 'consultare_header_options',
				'choices'           => array(
					'header-one'   => array(
						'image' => trailingslashit( get_stylesheet_directory_uri() ) . 'images/header-1.png',
						'name'  => esc_html__( 'Header Style One', 'consultare-light' ),
					),
					'header-two'   => array(
						'image' => trailingslashit( get_stylesheet_directory_uri() ) . 'images/header-2.png',
						'name'  => esc_html__( 'Header Style Two', 'consultare-light' ),
					),
				),
			)
		);
	}
}

/**
 * Initialize customizer class.
 */
$consultare_light_customizer = new Consultare_Light_Customizer();

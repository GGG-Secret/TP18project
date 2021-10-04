<?php
/**
 * Customizer functionality
 *
 * @package Zubin
 */

/**
 * Sets up the WordPress core custom header and custom background features.
 *
 * @since 1.0
 *
 * @see wen_travel_header_style()
 */
function wen_travel_custom_header_and_bg() {
	/**
	 * Filter the arguments used when adding 'custom-background' support in Zubin.
	 *
	 * @since 1.0
	 *
	 * @param array $args {
	 *     An array of custom-background support arguments.
	 *
	 *     @type string $default-color Default color of the background.
	 * }
	 */
	add_theme_support( 'custom-background', apply_filters( 'wen_travel_custom_bg_args', array(
		'default-color' => '#ffffff',
	) ) );

	/**
	 * Filter the arguments used when adding 'custom-header' support in Zubin.
	 *
	 * @since 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-text-color Default color of the header text.
	 *     @type int      $width            Width in pixels of the custom header image. Default 1200.
	 *     @type int      $height           Height in pixels of the custom header image. Default 280.
	 *     @type bool     $flex-height      Whether to allow flexible-height header images. Default true.
	 *     @type callable $wp-head-callback Callback function used to style the header image and text
	 *                                      displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'wen_travel_custom_header_args', array(
		'default-image'      => get_parent_theme_file_uri( '//images/header-image.jpg' ),
		'default-text-color' => '#000000',
		'width'              => 1920,
		'height'             => 1080,
		'flex-height'        => true,
		'flex-height'        => true,
		'wp-head-callback'   => 'wen_travel_header_style',
		'video'              => true,
	) ) );
}
add_action( 'after_setup_theme', 'wen_travel_custom_header_and_bg' );

/**
 * Customize video play/pause button in the custom header.
 *
 * @param array $settings header video settings.
 */
function wen_travel_video_controls( $settings ) {
	$settings['l10n']['play'] = '<span class="screen-reader-text">' . esc_html__( 'Play background video', 'wen-travel' ) . '</span>' . wen_travel_get_svg( array( 'icon' => 'play' ) );
	$settings['l10n']['pause'] = '<span class="screen-reader-text">' . esc_html__( 'Pause background video', 'wen-travel' ) . '</span>' . wen_travel_get_svg( array( 'icon' => 'pause' ) );
	return $settings;
}
add_filter( 'header_video_settings', 'wen_travel_video_controls' );


/**
 * Binds the JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since 1.0
 */
function wen_travel_customize_control_js() {
	wp_enqueue_style( 'wen-travel-custom-controls-css', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/css/customizer.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'wen_travel_customize_control_js' );


/**
 * Render the site title for the selective refresh partial.
 *
 * @since 1.0.0
 * @see wen_travel_customize_register()
 *
 * @return void
 */
function wen_travel_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since 1.0.0
 * @see wen_travel_customize_register()
 *
 * @return void
 */
function wen_travel_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

<?php
/**
 * Testimonial Options
 *
 * @package Consultare
 */

class Consultare_Testimonial_Options {
	public function __construct() {
		// Register Testimonial Options.
		add_action( 'customize_register', array( $this, 'register_options' ), 99 );

		// Add default options.
		add_filter( 'consultare_customizer_defaults', array( $this, 'add_defaults' ) );
	}

	/**
	 * Add options to defaults
	 */
	public function add_defaults( $default_options ) {
		$defaults = array(
			'consultare_testimonial_visibility' => 'disabled',
			'consultare_testimonial_number'     => 2,
		);

		$updated_defaults = wp_parse_args( $defaults, $default_options );

		return $updated_defaults;
	}

	/**
	 * Add layouts section and its controls
	 */
	public function register_options( $wp_customize ) {
		$wp_customize->add_section( 'consultare_ss_testimonial',
			array(
				'title' => esc_html__( 'Testimonial', 'consultare-light' ),
				'panel' => 'consultare_sp_sortable'
			)
		);

		Consultare_Customizer_Utilities::register_option(
			array(
				'settings'          => 'consultare_testimonial_visibility',
				'type'              => 'select',
				'sanitize_callback' => 'consultare_sanitize_select',
				'label'             => esc_html__( 'Visible On', 'consultare-light' ),
				'section'           => 'consultare_ss_testimonial',
				'choices'           => Consultare_Customizer_Utilities::section_visibility(),
			)
		);

		$wp_customize->selective_refresh->add_partial( 'consultare_testimonial_visibility', array(
			'selector' => '#testimonial-section',
		) );

		Consultare_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'WP_Customize_Image_Control',
				'sanitize_callback' => 'esc_url_raw',
				'settings'          => 'consultare_testimonial_bg_image',
				'label'             => esc_html__( 'Background Image', 'consultare-light' ),
				'section'           => 'consultare_ss_testimonial',
				'active_callback'   => array( $this, 'is_testimonial_visible' ),
			)
		);

		Consultare_Customizer_Utilities::register_option(
			array(
				'type'              => 'text',
				'sanitize_callback' => 'consultare_text_sanitization',
				'settings'          => 'consultare_testimonial_section_top_subtitle',
				'label'             => esc_html__( 'Section Top Sub-title', 'consultare-light' ),
				'section'           => 'consultare_ss_testimonial',
				'active_callback'   => array( $this, 'is_testimonial_visible' ),
			)
		);

		Consultare_Customizer_Utilities::register_option(
			array(
				'settings'          => 'consultare_testimonial_section_title',
				'type'              => 'text',
				'sanitize_callback' => 'consultare_text_sanitization',
				'label'             => esc_html__( 'Section Title', 'consultare-light' ),
				'section'           => 'consultare_ss_testimonial',
				'active_callback'   => array( $this, 'is_testimonial_visible' ),
			)
		);

		Consultare_Customizer_Utilities::register_option(
			array(
				'settings'          => 'consultare_testimonial_section_subtitle',
				'type'              => 'text',
				'sanitize_callback' => 'consultare_text_sanitization',
				'label'             => esc_html__( 'Section Subtitle', 'consultare-light' ),
				'section'           => 'consultare_ss_testimonial',
				'active_callback'   => array( $this, 'is_testimonial_visible' ),
			)
		);

		Consultare_Customizer_Utilities::register_option(
			array(
				'settings'          => 'consultare_testimonial_number',
				'type'              => 'number',
				'label'             => esc_html__( 'Number', 'consultare-light' ),
				'description'       => esc_html__( 'Please refresh the customizer page once the number is changed.', 'consultare-light' ),
				'section'           => 'consultare_ss_testimonial',
				'sanitize_callback' => 'absint',
				'input_attrs'       => array(
					'min'   => 1,
					'max'   => 80,
					'step'  => 1,
					'style' => 'width:100px;',
				),
				'active_callback'   => array( $this, 'is_testimonial_visible' ),
			)
		);

		$numbers = consultare_gtm( 'consultare_testimonial_number' );

		for( $i = 0, $j = 1; $i < $numbers; $i++, $j++ ) {
			Consultare_Customizer_Utilities::register_option(
				array(
					'custom_control'    => 'Consultare_Dropdown_Posts_Custom_Control',
					'sanitize_callback' => 'absint',
					'settings'          => 'consultare_testimonial_page_' . $i,
					'label'             => esc_html__( 'Select Page', 'consultare-light' ),
					'section'           => 'consultare_ss_testimonial',
					'active_callback'   => array( $this, 'is_testimonial_visible' ),
					'input_attrs' => array(
						'post_type'      => 'page',
						'posts_per_page' => -1,
						'orderby'        => 'name',
						'order'          => 'ASC',
					),
				)
			);
		}
	}

	/**
	 * Testimonial visibility active callback.
	 */
	public function is_testimonial_visible( $control ) {
		return ( consultare_display_section( $control->manager->get_setting( 'consultare_testimonial_visibility' )->value() ) );
	}
}

/**
 * Initialize class
 */
$consultare_ss_testimonial = new Consultare_Testimonial_Options();

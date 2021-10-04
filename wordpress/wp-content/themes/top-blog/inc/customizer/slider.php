<?php
/**
 * Slider Options
 *
 * @package Top Blog
 */

class Top_Blog_Slider_Options {
	public function __construct() {
		// Register Slider Options.
		add_action( 'customize_register', array( $this, 'register_options' ), 98 );

		// Add default options.
		add_filter( 'top_blog_customizer_defaults', array( $this, 'add_defaults' ) );
	}

	/**
	 * Add options to defaults
	 */
	public function add_defaults( $default_options ) {
		$defaults = array(
			'top_blog_slider_visibility'        => 'disabled',
			'top_blog_slider_autoplay_delay'    => 5000,
			'top_blog_slider_pause_on_hover'    => 1,
			'top_blog_slider_navigation'        => 1,
			'top_blog_slider_pagination'        => 1,
			'top_blog_slider_number'            => 2,
		);

		$updated_defaults = wp_parse_args( $defaults, $default_options );

		return $updated_defaults;
	}

	/**
	 * Add slider section and its controls
	 */
	public function register_options( $wp_customize ) {
		$wp_customize->add_section( 'top_blog_ss_slider',
			array(
				'title' => esc_html__( 'Slider', 'top-blog' ),
				'panel' => 'top_blog_theme_options'
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'settings'          => 'top_blog_slider_visibility',
				'type'              => 'select',
				'sanitize_callback' => 'top_blog_sanitize_select',
				'label'             => esc_html__( 'Visible On', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'choices'           => Top_Blog_Customizer_Utilities::section_visibility(),
			)
		);

		$wp_customize->selective_refresh->add_partial( 'top_blog_slider_visibility', array(
			'selector' => '#slider-section',
		) );

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'type'              => 'number',
				'settings'          => 'top_blog_slider_number',
				'label'             => esc_html__( 'Number', 'top-blog' ),
				'description'       => esc_html__( 'Please refresh the customizer page once the number is changed.', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'sanitize_callback' => 'absint',
				'input_attrs'       => array(
					'min'   => 1,
					'max'   => 80,
					'step'  => 1,
					'style' => 'width:100px;',
				),
				'active_callback'   => array( $this, 'is_slider_visible' ),
			)
		);

		$numbers = top_blog_gtm( 'top_blog_slider_number' );

		for( $i = 0, $j = 1; $i < $numbers; $i++, $j++ ) {
			Top_Blog_Customizer_Utilities::register_option(
				array(
					'custom_control'    => 'Top_Blog_Dropdown_Posts_Custom_Control',
					'sanitize_callback' => 'absint',
					'settings'          => 'top_blog_slider_post_' . $i,
					'label'             => esc_html__( 'Select Post', 'top-blog' ),
					'section'           => 'top_blog_ss_slider',
					'active_callback'   => array( $this, 'is_slider_visible' ),
					'input_attrs'       => array(
						'posts_per_page' => -1,
						'orderby'        => 'name',
						'order'          => 'ASC',
					),
				)
			);
		}

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Note_Control',
				'settings'          => 'top_blog_slider_advance_options_notice',
				'sanitize_callback' => 'top_blog_text_sanitization',
				'label'             => esc_html__( 'Slider Advance Options', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'active_callback'   => array( $this, 'is_slider_visible' ),
				'transport'         => 'postMessage',
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_slider_autoplay',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Autoplay', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'active_callback'   => array( $this, 'is_slider_visible' ),
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'settings'          => 'top_blog_slider_autoplay_delay',
				'type'              => 'number',
				'sanitize_callback' => 'absint',
				'label'             => esc_html__( 'Autoplay Delay', 'top-blog' ),
				'description'       => esc_html__( '(in ms)', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'input_attrs'           => array(
					'width' => '10px',
				),
				'active_callback'   => array( $this, 'is_slider_autoplay_on' ),
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_slider_pause_on_hover',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Pause On Hover', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'active_callback'   => array( $this, 'is_slider_autoplay_on' ),
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_slider_navigation',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Navigation', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'active_callback'   => array( $this, 'is_slider_visible' ),
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_slider_pagination',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Pagination', 'top-blog' ),
				'section'           => 'top_blog_ss_slider',
				'active_callback'   => array( $this, 'is_slider_visible' ),
			)
		);
	}

	/**
	 * Slider visibility active callback.
	 */
	public function is_slider_visible( $control ) {
		return ( top_blog_display_section( $control->manager->get_setting( 'top_blog_slider_visibility' )->value() ) );
	}

	/**
	 * Slider autoplay check.
	 */
	public function is_slider_autoplay_on( $control ) {
		return ( $this->is_slider_visible( $control ) && $control->manager->get_setting( 'top_blog_slider_autoplay' )->value() );
	}
}

/**
 * Initialize class
 */
$top_blog_ss_slider = new Top_Blog_Slider_Options();

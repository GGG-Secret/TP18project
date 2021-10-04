<?php
/**
 * Adds the theme options sections, settings, and controls to the theme customizer
 *
 * @package Top Blog
 */

class Top_Blog_Theme_Options {
	public function __construct() {
		// Register our Panel.
		add_action( 'customize_register', array( $this, 'add_panel' ) );

		// Register Breadcrumb Options.
		add_action( 'customize_register', array( $this, 'register_breadcrumb_options' ) );

		// Register Excerpt Options.
		add_action( 'customize_register', array( $this, 'register_excerpt_options' ) );

		// Register Homepage Options.
		add_action( 'customize_register', array( $this, 'register_homepage_options' ) );
		
		// Register Header Options.
		add_action( 'customize_register', array( $this, 'register_header_options' ) );

		// Register Layout Options.
		add_action( 'customize_register', array( $this, 'register_layout_options' ) );

		// Register Search Options.
		add_action( 'customize_register', array( $this, 'register_search_options' ) );

		// Add default options.
		add_filter( 'top_blog_customizer_defaults', array( $this, 'add_defaults' ) );
	}

	/**
	 * Add options to defaults
	 */
	public function add_defaults( $default_options ) {
		$defaults = array(
			// Header Media.
			'top_blog_header_image_visibility' => 'entire-site',

			// Breadcrumb
			'top_blog_breadcrumb_show' => 1,

			// Buttons
			'top_blog_button_border_radius' => 0,

			// Layout Options.
			'top_blog_default_layout'          => 'right-sidebar',
			'top_blog_homepage_archive_layout' => 'right-sidebar',

			// Excerpt Options
			'top_blog_excerpt_length'    => 30,
			'top_blog_excerpt_more_text' => esc_html__( 'Continue reading', 'top-blog' ),

			// Footer Options.
			'top_blog_footer_editor_style'      => 'one-column',
			'top_blog_footer_editor_text'       => sprintf( _x( 'Copyright &copy; %1$s %2$s. All Rights Reserved. %3$s', '1: Year, 2: Site Title with home URL, 3: Privacy Policy Link', 'top-blog' ), '[the-year]', '[site-link]', '[privacy-policy-link]' ) . ' &#124; ' . esc_html__( 'Top Blog by', 'top-blog' ). '&nbsp;<a target="_blank" href="'. esc_url( 'https://fireflythemes.com' ) .'">Firefly Themes</a>',
			'top_blog_footer_editor_text_left'  => sprintf( _x( 'Copyright &copy; %1$s %2$s. All Rights Reserved. %3$s', '1: Year, 2: Site Title with home URL, 3: Privacy Policy Link', 'top-blog' ), '[the-year]', '[site-link]', '[privacy-policy-link]' ),
			'top_blog_footer_editor_text_right' => esc_html__( 'Top Blog by', 'top-blog' ). '&nbsp;<a target="_blank" href="'. esc_url( 'https://fireflythemes.com' ) .'">Firefly Themes</a>',

			// Homepage/Frontpage Options.
			'top_blog_front_page_category'   => '',
			'top_blog_show_homepage_content' => 1,

			// Search Options.
			'top_blog_search_text'         => esc_html__( 'Search...', 'top-blog' ),

			// Font Family.
			'top_blog_body_font'     => 'roboto',
			'top_blog_title_font'    => 'roboto',
			'top_blog_tagline_font'  => 'roboto',
			'top_blog_menu_font' 	 => 'barlow-condensed',
			'top_blog_content_font'  => 'roboto',
			'top_blog_headings_font' => 'barlow-condensed',
			'top_blog_content_font'  => 'roboto',
		);


		$updated_defaults = wp_parse_args( $defaults, $default_options );

		return $updated_defaults;
	}

	/**
	 * Register the Customizer panels
	 */
	public function add_panel( $wp_customize ) {
		/**
		 * Add our Header & Navigation Panel
		 */
		 $wp_customize->add_panel( 'top_blog_theme_options',
		 	array(
				'title' => esc_html__( 'Theme Options', 'top-blog' ),
			)
		);
	}

	/**
	 * Add breadcrumb section and its controls
	 */
	public function register_breadcrumb_options( $wp_customize ) {
		// Add Excerpt Options section.
		$wp_customize->add_section( 'top_blog_breadcrumb_options',
			array(
				'title' => esc_html__( 'Breadcrumb', 'top-blog' ),
				'panel' => 'top_blog_theme_options',
			)
		);

		if ( function_exists( 'bcn_display' ) ) {
			Top_Blog_Customizer_Utilities::register_option(
				array(
					'custom_control'    => 'Top_Blog_Simple_Notice_Custom_Control',
					'sanitize_callback' => 'sanitize_text_field',
					'settings'          => 'ff_multiputpose_breadcrumb_plugin_notice',
					'label'             =>  esc_html__( 'Info', 'top-blog' ),
					'description'       =>  sprintf( esc_html__( 'Since Breadcrumb NavXT Plugin is installed, edit plugin\'s settings %1$shere%2$s', 'top-blog' ), '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=breadcrumb-navxt' ) ) . '" target="_blank">', '</a>' ),
					'section'           => 'ff_multiputpose_breadcrumb_options',
				)
			);

			return;
		}

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_breadcrumb_show',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Display Breadcrumb?', 'top-blog' ),
				'section'           => 'top_blog_breadcrumb_options',
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_breadcrumb_show_home',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Show on homepage?', 'top-blog' ),
				'section'           => 'top_blog_breadcrumb_options',
			)
		);
	}

	/**
	 * Add layouts section and its controls
	 */
	public function register_layout_options( $wp_customize ) {
		// Add layouts section.
		$wp_customize->add_section( 'top_blog_layouts',
			array(
				'title' => esc_html__( 'Layouts', 'top-blog' ),
				'panel' => 'top_blog_theme_options'
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'type'              => 'select',
				'settings'          => 'top_blog_default_layout',
				'sanitize_callback' => 'top_blog_sanitize_select',
				'label'             => esc_html__( 'Default Layout', 'top-blog' ),
				'section'           => 'top_blog_layouts',
				'choices'           => array(
					'right-sidebar'         => esc_html__( 'Right Sidebar', 'top-blog' ),
					'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'top-blog' ),
				),
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'type'              => 'select',
				'settings'          => 'top_blog_homepage_archive_layout',
				'sanitize_callback' => 'top_blog_sanitize_select',
				'label'             => esc_html__( 'Homepage/Archive Layout', 'top-blog' ),
				'section'           => 'top_blog_layouts',
				'choices'           => array(
					'right-sidebar'         => esc_html__( 'Right Sidebar', 'top-blog' ),
					'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'top-blog' ),
				),
			)
		);
	}

	/**
	 * Add excerpt section and its controls
	 */
	public function register_excerpt_options( $wp_customize ) {
		// Add Excerpt Options section.
		$wp_customize->add_section( 'top_blog_excerpt_options',
			array(
				'title' => esc_html__( 'Excerpt Options', 'top-blog' ),
				'panel' => 'top_blog_theme_options',
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'type'              => 'number',
				'settings'          => 'top_blog_excerpt_length',
				'sanitize_callback' => 'absint',
				'label'             => esc_html__( 'Excerpt Length (Words)', 'top-blog' ),
				'section'           => 'top_blog_excerpt_options',
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'type'              => 'text',
				'settings'          => 'top_blog_excerpt_more_text',
				'sanitize_callback' => 'sanitize_text_field',
				'label'             => esc_html__( 'Excerpt More Text', 'top-blog' ),
				'section'           => 'top_blog_excerpt_options',
			)
		);
	}

	/**
	 * Add Homepage/Frontpage section and its controls
	 */
	public function register_homepage_options( $wp_customize ) {
		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Dropdown_Select2_Custom_Control',
				'sanitize_callback' => 'top_blog_text_sanitization',
				'settings'          => 'top_blog_front_page_category',
				'description'       => esc_html__( 'Filter Homepage/Blog page posts by following categories', 'top-blog' ),
				'label'             => esc_html__( 'Categories', 'top-blog' ),
				'section'           => 'static_front_page',
				'input_attrs'       => array(
					'multiselect' => true,
				),
				'choices'           => array( esc_html__( '--Select--', 'top-blog' ) => Top_Blog_Customizer_Utilities::get_terms( 'category' ) ),
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'custom_control'    => 'Top_Blog_Toggle_Switch_Custom_control',
				'settings'          => 'top_blog_show_homepage_content',
				'sanitize_callback' => 'top_blog_switch_sanitization',
				'label'             => esc_html__( 'Show Home Content/Blog', 'top-blog' ),
				'section'           => 'static_front_page',
			)
		);
	}

	/**
	 * Add Header section and its controls
	 */
	public function register_header_options( $wp_customize ) {
		// Add header options section.
		$wp_customize->add_section( 'top_blog_header_options',
			array(
				'title' => esc_html__( 'Header Options', 'top-blog' ),
				'panel' => 'top_blog_theme_options'
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'type'              => 'text',
				'settings'          => 'top_blog_header_top_text',
				'sanitize_callback' => 'top_blog_text_sanitization',
				'label'             => esc_html__( 'Text', 'top-blog' ),
				'section'           => 'top_blog_header_options',
			)
		);
	}

	/**
	 * Add Homepage/Frontpage section and its controls
	 */
	public function register_search_options( $wp_customize ) {
		// Add Homepage/Frontpage Section.
		$wp_customize->add_section( 'top_blog_search',
			array(
				'title' => esc_html__( 'Search', 'top-blog' ),
				'panel' => 'top_blog_theme_options',
			)
		);

		Top_Blog_Customizer_Utilities::register_option(
			array(
				'settings'          => 'top_blog_search_text',
				'sanitize_callback' => 'top_blog_text_sanitization',
				'label'             => esc_html__( 'Search Text', 'top-blog' ),
				'section'           => 'top_blog_search',
				'type'              => 'text',
			)
		);
	}
}

/**
 * Initialize class
 */
$top_blog_theme_options = new Top_Blog_Theme_Options();

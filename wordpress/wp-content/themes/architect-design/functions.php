<?php
/**
 * Theme functions and definitions
 *
 * @package Architect Design
 */

/**
 * After setup theme hook
 */
function architect_design_theme_setup(){
    /*
     * Make child theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_child_theme_textdomain( 'architect-design', get_stylesheet_directory() . '/languages' );	
	require get_stylesheet_directory() . '/inc/customizer/architect-design-customizer-options.php';	
}
add_action( 'after_setup_theme', 'architect_design_theme_setup' );

/**
 * Load assets.
 */

function architect_design_theme_css() {
	wp_enqueue_style( 'architect-design-parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style('architect-design-child-style', get_stylesheet_directory_uri() . '/style.css');
	wp_enqueue_style('architect-design-default-css', get_stylesheet_directory_uri() . "/assets/css/theme-default.css" );
    wp_enqueue_style('architect-design-bootstrap-smartmenus-css', get_stylesheet_directory_uri() . "/assets/css/bootstrap-smartmenus.css" ); 	
}
add_action( 'wp_enqueue_scripts', 'architect_design_theme_css', 99);

/**
 * Import Options From Parent Theme
 *
 */
function architect_design_parent_theme_options() {
	$arilewp_mods = get_option( 'theme_mods_arilewp' );
	if ( ! empty( $arilewp_mods ) ) {
		foreach ( $arilewp_mods as $arilewp_mod_k => $arilewp_mod_v ) {
			set_theme_mod( $arilewp_mod_k, $arilewp_mod_v );
		}
	}
}
add_action( 'after_switch_theme', 'architect_design_parent_theme_options' );

/**
 * Fresh site activate
 *
 */
$fresh_site_activate = get_option( 'fresh_architect_design_site_activate' );
if ( (bool) $fresh_site_activate === false ) {
	set_theme_mod( 'arilewp_page_header_background_color', 'rgba(0,0,0,0.6)' );
	set_theme_mod( 'arilewp_testomonial_background_image', get_stylesheet_directory_uri().'/assets/img/theme-bg.jpg' );
	set_theme_mod( 'arilewp_testimonial_overlay_disable', true );
	set_theme_mod( 'arilewp_typography_disabled', true );
	set_theme_mod( 'arilewp_theme_color_skin', 'theme-dark' );
	set_theme_mod( 'arilewp_theme_color', 'theme-porsche' );
	set_theme_mod( 'arilewp_main_header_style', 'center_logo' );
	set_theme_mod( 'arilewp_project_layout', 'arilewp_project_layout2' );
	set_theme_mod( 'arilewp_project_layout', 'arilewp_slider_captoin_layout2' );
	set_theme_mod( 'arilewp_blog_column_layout', '3' );
	set_theme_mod( 'arilewp_theme_info_back_color', 'rgba(32, 32, 32, 0.9)' );
	set_theme_mod( 'arilewp_typography_h1_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_h2_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_h3_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_h4_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_h5_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_h6_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_widget_title_font_family', 'Fira Sans' );
	set_theme_mod( 'arilewp_typography_h1_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_h2_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_h3_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_h4_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_h5_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_h6_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_h1_letter_spacing', '2px' );
	set_theme_mod( 'arilewp_typography_h2_letter_spacing', '2px' );
	set_theme_mod( 'arilewp_typography_h3_letter_spacing', '3px' );
	set_theme_mod( 'arilewp_typography_h4_letter_spacing', '3px' );
	set_theme_mod( 'arilewp_typography_h5_letter_spacing', '3px' );
	set_theme_mod( 'arilewp_typography_h6_letter_spacing', '4px' );
	set_theme_mod( 'arilewp_typography_menu_bar_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_dropdown_bar_text_transform', 'uppercase' );
	set_theme_mod( 'arilewp_typography_widget_title_text_transform', 'uppercase' );
	update_option( 'fresh_architect_design_site_activate', true );
}

/**
 * Custom Theme Script
*/
function architect_design_custom_theme_css() {
	$architect_design_testomonial_background_image = get_theme_mod('arilewp_testomonial_background_image');
	?>
    <style type="text/css">
		<?php if($architect_design_testomonial_background_image != null) : ?>
		.theme-testimonial { 
		        background-image: url(<?php echo esc_url( $architect_design_testomonial_background_image ); ?>); 
                background-size: cover;
				background-position: center center;
		}
        <?php endif; ?>
    </style>
 
<?php }
add_action('wp_footer','architect_design_custom_theme_css');

/**
 * Page header
 *
 */
function architect_design_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'architect_design_custom_header_args', array(
		'default-image'      => get_stylesheet_directory_uri().'/assets/img/architect-design-page-header.jpg',
		'default-text-color' => 'fff',
		'width'              => 1920,
		'height'             => 500,
		'flex-height'        => true,
		'flex-width'         => true,
		'wp-head-callback'   => 'architect_design_header_style',
	) ) );
}

add_action( 'after_setup_theme', 'architect_design_custom_header_setup' );

/**
 * Custom background
 *
 */
function architect_design_custom_background_setup() {
	add_theme_support( 'custom-background', apply_filters( 'architect_design_custom_background_args', array(
		'default-color' => '202020',
		'default-image' => '',
	) ) );
}
add_action( 'after_setup_theme', 'architect_design_custom_background_setup' );


if ( ! function_exists( 'architect_design_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see architect_design_custom_header_setup().
	 */
	function architect_design_header_style() {
		$header_text_color = get_header_textcolor();

		/*
		 * If no custom options for text are set, let's bail.
		 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
		 */
		if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
			<?php
			// Has the text been hidden?
			if ( ! display_header_text() ) :
				?>
			.site-title,
			.site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}

			<?php
			// If the user has set a custom color for the text use that.
			else :
				?>
			.site-title a,
			.site-description {
				color: #<?php echo esc_attr( $header_text_color ); ?> !important;
			}

			<?php endif; ?>
		</style>
		<?php
	}
endif;
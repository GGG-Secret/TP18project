<?php
/**
 * Child Theme functions and definitions.
 * This theme is a child theme for Consultare.
 *
 * @package Consultare_Light
 * @author  FireflyThemes https://fireflythemes.com
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 */

/**
 * Theme functions and definitions.
 */
function consultare_light_enqueue_styles() {
	// Parent Theme stylesheet.
	wp_enqueue_style( 'consultare-style', get_template_directory_uri() . '/style.css', null, consultare_light_get_file_mod_date( get_template_directory() . '/style.css' ) );

	wp_enqueue_style( 'consultare-light-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'consultare-style' ),
        consultare_light_get_file_mod_date( get_stylesheet_directory() . '/style.css' )
    );
}
add_action(  'wp_enqueue_scripts', 'consultare_light_enqueue_styles' );

/**
 * Get file modified date
 */
function consultare_light_get_file_mod_date( $file ) {
	return date( 'Ymd-Gis', filemtime( $file ) );
}

/**
 * Loads the child theme textdomain.
 */
function consultare_light_setup() {
    load_child_theme_textdomain( 'consultare-light', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'consultare_light_setup', 11 );

/**
 * Override parent function to load Oswald Google font
 */
function consultare_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
    * supported by Heebo, translate this to 'off'. Do not translate
    * into your own language.
    */
    $roboto_slab = _x( 'on', 'Roboto Slab font: on or off', 'consultare-light' );
    $poppins     = _x( 'on', 'Poppins font: on or off', 'consultare-light' );
    $oswald      = _x( 'on', 'Oswald font: on or off', 'consultare-light' );

    if ( 'off' !== $roboto_slab && 'off' !== $poppins && 'off' !== $oswald ) {
        $font_families = array();

        if ( 'off' !== $roboto_slab ) {
            $font_families[] = 'Roboto Slab:300,400,500,600,700,800,900';
        }

        if ( 'off' !== $poppins ) {
            $font_families[] = 'Poppins:300,400,500,600,700,800,900';
        }

        if ( 'off' !== $oswald ) {
            $font_families[] = 'Oswald:300,400,500,600,700,800,900';
        }


        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );

        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }

    return esc_url_raw( $fonts_url );
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function consultare_light_body_classes( $classes ) {
    // Add header Style Class.
    $classes['header-class'] = esc_attr( consultare_gtm( 'consultare_header_style' ) );

    return $classes;
}
add_filter( 'body_class', 'consultare_light_body_classes', 99 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function consultare_light_widgets_init() {
    register_sidebar( array(
            'name'        => esc_html__( 'Footer 4', 'consultare-light' ),
            'id'          => 'sidebar-5',
            'description' => esc_html__( 'Add widgets here to appear in your footer.', 'consultare-light' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'consultare_light_widgets_init', 100 );

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 *
 * @since 1.0
 */
function consultare_light_footer_sidebar_class() {
    $count = 0;

    if ( is_active_sidebar( 'sidebar-2' ) ) {
        $count++;
    }

    if ( is_active_sidebar( 'sidebar-3' ) ) {
        $count++;
    }

    if ( is_active_sidebar( 'sidebar-4' ) ) {
        $count++;
    }

    if ( is_active_sidebar( 'sidebar-5' ) ) {
        $count++;
    }

    $class = '';

    switch ( $count ) {
        case '1':
            $class = 'one';
            break;
        case '2':
            $class = 'two';
            break;
        case '3':
            $class = 'three';
            break;
        case '4':
            $class = 'four';
            break;
    }

    if ( $class ) {
        echo 'class="widget-area footer-widget-area ' . $class . '"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Load Customizer Options
 */
require get_theme_file_path( '/inc/customizer.php' );
require get_theme_file_path( '/inc/testimonial.php' );

<?php
/**
 * Useful Utility methods shared by most theme options
 *
 * @package Top Blog
 */

class Top_Blog_Customizer_Utilities {
	/**
	 * Function to register control and setting
	 */
	public static function register_option( $option ) {
		global $wp_customize;

		// Get our Customizer defaults
		$defaults = apply_filters( 'top_blog_customizer_defaults', array() );

		// Add Setting.
		$wp_customize->add_setting( $option['settings'],
			array(
				'sanitize_callback'  => $option['sanitize_callback'],
				'default'            => isset( $option['default'] ) ? $option['default'] : ( isset( $defaults[ $option['settings'] ] ) ? $defaults[ $option['settings'] ] : '' ),
				'transport'          => isset( $option['transport'] ) ? $option['transport'] : 'refresh',
				'theme_supports'     => isset( $option['theme_supports'] ) ? $option['theme_supports'] : '',
				'description_hidden' => isset( $option['description_hidden'] ) ? $option['description_hidden'] : 0,
			)
		);

		$control = array(
			'label'    => $option['label'],
			'section'  => $option['section'],
			'settings' => $option['settings'],
		);

		if ( isset( $option['active_callback'] ) ) {
			$control['active_callback'] = $option['active_callback'];
		}

		if ( isset( $option['priority'] ) ) {
			$control['priority'] = $option['priority'];
		}

		if ( isset( $option['choices'] ) ) {
			$control['choices'] = $option['choices'];
		}

		if ( isset( $option['type'] ) ) {
			$control['type'] = $option['type'];
		}

		if ( isset( $option['input_attrs'] ) ) {
			$control['input_attrs'] = $option['input_attrs'];
		}

		if ( isset( $option['description'] ) ) {
			$control['description'] = $option['description'];
		}

		if ( isset( $option['width'] ) ) {
			$control['width'] = $option['width'];
		}

		if ( isset( $option['height'] ) ) {
			$control['height'] = $option['height'];
		}

		if ( isset( $option['flex_width'] ) ) {
			$control['flex_width'] = $option['flex_width'];
		}

		if ( isset( $option['flex_height'] ) ) {
			$control['flex_height'] = $option['flex_height'];
		}

		if ( isset( $option['custom_control'] ) ) {
			$wp_customize->add_control( new $option['custom_control']( $wp_customize, $option['settings'], $control ) );
		} else {
			$wp_customize->add_control( $option['settings'], $control );
		}
	}

	/**
	 * Get array of terms.
	 */
	public static function get_terms( $term ) {
		$output_terms = array();

		$terms = get_terms(
			array(
				'taxonomy' => $term,
				'order'    => 'ASC',
				'orderby'  => 'id',
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			if ( $terms ) {
				foreach( $terms as $term ) {
					$output_terms[$term->term_id] = $term->name;
				}
			}
		}

		return $output_terms;
	}

	/**
	 * Get array of posts.
	 */
	public static function get_posts_as_array( $args ) {
		$output_terms = array();
		
		$args['suppress_filters'] = false; // Set this to emable advanced caching for get_posts lile WP_Query.

		$get_posts = get_posts( $args );

		if ( ! empty( $get_posts ) ) {
			foreach ( $get_posts as $get_post ) {
				$title = $get_post->post_title;

				if ( empty( $title ) ) {
					/* translators: 1: Post ID. */
					$title = sprintf( esc_html__( 'ID: %s No Title', 'top-blog' ), $get_post->ID );
				}

				$output_terms[ $get_post->ID ] = $title;
			}
		}

		return $output_terms;
	}

	/**
	 * Returns choices array for section visibility.
	 * @return array
	 */
	static function section_visibility() {
		$options = array(
			'disabled'     => esc_html__( 'Disabled', 'top-blog' ),
			'homepage'     => esc_html__( 'Homepage / Frontpage', 'top-blog' ),
			'entire-site'  => esc_html__( 'Entire Site', 'top-blog' ),
		);

		return apply_filters( 'top_blog_section_visibility_options', $options );
	}

	/**
	 * Returns choices array for section types
	 * @return array
	 */
	static function section_types() {
		$options = array(
			''         => esc_html__( '--Select--', 'top-blog' ),
			'page'     => esc_html__( 'Page', 'top-blog' ),
			'post'     => esc_html__( 'Post', 'top-blog' ),
			'category' => esc_html__( 'Category', 'top-blog' ),
			'tag'      => esc_html__( 'Tags', 'top-blog' ),
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$options['product']     = esc_html__( 'Products', 'top-blog' );
			$options['product_cat'] = esc_html__( 'Product Category', 'top-blog' );
			$options['product_tag'] = esc_html__( 'Product Tag', 'top-blog' );
		}

		$options['custom'] = esc_html__( 'Custom', 'top-blog' );

		return apply_filters( 'top_blog_section_type_options', $options );
	}

	/**
	 * Returns choices array for Layout Option.
	 * @return array
	 */
	public static function section_layouts() {
		$options = array(
			'1' => esc_html__( 'One Column', 'top-blog' ),
			'2' => esc_html__( 'Two Columns', 'top-blog' ),
			'3' => esc_html__( 'Three Columns', 'top-blog' ),
			'4' => esc_html__( 'Four Columns', 'top-blog' ),
		);

		return apply_filters( 'top_blog_section_layout_options', $options );
	}

	/**
	 * Returns fonts list.
	 * @return array
	 */
	public static function get_fonts() {
		// Fail Safe.
		$fonts_fail_safe = array(
			'arial-black'         => array(
				'font-family' => '"Arial Black", Gadget, sans-serif',
			),
			'allan'               => array(
				'web-font'    => true,
				'font-family' => '"Allan", sans-serif',
				'web-value'   => 'Allan',
			),
			'allerta'             => array(
				'web-font'    => true,
				'font-family' => '"Allerta", sans-serif',
				'web-value'   => 'Allerta',
			),
			'amaranth'            => array(
				'web-font'    => true,
				'font-family' => '"Amaranth", sans-serif',
				'web-value'   => 'Amaranth',
			),
			'amatic-sc'           => array(
				'web-font'    => true,
				'font-family' => '"Amatic SC", cursive',
				'web-value'   => 'Amatic SC',
			),
			'arial'               => array(
				'font-family' => 'Arial, Helvetica, sans-serif',
			),
			'barlow-condensed'              => array(
				'web-font'    => true,
				'font-family' => '"Barlow Condensed", sans-serif',
				'web-value'   => 'Barlow Condensed',
			),
			'bitter'              => array(
				'web-font'    => true,
				'font-family' => '"Bitter", sans-serif',
				'web-value'   => 'Bitter',
			),
			'inter'              => array(
				'web-font'    => true,
				'font-family' => '"Inter", sans-serif',
				'web-value'   => 'Inter',
			),
			'cabin'               => array(
				'web-font'    => true,
				'font-family' => '"Cabin", sans-serif',
				'web-value'   => 'Cabin',
			),
			'cantarell'           => array(
				'web-font'    => true,
				'font-family' => '"Cantarell", sans-serif',
				'web-value'   => 'Cantarell',
			),
			'century-gothic'      => array(
				'font-family' => '"Century Gothic", sans-serif',
			),
			'courier-new'         => array(
				'font-family' => '"Courier New", Courier, monospace',
			),
			'crimson-text'        => array(
				'web-font'    => true,
				'font-family' => '"Crimson Text", sans-serif',
				'web-value'   => 'Crimson+Text',
			),
			'cuprum'              => array(
				'web-font'    => true,
				'font-family' => '"Cuprum", sans-serif',
				'web-value'   => 'Cuprum',
			),
			'dancing-script'      => array(
				'web-font'    => true,
				'font-family' => '"Dancing Script", sans-serif',
				'web-value'   => 'Dancing Script',
			),
			'droid-sans'          => array(
				'web-font'    => true,
				'font-family' => '"Droid Sans", sans-serif',
				'web-value'   => 'Droid Sans',
			),
			'droid-serif'         => array(
				'web-font'    => true,
				'font-family' => '"Droid Serif", sans-serif',
				'web-value'   => 'Droid Serif',
			),
			'exo'                 => array(
				'web-font'    => true,
				'font-family' => '"Exo", sans-serif',
				'web-value'   => 'Exo',
			),
			'exo-2'               => array(
				'web-font'    => true,
				'font-family' => '"Exo 2", sans-serif',
				'web-value'   => 'Exo 2',
			),
			'heebo'               => array(
				'web-font'    => true,
				'font-family' => 'Heebo, sans-serif',
				'web-value'   => 'Heebo',
			),
			'georgia'             => array(
				'font-family' => 'Georgia, "Times New Roman", Times, serif',
			),
			'helvetica'           => array(
				'font-family' => 'Helvetica, "Helvetica Neue", Arial, sans-serif',
			),
			'helvetica-neue'      => array(
				'font-family' => '"Helvetica Neue",Helvetica,Arial,sans-serif',
			),
			'istok-web'           => array(
				'web-font'    => true,
				'font-family' => '"Istok Web", sans-serif',
				'web-value'   => 'Istok Web',
			),
			'impact'              => array(
				'font-family' => 'Impact, Charcoal, sans-serif',
			),
			'inter'                => array(
				'web-font'    => true,
				'font-family' => '"Inter", sans-serif',
				'web-value'   => 'Inter',
			),
			'josefin-sans'        => array(
				'web-font'    => true,
				'font-family' => '"Josefin Sans", sans-serif',
				'web-value'   => 'Josefin Sans',
			),
			'lato'                => array(
				'web-font'    => true,
				'font-family' => '"Lato", sans-serif',
				'web-value'   => 'Lato',
			),
			'lucida-sans-unicode' => array(
				'font-family' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			),
			'lucida-grande'       => array(
				'font-family' => '"Lucida Grande", "Lucida Sans Unicode", sans-serif',
			),
			'lobster'             => array(
				'web-font'    => true,
				'font-family' => '"Lobster", sans-serif',
				'web-value'   => 'Lobster',
			),
			'lora'                => array(
				'web-font'    => true,
				'font-family' => '"Lora", serif',
				'web-value'   => 'Lora',
			),
			'monaco'              => array(
				'font-family' => 'Monaco, Consolas, "Lucida Console", monospace, sans-serif',
			),
			'muli'                => array(
				'font-family' => 'Muli, sans-serif',
			),
			'mrs-saint-delafield' => array(
				'font-family' => '"Mrs Saint Delafield", cursive',
			),
			'merriweather'        => array(
				'web-font'    => true,
				'font-family' => '"Merriweather", serif',
				'web-value'   => 'Merriweather',
			),
			'montserrat'          => array(
				'web-font'    => true,
				'font-family' => '"Montserrat", sans-serif',
				'web-value'   => 'Montserrat',
			),
			'nobile'              => array(
				'web-font'    => true,
				'font-family' => '"Nobile", sans-serif',
				'web-value'   => 'Nobile',
			),
			'noto-sans'           => array(
				'web-font'    => true,
				'font-family' => '"Noto Sans", sans-serif',
				'web-value'   => 'Noto Sans',
			),

			'neuton'              => array(
				'web-font'    => true,
				'font-family' => '"Neuton", serif',
				'web-value'   => 'Neuton',
			),
			'open-sans'           => array(
				'web-font'    => true,
				'font-family' => '"Open Sans", sans-serif',
				'web-value'   => 'Open Sans',
			),
			'oswald'              => array(
				'web-font'    => true,
				'font-family' => '"Oswald", sans-serif',
				'web-value'   => 'Oswald',
			),
			'patua-one'           => array(
				'web-font'    => true,
				'font-family' => '"Patua One", sans-serif',
				'web-value'   => 'Patua One',
			),
			'poppins'             => array(
				'web-font'    => true,
				'font-family' => '"Poppins", sans-serif',
				'web-value'   => 'Poppins',
			),
			'playfair-display'    => array(
				'web-font'    => true,
				'font-family' => '"Playfair Display", sans-serif',
				'web-value'   => 'Playfair Display',
			),
			'pt-sans'             => array(
				'web-font'    => true,
				'font-family' => '"PT Sans", sans-serif',
				'web-value'   => 'PT Sans',
			),
			'pt-serif'            => array(
				'web-font'    => true,
				'font-family' => '"PT Serif", serif',
				'web-value'   => 'PT Serif',
			),
			'quattrocento-sans'   => array(
				'web-font'    => true,
				'font-family' => '"Quattrocento Sans", sans-serif',
				'web-value'   => 'Quattrocento Sans',
			),
			'roboto'              => array(
				'web-font'    => true,
				'font-family' => '"Roboto", sans-serif',
				'web-value'   => 'Roboto',
			),
			'roboto-condensed'    => array(
				'web-font'    => true,
				'font-family' => '"Roboto Condensed", sans-serif',
				'web-value'   => 'Roboto Condensed',
			),
			'roboto-slab'         => array(
				'web-font'    => true,
				'font-family' => '"Roboto Slab", serif',
				'web-value'   => 'Roboto Slab',
			),
			'rubik'               => array(
				'web-font'    => true,
				'font-family' => '"Rubik", serif',
				'web-value'   => 'Rubik',
			),
			'sans-serif'          => array(
				'font-family' => 'Sans Serif, Arial',
			),
			'source-sans'     => array(
				'web-font'    => true,
				'font-family' => '"Source Sans Pro", sans-serif',
				'web-value'   => 'Source Sans Pro',
			),
			'tahoma'              => array(
				'font-family' => 'Tahoma, Geneva, sans-serif',
			),
			'trebuchet-ms'        => array(
				'font-family' => '"Trebuchet MS", "Helvetica", sans-serif',
			),
			'times-new-roman'     => array(
				'font-family' => '"Times New Roman", Times, serif',
			),
			'ubuntu'              => array(
				'web-font'    => true,
				'font-family' => '"Ubuntu", sans-serif',
				'web-value'   => 'Ubuntu',
			),
			'varela'              => array(
				'font-family' => '"Varela", sans-serif',
			),
			'work-sans'           => array (
				'web-font'    => true,
				'font-family' => '"Work Sans", sans-serif',
				'web-value'   => 'Work Sans',
			),
			'verdana'             => array(
				'font-family' => 'Verdana, Geneva, sans-serif',
			),
			'yanone-kaffeesatz'   => array(
				'web-font'    => true,
				'font-family' => '"Yanone Kaffeesatz", sans-serif',
				'web-value'   => 'Yanone Kaffeesatz',
			),
		);

		if ( false === ( $fonts = get_transient( 'top_blog_web_fonts_list' ) ) || empty( $fonts ) ) {
		    $fonts = array();

			$url = 'https://fireflythemes.github.io/font-family-json/fonts.json';

			$response = wp_remote_get( esc_url_raw( $url ) );

			if ( ! is_wp_error( $response ) ) {
				/* Will result in $api_response being an array of data,
				parsed from the JSON response of the API listed above */
				$fonts = json_decode( wp_remote_retrieve_body( $response ), true );

			    // Put the results in a transient. Expire after 24 hours.
			    set_transient( 'top_blog_web_fonts_list', $fonts, 24 * HOUR_IN_SECONDS );
			}
		}

		if ( empty( $fonts ) ) {
			$fonts = $fonts_fail_safe;
		}

		return $fonts;
	}
}

/**
 * Initialize class
 */
$top_blog_utilities = new Top_Blog_Customizer_Utilities();

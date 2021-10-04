<?php
/**
 * Adds Theme page
 *
 * @package Radiant Business
 */

function top_blog_about_admin_style( $hook ) {
	if ( 'appearance_page_top-blog-about' === $hook ) {
		wp_enqueue_style( 'top-blog-theme-about', get_theme_file_uri( 'css/theme-about.css' ), null, '1.0' );
	}
}
add_action( 'admin_enqueue_scripts', 'top_blog_about_admin_style' );

/**
 * Add theme page
 */
function top_blog_menu() {
	add_theme_page( esc_html__( 'About Theme', 'top-blog' ), esc_html__( 'About Theme', 'top-blog' ), 'edit_theme_options', 'top-blog-about', 'top_blog_about_display' );
}
add_action( 'admin_menu', 'top_blog_menu' );

/**
 * Display About page
 */
function top_blog_about_display() {
	$theme = wp_get_theme();
	?>
	<div class="wrap about-wrap full-width-layout">
		<h1><?php echo esc_html( $theme ); ?></h1>
		<div class="about-theme">
			<div class="theme-description">
				<p class="about-text">
					<?php
					// Remove last sentence of description.
					$description = explode( '. ', $theme->get( 'Description' ) );

					array_pop( $description );

					$description = implode( '. ', $description );

					echo esc_html( $description . '.' );
				?></p>
				<p class="actions">
					<a href="https://fireflythemes.com/themes/top-blog-pro" class="button button-secondary" target="_blank"><?php esc_html_e( 'Info', 'top-blog' ); ?></a>

					<a href="https://fireflythemes.com/documentation/top-blog/" class="button button-primary" target="_blank"><?php esc_html_e( 'Documentation', 'top-blog' ); ?></a>

					<a href="https://demo.fireflythemes.com/top-blog" class="button button-primary green" target="_blank"><?php esc_html_e( 'Demo', 'top-blog' ); ?></a>

					<a href="https://fireflythemes.com/support" class="button button-secondary" target="_blank"><?php esc_html_e( 'Support', 'top-blog' ); ?></a>
				</p>
			</div>

			<div class="theme-screenshot">
				<img src="<?php echo esc_url( $theme->get_screenshot() ); ?>" />
			</div>

		</div>

		<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu', 'top-blog' ); ?>">
			<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'top-blog-about' ), 'themes.php' ) ) ); ?>" class="nav-tab<?php echo ( isset( $_GET['page'] ) && 'top-blog-about' === $_GET['page'] && ! isset( $_GET['tab'] ) ) ?' nav-tab-active' : ''; ?>"><?php esc_html_e( 'About', 'top-blog' ); ?></a>
		</nav>

		<?php
			top_blog_main_screen();
		?>

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? esc_html_e( 'Return to Updates', 'top-blog' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'top-blog' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'top-blog' ) : esc_html_e( 'Go to Dashboard', 'top-blog' ); ?></a>
		</div>
	</div>
	<?php
}

/**
 * Output the main about screen.
 */
function top_blog_main_screen() {
	if ( isset( $_GET['page'] ) && 'top-blog-about' === $_GET['page'] && ! isset( $_GET['tab'] ) ) {
	?>
		<div class="feature-section two-col">
			<div class="col card">
				<h2 class="title"><?php esc_html_e( 'Theme Customizer', 'top-blog' ); ?></h2>
				<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'top-blog' ) ?></p>
				<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Customize', 'top-blog' ); ?></a></p>
			</div>

			<div class="col card">
				<h2 class="title"><?php esc_html_e( 'Got theme support question?', 'top-blog' ); ?></h2>
				<p><?php esc_html_e( 'Get genuine support from genuine people. Whether it\'s customization or compatibility, our seasoned developers deliver tailored solutions to your queries.', 'top-blog' ) ?></p>
				<p><a href="https://fireflythemes.com/support" class="button button-primary"><?php esc_html_e( 'Support Forum', 'top-blog' ); ?></a></p>
			</div>
		</div>
	<?php
	}
}

/**
 * Parse changelog from readme file.
 * @param  string $content
 * @return string
 */
function top_blog_parse_changelog( $content ) {
	// Explode content with ==  to juse separate main content to array of headings.
	$content = explode ( '== ', $content );

	$changelog_isolated = '';

	// Get element with 'Changelog ==' as starting string, i.e isolate changelog.
	foreach ( $content as $key => $value ) {
		if (strpos( $value, 'Changelog ==') === 0) {
			$changelog_isolated = str_replace( 'Changelog ==', '', $value );
		}
	}

	// Now Explode $changelog_isolated to manupulate it to add html elements.
	$changelog_array = explode( '= ', $changelog_isolated );

	// Unset first element as it is empty.
	unset( $changelog_array[0] );

	$changelog = '<pre class="changelog">';
		
	foreach ( $changelog_array as $value) {
		// Replace all enter (\n) elements with </span><span> , opening and closing span will be added in next process.
		$value = preg_replace( '/\n+/', '</span><span>', $value );

		// Add openinf and closing div and span, only first span element will have heading class.
		$value = '<div class="block"><span class="heading">= ' . $value . '</span></div>';

		// Remove empty <span></span> element which newr formed at the end.
		$changelog .= str_replace( '<span></span>', '', $value );
	}

	$changelog .= '</pre>';

	return wp_kses_post( $changelog );
}

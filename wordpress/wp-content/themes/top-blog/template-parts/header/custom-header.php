<?php
/**
 * Displays header site branding
 *
 * @package Top Blog
 */

// Check metabox option.
$meta_option = get_post_meta( get_the_ID(), 'top-blog-header-image', true );

if ( empty( $meta_option ) ) {
	$meta_option = 'default';
}

// Bail if header image is removed via metabox option.
if ( 'disable' === $meta_option ) {
	return;
}

$top_blog_enable = top_blog_gtm( 'top_blog_header_image_visibility' );

if ( top_blog_display_section( $top_blog_enable ) ) : ?>
<div id="custom-header">
	<?php is_header_video_active() && has_header_video() ? the_custom_header_markup() : ''; ?>

	<div class="custom-header-content">
		<div class="container">
			<?php top_blog_header_title(); ?>
		</div> <!-- .container -->
	</div>  <!-- .custom-header-content -->
</div>
<?php
endif;

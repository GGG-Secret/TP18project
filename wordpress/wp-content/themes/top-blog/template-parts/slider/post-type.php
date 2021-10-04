<?php
/**
 * Template part for displaying Post Types Slider
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Top Blog
 */

$top_blog_slider_args = top_blog_get_section_args();

$top_blog_loop = new WP_Query( $top_blog_slider_args );

while ( $top_blog_loop->have_posts() ) :
	$top_blog_loop->the_post();

	$text_align  = top_blog_gtm( 'top_blog_slider_text_align_' . $top_blog_loop->current_post );
	?>
	<article id="post-<?php echo esc_attr( get_the_ID() ); ?>" class="swiper-slide type-post text-alignleft caption-animate <?php echo esc_attr( $text_align ); ?> <?php echo has_post_thumbnail() ? 'has-post-thumbnail' : ''; ?>">
		<div class="slider-image-wrapper">
			<?php
			$elegane_blog_style = top_blog_gtm( 'top_blog_slider_style' );
			if ( 'style-three' === $elegane_blog_style ) :
				$top_blog_second_image = top_blog_gtm( 'top_blog_slider_second_custom_image_' . $top_blog_loop->current_post );

				if ( $top_blog_second_image ) :
				?>
				<div class="feature-two-img"><img src="<?php echo esc_url( $top_blog_second_image ); ?>" /></div>
			<?php endif;
			endif; ?>

			<div class="slider-content-image featured-image">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'top-blog-slider' );
				} else {
					echo '<img class="wp-post-image no-thumb" src="' . trailingslashit( esc_url( get_template_directory_uri() ) ) . 'images/no-thumb-1920x900.jpg">';
				}
				?>
			</div><!-- .featured-image -->
		</div><!-- .slider-image-wrapper -->

		<div class="slider-content-wrapper">
			<div class="container">
				<?php top_blog_posted_cats(); ?>

				<div class="slider-title-wrap">
				<?php the_title( '<h2 class="slider-title">', '</h2><!-- .slider-title -->' ); ?>
				</div><!-- .slider-title-wrap -->
				<div class="slider-content-inner-wrapper">
					<div class="slider-content clear-fix">
						<?php the_excerpt(); ?>
					</div><!-- .entry-content -->
				</div><!-- .slider-content-wrapper -->
			</div><!-- .entry-container -->
		</div><!-- .slider-content-wrapper -->

	</article><!-- .hentry -->
<?php
endwhile;

wp_reset_postdata();

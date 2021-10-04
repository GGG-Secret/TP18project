<?php
/**
 * Template part for displaying Service
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Consultare
 */

$consultare_visibility = consultare_gtm( 'consultare_testimonial_visibility' );

if ( ! consultare_display_section( $consultare_visibility ) ) {
	return;
}

$image = consultare_gtm( 'consultare_testimonial_bg_image' );

$consultare_classes   = array();
?>
<div id="testimonial-section" class="page testimonial-section section style-one clear-fix" <?php echo $image ? 'style="background-image: url( ' . esc_url( $image ) . ' )"' : ''; ?>>
	<div class="section-testimonial testimonial-layout-2; ?>">
		<div class="container">
			<?php consultare_section_title( 'testimonial' ); ?>
	      
	      <div class="section-carousel-wrapper">
			<?php get_template_part( 'template-parts/testimonial/post-type' ); ?>
			</div><!-- .section-carousel-wrapper -->
		</div><!-- .container -->
	</div><!-- .section-testimonial  -->
</div><!-- .section -->

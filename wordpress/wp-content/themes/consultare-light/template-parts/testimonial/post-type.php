<?php
/**
 * Template part for displaying Post Types Slider
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Consultare
 */

$consultare_args = consultare_get_section_args( 'testimonial' );
$consultare_loop = new WP_Query( $consultare_args );

if ( $consultare_loop->have_posts() ) :
	?>
	<div class="testimonial-content-wrapper">
		<div >
		<?php

		while ( $consultare_loop->have_posts() ) :
			$consultare_loop->the_post();
			?>
			<div class="testimonial-item ff-grid-6">
				<div class="testimonial-wrapper clear-fix">

					<div class="testimonial-summary pull-right">
						
						<?php consultare_display_content( 'testimonial' ); ?>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="testimonial-thumb">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'consultare-portfolio', array( 'class' => 'pull-left' ) ); ?>
							</a>
							<div class="client-info">
							<?php the_title( '<h3><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h3>'); ?>
						</div>
						<!-- .client-info -->

						</div>
					<?php endif; ?>
				</div><!-- .testimonial-wrapper -->
			</div><!-- .testimonial-item -->
		<?php
		endwhile;
		?>
		</div><!-- .swiper-wrapper -->
	</div><!-- .testimonial-content-wrapper -->
<?php
endif;

wp_reset_postdata();

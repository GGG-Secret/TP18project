<?php
/**
 * Header one Style Template
 *
 * @package Top Blog
 */

$top_blog_header_top_text = top_blog_gtm( 'top_blog_header_top_text' );
?>
<div class="header-wrapper button-disabled">
	<?php if ( $top_blog_header_top_text || has_nav_menu( 'menu-2' ) ) : ?>
	<div id="top-header" class="main-top-header-one dark-top-header">
		<div class="site-top-header-mobile">
			<div class="container">
				<button id="header-top-toggle" class="header-top-toggle" aria-controls="header-top" aria-expanded="false">
					<i class="fas fa-bars"></i><span class="menu-label"> <?php esc_html_e( 'Top Bar', 'top-blog' ); ?></span>
				</button><!-- #header-top-toggle -->
				<div id="site-top-header-mobile-container">
					<?php if ( $top_blog_header_top_text ) : ?>
					<div id="quick-info" class="text-aligncenter">
	                	<p><?php echo esc_html( $top_blog_header_top_text ); ?></p>
					</div>
					<?php endif; ?>

					<?php get_template_part( 'template-parts/navigation/navigation-secondary' ); ?>
				</div>
			</div><!-- .container -->
		</div><!-- .site-top-header-mobile -->

		<div class="site-top-header">
			<div class="container">
				<?php if ( $top_blog_header_top_text ) : ?>
				<div id="quick-info" class="mobile-off pull-left">
                	<p><?php echo esc_html( $top_blog_header_top_text ); ?></p>
				</div>
				<?php endif; ?>

				<div id="secondary-nav" class="pull-right">
					<?php get_template_part( 'template-parts/navigation/navigation-secondary' ); ?>
				</div><!-- .secondary-nav -->
			</div><!-- .container -->
		</div><!-- .site-top-header -->
	</div><!-- #top-header -->
	<?php endif; ?>

	<header id="masthead" class="site-header header-box-shadow main-header-one clear-fix<?php echo top_blog_gtm( 'top_blog_header_sticky' ) ? ' sticky-enabled' : ''; ?>">
		<div class="container">
			<div class="site-header-main">
				<div class="site-branding pull-left">
					<?php get_template_part( 'template-parts/header/site-branding' ); ?>
				</div><!-- .site-branding -->
					
				<div id="main-nav" class="pull-left">
					<?php get_template_part( 'template-parts/navigation/navigation-primary' ); ?>
				</div><!-- .main-nav -->

				<div class="right-header pull-right">
					<div class="right-search-cart-button pull-right">
						<div class="head-search-cart-wrap pull-left">
							<div class="header-search default-search-toggle-off pull-right">
								<?php get_template_part( 'template-parts/header/header-search' ); ?>
							</div><!-- .header-search -->
						</div><!-- .head-search-cart-wrap -->

						<?php if ( has_nav_menu( 'social' ) ): ?>
						<div id="top-social" class="pull-left">
							<div class="primary-social-wrapper default-social-toggle-off">
								<a href="#" id="social-toggle" class="menu-social-toggle"><span class="screen-reader-text"><?php esc_html_e( 'social', 'top-blog' ); ?></span><i class="fas fa-share-alt"></i></a>
								<div id="social-container" class="displaynone">
									<div class="social-nav no-border circle-icon brand-bg">
										<nav id="social-primary-navigation" class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'top-blog' ); ?>">
											<?php
												wp_nav_menu( array(
													'theme_location' => 'social',
													'menu_class'     => 'social-links-menu',
													'depth'          => 1,
													'link_before'    => '<span class="screen-reader-text">',
												) );
											?>
										</nav><!-- .social-navigation -->
									</div>
								</div><!-- #social-container -->
							</div><!-- .primary-social-wrapper -->
						</div><!-- #top-social -->
						<?php endif; ?>

						<?php get_template_part( 'template-parts/header/header-sidebar' ); ?>
					</div><!-- .right-search-cart-button -->
				</div><!-- .right-head -->
			</div><!-- .site-header-main -->
		</div><!-- .container -->
	</header><!-- #masthead -->
</div><!-- .header-wrapper -->

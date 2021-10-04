<?php
/**
 * Displays secondary Navigation
 *
 * @package Top Blog
 */

if ( ! has_nav_menu( 'menu-2' ) ) {
	// Bail if secondary menu is disabled.
	return;
}
?>


<div id="site-secondary-header-menu" class="site-secondary-menu">
	<nav id="site-secondary-navigation" class="secondary-navigation site-navigation custom-secondary-menu" role="navigation" aria-label="<?php esc_attr_e( 'Secondary Menu', 'top-blog' ); ?>">
		<?php wp_nav_menu( array(
			'theme_location'  => 'menu-2',
			'container_class' => 'secondary-menu-container',
			'menu_class'      => 'secondary-menu',
			'depth'           => 1,
		) ); ?>
	</nav><!-- #site-secondary-navigation.custom-secondary-menu -->
</div><!-- .site-header-masecondaryin -->

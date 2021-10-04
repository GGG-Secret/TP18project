<?php
/**
 * Header Sticky Sidebar
 *
 * @package Top Blog
 */

if ( ! is_active_sidebar( 'header-sticky-sidebar' ) ) {
	// Bail if search is disabled.
	return;
}
?>
<div class="sidebar-toggle-button"><i class="sicky-sidebar-toggle fas fa-align-left"></i></div>

<div id="sticky-sidebar" class="sticky-sidebar widget-area sidebar">
	<div class="inner-sidebar">
		<?php dynamic_sidebar( 'header-sticky-sidebar' ); ?>
		<span class="sicky-sidebar-toggle-close pull-right"><i class="fas fa-times"></i></span>
	</div><!-- .inner-sidebar -->
</div><!-- #sticky-sidebar -->

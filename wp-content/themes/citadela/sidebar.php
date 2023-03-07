<?php
/**
 * The sidebar containing the main widget area
 *
 */

if ( ! is_active_sidebar( 'sidebar-name' ) ) {
	return;
}
?>

<aside id="secondary" class="sidebar-name-widgets-area widget-area right-widget-area">
	<div class="widget-area-wrap">
		<?php dynamic_sidebar( 'sidebar-name' ); ?>
	</div>
</aside><!-- #secondary -->

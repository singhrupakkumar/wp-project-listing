<?php
/**
 * Single Event Back link Template Part
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/single-event/back-link.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 4.7
 *
 */
?>

<?php
$event_id = $this->get( 'post_id' );
$label = esc_html_x( 'All %s', '%s Events plural label', 'the-events-calendar' );
$events_label_plural = tribe_get_event_label_plural();
?>
<p class="tribe-events-back">
	<?php if ($item_id = get_post_meta($event_id, '_EventDirectoryItem', true)) {
		$category_id = get_post_meta($item_id, '_citadela_featured_category', true);
		if (!$category_id) {
			$categories = get_the_terms($item_id, 'citadela-item-category');
			if (!empty($categories)) {
				$category_id = $categories[0]->term_id;
			}
		}
		if ($category_id) {
			$meta = get_term_meta($category_id, 'citadela-item-category-meta', true);
			$category_icon = empty($meta['category_icon']) ? null : $meta['category_icon'];
		} ?>
		<a href="<?php echo esc_url(get_permalink($item_id)); ?>" class="listing-item-link">
			<?php if (isset($category_icon)) { ?>
				<i class="fas fa-<?php echo esc_attr($category_icon); ?>"></i>
			<?php }
			echo esc_html(get_the_title($item_id));
			?>
		</a>
	<?php } ?>
	<a href="<?php echo esc_url( tribe_get_events_link() ); ?>">
		&laquo; <?php printf( $label, $events_label_plural ); ?>
	</a>
</p>

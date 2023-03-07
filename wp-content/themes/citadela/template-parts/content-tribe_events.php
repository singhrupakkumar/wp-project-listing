<?php
/**
 * Template part for displaying page content in page.php
 *
  */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php citadela_theme_post_thumbnail(); ?>

    <div class="event-date"><?php echo tribe_events_event_schedule_details( get_the_ID() ); ?></div>

    <header class="entry-header">
        <h2 class="entry-title">
            <?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?>
        </h2>
	</header><!-- .entry-header -->

	<div class="entry-content">
        <?php
        if (has_excerpt()) {
            $content = strip_shortcodes(get_the_excerpt());
            echo strip_tags( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            $content = strip_shortcodes(get_the_content());
            echo wp_trim_words( $content, 50, "..." ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        citadela_theme_edit_post_link();
        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'citadela' ),
            'after'  => '</div>',
        ));
        ?>
	</div><!-- .entry-content -->

    <?php if (tribe_get_cost()) { ?>
        <div class="event-cost"><?php echo tribe_get_cost(null, true) ?></div>
    <?php } ?>

</article><!-- #post-<?php the_ID(); ?> -->

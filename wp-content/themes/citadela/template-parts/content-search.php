<?php
/**
 * Template part for displaying results in search pages
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( [ is_sticky() ? "sticky": "" ] ); ?>>

	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			citadela_theme_posted_on();
			citadela_theme_posted_by();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php citadela_theme_post_thumbnail(); ?>

	<div class="entry-summary">
		<?php
		if( has_excerpt() ){
				$content = strip_shortcodes(get_the_excerpt());
				echo strip_tags( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}else{
				$content = strip_shortcodes(get_the_content());
				echo wp_trim_words( $content, 50, "..." ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<?php
			//list post locations if Citadela Listing plugin is active
			do_action( 'ctdl_directory_post_locations_list' );
			citadela_theme_categories_list( ' ' );
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

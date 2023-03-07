<?php
/**
 * Template part for displaying posts
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( [ is_sticky() ? "sticky": "" ] ); ?>>
	<?php if ( !is_singular() && get_post_type() === 'post') : ?>
		<header class="entry-header">
			<h2 class="entry-title">
				<?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?>
			</h2>
			<div class="entry-meta">
				<?php
				citadela_theme_posted_on();
				citadela_theme_posted_by();
				?>
			</div><!-- .entry-meta -->
			<?php
				citadela_theme_leave_comment();
			?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<?php citadela_theme_post_thumbnail(); ?>

	<div class="entry-content">
		<?php

		if ( is_singular() ){

			the_content();

		}else{

			if( has_excerpt() ){
				$content = strip_shortcodes(get_the_excerpt());
				echo strip_tags( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}else{
				$content = strip_shortcodes(get_the_content());
				echo wp_trim_words( $content, 50, "..." ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			citadela_theme_edit_post_link();
		}

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'citadela' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php

			if ( is_singular( 'post' ) ) :
				citadela_theme_post_locations_list( ' | ' ); //optional param: locations separator
				citadela_theme_categories_list( ' | ' ); 	//optional param: categories separator
				citadela_theme_tags_list( ' | ' ); 			//optional param: tags separator
			else :
				citadela_theme_categories_list( ' ' ); 		//optional param: categories separator
			endif;

		?>
	</footer><!-- .entry-footer -->
</article>

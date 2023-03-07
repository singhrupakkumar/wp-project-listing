<?php
/**
 * Citadela Theme Custom template tags functions
 *
 */

if ( ! function_exists( 'citadela_theme_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function citadela_theme_posted_on( $styles = [] ) {

		$links_style = ! empty( $styles ) && isset( $styles['entryMetaLinksStyle'] ) ? $styles['entryMetaLinksStyle'] : "";

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$archive_year  = get_the_time('Y');
		$archive_month = get_the_time('m');
		$archive_day   = get_the_time('d');
		$archive_link = get_day_link( $archive_year, $archive_month, $archive_day );

		echo '<span class="posted-on">';
				/* translators: Posted on [post date]. */
        echo 	'<span class="posted-on-text">';
        esc_html_e( 'Posted on', 'citadela' );
        echo '</span> ';
		echo 	'<span class="posted-on-date"><a href="' . esc_url( $archive_link ) . '" rel="bookmark" ' . $links_style . '>' . $time_string . '</a></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</span>';

	}
endif;

if ( ! function_exists( 'citadela_theme_posted_on_data' ) ) :
	/**
	 * Returns data related to post date
	 */
	function citadela_theme_posted_on_data() {
		$archive_year  = get_the_time('Y');
		$archive_month = get_the_time('m');
		$archive_day   = get_the_time('d');
		$archive_link = get_day_link( $archive_year, $archive_month, $archive_day );

		return (object) [
			'date'	=> esc_html( get_the_date() ),
			'year' 	=> esc_html( $archive_year ),
			'month' => esc_html( $archive_month ),
			'day' 	=> esc_html( $archive_day ),
			'monthText' => (object) [
					'full' => esc_html( get_the_time('F') ),
					'short' => esc_html( get_the_time('M') ),
				],
			'link'	=> (object) [
					'year' 	=> esc_url( get_year_link( $archive_year ) ),
					'month' => esc_url( get_month_link( $archive_year, $archive_month ) ),
					'day' 	=> esc_url( get_day_link( $archive_year, $archive_month, $archive_day ) ),
				],
		];
	}
endif;

if ( ! function_exists( 'citadela_theme_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function citadela_theme_posted_by( $styles = [] ) {
		
		$links_style = ! empty( $styles ) && isset( $styles['entryMetaLinksStyle'] ) ? $styles['entryMetaLinksStyle'] : "";

		if( is_single() ){
			global $post;
			$author_name = get_the_author_meta( 'display_name', $post->post_author);
			$author_url = get_author_posts_url( $post->post_author);
		}else{
			$author_url = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
			$author_name = esc_html( get_the_author() );
		}
		echo '<span class="byline">';
				/* translators: [posted] by [post author]. */
        echo 	'<span class="byline-text">';
        esc_html_e( 'by', 'citadela' );
        echo '</span> ';
		echo 	'<span class="author vcard"><a class="url fn n" href="' . esc_url($author_url) . '" ' . $links_style . '>' . esc_html($author_name) . '</a></span>';
		echo '</span>';

	}
endif;

if ( ! function_exists( 'citadela_theme_categories_list' ) ) :
	/**
	 * Prints HTML with meta information for categories
	 */
	function citadela_theme_categories_list( $separator = '', $styles = [] ) {
		if ( 'post' === get_post_type() ) {
			global $post;
	    	$categories = get_the_terms($post->ID, 'category');
			$categories = apply_filters( 'the_category_list', $categories, $post->ID);
			
			if ( $categories && ! is_wp_error( $categories ) ) {

				$item_data_category_style = ! empty( $styles ) && isset( $styles['itemDataCategoryStyle'] ) ? $styles['itemDataCategoryStyle'] : "";

				$cat_links = [];
				foreach ($categories as $cat) {
	            	array_push(
	            		$cat_links, 
	            		'<a href="' . esc_url( get_term_link( $cat->term_id ) ) . '" rel="category tag" ' . $item_data_category_style . '>' . $cat->name . '</a>'
	            	);
					
				}

			}
			
			echo '<span class="cats-links">';
			/* translators: Posted in [categories list]. */
            echo 	'<span class="cats-text">';
            esc_html_e( 'Posted in', 'citadela' );
            echo '</span> ';
			echo 	'<span class="cats-list">' . implode($separator, $cat_links) . '</span>';
			echo '</span>';

		}
	}
endif;

if ( ! function_exists( 'citadela_theme_post_locations_list' ) ) :
	/**
	 * Prints HTML with meta information for categories
	 */
	function citadela_theme_post_locations_list( $separator = '', $styles = [] ) {
		if ( 'post' === get_post_type() ) {
			global $post;
	    	$locations = get_the_terms($post->ID, 'citadela-post-location');
			$locations = apply_filters( 'the_category_list', $locations, $post->ID);

	        if ( $locations && ! is_wp_error( $locations ) ) {
	        	
				$item_data_location_style = ! empty( $styles ) && isset( $styles['itemDataLocationStyle'] ) ? $styles['itemDataLocationStyle'] : "";

				$loc_links = [];
				foreach ($locations as $loc) {
	            	array_push(
	            		$loc_links, 
	            		'<a href="' . esc_url( add_query_arg( array( 'ctdl' => 'true', 'post_type' => 'post', 's' => '', 'category' => '', 'location' => $loc->slug ), get_home_url() ) ) . '" rel="location tag" ' . $item_data_location_style . '>' . $loc->name . '</a>'
	            	);
					
				}
	            echo '<span class="locs-links">';
	            echo 	'<span class="locs-text">';
	            esc_html_e( 'Location', 'citadela' );
	            echo 	'</span> ';
	            echo 	'<span class="locs-list">';
	            echo 		implode($separator, $loc_links);
	            echo 	'</span>';
	            echo '</span>';
	        }
		}
	}
endif;



if ( ! function_exists( 'citadela_theme_tags_list' ) ) :
	/**
	 * Prints HTML with meta information for tags .
	 */
	function citadela_theme_tags_list( $tags_sep = '' ) {
		if ( 'post' === get_post_type() ) {
			$tags_list = get_the_tag_list( '', $tags_sep );
			if ( $tags_list ) {
				echo '<span class="tags-links">';
				/* translators: Tags assigned to post. */
                echo 	'<span class="tags-text">';
                esc_html_e( 'Tagged', 'citadela' );
                echo '</span> ';
				echo 	'<span class="tags-list">' . $tags_list . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</span>';
			}
		}
	}
endif;

if ( ! function_exists( 'citadela_theme_leave_comment' ) ) :
	/**
	 * Prints HTML with Leave Comment information
	 */
	function citadela_theme_leave_comment( $styles = [] ) {
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {

			$post_title = get_the_title();
			$number = get_comments_number();


			$leave_comment =
				'<span class="comments-number">0</span> '.
				'<span class="comments-text">'.
					sprintf(
						wp_kses(
							/* translators: %s: post title */
							__( 'Comments<span class="screen-reader-text"> on %s</span>', 'citadela' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						$post_title
					).
				'</span>';

			$one_comment =
				'<span class="comments-number">1</span> '.
				/* translators: %s: post title */
				'<span class="comments-text">' . sprintf( __( 'Comment<span class="screen-reader-text"> on %s', 'citadela' ), $post_title ) . '</span>';

			$more_comments =
				'<span class="comments-number">' . $number . '</span> '.
				/* translators: %s: post title */
				'<span class="comments-text">' . sprintf( __( 'Comments<span class="screen-reader-text"> on %s', 'citadela' ), $post_title ) . '</span>';

			$comments_link_style = ! empty( $styles ) && isset( $styles['commentsLinkStyle'] ) ? $styles['commentsLinkStyle'] : "";

			echo '<span class="comments-link" ' . $comments_link_style . '>';
				comments_popup_link(
					$leave_comment,
					$one_comment,
					$more_comments
				);
			echo '</span>';
		}
	}
endif;


if ( ! function_exists( 'citadela_theme_edit_post_link' ) ) :
	/**
	 * Prints HTML with Edit Post link
	 */
	function citadela_theme_edit_post_link() {
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'citadela' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'citadela_theme_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function citadela_theme_post_thumbnail() {
		if ( post_password_required() ) {
			return;
		}

		global $post;

		if ( is_singular() ) :

			if ( is_attachment() && wp_attachment_is_image( $post->ID ) ) :
			?>
				<div class="post-thumbnail">
					<a href="<?php echo esc_url( get_the_post_thumbnail_url( $post->ID ) ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
						<?php echo wp_get_attachment_image($post->ID, 'large'); ?>
					</a>
				</div><!-- .post-thumbnail -->

			<?php else : ?>

				<?php 
				$post_thumbnail_url = get_the_post_thumbnail_url( $post->ID );
				if ( $post_thumbnail_url ) :
					$use_post_link = apply_filters( 'ctdl_use_post_featured_image_link', true );
				?>
				
				<div class="post-thumbnail">
					
					<?php if( $use_post_link ) :  ?>
					<a href="<?php echo esc_url( get_the_post_thumbnail_url( $post->ID ) ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
					<?php endif; ?>

					<?php
					the_post_thumbnail( 'large', array(
						'alt' => the_title_attribute( array(
							'echo' => false,
						) ),
					) );
					?>

					<?php if( $use_post_link ) :  ?>
					</a>
					<?php endif; ?>
				</div><!-- .post-thumbnail -->
				<?php endif; ?>
					
			<?php endif; ?>

		<?php else : ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
					<?php
					the_post_thumbnail( 'post-thumbnail', array(
						'alt' => the_title_attribute( array(
							'echo' => false,
						) ),
					) );
					?>
				</a>
			</div>

		<?php
		endif; // End is_singular().
	}
endif;

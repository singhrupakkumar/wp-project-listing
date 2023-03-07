<?php

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

$item_post = get_post();

?>

<div id="item-reviews" class="comments-area item-reviews">

	<?php
	
	

	if ( have_comments() ) :
			?>
			<h2 class="comments-title">
				<?php
				$citadela_theme_comment_count = intval( get_post_meta( $item_post->ID, '_citadela_ratings_count', true ) );
				if ( '1' === $citadela_theme_comment_count ) {
					$commentsTitle = sprintf(
						/* translators: 1: title. */
						esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'citadela-directory' ),
						'<span>' . get_the_title() . '</span>'
					);
				} else {
					$commentsTitle = sprintf(
						/* translators: 1: comment count number, 2: title, 3: &ldquo; left quote, 4: &rdquo; right quote */
						esc_html( _nx( '%1$s review for &ldquo;%2$s&rdquo;', '%1$s reviews for &ldquo;%2$s&rdquo;', $citadela_theme_comment_count, 'count of reviews title', 'citadela-directory' ) ),
						number_format_i18n( $citadela_theme_comment_count ),
						'<span>' . get_the_title() . '</span>'
					);
	            }
	            echo $commentsTitle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</h2><!-- .comments-title -->

			<ol class="comment-list">
				<?php
				wp_list_comments( array(
					'type' 			=> 'item_review',
					'style'      	=> 'ol',
					'avatar_size'	=> 48,
					'callback'		=> [ 'Citadela\Directory\ItemReviews', 'render_review' ],
				));

				?>
			</ol><!-- .comment-list -->

			<?php
			the_comments_navigation([
				'prev_text' => esc_html__('Older reviews', 'citadela-directory'),
				'next_text' => esc_html__('Newer reviews', 'citadela-directory'),
			]);

	endif; // Check for have_comments().

	$comment_form = [
		'title_reply' 		=> esc_html__( 'Leave a review', 'citadela-directory'),
		'title_reply_to' 	=> esc_html__( 'Respond to review', 'citadela-directory'),
		'label_submit' 		=> esc_html__( 'Submit review', 'citadela-directory'),
		'must_log_in'		=> esc_html__( 'You must be logged in to post a review.', 'citadela-directory'),
		'logged_in_as'		=> '',
		'action' 			=> add_query_arg( [ 'item_reviews_form' => '1' ], home_url( '/wp-comments-post.php' ) ),
		'submit_button'		=> sprintf( '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" data-submit-review-text="%4$s" data-submit-reply-text="%5$s"/>',
								"submit-review-button",
								"submit-review-button",
								"submit",
								esc_html__( 'Submit review', 'citadela-directory' ),
								esc_html__( 'Submit reply', 'citadela-directory' )
								),
	];

	// Check if Citadela Pro Comments Extension is active to add additional information
	$label_text = esc_html__( 'Your review', 'citadela-directory' );
	$reply_text = esc_html__( 'Your reply to review', 'citadela-directory' );

	$help_text = '';
	if ( defined('CITADELA_PRO_PLUGIN') ) {
		$comments_extension = get_option( 'citadela_pro_comments_extension', true );
		if( $comments_extension && isset($comments_extension['show']) && $comments_extension['show'] ){
			$help_text = Citadela\Pro\Features::$settings['comments-extension']->value('comment_help');
			if( $help_text ){
				$help_text = sprintf( '<div class="citadela-comments-extension-help">%s</div>', $help_text );
			}
			$comment_label = Citadela\Pro\Features::$settings['comments-extension']->value('comment_label');
			if( $comment_label ){
				$label_text = $comment_label;
			}
		}
	}
	$default_comment_textarea = sprintf(
            '<p class="comment-form-comment review-text">%s %s</p>%s',
            sprintf(
                '<label for="comment"><span class="review-label">%s</span><span class="reply-label">%s</span>&nbsp;<span class="required">*</span></label>',
                $label_text,
                $reply_text
            ),
            '<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>',
            $help_text
        );
	
	$comment_form['comment_field'] = apply_filters( 'citadela_directory_add_item_rating_stars_selection', $default_comment_textarea );

	comment_form( $comment_form );

	
	?>

</div><!-- #comments -->

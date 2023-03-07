<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

	<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
		<footer class="comment-meta">
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment->comment_author_email, 48, '' ); ?>
				<?php 
				//try to get WP user by email for more data about comment author
					$user = get_user_by( 'email', $comment->comment_author_email );
					if( $user ){
						$author_name = $user->display_name;
					}else{
						$author_name = get_comment_author();
					}
				?>
				<b class="fn author"><?php echo esc_html( $author_name ); ?></b>			
			</div><!-- .comment-author -->
			<div class="comment-metadata">
				<?php if( $rating ): ?>
					<div class="comment-rating <?php if( $review_data['rating_stars_color'] ) echo 'custom-color'; ?>">
						<?php
							for ($i = 1; $i <= 5 ; $i++) { 
								$style = 'far';
								if( $i <= $rating ){
									$style = 'fas';
								}
									?><i class="<?php echo esc_attr( $style ); ?> fa-star" <?php if( $review_data['rating_stars_color'] ) echo 'style="color: ' . esc_attr( $review_data['rating_stars_color'] ) .';"'; ?>></i><?php
									
							}
						?>
					</div>

				<?php else: ?>
					<div class="item-owner"><span><?php esc_html_e( 'Owner', 'citadela-directory' );?></span></div>
				<?php endif; ?>

				<div class="comment-date">
					<?php echo get_comment_date( '', $comment ); ?>
				</div>
			</div>
		</footer>
		<div class="comment-content">
			<?php echo comment_text(); ?>
		</div>
		<?php if( $can_reply ) : ?>
			<div class="reply">
				<?php comment_reply_link( 
					array_merge( 
						$args, 
						array( 
			                'add_below' => "div-comment", 
			                'max_depth'     => 2,
			                'depth'     => 1,
				        )
			    ), $comment ); ?>
			</div>
		<?php endif; ?>

	</article>

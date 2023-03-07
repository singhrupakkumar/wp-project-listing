<?php
	$permalink = get_permalink( $post->ID );
	$featured_image = wp_get_attachment_image(get_post_thumbnail_id( $post->ID ), 'large');

	//get post categories
	$categories = get_the_terms($post->ID, 'category');
    $categories = apply_filters( 'the_category_list', $categories, $post->ID);

    //get post locations
    $locations = get_the_terms($post->ID, 'citadela-post-location');
	$locations = apply_filters( 'the_category_list', $locations, $post->ID);

	/* prepare classes based on features and meta */
	$hasThumbnail = get_the_post_thumbnail_url($post->ID, 'large') && $template_args['showFeaturedImage']
		? 'has-thumbnail'
		: false;
	$hasCategories = !empty($categories) && $template_args['showCategories']
		? 'has-categories'
        : false;
    $hasLocations = !empty($locations) && $template_args['showLocations']
		? 'has-locations'
        : false;
	$excerpt = has_excerpt() ?
		strip_tags( strip_shortcodes(get_the_excerpt()) ) :
		wp_trim_words( strip_shortcodes(get_the_content()), 50, "..." );
	$hasDescription = $excerpt && $template_args['showDescription']
		? 'has-description'
		: false;

	$hasDate = $template_args['showDate'] ? 'has-date' : false;

	$isSticky = is_sticky($post->ID)
        ? 'sticky'
        : false;

    $hasFeatureClasses = implode(' ', array_filter([$hasThumbnail, $hasDate, $hasDescription, $hasCategories, $hasLocations, $isSticky]));

    $dateData = CitadelaDirectory::getInstance()->posted_on_data();

	$layout = $template_args['layout'];
?>

<article class="citadela-article <?php echo esc_attr( $hasFeatureClasses ); ?>" <?php echo $styles['articleStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="item-content" <?php echo $styles['itemContentStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if($hasThumbnail) : ?>
		<div class="item-thumbnail">
			<?php if( $layout === "box" && $template_args['showDate'] ) : ?>
				<span class="item-date">
					<a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark" <?php echo $styles['dateStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<span class="item-date-day"><?php echo esc_html( $dateData->day ); ?></span><span class="item-date-month"><?php echo esc_html( $dateData->monthText->short ); ?></span><span class="item-date-year"><?php echo esc_html( $dateData->year ); ?></span>
					</a>
				</span>
			<?php endif; ?>
			<a href="<?php echo esc_url( $permalink ); ?>"><img src="<?php echo esc_url( get_the_post_thumbnail_url($post->ID, 'citadela_item_thumbnail') ); ?>" class="item-image"></a>
		</div>
		<?php endif; ?>

		<div class="item-body">
			<div class="item-title">
				<?php if( $layout === "box" && ! $hasThumbnail && $template_args['showDate'] ) : ?>
					<span class="item-date">
						<a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark" <?php echo $styles['dateStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<span class="item-date-day"><?php echo esc_html( $dateData->day ); ?></span><span class="item-date-month"><?php echo esc_html( $dateData->monthText->short ); ?></span><span class="item-date-year"><?php echo esc_html( $dateData->year ); ?></span>
						</a>
					</span>
				<?php endif; ?>
				<a href="<?php echo esc_url( $permalink ); ?>">
					<div class="item-title-wrap">
						<div class="post-title">
							<?php 
								echo esc_html( $post->post_title );
								if( $isSticky ):
								?>
									<span class="featured" <?php echo $styles['stickyStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></span>
								<?php
								endif;
							?>
						</div>
					</div>
				</a>
				<?php if( $layout === "list" && ! $hasDescription && $template_args['showDate'] ) : ?>
					<span class="item-date">
						<a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark"  <?php echo $styles['dateStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $dateData->date ); ?></a>
					</span>
				<?php endif; ?>
			</div>
			<?php if($hasDescription ) : ?>
			<div class="item-text">
				<div class="item-description">
					<p>
						<?php if( $layout === "list" && $template_args['showDate'] ) : ?>
							<span class="item-date">
								<a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark" <?php echo $styles['dateStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $dateData->date ); ?></a>
							</span>
						<?php endif; ?>
						<?php echo esc_html( $excerpt ); ?>
					</p>
					<a href="<?php echo esc_url( $permalink ); ?>" class="more"><?php esc_html_e('View more', 'citadela-directory'); ?></a>
				</div>
			</div>
			<?php endif; ?>
			<div class="item-footer" <?php echo $styles['footerStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if( $hasLocations ): ?>
				<div class="item-data location" <?php echo $styles['itemDataStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="label"><?php esc_html_e('Location', 'citadela-directory'); ?></span>
					<span class="values">
						<?php foreach ($locations as $loc) : ?>
						<a 
							href="<?php echo esc_url( add_query_arg( array(
									    'ctdl' 		=> 'true',
									    'post_type' => 'post',
									    's' 		=> '',
									    'category' 	=> '',
									    'location' 	=> $loc->slug,
									), get_home_url() ) );
								?>" 
							class="value" <?php echo $styles['itemDataLocationStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $loc->name ); ?></a>
						<?php endforeach; ?>
					</span>
				</div>
				<?php endif; ?>
				<?php if( $hasCategories ): ?>
				<div class="item-data categories" <?php echo $styles['itemDataStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="label"><?php esc_html_e('Categories', 'citadela-directory'); ?></span>
					<span class="values">
						<?php foreach ($categories as $cat) : ?>
						<a href="<?php echo esc_url( get_term_link($cat) ); ?>" class="value" <?php echo $styles['itemDataCategoryStyle']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $cat->name ); ?></a>
						<?php endforeach; ?>
					</span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

</article>


<?php

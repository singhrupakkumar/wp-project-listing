<?php
	$permalink = get_permalink( $post->ID );

	//get posts terms
	$categories = [];
	$locations = [];

	if( $post->post_type === 'post' ){
		$categories = get_the_terms($post->ID, 'category');
	}elseif ($post->post_type === 'product'){
		$categories = get_the_terms($post->ID, 'product_cat');
	}elseif ($post->post_type === 'citadela-item'){
		$categories = get_the_terms($post->ID, 'citadela-item-category');
		$locations = get_the_terms($post->ID, 'citadela-item-location');
	}
	
	$categories = apply_filters( 'the_category_list', $categories, $post->ID);
	$locations = apply_filters( 'the_category_list', $locations, $post->ID);
	
	$thumbnail = get_the_post_thumbnail_url($post->ID, 'thumbnail');
	/* prepare classes based on features and meta */
	$hasThumbnail = $thumbnail && $template_args['showFeaturedImage']
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

	$hasDate = $post->post_type == 'post' && $template_args['showDate'] ? 'has-date' : false;

	$isSticky = is_sticky($post->ID)
        ? 'sticky'
        : false;

    $hasFeatureClasses = implode(' ', array_filter([$hasThumbnail, $hasDate, $hasDescription, $hasCategories, $hasLocations, $isSticky]));

    $dateData = CitadelaDirectory::getInstance()->posted_on_data();

	$layout = $template_args['layout'];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( [ "citadela-article", esc_attr( $hasFeatureClasses ) , is_sticky() ? "sticky": "" ] ); ?>>

	<div class="item-content">
		<?php if($hasThumbnail) : ?>
		<div class="item-thumbnail">
			<?php if( $layout === "box" && $hasDate ) : ?>
				<span class="item-date">
					<a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark">
						<span class="item-date-day"><?php echo esc_html( $dateData->day ); ?></span><span class="item-date-month"><?php echo esc_html( $dateData->monthText->short ); ?></span><span class="item-date-year"><?php echo esc_html( $dateData->year ); ?></span>
					</a>
				</span>
			<?php endif; ?>
			<a href="<?php echo esc_url( $permalink ); ?>"><img src="<?php echo esc_url( $thumbnail ); ?>" class="item-image"></a>
		</div>
		<?php endif; ?>

		<div class="item-body">
			<div class="item-title">
				<?php if( $layout === "box" && ! $hasThumbnail && $hasDate ) : ?>
					<span class="item-date">
						<a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark">
						<span class="item-date-day"><?php echo esc_html( $dateData->day ); ?></span><span class="item-date-month"><?php echo esc_html( $dateData->monthText->short ); ?></span><span class="item-date-year"><?php echo esc_html( $dateData->year ); ?></span>
						</a>
					</span>
				<?php endif; ?>
				<a href="<?php echo esc_url( $permalink ); ?>">
					<div class="item-title-wrap">
						<div class="post-title"><?php echo esc_html( $post->post_title ); ?></div>
					</div>
				</a>
				<?php if( $layout === "list" && ! $hasDescription && $hasDate ) : ?>
					<span class="item-date"><a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark"><?php echo esc_html( $dateData->date ); ?></a></span>
				<?php endif; ?>
			</div>
			<?php if($hasDescription ) : ?>
			<div class="item-text">
				<div class="item-description">
					<p>
						<?php if( $layout === "list" && $hasDate ) : ?>
							<span class="item-date"><a href="<?php echo esc_url( $dateData->link->day ); ?>" rel="bookmark"><?php echo esc_html( $dateData->date ); ?></a></span>
						<?php endif; ?>
						<?php echo esc_html( $excerpt ); ?>
					</p>
					<a href="<?php echo esc_url( $permalink ); ?>" class="more"><?php esc_html_e('View more', 'citadela-directory'); ?></a>
				</div>
			</div>
			<?php endif; ?>
			<div class="item-footer">
				<?php if( $hasLocations ): ?>
				<div class="item-data location">
					<span class="label"><?php esc_html_e('Location', 'citadela-directory'); ?></span>
					<span class="values">
						<?php foreach ($locations as $loc) : ?>
						<a 
							href="<?php echo esc_url( get_term_link($loc) ); ?>" 
							class="value"><?php echo esc_html( $loc->name ); ?></a>
						<?php endforeach; ?>
					</span>
				</div>
				<?php endif; ?>
				<?php if( $hasCategories ): ?>
				<div class="item-data categories">
					<span class="label"><?php esc_html_e('Categories', 'citadela-directory'); ?></span>
					<span class="values">
						<?php foreach ($categories as $cat) : ?>
						<a href="<?php echo esc_url( get_term_link($cat) ); ?>" class="value"><?php echo esc_html( $cat->name ); ?></a>
						<?php endforeach; ?>
					</span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

</article>


<?php

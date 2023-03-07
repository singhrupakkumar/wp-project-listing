<?php
	$allowed_html = array(
		'a' => array(
				'href' => array(),
        		'title' => array(),
        		'target' => array(),
        		'follow' => array()
        	),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
		'i' => array(),
	);

	$is_woocommerce = Citadela_Theme::get_instance()->is_active_woocommerce();
	$is_citadela_pro = Citadela_Theme::get_instance()->is_active_pro_plugin();

	$header_class = [];
/*
	Latest posts page selected as front page in Reading settings do not use page title at all
	if necessary to acces only this page, use condition
	( is_front_page() && is_home() )
*/

	if (function_exists('tribe_is_event_query') && tribe_is_event_query()) {
	} elseif ( is_front_page() && !is_home()) {
		/*
		*	Static Page homepage defined in Reading Settings
		*/

		$post_id = get_the_ID();
		//check if page shows title
		$hide_title = get_post_meta( $post_id, '_citadela_hide_page_title', true );
		if(!$hide_title) :
			$title_text = get_the_title();
			?>
			<div class="page-title standard">
				<header class="entry-header">
					<div class="entry-header-wrap">
						<h1 class="entry-title"><?php echo esc_html($title_text); ?></h1>
					</div>
				</header>
			</div>
			<?php
		endif;

	} elseif ( is_home() && !is_front_page() ) {
		/*
		*	Blog Page defined in Reading Settings
		*/

		if( ! $is_citadela_pro ) : 
			//free theme, show page title, with pro plugin is title displayed via plugin
			$post_id = get_option('page_for_posts'); //id of page defined as Posts Page in Reading settings
			$blog_page_post = get_post($post_id);
			$title_text = $blog_page_post->post_title;
			?>

			<div class="page-title standard">
				<header class="entry-header">
					<div class="entry-header-wrap">
						<h1 class="entry-title"><?php echo esc_html($title_text); ?></h1>
					</div>
				</header>
			</div>

		<?php endif; 

	} else {
		// other types
		if( is_page() ){
			/*
			*	Standard Page
			*/

			$post_id = get_the_ID();
			//check if page shows title
			$hide_title = get_post_meta( $post_id, '_citadela_hide_page_title', true );
			if(!$hide_title) :
				$title_text = get_the_title();
				?>

				<div class="page-title standard">
				<header class="entry-header">
					<div class="entry-header-wrap">
						<h1 class="entry-title"><?php echo esc_html($title_text); ?></h1>
					</div>
				</header>
			</div>

				<?php
			endif;

		}elseif( is_single() ){
			/*
			*	Single posts pages
			*/

				if( get_post_type() === 'post' ){
					/*
					*	Blog Post page
					*/
					$title_text = get_the_title();

					?>

					<div class="page-title standard">
						<header class="entry-header">
							<div class="entry-header-wrap">
								<h1 class="entry-title"><?php echo esc_html($title_text); ?></h1>
								<div class="entry-meta">
									<?php
									citadela_theme_posted_on();
									citadela_theme_posted_by();
									?>
								</div>
							</div>
						</header>
					</div>

					<?php

				}else{
					/*
					*	Other single post pages
					*/
					$title_text = get_the_title();
					?>

					<div class="page-title standard">
						<header class="entry-header">
							<div class="entry-header-wrap">
								<h1 class="entry-title"><?php echo esc_html($title_text); ?></h1>
							</div>
						</header>
					</div>

					<?php
				}

		}elseif( is_tax() ){

			if( !is_tax('citadela-item-category') && !is_tax('citadela-item-location') ){
				$title_text = single_term_title('', false);
				$description = term_description();
				if( $description ) { $header_class[] = 'has-subtitle'; }
				?>
				<div class="page-title standard">
					<header class="entry-header <?php echo implode( ' ', $header_class ); ?>">
						<div class="entry-header-wrap">
							<?php 
								// add breadcrumbs for woocommerce product category page
								if ( $is_woocommerce ) {
									woocommerce_breadcrumb();
								}
							?>

							<h1 class="entry-title"><?php echo esc_html($title_text); ?></h1>

							<?php if( $description ) : ?>
							<div class="entry-subtitle">
								<p class="ctdl-subtitle">
									<?php echo wp_kses($description, $allowed_html); ?>
								</p>
							</div>
							<?php endif; ?>

						</div>

					</header>
				</div>
				<?php
			}

		}elseif( is_archive() ){
			/*
			*	Archives pages
			*/

			if( is_category() ){
				/*
				*	Category archives page
				*/
				$title_prefix = '<span class="main-text">' . esc_html__('Category archives: ', 'citadela') . '</span>';
				$title_text = '<span class="main-data">' . single_cat_title('', false) . '</span>';
				$description = get_the_archive_description();
				if( $description ) { $header_class[] = 'has-subtitle'; }
				?>
				<div class="page-title standard">
					<header class="entry-header <?php echo implode( ' ', $header_class ); ?>">
						<div class="entry-header-wrap">

							<h1 class="entry-title"><?php echo wp_kses_post( $title_prefix . $title_text );  ?></h1>

							<?php if( $description ) : ?>
							<div class="entry-subtitle">
								<p class="ctdl-subtitle">
									<?php echo wp_kses($description, $allowed_html); ?>
								</p>
							</div>
							<?php endif; ?>

						</div>
					</header>
				</div>
				<?php
			}


			if (is_author()){
				/*
				*	Author archives page
				*/
				$author_url = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
				$author_name = esc_html( get_the_author() );
				$title_prefix = '<span class="main-text">' . esc_html__('Author archives: ', 'citadela') . '</span>';
				$title_text = '<span class="author vcard main-data"><a class="url fn n" href="' . $author_url . '">' . $author_name . '</a></span>';
				$description = get_the_archive_description();
				if( $description ) { $header_class[] = 'has-subtitle'; }
				?>
				<div class="page-title standard">
					<header class="entry-header <?php echo implode( ' ', $header_class ); ?>">
						<div class="entry-header-wrap">

							<h1 class="entry-title"><?php echo wp_kses_post( $title_prefix . $title_text ); ?></h1>

							<?php if( $description ) : ?>
								<div class="entry-subtitle">
									<p class="ctdl-subtitle">
										<?php echo wp_kses($description, $allowed_html); ?>
									</p>
								</div>
							<?php endif; ?>

						</div>
					</header>
				</div>
				<?php
			}

			if( is_tag() ){
				/*
				*	Tag archives page
				*/
				$title_prefix = '<span class="main-text">' . esc_html__('Tag archives: ', 'citadela') . '</span>';
				$title_text = '<span class="main-data">' . single_tag_title('', false) . '</span>';
				$description = get_the_archive_description();
				if( $description ) { $header_class[] = 'has-subtitle'; }
				?>
				<div class="page-title standard">
					<header class="entry-header <?php echo implode( ' ', $header_class ); ?>">
						<div class="entry-header-wrap">

							<h1 class="entry-title"><?php echo wp_kses_post( $title_prefix . $title_text ); ?></h1>

							<?php if( $description ) : ?>
								<div class="entry-subtitle">
									<p class="ctdl-subtitle">
										<?php echo wp_kses($description, $allowed_html); ?>
									</p>
								</div>
							<?php endif; ?>

						</div>
					</header>
				</div>
				<?php
			}

			if( is_date() ){
				/*
				*	Date archives page
				*/
				$title_prefix = '<span class="main-text">' . esc_html__('Date archives: ', 'citadela') . '</span>';
				$title_text = '<span class="main-data">' . get_the_date() . '</span>';
				?>
				<div class="page-title standard">
					<header class="entry-header">
						<div class="entry-header-wrap">
							<h1 class="entry-title"><?php echo wp_kses_post( $title_prefix . $title_text ); ?></h1>
						</div>
					</header>
				</div>
				<?php
			}

			if ( $is_woocommerce && is_shop() ){
				/*
				*	Woocommerce shop page
				*/
				$post_id = get_option( 'woocommerce_shop_page_id' );
				$hide_title = get_post_meta( $post_id, '_citadela_hide_page_title', true );
				?>
				
				<?php if ( ! $hide_title ) : ?>
					<div class="page-title standard">
						<header class="entry-header">
							<div class="entry-header-wrap">
								<h1 class="entry-title"><?php woocommerce_page_title() ?></h1>
							</div>
						</header>
					</div>
				<?php endif;
			}
			
		}elseif( is_404() ){
			/*
			*	404 Nothing Found Page
			*/
			?>
			<div class="page-title standard">
				<header class="entry-header">
					<div class="entry-header-wrap">
						<h1 class="entry-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'citadela') ; ?></h1>
					</div>
				</header>
			</div>

			<?php

		}elseif( is_search() ){
			/*
			*	Search results page
			*/
			$search_query = '<span class="main-data">' . get_search_query() . '</span>';
			$title_text = '<span class="main-text">' . esc_html__('Search results for: ', 'citadela') . '</span>' . $search_query;
			?>
			<div class="page-title standard">
				<header class="entry-header">
					<div class="entry-header-wrap">
						<h1 class="entry-title"><?php echo wp_kses_post( $title_text ); ?></h1>
					</div>
				</header>
			</div>

			<?php
		}
	}
?>
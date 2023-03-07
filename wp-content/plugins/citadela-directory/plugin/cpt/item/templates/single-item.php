<?php

	get_header();

	$qo = get_queried_object();
	$plugin = CitadelaDirectory::getInstance();
	$pageType = 'citadela-item';
	$meta = CitadelaDirectoryFunctions::getItemMeta($post->ID);

	$item_detail_options = get_option('citadela_directory_item_detail');

	//prepare featured image
	$featuredImage = array();
	$featuredImageId = get_post_thumbnail_id();
	$featuredImageUrl = '';
	if($featuredImageId){
		$featuredImage = wp_get_attachment_image_src($featuredImageId, 'full');
		$featuredImageUrl = $featuredImage[0];
	}

    $page_id = $plugin->ItemPageLayout_instance->ignore_special_page ? $post->ID : CitadelaDirectoryLayouts::getSpecialPageId('single-item');

	$hideTitle = get_post_meta( $page_id, '_citadela_hide_page_title', true );

	//Reviews
	$item_reviews_enabled = Citadela\Directory\ItemReviews::$enabled;
	$item_rating = Citadela\Directory\ItemReviews::get_post_rating( $post->ID );
?>

    <?php
		if(!$hideTitle){
	    	include_once dirname( __FILE__ ) . '/parts/single-item-page-title.php';
		}
	?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<article id="post-<?php echo esc_attr( $post->ID ); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/LocalBusiness">
				
				<meta itemprop="name" content="<?php echo esc_html( $post->post_title ); ?>">
				<?php if ($featuredImageUrl) : ?>
					<meta itemprop="image" content="<?php echo esc_url( $featuredImageUrl ); ?>">
				<?php endif; ?>
				<?php if ($meta->address) : ?>
					<meta itemprop="address" content="<?php echo esc_html( $meta->address ); ?>">
				<?php endif; ?>

				<div class="entry-content" itemprop="description">
					<?php
						if( $plugin->ItemPageLayout_instance->ignore_special_page ){
							the_content();
						}else{
							do_action('ctdl_special_page_content');
						}
					?>
					
				</div>
			</article>

			<?php
			
			if( $item_reviews_enabled ) :

				do_action( 'citadela_directory_item_reviews_comments_template' );
			
			else : 
			
				// If comments are open or we have at least one comment, load up the comment template.
				if ( !is_attachment() ) :

					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				endif;

			endif;
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
	if ( is_active_sidebar( 'item-sidebar' ) && 'half-layout-template' !== get_post_meta( $page_id, '_wp_page_template', true ) ) :
?>
	<aside id="secondary" class="item-widget-area widget-area right-widget-area">
		<div class="widget-area-wrap">
			<?php dynamic_sidebar( 'item-sidebar' ); ?>
		</div>
	</aside>
<?php
	endif;
?>

<?php
get_footer();

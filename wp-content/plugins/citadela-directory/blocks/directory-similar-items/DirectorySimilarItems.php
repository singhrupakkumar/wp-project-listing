<?php

namespace Citadela\Directory\Blocks;

class DirectorySimilarItems extends Block {

    protected static $slug = 'directory-similar-items';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );
        
        

        $blockTitle = $attributes['title'];

        $args = [
            'posts_per_page' => isset( $attributes['numberOfItems'] ) ? $attributes['numberOfItems'] : -1 ,
            'order' => $attributes['itemsOrder'],
            'orderby' => $attributes['itemsOrderBy'],
            'only_featured' => $attributes['onlyFeatured'],
            'featured_first' => $attributes['featuredFirst'],
            'blockLayout' => $attributes['layout'],
            'blockSize' => $attributes['size'],
            'showItemFeaturedImage' => $attributes['showItemFeaturedImage'],
            'showItemSubtitle' => $attributes['showItemSubtitle'],
            'showItemDescription' => $attributes['showItemDescription'],
            'showItemAddress' => $attributes['showItemAddress'],
            'showItemWeb' => $attributes['showItemWeb'],
            'showItemCategories' => $attributes['showItemCategories'],
            'showItemLocations' => $attributes['showItemLocations'],
            'useCarousel' => $attributes['useCarousel'],
            'carouselNavigation' => $attributes['carouselNavigation'],
            'carouselPagination' => $attributes['carouselPagination'],
            'carouselAutoplay' => $attributes['carouselAutoplay'],
            'carouselAutoHeight' => $attributes['carouselAutoHeight'],
            'carouselAutoplayDelay' => $attributes['carouselAutoplayDelay'],
            'carouselLoop' => $attributes['carouselLoop'],
            'borderWidth' => $attributes['borderWidth'],
            'borderColor' => $attributes['borderColor'],
            'backgroundColor' => $attributes['backgroundColor'],
            'textColor' => $attributes['textColor'],
            'decorColor' => $attributes['decorColor'],
            'carouselColor' => $attributes['carouselColor'],
            'showRating' => $attributes['showRating'],
            'ratingColor' => $attributes['ratingColor'],
            'onlyFeaturedCategory' => $attributes['onlyFeaturedCategory'],
            'refPost' => $attributes['refPost'],
            'similarByAuthor' => $attributes['similarByAuthor'],
            'similarByCategory' => $attributes['similarByCategory'],
            'similarByLocation' => $attributes['similarByLocation'],
            'showReferencePost' => $attributes['showReferencePost'],
            'imageSize' => $attributes['imageSize'],
        ];

        if( is_singular('citadela-item') ){
            // on item detail page set as reference post ID of already opened item page
            $ref_post = get_post();
            $args['refPost'] = [ $ref_post->ID ];
        }else{
            // we are on another page
            if( empty( $args['refPost'] ) ){
                return;
            }
            $ref_post = get_post( $args['refPost'][0] );
        }

        $useResponsive = isset( $attributes['useResponsiveOptionsImageHeight'] ) && $attributes['useResponsiveOptionsImageHeight'];

        if( $useResponsive ){
            self::enqueueFrontendScript();
        }

        //set grid type
        $gridType = "grid-type-1";
        if( $args['blockLayout'] == "list"){
            $gridType = "grid-type-3";
        }
        
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "layout-{$args['blockLayout']}";
        $classes[] = "size-{$args['blockSize']}";
        $classes[] = $gridType;
        $classes[] = "image-size-{$attributes['imageSize']}";

        if( $attributes['proportionalImageHeight'] ){
            $classes[] = 'proportional-image-height'; 
        }else{
            $classes[] = 'image-position-'.str_replace(' ', '-', $attributes['imageObjectPosition']);
            $classes[] = "{$attributes['imageHeightType']}-image-height"; 
        }

        if( $useResponsive ){
            $classes[] = "responsive-options";
            $classes[] = "loading-content";
            
            $data = [
                "desktop" => [],
                "mobile" => [],
            ];

            $unit = $attributes['imageHeightUnit'];
            $height = isset( $attributes['imageHeight'] ) && $attributes['imageHeight'] ? $attributes['imageHeight'].$unit : '';
            if( $height && ! $attributes['proportionalImageHeight'] && $attributes['imageHeightType'] == 'custom' ) $data['desktop']["imageHeight"] = $height;
            if( ! $attributes['proportionalImageHeight'] ){
                $data['desktop']["imageHeightType"] = $attributes['imageHeightType'];
                $data['desktop']["imageObjectPosition"] = 'image-position-'.str_replace(' ', '-', $attributes['imageObjectPosition']);
            }
            $data['desktop']["proportionalImageHeight"] = $attributes['proportionalImageHeight'];
            

            $unit = $attributes['imageHeightUnitMobile'];
            $height = isset( $attributes['imageHeightMobile'] ) && $attributes['imageHeightMobile'] ? $attributes['imageHeightMobile'].$unit : '';
            if( $height && ! $attributes['proportionalImageHeightMobile'] && $attributes['imageHeightTypeMobile'] == 'custom' ) $data['mobile']["imageHeight"] = $height;
            if( ! $attributes['proportionalImageHeightMobile'] ) {
                $data['mobile']["imageHeightType"] = $attributes['imageHeightTypeMobile'];
                $data['mobile']["imageObjectPosition"] = 'image-position-'.str_replace(' ', '-', $attributes['imageObjectPositionMobile']);
            }
            $data['mobile']["proportionalImageHeight"] = $attributes['proportionalImageHeightMobile'];
        }

        $imgStyles = [];
        if( ! $attributes['proportionalImageHeight'] && $attributes['imageHeightType'] == 'custom' && ( isset( $attributes['imageHeight'] ) && $attributes['imageHeight'] ) ) $imgStyles[] = "height:{$attributes['imageHeight']}{$attributes['imageHeightUnit']};";
        $imgStylesText = implode( ' ', $imgStyles);

        if( $args['showItemFeaturedImage'] ) $classes[] = "show-item-featured-image";
        if( $args['showItemSubtitle'] ) $classes[] = "show-item-subtitle";
        if( $args['showItemDescription'] ) $classes[] = "show-item-description";
        if( $args['showItemAddress'] ) $classes[] = "show-item-address";
        if( $args['showItemWeb'] ) $classes[] = "show-item-web";
        if( $args['showItemCategories'] ) $classes[] = "show-item-categories";
        if( $args['showItemLocations'] ) $classes[] = "show-item-locations";
        if( $args['showRating'] ) $classes[] = "show-item-rating";
        
        if ( $activeProPlugin ){
            $classes[] = "border-{$args['borderWidth']}";
            if( $args['textColor'] ) $classes[] = "custom-text-color";
            if( $args['decorColor'] ) $classes[] = "custom-decor-color";
            if( $args['backgroundColor'] ) $classes[] = "custom-background-color";
            if( $args['carouselColor'] ) $classes[] = "custom-carousel-color";
        }

        if( $args['useCarousel'] ){
            //add loader
            $classes[] = "loading-content";
            //carousel classes            
            $classes[] = "use-carousel";
            if( $args['carouselNavigation'] ) $classes[] = "carousel-navigation";
            if( $args['carouselPagination'] ) $classes[] = "carousel-pagination";
            if( $args['carouselAutoHeight'] ) $classes[] = "carousel-autoheight";
            
            //prepare carousel data
            self::enqueueCarouselScript();
            $carouselData = [
                'pagination' => $args['carouselPagination'],
                'navigation' => $args['carouselNavigation'],
                'autoplay' => $args['carouselAutoplay'],
                'autoHeight' => $args['carouselAutoHeight'],
                'autoplayDelay' => $args['carouselAutoplayDelay'],
                'loop' => $args['carouselLoop'],
            ];
        } 
        
        //just check if ratings should by shown generally, we'll check if rating is visible for each item in item-container template
        $show_ratings_generally = \Citadela\Directory\ItemReviews::$enabled && $args['showRating'];
        
        
        //styles
        $carouselStyles = [];
        $carouselColorStyle = $activeProPlugin && isset( $args['carouselColor'] ) && $args['carouselColor'] ? "color: ".esc_attr( $args['carouselColor'] ) .";" : "";
        if( $carouselColorStyle ) $carouselStyles[] = $carouselColorStyle;
        
        $showLoader = $args['useCarousel'] || $useResponsive;
        if( $showLoader ){
            $classes[] = "loading-content";
        }
        
        $query = \CitadelaDirectorySearch::getItems($args);
        
        if( ! $args['showReferencePost'] && $query->found_posts == 0 ){
            return;
        }

        ob_start();
        ?>

        <div 
            class="wp-block-citadela-blocks ctdl-directory-similar-items <?php echo esc_attr( implode( " ", $classes ) );?>"
            <?php if( $useResponsive ) echo 'data-block-mobile-attr="' . htmlspecialchars( json_encode( $data ) ) . '"' ?>
            <?php if( $useResponsive ) echo 'data-block-mobile-breakpoint="' . $attributes['breakpointMobileImageHeight'] . '"' ?>	
            <?php if( $args['useCarousel'] ) : ?>
                data-carousel="<?php echo htmlspecialchars(json_encode($carouselData)) ?>"
            <?php endif; ?>
            >
            
            <?php if( $showLoader ) : ?>
                <div class="citadela-loader">
                    <div class="inner-wrapper">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($blockTitle) : ?>
            <header class="citadela-block-header">
                <div class="citadela-block-title">
                    <h2><?php echo esc_html( $blockTitle ); ?></h2>
                </div>
            </header>
            <?php endif; ?>

            <div class="citadela-block-articles <?php if( $args['useCarousel'] )  echo esc_attr( 'swiper-container' ); ?>">
               
                <div class="citadela-block-articles-wrap <?php if( $args['useCarousel'] )  echo esc_attr( 'swiper-wrapper' ); ?>">
                <?php
                // show reference post if would be displayed
                if( $ref_post && $args['showReferencePost'] && $args['posts_per_page'] == 1 ){
                    
                    $post = $ref_post;
                    include dirname( __FILE__ ) . '/../../plugin/cpt/item/templates/parts/item-container.php';

                }else{
                    // show reference post before all other similar posts
                    if( $ref_post && $args['showReferencePost'] ){
                        $post = $ref_post;
                        include dirname( __FILE__ ) . '/../../plugin/cpt/item/templates/parts/item-container.php';
                    }

                    while ( $query->have_posts() ) {
                        /* activate global tag templates and global $post variable within custom loop (do not forget to reset postdata from where custom loop was created) */
                        $query->the_post();
                        global $post;

                        include dirname( __FILE__ ) . '/../../plugin/cpt/item/templates/parts/item-container.php';
                    }
                    wp_reset_postdata();

                }

                ?>
                </div>
                
                
            </div>
            
            <?php if( $args['useCarousel'] ) : ?>
                <?php if( $args['carouselNavigation'] ) : ?>
                    <div class="carousel-navigation-wrapper" <?php if( $carouselStyles ) echo 'style="' . implode("", $carouselStyles) . '"'; ?>>
                        <div class="carousel-button-prev"><i class="fas fa-chevron-left"></i></div>
                        <div class="carousel-button-next"><i class="fas fa-chevron-right"></i></div>
                    </div>
                <?php endif; ?>
                <?php if( $args['carouselPagination'] ) : ?>
                    <div class="carousel-pagination-wrapper" <?php if( $carouselStyles ) echo 'style="' . implode("", $carouselStyles) . '"'; ?>></div>
                <?php endif; ?>

            <?php endif; ?>
            
        </div>
        <?php

        return ob_get_clean();
    }

    private static function enqueueCarouselScript() {
        $paths = \CitadelaDirectory::getInstance()->paths;
        
        $script_path       = "{$paths->dir->blocks}/common-scripts/swiper-initializer.js";
        $script_asset_path = "{$paths->dir->blocks}/common-scripts/swiper-initializer.asset.php";

        $script_asset      = file_exists( $script_asset_path )
            ? require( $script_asset_path ) 
            : [ 'dependencies' => [], 'version' => filemtime( $script_asset_path ) ];
        
        $script_url = "{$paths->url->blocks}/common-scripts/swiper-initializer.js";

        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'jquery' ] );

        wp_register_script(
            "swiper-initializer",
            $script_url,
            $script_dependencies,
            $script_asset['version'],
            true
        );

        wp_enqueue_script( 'swiper-initializer' );
       
        wp_enqueue_style( 
            'swiper-css', 
            "{$paths->url->css}/swiper/swiper.min.css", 
            [], 
            filemtime( "{$paths->dir->css}/swiper/swiper.min.css" ), 
            false 
        );
    }

    private static function enqueueFrontendScript() {
        $dir = dirname( __FILE__ );
        $script_path = plugin_dir_path( __FILE__ ) . 'src/frontend-js.js';
        $script_url = plugins_url( '/src/frontend-js.js', __FILE__ );
        $script_dependencies = [ 'jquery' ];
        wp_enqueue_script( 'directory-similar-items-block-frontend', $script_url, $script_dependencies, filemtime( $script_path ), false );
    }
}
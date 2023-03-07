<?php

namespace Citadela\Directory\Blocks;

class AuthorsList extends Block {

    protected static $slug = 'authors-list';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
        
        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );

        $blockTitle = $attributes['title'];
        $selectedAuthors = $attributes['selectedAuthors'];
        
        if( empty( $selectedAuthors ) ) {
            return;
        }

        $args = [
            'blockLayout' => 'box',
            'blockSize' => 'small',
            'showCover' => $attributes['showCover'],
            'showIcon' => $attributes['showIcon'],
            'showDescription' => $attributes['showDescription'],
            'showPostsNumber' => $attributes['showPostsNumber'],
            'showLink' => $attributes['showLink'],
            'linkText' => $attributes['linkText'],
            'useCarousel' => $attributes['useCarousel'],
            'carouselNavigation' => $attributes['carouselNavigation'],
            'carouselPagination' => $attributes['carouselPagination'],
            'carouselAutoplay' => $attributes['carouselAutoplay'],
            'carouselAutoHeight' => $attributes['carouselAutoHeight'],
            'carouselAutoplayDelay' => $attributes['carouselAutoplayDelay'],
            'carouselLoop' => $attributes['carouselLoop'],
            'carouselColor' => $attributes['carouselColor'],
            'borderColor' => $attributes['borderColor'],
            'backgroundColor' => $attributes['backgroundColor'],
            'textColor' => $attributes['textColor'],
            'decorColor' => $attributes['decorColor'],
            'postsNumberColor' => $attributes['postsNumberColor'],
            'borderWidth' => $attributes['borderWidth'],
        ];
        
        //set grid type
        $gridType = "grid-type-1";
        
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = $gridType;
        $classes[] = "layout-{$args['blockLayout']}";
        $classes[] = "size-{$args['blockSize']}";
        if( $args['showCover'] ) $classes[] = "show-author-cover";
        if( $args['showIcon'] ) $classes[] = "show-author-icon";
        if( $args['showDescription'] ) $classes[] = "show-author-description";
        if( $args['showPostsNumber'] ) $classes[] = "show-posts-number";
        if( $args['showLink'] ) $classes[] = "show-posts-link";

        if( $args['useCarousel'] ){
            
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
        if ( $activeProPlugin ){
            $classes[] = "border-{$args['borderWidth']}";
            if( $args['textColor'] ) $classes[] = "custom-text-color";
            if( $args['decorColor'] ) $classes[] = "custom-decor-color";
            if( $args['postsNumberColor'] ) $classes[] = "custom-posts-number-color";
            if( $args['backgroundColor'] ) $classes[] = "custom-background-color";
            if( $args['carouselColor'] ) $classes[] = "custom-carousel-color";
        }

        //styles
        $carouselStyles = [];
        $carouselColorStyle = $activeProPlugin && isset( $args['carouselColor'] ) && $args['carouselColor'] ? "color: ".esc_attr( $args['carouselColor'] ) .";" : "";
        if( $carouselColorStyle ) $carouselStyles[] = $carouselColorStyle;
        
        $showLoader = $args['useCarousel'];
        if( $showLoader ){
            $classes[] = "loading-content";
        }
        
        $query_args = [
            'include' => $selectedAuthors,
            'orderby' => $attributes['itemsOrderBy'],
            'order' => $attributes['itemsOrder'],
        ];

        $query = new \WP_User_Query( $query_args );
        $users = $query->get_results();
        
        ob_start();
        
        ?>

        <div 
            class="wp-block-citadela-blocks ctdl-authors-list <?php echo esc_attr( implode( " ", $classes ) );?>"
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
                    
                    <?php include dirname( __FILE__ ) . '/../../plugin/parts/authors-list.php'; ?>
                    
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
            $script_asset['version']
        );

        wp_enqueue_script( 'swiper-initializer' );
       
        wp_enqueue_style( 
            'swiper-css', 
            "{$paths->url->css}/swiper/swiper.min.css", 
            [], 
            filemtime( "{$paths->dir->css}/swiper/swiper.min.css" ), 
            true 
        );
    }

}
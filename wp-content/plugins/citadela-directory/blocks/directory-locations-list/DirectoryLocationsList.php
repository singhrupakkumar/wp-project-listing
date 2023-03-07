<?php

namespace Citadela\Directory\Blocks;

class DirectoryLocationsList extends Block {

    protected static $slug = 'directory-locations-list';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        $taxonomy = 'citadela-item-location';
        
        $meta_query_args = [];
        if( $attributes['onlyFeatured'] ){
            $meta_query_args['featured'] = true;
        }

        $terms = \CitadelaItem::citadelaGetTermChilds( intval($attributes['location']), $taxonomy, $meta_query_args );

        $blockTitle = $attributes['title'];
        $blockLayout = $attributes['layout'];
        $blockSize = $attributes['size'];
        $showLocationDescription = $attributes['showLocationDescription'];
        $showLocationIcon = $attributes['showLocationIcon'];

        $useCarousel = $attributes['useCarousel'];
        $carouselNavigation = $attributes['carouselNavigation'];
        $carouselPagination = $attributes['carouselPagination'];
        $carouselAutoplay = $attributes['carouselAutoplay'];
        $carouselAutoHeight = $attributes['carouselAutoHeight'];
        $carouselAutoplayDelay = $attributes['carouselAutoplayDelay'];
        $carouselLoop = $attributes['carouselLoop'];

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "layout-".$blockLayout;
        $classes[] = "size-".$blockSize;
        if(!$showLocationDescription) $classes[] = "hide-description";
        if(!$showLocationIcon) $classes[] = "hide-icon";
        if( $attributes['onlyFeatured'] ){ $classes[] = "only-featured"; }

        if( $useCarousel ){
            //add loader
            $classes[] = "loading-content";
            //carousel classes            
            $classes[] = "use-carousel";
            if( $carouselNavigation ) $classes[] = "carousel-navigation";
            if( $carouselPagination ) $classes[] = "carousel-pagination";
            
            //prepare carousel data
            self::enqueueCarouselScript();
            $carouselData = [
                'pagination'    => $carouselPagination,
                'navigation'    => $carouselNavigation,
                'autoplay'      => $carouselAutoplay,
                'autoplayDelay' => $carouselAutoplayDelay,
                'autoHeight'    => $carouselAutoHeight,
                'loop'          => $carouselLoop,
            ];
        }

        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-directory-locations-list grid-type-2 <?php echo esc_attr( implode(" ", $classes) ) ;?>"
            <?php if( $useCarousel ) : ?>
                data-carousel="<?php echo htmlspecialchars(json_encode($carouselData)) ?>"
            <?php endif; ?>
        >

            <?php if( $useCarousel ) : ?>
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

            <div class="citadela-block-articles <?php if( $useCarousel )  echo 'swiper-container'; ?>">
                <div class="citadela-block-articles-wrap <?php if( $useCarousel )  echo 'swiper-wrapper'; ?>">

                    <?php foreach ($terms as $term) : ?>
                    <?php
                        $term_id = $term->term_id;
                        $url = get_term_link($term_id);
                        $description = strip_tags(term_description($term_id));
                    ?>

                    <a href="<?php echo esc_url( $url ); ?>" class="<?php if( $useCarousel ) echo 'swiper-slide';?>">
                        <article class=folder-card>
                            <div class="folder-header">
                                <?php if($showLocationIcon): ?>
                                <div class="folder-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="folder-content">
                                <div class="folder-content-wrap">
                                    <p class="folder-title"><?php echo esc_html( $term->name ); ?></p>
                                    <?php if($description && $showLocationDescription) : ?>
                                    <p class="folder-description"><?php echo wp_kses_data( $description ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>

                </div>


            </div>
            
            <?php if( $useCarousel ) : ?>
                <?php if( $carouselNavigation ) : ?>
                    <div class="carousel-navigation-wrapper">
                        <div class="carousel-button-prev"><i class="fas fa-chevron-left"></i></div>
                        <div class="carousel-button-next"><i class="fas fa-chevron-right"></i></div>
                    </div>
                <?php endif; ?>
                <?php if( $carouselPagination ) : ?>
                    <div class="carousel-pagination-wrapper"></div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
        <?php

        return ob_get_clean();
    }

    private static function enqueueCarouselScript() {
        $dir = dirname( __FILE__ );

        $script_path       = '../common-scripts/swiper-initializer.js';
        $script_asset_path = '../common-scripts/swiper-initializer.asset.php';

        $script_asset      = file_exists( "$dir/$script_asset_path" )
            ? require( "$dir/$script_asset_path" ) 
            : [ 'dependencies' => [], 'version' => filemtime( "$dir/$script_path" ) ];
        
        $script_url = plugins_url( $script_path, __FILE__ );

        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'jquery' ] );

        wp_enqueue_script( 'swiper-initializer', $script_url, $script_dependencies, $script_asset['version'], true );

        $paths = \CitadelaDirectory::getInstance()->paths;
        
        $style_path = "{$paths->dir->css}/swiper/swiper.min.css";
        $style_url = "{$paths->url->css}/swiper/swiper.min.css";
        wp_enqueue_style( 'swiper-css', $style_url, [], filemtime( $style_path ), false );
    }

}
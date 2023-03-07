<?php

namespace Citadela\Directory\Blocks;

class ItemGallery extends Block {

    protected static $slug = 'item-gallery';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        global $post;
        
        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );
        
        $blockTitle = $attributes['title'];

        $args = [
            'blockSize' => $attributes['size'],
            'useCarousel' => $attributes['useCarousel'],
            'carouselNavigation' => $attributes['carouselNavigation'],
            'carouselPagination' => $attributes['carouselPagination'],
            'carouselAutoplay' => $attributes['carouselAutoplay'],
            'carouselAutoHeight' => $attributes['carouselAutoHeight'],
            'carouselAutoplayDelay' => $attributes['carouselAutoplayDelay'],
            'carouselLoop' => $attributes['carouselLoop'],
            'imageSize' => $attributes['imageSize'],
            'carouselColor' => $attributes['carouselColor'],
            'textColor' => $attributes['textColor'],
            'imageSize' => $attributes['imageSize'],
        ];
        
        $useResponsive = isset( $attributes['useResponsiveOptionsImageHeight'] ) && $attributes['useResponsiveOptionsImageHeight'];

        if( $useResponsive ){
            self::enqueueFrontendScript();
        }

        //set grid type
        $gridType = "grid-type-1";
        $gridLayout = "layout-default";

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = $gridType;
        $classes[] = $gridLayout;
        $classes[] = "size-{$args['blockSize']}";
        $classes[] = "image-size-{$attributes['imageSize']}";
        $classes[] = "image-vertical-align-{$attributes['imagesVerticalAlign']}";
        
        if( $attributes['captionPosition'] !== 'hidden' ){
            $classes[] = "caption-{$attributes['captionPosition']}";
        }

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

        if ( $activeProPlugin ){
            if( $args['carouselColor'] ) $classes[] = "custom-carousel-color";
            if( $args['textColor'] ) $classes[] = "custom-text-color";
        }

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
        
        $textColorStyle = "";
        if( $activeProPlugin ){
            $textColorStyle = isset( $args['textColor'] ) && $args['textColor'] ? "color: " . esc_attr( $args['textColor'] ) . ";" : "";
        }

        $itemContentStyle = 'style="' . implode('', [ $textColorStyle ] ) . '"';

        //styles
        $carouselStyles = [];
        $carouselColorStyle = $activeProPlugin && isset( $args['carouselColor'] ) && $args['carouselColor'] ? "color: ".esc_attr( $args['carouselColor'] ) .";" : "";
        if( $carouselColorStyle ) $carouselStyles[] = $carouselColorStyle;
       
        $imgStyles = [];
        if( ! $attributes['proportionalImageHeight'] && $attributes['imageHeightType'] == 'custom' && ( isset( $attributes['imageHeight'] ) && $attributes['imageHeight'] ) ) $imgStyles[] = "height:{$attributes['imageHeight']}{$attributes['imageHeightUnit']};";
        $imgStylesText = implode( ' ', $imgStyles);

        $showLoader = $args['useCarousel'] || $useResponsive;
        if( $showLoader ){
            $classes[] = "loading-content";
        }

        $meta = get_post_meta($post->ID, '_citadela_gallery_images', true);
        $gallery_data = $meta ? $meta : [];
        if( ! $gallery_data || empty($gallery_data) ){
            return;
        }

        ob_start();
        ?>

        <div 
            class="wp-block-citadela-blocks ctdl-item-gallery <?php echo esc_attr( implode( " ", $classes ) );?>"
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
               
                <div class="citadela-block-articles-wrap citadelaFancyboxGallery <?php if( $args['useCarousel'] )  echo esc_attr( 'swiper-wrapper' ); ?>">

                    <?php foreach ($gallery_data as $data) {
                        $image_id = is_array( $data ) ? $data['id'] : $data;
                        $caption_text = isset( $data['caption'] ) ? $data['caption'] : '';

                        // full image data for link
                        $full_image_src = wp_get_attachment_image_src( $image_id, 'full' ); 
                        $full_image_data = [];
                        $full_image_data['url'] = $full_image_src[0];
                        $full_image_data['width'] = $full_image_src[1];
                        $full_image_data['height'] = $full_image_src[2];

                        // image displayed in grid
                        $image_data = [];
                        $image_data['src'] = wp_get_attachment_image_src( $image_id, $args['imageSize'] );
                        $image_data['url'] = $image_data['src'][0];
                        $image_data['width'] = $image_data['src'][1];
                        $image_data['height'] = $image_data['src'][2];
                        $image_data['srcset'] = wp_get_attachment_image_srcset( $image_id, $args['imageSize'] );
                        $image_data['sizes'] = wp_get_attachment_image_sizes( $image_id, $args['imageSize'] );
                        $image_data['alt'] = $caption_text;
                    ?>
                        <article class="citadela-directory-gallery-item<?php if( $image_data['alt'] ) { echo esc_attr( ' has-caption' ); } if( $args['useCarousel'] ){  echo esc_attr( ' swiper-slide' ); } ?>">
                            <div class="item-content" <?php echo $itemContentStyle; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                                <div class="item-thumbnail">
                                    <a href="<?php echo esc_url( $full_image_data['url'] ); ?>" class="citadelaFancyboxElement" data-gallery="item-gallery" data-image-size="<?php echo esc_attr( $full_image_data['width'] . 'x' . $full_image_data['height'] ) ?>">
                                        <img 
                                            class="item-image"
                                            src="<?php echo esc_url( $image_data['url'] ); ?>"
                                            width="<?php esc_attr_e( $image_data['width'] ); ?>"
                                            height="<?php esc_attr_e( $image_data['height'] ); ?>"
                                            srcset="<?php esc_attr_e( $image_data['srcset'] ); ?>"
                                            sizes="<?php esc_attr_e( $image_data['sizes'] ); ?>"
                                            <?php if( $image_data['alt'] ) : ?>
                                                alt="<?php echo esc_html( $image_data['alt'] ); ?>"
                                            <?php endif; ?>
                                            <?php echo "style=\"{$imgStylesText}\""; ?>
                                        />
                                        <?php if( $attributes['captionPosition'] !== 'hidden' && $caption_text ) : ?>
                                            <div class="caption"><?php esc_html_e($caption_text); ?></div>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                        
                    <?php } ?>
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
        wp_enqueue_script( 'directory-item-gallery-block-frontend', $script_url, $script_dependencies, filemtime( $script_path ), false );
    }

}
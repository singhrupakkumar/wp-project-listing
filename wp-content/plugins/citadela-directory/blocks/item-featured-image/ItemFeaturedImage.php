<?php

namespace Citadela\Directory\Blocks;

class ItemFeaturedImage extends Block {

    protected static $slug = 'item-featured-image';
   
    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        global $post;
        if(!isset($post)) return '';

        $useResponsive = isset( $attributes['useResponsiveOptions'] ) && $attributes['useResponsiveOptions'];

        if( $useResponsive ){
            self::enqueueFrontendScript();
        }

        $size  = $attributes['size'];
        $in_popup  = $attributes['inPopup'];

        //$src = get_the_post_thumbnail_url( $post->ID, $size );
        $image_id = get_post_thumbnail_id( $post );
        
        if( ! $image_id ) return '';

        $src = wp_get_attachment_image_src( $image_id, $size );
        $url = $fullurl = $src[0];
        $image_width = $fullwidth = $src[1];
        $image_height = $fullheight = $src[2];
        $srcset = wp_get_attachment_image_srcset( $image_id, $size );
        $sizes = wp_get_attachment_image_sizes( $image_id, $size );
        $alt = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );
        if( ! $alt ) $alt = $post->post_title;
        $caption = get_the_post_thumbnail_caption( $post );
        
        // get full image data if is displayed another image size
        if( $size !== 'full' ){
            $fullsrc = wp_get_attachment_image_src( $image_id, 'full' );
            $fullurl = $fullsrc[0];
            $fullwidth = $fullsrc[1];
            $fullheight = $fullsrc[2];
        }

        $dataSize = "{$fullwidth}x{$fullheight}";

        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "align-{$attributes['align']}";
        $classes[] = "size-{$attributes['size']}";
        if( isset( $attributes['height'] ) && $attributes['height'] && ! ( $attributes['inColumn'] && $attributes['coverHeight'] )  ){
            $classes[] = 'custom-height';
        }
        if( $attributes['inColumn'] && $attributes['coverHeight'] ){
            $classes[] = 'cover-height';
        }
        
        $customizedHeight = ( isset( $attributes['height'] ) && $attributes['height'] && ! ( $attributes['inColumn'] && $attributes['coverHeight'] ) ) || ( $attributes['inColumn'] && $attributes['coverHeight'] );
        $customizedHeightMobile = $useResponsive && ( ( isset( $attributes['heightMobile'] ) && $attributes['heightMobile'] && ! ( $attributes['inColumn'] && $attributes['coverHeightMobile'] ) ) || ( $attributes['inColumn'] && $attributes['coverHeightMobile'] ) );
        if( $customizedHeight || $customizedHeightMobile ){
            $classes[] = 'position-'.str_replace(' ', '-', $attributes['objectPosition']);
        }

        if( $useResponsive ) {
            
            $data = [
                "desktop" => [],
                "mobile" => [],
            ];
            
            $unit = $attributes['unit'];
            $height = isset( $attributes['height'] ) && $attributes['height'] && ! ( $attributes['inColumn'] && $attributes['coverHeight'] ) ? $attributes['height'].$attributes['unit'] : '';
            if( $height ) $data['desktop']["height"] = $height;
            $data['desktop']["coverHeight"] = $attributes['inColumn'] && $attributes['coverHeight'];
            if( $attributes['inColumn'] && $attributes['coverHeight'] ){
                $data['desktop']["minHeight"] = $attributes['minHeight'].$attributes['minUnit'];
            }
            //$data['desktop']['objectPosition'] = $customizedHeight ? 'position-'.str_replace(' ', '-', $attributes['objectPosition']) : false;

            $unitMobile = $attributes['unitMobile'];
            $heightMobile = isset( $attributes['heightMobile'] ) && $attributes['heightMobile'] && ! ( $attributes['inColumn'] && $attributes['coverHeightMobile'] ) ? $attributes['heightMobile'].$attributes['unitMobile'] : '';
            if( $heightMobile ) $data['mobile']["height"] = $heightMobile;
            $data['mobile']["coverHeight"] = $attributes['inColumn'] && $attributes['coverHeightMobile'];
            
            // not used yet, class for object position always available 
            $customizedHeightMobile = ( isset( $attributes['heightMobile'] ) && $attributes['heightMobile'] && ! ( $attributes['inColumn'] && $attributes['coverHeightMobile'] ) ) || ( $attributes['inColumn'] && $attributes['coverHeightMobile'] );
            //$data['mobile']['objectPosition'] = $customizedHeightMobile ? 'position-'.str_replace(' ', '-', $attributes['objectPosition']) : false;
            if( $attributes['inColumn'] && $attributes['coverHeightMobile'] ){
                $data['mobile']["minHeight"] = $attributes['minHeightMobile'].$attributes['minUnitMobile'];
            }
        }
        
        //if there are no mobile data, we do not need use responsive options
        if( empty( $data['mobile'] ) ) $useResponsive = false;

        if( $useResponsive ) {
            $classes[] = "responsive-options";
            $classes[] = "loading";
        }

        $imgStyles = [];
        if( ( isset( $attributes['height'] ) && $attributes['height'] ) && ! ( $attributes['inColumn'] && $attributes['coverHeight'] ) ) $imgStyles[] = "height:{$attributes['height']}{$attributes['unit']};";
        $imgStylesText = implode( ' ', $imgStyles);

        $mainStyles = [];
        if( $attributes['inColumn'] && $attributes['coverHeight'] ) $mainStyles[] = "min-height:{$attributes['minHeight']}{$attributes['minUnit']};";
        $mainStylesText = implode( ' ', $mainStyles);

        ob_start();
        ?>
        <?php if($src) : ?>
        <div 
            class="wp-block-citadela-blocks ctdl-item-featured-image <?php echo esc_attr( implode( ' ', $classes ) ); ?>"
            <?php if( $useResponsive ) echo 'data-block-mobile-attr="' . htmlspecialchars( json_encode( $data ) ) . '"' ?>
            <?php if( $useResponsive ) echo 'data-block-mobile-breakpoint="' . $attributes['breakpointMobile'] . '"' ?>	
            <?php echo "style=\"{$mainStylesText}\""; ?>
        >
            <div class="ft-image-thumb">
                <div class="ft-image">
                <?php if( $in_popup ) : ?>
                    <a href="<?php echo esc_url( $fullurl ); ?>" class="citadelaFancyboxElement" data-image-size="<?php esc_attr_e( $dataSize ); ?>" >
                <?php endif; ?>
                    <img 
                        src="<?php echo esc_url( $url ); ?>"
                        width="<?php esc_attr_e( $image_width ); ?>"
                        height="<?php esc_attr_e( $image_height ); ?>"
                        srcset="<?php esc_attr_e( $srcset ); ?>"
                        sizes="<?php esc_attr_e( $sizes ); ?>"
                        alt="<?php echo esc_html( $alt ); ?>"
                        <?php echo "style=\"{$imgStylesText}\""; ?>
                    />
                <?php if( $in_popup ) : ?>
                    </a>
                <?php endif; ?>
                </div>
                <?php if($caption) : ?>
                <div class="ft-image-caption">
                    <p><?php echo esc_html( $caption ); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php

        $template = ob_get_clean();
        return $template;
    }

    private static function enqueueFrontendScript() {
        $dir = dirname( __FILE__ );
        $script_path = plugin_dir_path( __FILE__ ) . 'src/frontend-js.js';
        $script_url = plugins_url( '/src/frontend-js.js', __FILE__ );
        $script_dependencies = [ 'jquery' ];
        wp_enqueue_script( 'item-featured-image-frontend', $script_url, $script_dependencies, filemtime( $script_path ), false );
    }
}
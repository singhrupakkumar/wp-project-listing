<?php

namespace Citadela\Pro\Blocks;

use Citadela\Pro\Template;

class Page_Title extends Block {

   public $slug = 'page-title';
    
    function allowed_contexts() {
        return [
            'page', 
            'special_page', 
            'citadela-item',
        ];
    }

    function init(){
        // we need to register meta of Item post type from Citadela Listing plugin in order to support it in block javascripts
        register_meta( 'post', '_citadela_subtitle', [
            'object_subtype' => 'citadela-item',
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'auth_callback' => function() {
                return true;
            }
        ] );
    }

    function block_vars(){
        $settings = [];
        if( is_admin() ){
            $screen = get_current_screen();
            $settings['current_screen'] = $screen;
        }
        return $settings;
    }

    public function render( $attributes, $content ) {
        $metaHtml = '';
        $iconHtml = '';
        $item_ratingHtml = '';
        $subtitle = '';
        $subtitleHtml = '';
        $header_classes = [];
        $attr = (object) $attributes;

        if( is_tax('citadela-item-category') ){
            $qo = get_queried_object();
            // Item Category special page
            $mainText = __('Category: ', 'citadela-pro');
            $mainData = single_term_title('', false);
            $title = '<span class="main-text">' . $mainText . '</span>';
            $title .= '<span class="main-data">' . $mainData . '</span>';

            //define subtitle
            $description = term_description();
            $subtitle = wp_kses_post($description);

            //define category icon
            $taxonomy = $qo->taxonomy;
            $term_id = $qo->term_id;
            $term_meta = get_term_meta( $term_id, $taxonomy.'-meta', true );
            $icon = (isset($term_meta['category_icon']) && $term_meta['category_icon'] != '') ? $term_meta['category_icon'] : '';
            $color = (isset($term_meta['category_color']) && $term_meta['category_color'] != '') ? $term_meta['category_color'] : '';
            $bgStyles = $color ? 'style="background-color: '.$color.';"' : '';
            $iconStyles = $color ? 'style="color: '.$color.'; border-color: '.$color.';"' : '';
            $iconHtml = '';

            if($icon){
                $iconHtml .= '<div class="entry-icon">';
                $iconHtml .=    '<span class="entry-icon-wrap">';
                $iconHtml .=        '<span class="icon-bg" '.$bgStyles.'></span>';
                $iconHtml .=        '<i class="'.$icon.'" '.$iconStyles.'></i>';
                $iconHtml .=    '</span>';
                $iconHtml .= '</div>';
            }

        }elseif ( is_tax('citadela-item-location') ) {
            // Item Location special page
            $mainText = __('Location: ', 'citadela-pro');
            $mainData = single_term_title('', false);
            $title = '<span class="main-text">' . $mainText . '</span>';
            $title .= '<span class="main-data">' . $mainData . '</span>';

            //define subtitle
            $description = term_description();
            $subtitle = wp_kses_post($description);

            $iconHtml = '<div class="entry-icon">';
            $iconHtml .=    '<span class="entry-icon-wrap">';
            $iconHtml .=        '<span class="icon-bg"></span>';
            $iconHtml .=        '<i class="fas fa-map-marker-alt"></i>';
            $iconHtml .=    '</span>';
            $iconHtml .= '</div>';
        }elseif ( is_category() ) {
            // Posts Category special page
            $mainText = esc_html('Category archives: ', 'citadela-pro');
            $mainData = single_term_title('', false);
            $title = '<span class="main-text">' . $mainText . '</span>';
            $title .= '<span class="main-data">' . $mainData . '</span>';

            //define subtitle
            $description = term_description();
            $subtitle = wp_kses_post($description);
        }elseif ( is_tag() ) {
            // Posts Tag special page
            $mainText = esc_html('Tag archives: ', 'citadela-pro');
            $mainData = single_tag_title('', false);
            $title = '<span class="main-text">' . $mainText . '</span>';
            $title .= '<span class="main-data">' . $mainData . '</span>';

            //define subtitle
            $description = get_the_archive_description();
            $subtitle = wp_kses_post($description);
        }elseif ( is_date() ) {
            // Posts Date special page
            $mainText = esc_html('Date archives: ', 'citadela-pro');
            $mainData = get_the_date();
            $title = '<span class="main-text">' . $mainText . '</span>';
            $title .= '<span class="main-data">' . $mainData . '</span>';
        }elseif ( is_author() ) {
            // Posts Author special page
            $mainText = esc_html('Author archives: ', 'citadela-pro');
            $authorUrl = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
            $authorName = esc_html( get_the_author() );
            $title = '<span class="main-text">' . $mainText . '</span>';
            $title .= '<span class="author vcard main-data"><a class="url fn n" href="' . $authorUrl . '">' . $authorName . '</a></span>';
            

            //define subtitle
            $description = get_the_archive_description();
            $subtitle = wp_kses_post($description);
        }elseif ( is_404() ) {
            // 404 special page
            $title = esc_html('Oops! That page can&rsquo;t be found.', 'citadela-pro');
            $subtitle = ( isset($attr->subtitle) && $attr->subtitle != "" ) ?  $attr->subtitle : '';
        }else{
            global $post;
            $subtitle = ( isset($attr->subtitle) && $attr->subtitle != "" ) ?  $attr->subtitle : '';
            $title = '';
            if ( $post ) {

                $title = $post->post_title;
                if ( $post->post_type == 'post') {
                    //build meta Posted On and Posted By
                    $metaHtml = '<div class="entry-meta">';
                    $metaHtml .= self::get_posted_on();
                    $metaHtml .= self::get_posted_by($post);
                    $metaHtml .= '</div>';
                }

                if ( $post->post_type == 'citadela-item') {
                    $subtitle = get_post_meta($post->ID, '_citadela_subtitle', true);           
                    $item_ratingHtml = \Citadela\Directory\ItemReviews::render_post_rating( $post->ID );
                }

                if ( class_exists( 'woocommerce' ) ){
                    // Woocommerce shop page
                    if( is_shop() ){
                        $title = woocommerce_page_title( false );
                    }
                }
            }

        }


        $useResponsive = isset( $attr->useResponsiveOptions ) && $attr->useResponsiveOptions;


        // TODO: maybe use default value of block attribute
        if( !isset($attr->align) ){
            $attr->align = 'left';
        }
        
        $classes = [ "align-{$attr->align}" ];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        if( isset( $attr->hideSeparator ) && $attr->hideSeparator ) $classes[] = "hidden-separator";
        if( isset( $attr->fontWeight ) && $attr->fontWeight ) $classes[] = "weight-{$attr->fontWeight}";

        $subtitleStyles = [];
        if( isset( $attr->subtitleColor ) && $attr->subtitleColor ) $subtitleStyles[] = "color: {$attr->subtitleColor};";
        
        $subtitleStylesText = implode( ' ', $subtitleStyles);
        $subtitleStylesText = $subtitleStylesText ? "style=\"{$subtitleStylesText}\"" : "";
        if( $subtitle ) {
            $subtitleHtml =  '<div class="entry-subtitle"><p class="ctdl-subtitle" ' . $subtitleStylesText . '>' . $subtitle . '</p></div>';
            $header_classes[] = 'has-subtitle';
        }

        
        if( $item_ratingHtml ){
            $item_ratingHtml = '<div class="entry-item-rating">'.$item_ratingHtml.'</div>';
            $header_classes[] = 'has-rating';
        }


        $styles = [];
        if( isset( $attr->googleFont ) && $attr->googleFont['family'] != '' ) $styles[] = "font-family:'{$attr->googleFont['family']}';";
        if( isset( $attr->fontSize ) && $attr->fontSize ) $styles[] = "font-size: {$attr->fontSize}{$attr->fontSizeUnit};";
        if( isset( $attr->fontWeight ) && $attr->fontWeight ) $styles[] = "font-weight: {$attr->fontWeight};";
        if( isset( $attr->lineHeight ) && $attr->lineHeight ) $styles[] = "line-height: {$attr->lineHeight};";
        if( isset( $attr->letterSpacing ) && $attr->letterSpacing ) $styles[] = "letter-spacing: {$attr->letterSpacing}em;";
        
        $entryHeaderStyles = [];
        if( isset( $attr->titleColor ) && $attr->titleColor ) $entryHeaderStyles[] = "color: {$attr->titleColor};";

        if( $useResponsive ) {
            
            $data = [
                "desktop" => [],
                "mobile" => [],
            ];
            
            //$fontSizeUnit is always defined
            $fontSize = isset( $attr->fontSize ) ? "{$attr->fontSize}{$attr->fontSizeUnit}" : '';
            $lineHeight = isset( $attr->lineHeight ) ? $attr->lineHeight : '';
            if( $fontSize ) $data['desktop']["fontSize"] = $fontSize;
            if( $lineHeight ) $data['desktop']["lineHeight"] = $lineHeight;
            
            $fontSizeUnitMobile = isset( $attr->fontSizeUnitMobile ) ? $attr->fontSizeUnitMobile : $attr->fontSizeUnit;
            $fontSizeMobile = isset( $attr->fontSizeMobile ) ? "{$attr->fontSizeMobile}{$fontSizeUnitMobile}" : '';
            $lineHeightMobile = isset( $attr->lineHeightMobile ) ? $attr->lineHeightMobile : '';
            if( $fontSizeMobile ) $data['mobile']["fontSize"] = $fontSizeMobile;
            if( $lineHeightMobile ) $data['mobile']["lineHeight"] = $lineHeightMobile;
            
            $align = $attr->align;
            $alignMobile = isset( $attr->alignMobile ) ? $attr->alignMobile : $attr->align;
            if( $align != $alignMobile){
                $data['desktop']["align"] = $align;
                $data['mobile']["align"] = $alignMobile;
            }
        }
        
        if( empty( $data['mobile'] ) ) $useResponsive = false;

        if( $useResponsive ) {
            $classes[] = "responsive-options";
            $classes[] = "loading";
        }

        $stylesText = implode( ' ', $styles);
        
        $result =               $iconHtml;
        $result .=              "<h1 class=\"entry-title\" style=\"{$stylesText}\">{$title}</h1>";
        $result .=              $metaHtml;
        $result .=              $subtitleHtml;
        $result .=              $item_ratingHtml;

        self::google_fonts( $attr->googleFont );

        // we need to pass title styles for search results pages title created via filter in CitadelaDirectorySpecialPages custom_pate_title() method

        $title_styles = $stylesText;

        ob_start();
        ?>

        <div 
            class="citadela-block-page-title <?php echo implode( ' ' , $classes ); ?>"
            <?php if( $useResponsive ) echo 'data-block-mobile-attr="' . htmlspecialchars( json_encode( $data ) ) . '"' ?>
            <?php if( $useResponsive ) echo 'data-block-mobile-breakpoint="' . $attr->breakpointMobile . '"' ?> 
        >
            <div class="page-title custom">
                <header class="entry-header <?php echo implode( ' ', $header_classes ); ?>" style="<?php echo implode( ' ', $entryHeaderStyles ); ?>">
                    <div class="entry-header-wrap">

                        <?php echo apply_filters( 'ctdl_block_page_title', $result, $attr, $title_styles ); ?>
                    
                    </div>
                </header>
            </div>
        </div>

        <?php

        $html = ob_get_clean();

        return $html;
    }

    protected static function get_posted_on(){
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        $archiveYear  = get_the_time('Y');
        $archiveMonth = get_the_time('m');
        $archiveDay   = get_the_time('d');
        $archiveLink = get_day_link( $archiveYear, $archiveMonth, $archiveDay );

        $result = '<span class="posted-on">';
                    /* translators: Posted on [post date]. */
        $result .=  '<span class="posted-on-text">' . __( 'Posted on', 'citadela-theme' ) . '</span> ';
        $result .=  '<span class="posted-on-date"><a href="' . esc_url( $archiveLink ) . '" rel="bookmark">' . $time_string . '</a></span>';
        $result .='</span>';

        return $result;
    }

    protected static function get_posted_by( $post ) {
        $authorName = get_the_author_meta( 'display_name', $post->post_author);
        $authorUrl = get_author_posts_url( $post->post_author);
        $result = '<span class="byline">';
                /* translators: [posted] by [post author]. */
        $result .=  '<span class="byline-text">' . __( 'by', 'citadela-theme' ) . '</span> ';
        $result .=  '<span class="author vcard"><a class="url fn n" href="' . $authorUrl . '">' . $authorName . '</a></span>';
        $result .= '</span>';

        return $result;
    }

    protected function google_fonts( $googleFont ){
        if( isset( $googleFont['family'] ) && $googleFont['family'] != '' ) {
            
            $variants = implode( ',', $googleFont['variants'] );

            $url = add_query_arg( [
                'family' => "{$googleFont['family']}:{$variants}",
                'subset' => implode( ',', $googleFont['subsets'] ),
                'display' => 'swap',
            ], 'https://fonts.googleapis.com/css' );
            
            $fontSlug = str_replace(" ", "-", $googleFont['family']);
            wp_enqueue_style( "citadela-{$this->slug}-font-{$fontSlug}", $url );

        }
    }

    protected static function get_classes( $attributes ) {
        $classes = [];
        return $classes;
    }
}

<?php

namespace Citadela\Directory\Blocks;

class DirectorySearchResults extends Block {

    protected static $slug = 'directory-search-results';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );
        
        $isSearchResults = is_search() && isset( $_REQUEST[ 'ctdl' ] ) ? true : false ;

        if ( is_tax( 'citadela-item-category' ) || is_tax ('citadela-item-location' ) || $isSearchResults  ) {
            

            if( $isSearchResults ){
                
                $args = [
                    's'                 => isset($_REQUEST['s']) ? $_REQUEST['s'] : '',
                    'category'          => isset($_REQUEST['category']) ? $_REQUEST['category'] : null,
                    'location'          => isset($_REQUEST['location']) ? $_REQUEST['location'] : null,
                    'posts_per_page'    => isset($_REQUEST['posts_per_page']) ? $_REQUEST['posts_per_page'] : get_query_var( 'posts_per_page' ),
                    'order'             => $attributes['itemsOrder'],
                    'orderby'           => $attributes['itemsOrderBy'],
                    'paged'             => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1 ,
                    'only_featured'      => isset($_REQUEST['only_featured']) ? $_REQUEST['only_featured'] : false,
                    'featured_first'    => $attributes['featuredFirst'],
                    
                ];

                if( \Citadela\Directory\AdvancedFilters::$use ){
                    $args['advanced_filters']['filters'] = \Citadela\Directory\AdvancedFilters::$current_filters;
                    $args['advanced_filters']['operators'] = \Citadela\Directory\AdvancedFilters::getFiltersOperator('search-results');
                }

                $isGeolocation = isset( $_REQUEST['rad'] ) && $_REQUEST['rad'] != '' && isset( $_REQUEST['lat'] ) && $_REQUEST['lat'] != '' && isset( $_REQUEST['lon'] ) && $_REQUEST['lon'] != '';
                if( $isGeolocation ){
                    $geoData = [
                        'rad' => $_REQUEST['rad'],
                        'lat' => $_REQUEST['lat'],
                        'lon' => $_REQUEST['lon'],
                        'unit' => isset( $_REQUEST['unit'] ) && $_REQUEST['unit'] != '' ? $_REQUEST['unit'] : 'km',
                    ];
                    $args[ 'geolocation' ] = $geoData;
                }

            }else{

                $args = [
                    'posts_per_page'    => get_query_var( 'posts_per_page' ),
                    'order'             => $attributes['itemsOrder'],
                    'orderby'           => $attributes['itemsOrderBy'],
                    'paged'             => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1 ,
                    'featured_first'    => $attributes['featuredFirst'],
                ];
                
                if( \Citadela\Directory\AdvancedFilters::$use ){
                    $args['advanced_filters']['filters'] = \Citadela\Directory\AdvancedFilters::$current_filters;
                }

                if( is_tax( 'citadela-item-category' ) ) {
                    $args[ 'category' ] = get_queried_object()->term_id;

                    if( \Citadela\Directory\AdvancedFilters::$use ){
                        $args['advanced_filters']['operators'] = \Citadela\Directory\AdvancedFilters::getFiltersOperator('item-category');
                    }
                }
                
                if( is_tax( 'citadela-item-location' ) ) {
                    $args[ 'location' ] = get_queried_object()->term_id;
                    
                    if( \Citadela\Directory\AdvancedFilters::$use ){
                        $args['advanced_filters']['operators'] = \Citadela\Directory\AdvancedFilters::getFiltersOperator('item-location');
                    }

                }

            }
            
            

            $query = \CitadelaDirectorySearch::getItems( $args );

        } else {
            return;
        }

        $args = [
            'blockLayout' => $attributes['layout'],
            'blockSize' => $attributes['size'],
            'showItemFeaturedImage' => $attributes['showItemFeaturedImage'],
            'showItemSubtitle' => $attributes['showItemSubtitle'],
            'showItemDescription' => $attributes['showItemDescription'],
            'showItemAddress' => $attributes['showItemAddress'],
            'showItemWeb' => $attributes['showItemWeb'],
            'showItemCategories' => $attributes['showItemCategories'],
            'showItemLocations' => $attributes['showItemLocations'],
            'borderWidth' => $attributes['borderWidth'],
            'borderColor' => $attributes['borderColor'],
            'backgroundColor' => $attributes['backgroundColor'],
            'textColor' => $attributes['textColor'],
            'decorColor' => $attributes['decorColor'],
            'showRating' => $attributes['showRating'],
            'ratingColor' => $attributes['ratingColor'],
            'onlyFeaturedCategory' => $attributes['onlyFeaturedCategory'],
            'imageSize' => $attributes['imageSize'],
        ];
        
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
        $classes[] = "layout-".$args['blockLayout'];
        $classes[] = "size-".$args['blockSize'];
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
        }
        
        //just check if ratings should by shown generally, we'll check if rating is visible for each item in item-container template
        $show_ratings_generally = \Citadela\Directory\ItemReviews::$enabled && $args['showRating'];

        $postType = 'citadela-item';

        $showLoader = $useResponsive;
        if( $showLoader ){
            $classes[] = "loading-content";
        }

        ob_start();
        ?>

        <div 
            class="wp-block-citadela-blocks ctdl-directory-search-results <?php echo esc_attr( implode(" ", $classes) ) ;?>"
            <?php if( $useResponsive ) echo 'data-block-mobile-attr="' . htmlspecialchars( json_encode( $data ) ) . '"' ?>
            <?php if( $useResponsive ) echo 'data-block-mobile-breakpoint="' . $attributes['breakpointMobileImageHeight'] . '"' ?>	
            >
            <?php if( $showLoader ) : ?>
                <div class="citadela-loader">
                    <div class="inner-wrapper">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </div>
                </div>
            <?php endif; ?>
            <?php include dirname( __FILE__ ) . "/../../plugin/parts/search-results.php"; ?>
        </div>

        <?php

        /* Restore original Post Data */
        wp_reset_postdata();

        return ob_get_clean();
    }

    private static function enqueueFrontendScript() {
        $dir = dirname( __FILE__ );
        $script_path = plugin_dir_path( __FILE__ ) . 'src/frontend-js.js';
        $script_url = plugins_url( '/src/frontend-js.js', __FILE__ );
        $script_dependencies = [ 'jquery' ];
        wp_enqueue_script( 'directory-search-results-block-frontend', $script_url, $script_dependencies, filemtime( $script_path ), false );
    }

}

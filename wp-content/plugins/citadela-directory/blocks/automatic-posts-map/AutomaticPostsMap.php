<?php

namespace Citadela\Directory\Blocks;

class AutomaticPostsMap extends MapBlock {

    protected static $slug = 'automatic-posts-map';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        $provider = $attributes[ 'provider' ];
        self::enqueueMapInitializerByProvider( $provider );

        $panoramaPov = false;
        if ( is_singular('post') ) {
            global $post;
            // if there are no GPS coordinates, do not show map at all if would be hidden
            if( $attributes['noDataBehavior'] == 'hidden-map' ){
                $lat = get_post_meta($post->ID, '_citadela_latitude', true);
                $lon = get_post_meta($post->ID, '_citadela_longitude', true);
                if( ! $lon && ! $lat ){
                    return '';
                }
            }
            if ( !empty(get_post_meta($post->ID, '_citadela_streetview', true)) ) {
                $panoramaPov = [
                    'heading' => intval( get_post_meta($post->ID, '_citadela_swheading', true) ),
                    'pitch' => intval( get_post_meta($post->ID, '_citadela_swpitch', true) ),
                    'zoom' => intval( get_post_meta($post->ID, '_citadela_swzoom', true) ),
                ];
            }
        }

        $customTheme = self::validate_custom_theme( $attributes['customTheme'] );
        $endpointAtts = [];
        if( isset( $attributes['limitPosts'] ) && $attributes['limitPosts'] && isset( $attributes['maxPosts'] ) && intval( $attributes['maxPosts'] ) > 0 ){
            $endpointAtts['limitPosts'] = $attributes['limitPosts'];   
            $endpointAtts['maxPosts'] = $attributes['maxPosts'];   
        }

        $dataEndpoint = \CitadelaDirectoryFunctions::guessMapEndpoint( $endpointAtts );

        $dataAttributes = 'data-endpoint="' . htmlspecialchars( ($dataEndpoint) ) . '"';
        
        $theme = $provider == "openstreetmap" ? $attributes['themeOSM'] : $attributes['theme'];
        $dataAttributes .= 'data-theme="' . $theme . '"';
        $dataAttributes .= $provider == "google-map" && $customTheme ? 'data-custom-theme="' . htmlspecialchars( $customTheme ) . '"' : "";

        $dataAttributes .= 'data-streetview="' . htmlspecialchars( json_encode($panoramaPov) ) . '"';

        $dataAttributes .= isset( $attributes['height'] ) && $attributes['height'] && ! ( $attributes['inColumn'] && $attributes['coverHeight'] ) ? 'data-map-height="' . $attributes['height'].$attributes['unit'] . '"' : "";
        $dataAttributes .= $attributes['withSearchForm'] ? 'data-outside-form-breakpoint="' . $attributes['outsideFormBreakpoint'] . '"' : "";

        $dataAttributes .= 'data-no-data-behavior="' . $attributes['noDataBehavior'] . '"';
        $dataAttributes .= $attributes['noDataBehavior'] == 'empty-map' ? 'data-no-data-text="' . esc_html( $attributes['noDataText'] ) . '"' : "";
        $dataAttributes .= 'data-cluster="' . $attributes['clusterGridSize'] . '"';
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        if( isset( $attributes['height'] ) && $attributes['height'] && ! ( $attributes['inColumn'] && $attributes['coverHeight'] )  ){
            $classes[] = 'custom-height';
        }
        if( $attributes['inColumn'] && $attributes['coverHeight'] ){
            $classes[] = 'cover-height';
        }
        
        /* right now the whole content is either empty string or innerBlock citadela-directory-blocks/directory-search-form */
        $searchForm = $content;

        return "
        <div class='wp-block-citadela-blocks ctdl-automatic-posts-map loading-content " . esc_attr( implode( ' ', $classes ) ) . "'>
            <div class='citadela-{$provider} provider-wrapper'>
                <div {$dataAttributes} class='component-container'></div>
                {$searchForm}
            </div>
            <div class='citadela-loader'>
                <div class='inner-wrapper'>
                    <i class='fas fa-circle-notch fa-spin'></i>
                </div>
            </div>
        </div>
        ";
    }
    
}
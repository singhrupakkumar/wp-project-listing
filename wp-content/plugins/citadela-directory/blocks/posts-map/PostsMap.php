<?php

namespace Citadela\Directory\Blocks;

class PostsMap extends MapBlock {

    protected static $slug = 'posts-map';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }

        $provider = $attributes[ 'provider' ];
        self::enqueueMapInitializerByProvider( $provider );

        $customTheme = self::validate_custom_theme( $attributes['customTheme'] );

        //define fixed-map attribute to specify endpoint and recognize it's not automatic map
        $attributes['fixed-map'] = true;
        $dataEndpoint = \CitadelaDirectoryFunctions::guessMapEndpoint($attributes);

        $dataAttributes = 'data-endpoint="' . htmlspecialchars( ($dataEndpoint) ) . '"';
        
        $theme = $provider == "openstreetmap" ? $attributes['themeOSM'] : $attributes['theme'];
        $dataAttributes .= 'data-theme="' . $theme . '"';
        $dataAttributes .= $provider == "google-map" && $customTheme ? 'data-custom-theme="' . htmlspecialchars( $customTheme ) . '"' : "";

        $dataAttributes .= isset( $attributes['height'] ) && $attributes['height'] && ! ( $attributes['inColumn'] && $attributes['coverHeight'] ) ? 'data-map-height="' . $attributes['height'].$attributes['unit'] . '"' : "";
        $dataAttributes .= $attributes['withSearchForm'] ? 'data-outside-form-breakpoint="' . $attributes['outsideFormBreakpoint'] . '"' : "";
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
        <div class='wp-block-citadela-blocks ctdl-posts-map loading-content " . esc_attr( implode( ' ', $classes ) ) . "'>
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
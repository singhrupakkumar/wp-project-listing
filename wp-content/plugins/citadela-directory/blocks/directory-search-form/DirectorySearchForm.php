<?php

namespace Citadela\Directory\Blocks;

class DirectorySearchForm extends Block {

    protected static $slug = 'directory-search-form';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
        
        $activeProPlugin = defined( 'CITADELA_PRO_PLUGIN' );

        self::enqueueFrontendScript();

        $searchUrl = esc_url( get_home_url() );

        $postType = 'citadela-item';

        $category = 'citadela-item-category';
        $location = 'citadela-item-location';

        $dataAttributes = 'data-action="' . htmlspecialchars( json_encode($searchUrl) ) . '"';

        $dataAttributes .= 'data-post-type="' . $postType . '"';
        $dataAttributes .= 'data-category-taxonomy="' . $category . '"';
        $dataAttributes .= 'data-location-taxonomy="' . $location . '"';

        $args = [];
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $mainFormStyles = [];

        if( $attributes['withAdvancedFilters'] ){
            $classes[] = 'with-advanced-filters';
            $args['withAdvancedFilters'] = $attributes['withAdvancedFilters'];           
        }
        
        if( $attributes['useGeolocationInput'] ){
            $classes[] = "has-geolocation-input";
            $args['useGeolocationInput'] = $attributes['useGeolocationInput'];
            $args['geoUnit'] = $attributes['geoUnit'];
            $args['geoMax'] = $attributes['geoMax'];
            $args['geoStep'] = $attributes['geoStep'];
            $args['geoDistanceLabel'] = $attributes['geoDistanceLabel'];
            $args['geoDistanceSubmitLabel'] = $attributes['geoDistanceSubmitLabel'];
            $args['geoDisableLabel'] = $attributes['geoDisableLabel'];
        }
        

        if( $activeProPlugin ){
            
            $hasBackground = $attributes['backgroundType'] == 'background' || $attributes['backgroundType'] == 'background-collapsed';
            
            $classes[] = "border-{$attributes['borderWidth']}";
            $classes[] = "shadow-{$attributes['boxShadowType']}";
            $classes[] = "align-{$attributes['align']}";

            if( isset( $attributes['buttonBackgroundColor'] ) && $attributes['buttonBackgroundColor'] ){
                $classes[] = 'custom-button-background-color';
                $args['buttonBackgroundColor'] = esc_attr( $attributes['buttonBackgroundColor'] );
            }
            if( isset( $attributes['buttonTextColor'] ) && $attributes['buttonTextColor'] ){
                $classes[] = 'custom-button-text-color';
                $args['buttonTextColor'] = esc_attr( $attributes['buttonTextColor'] );
            }

            if( $attributes['backgroundType'] == 'background' ){
                $classes[] = "has-background";
                if( $attributes['backgroundBlur'] ){
                    $classes[] = "blur-background";
                    $mainFormStyles[] = "-webkit-backdrop-filter: blur(" . esc_attr( $attributes['blurRadius'] ) . "px);";
                    $mainFormStyles[] = "backdrop-filter: blur(" . esc_attr( $attributes['blurRadius'] ) . "px);";
                }
            }
            if( $attributes['backgroundType'] == 'background-collapsed' ){
                $classes[] = "has-background-collapsed";
                if( $attributes['backgroundBlur'] ){
                    $classes[] = "blur-background";
                    $mainFormStyles[] = "-webkit-backdrop-filter: blur(" . esc_attr( $attributes['blurRadius'] ) . "px);";
                    $mainFormStyles[] = "backdrop-filter: blur(" . esc_attr( $attributes['blurRadius'] ) . "px);";
                }
            }

            if( $hasBackground && $attributes['backgroundColorType'] == 'solid' ){
                $classes[] = "solid-background";
                if( $attributes['backgroundColor'] ){
                    $classes[] = "custom-solid-background-color";
                    $mainFormStyles[] = "background-color: " . esc_attr( $attributes['backgroundColor'] ) . ";";
                }
            }

            if( $hasBackground && $attributes['backgroundColorType'] == 'gradient' ){
                $classes[] = "gradient-background";
                $gradient = $attributes['backgroundGradient'];
                $gradientStyle = $gradient['type'] == 'linear'
                    ? "linear-gradient({$gradient['degree']}deg, {$gradient['first']} 0%, {$gradient['second']} 100%)"
                    : "radial-gradient({$gradient['first']} 0%, {$gradient['second']} 100%)";
                $mainFormStyles[] = "background-image: " . esc_attr( $gradientStyle ) . ";";
            }

            if( isset( $attributes['borderRadius'] ) ){
                $classes[] = "custom-border-radius";
                $args['borderRadius'] = esc_attr( $attributes['borderRadius'] );
            }
            
            if( $attributes['borderWidth'] != 'none' && $attributes['borderColor'] ){
                $args['borderColor'] = esc_attr( $attributes['borderColor'] );
            }
    
            if( $attributes['boxShadowType'] === 'custom' && isset( $attributes['boxShadow'] ) ){
                $boxShadow = $attributes['boxShadow'];
                $shadow = "{$boxShadow['horizontal']}px {$boxShadow['vertical']}px {$boxShadow['blur']}px {$boxShadow['spread']}px rgba({$boxShadow['color']['r']}, {$boxShadow['color']['g']}, {$boxShadow['color']['b']}, {$boxShadow['color']['a']})";
                $args['boxShadow'] = esc_attr( $shadow );
            }
            
        }

        $dataAttributes .= ! empty( $args ) ? 'data-attributes="' . htmlspecialchars( json_encode( $args ) ) . '"' : '';
        
        
        ob_start();
        
        ?>

        <div class="wp-block-citadela-blocks ctdl-directory-search-form <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <div class="citadela-block-form search-form-component-container" <?php echo 'style="' . implode( ' ', $mainFormStyles ) . '"'; ?> <?php echo $dataAttributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
            </div>
            <?php 
                /* right now the whole content is either empty string or innerBlock citadela-directory-blocks/directory-advanced-filters */
                echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </div>

        <?php
        return ob_get_clean();
    }

    private static function enqueueFrontendScript() {
        $paths = \CitadelaDirectory::getInstance()->paths;
        
        $script_path       = "{$paths->dir->blocks}/common-scripts/search-forms-initializer.js";
        $script_asset_path = "{$paths->dir->blocks}/common-scripts/search-forms-initializer.asset.php";

        $script_asset      = file_exists( $script_asset_path )
            ? require( $script_asset_path ) 
            : [ 'dependencies' => [], 'version' => filemtime( $script_asset_path ) ];
        
        $script_url = "{$paths->url->blocks}/common-scripts/search-forms-initializer.js";

        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'jquery', 'wp-i18n', 'wp-components', 'lodash', 'wp-api-fetch' ] );

        wp_register_script(
            "search-forms-initializer",
            $script_url,
            $script_dependencies,
            $script_asset['version'],
            true
        );
        
        wp_set_script_translations( "search-forms-initializer", 'citadela-directory', $paths->dir->languages );

        wp_enqueue_script( 'search-forms-initializer' );

    }
}
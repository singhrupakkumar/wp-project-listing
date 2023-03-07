<?php

namespace Citadela\Directory\Blocks;

class MapBlock extends Block {

    protected static function enqueueMapInitializerByProvider( $provider ) {
        if ( $provider == 'openstreetmap' ) {
            self::enqueueLeaflet();
        } else {
            self::enqueueGoogle();
        }
    }

    

    protected static function enqueueGoogle() {
        \CitadelaDirectory::getInstance()->enqueueGoogleMasApi();

        $dir = dirname( __FILE__ );
        $paths = \CitadelaDirectory::getInstance()->paths;
        $script_path       = '/common-scripts/google-maps-initializer.js';
        $script_asset_path = '/common-scripts/google-maps-initializer.asset.php';
            
        $script_asset      = file_exists( "$dir/$script_asset_path" )
            ? require( "$dir/$script_asset_path" ) 
            : [ 'dependencies' => [], 'version' => filemtime( "$dir/$script_path" ) ];
        
        $script_url = plugins_url( $script_path, __FILE__ );

        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'wp-i18n', 'wp-polyfill', 'citadela-google-maps', 'citadela-markerwithlabel', 'citadela-overlapping-marker-spiderfier' ] );

        wp_register_script(
            "google-maps-initializer",
            $script_url,
            $script_dependencies,
            $script_asset['version'],
            true
        );
        wp_set_script_translations( "google-maps-initializer", 'citadela-directory', $paths->dir->languages );
        wp_enqueue_script( 'google-maps-initializer' );
    }



    protected static function enqueueLeaflet() {
        $dir = dirname( __FILE__ );
        $paths = \CitadelaDirectory::getInstance()->paths;

        $script_path       = '/common-scripts/leaflet-maps-initializer.js';
        $script_asset_path = '/common-scripts/leaflet-maps-initializer.asset.php';

        $script_asset      = file_exists( "$dir/$script_asset_path" )
            ? require( "$dir/$script_asset_path" ) 
            : [ 'dependencies' => [], 'version' => filemtime( "$dir/$script_path" ) ];
        
        $script_url = plugins_url( $script_path, __FILE__ );

        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'wp-i18n', 'citadela-leaflet' ] );
        
        wp_register_script(
            "leaflet-maps-initializer",
            $script_url,
            $script_dependencies,
            $script_asset['version'],
            true
        );

        wp_set_script_translations( "leaflet-maps-initializer", 'citadela-directory', $paths->dir->languages );
        wp_enqueue_script( 'leaflet-maps-initializer' );
    }

    protected static function validate_custom_theme( $theme_string ) {
        json_decode( $theme_string );

        return (json_last_error() == JSON_ERROR_NONE ) ? $theme_string : '[]';
    }

}
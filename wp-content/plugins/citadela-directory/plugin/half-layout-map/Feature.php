<?php

namespace Citadela\Directory\HalfLayoutMap;

class Feature {

    protected $plugin = null;

    function __construct() {
        $this->plugin = \CitadelaDirectory::getInstance();

        add_action( 'init', [ $this, 'init' ] );

    }

    function init() {
        //allow only with Citadela Pro plugin
        if ( ! defined('CITADELA_PRO_PLUGIN') ) return;

        $this->register_pages_meta();

        
        if( ! current_user_can( 'edit_posts' ) ) return;
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
    }
    
    function render_map_html() {
        $theme_instance = \Citadela_Theme::get_instance();
        $post_id = $theme_instance->get_page_id();
        
        // do not render map if it's not half layout template
        if( $theme_instance->get_page_template_type() !== 'half-layout' ) return null;
        // do not render map on item detail page if there are no data to show on map
        if ( is_singular('citadela-item') ) {
            global $post;
            $item_post_id = $post->ID;
            // if there are no GPS coordinates and GPX track, do not show map block on item page at all if map would be hidden
            $noDataBehavior = get_post_meta( $post_id, '_citadela_half_map_noDataBehavior', true );
            if( $noDataBehavior == 'hidden-map' ){
                $track = get_post_meta($item_post_id, '_citadela_gpx_track', true);
                $lat = get_post_meta($item_post_id, '_citadela_latitude', true);
                $lon = get_post_meta($item_post_id, '_citadela_longitude', true);
                if( ! $track && ! $lon && !$lat ){
                    return null;
                }
            }
        }

        
        $attributes = [
            'position' =>  get_post_meta( $post_id, '_citadela_half_map_position', true ),
            'theme' =>  get_post_meta( $post_id, '_citadela_half_map_theme', true ),
            'themeOSM' =>  get_post_meta( $post_id, '_citadela_half_map_themeOSM', true ),
            'customTheme' =>  get_post_meta( $post_id, '_citadela_half_map_customTheme', true ),
            'provider' =>  get_post_meta( $post_id, '_citadela_half_map_provider', true ),
            'dataType' =>  get_post_meta( $post_id, '_citadela_half_map_dataType', true ),
            'dynamicTrack' =>  get_post_meta( $post_id, '_citadela_half_map_dynamicTrack', true ),
            'trackColor' =>  get_post_meta( $post_id, '_citadela_half_map_trackColor', true ),
            'trackEndpointsColor' =>  get_post_meta( $post_id, '_citadela_half_map_trackEndpointsColor', true ),
            'category' =>  get_post_meta( $post_id, '_citadela_half_map_filterCategory', true ),
            'location' =>  get_post_meta( $post_id, '_citadela_half_map_filterLocation', true ),
            'onlyFeatured' =>  get_post_meta( $post_id, '_citadela_half_map_filterFeatured', true ),
            'noDataBehavior' =>  get_post_meta( $post_id, '_citadela_half_map_noDataBehavior', true ),
            'noDataText' =>  get_post_meta( $post_id, '_citadela_half_map_noDataText', true ),
            'clusterGridSize' =>  get_post_meta( $post_id, '_citadela_half_map_clusterGridSize', true ),
            
        ];

        //override with defaults if meta were not available
        $attributes[ 'provider' ] = $attributes[ 'provider' ] ? $attributes[ 'provider' ] : 'openstreetmap';
        $attributes[ 'position' ] = $attributes[ 'position' ] ? $attributes[ 'position' ] : 'right';
        $attributes[ 'theme' ] = $attributes[ 'theme' ] ? $attributes[ 'theme' ] : 'citadela';
        $attributes[ 'themeOSM' ] = $attributes[ 'themeOSM' ] ? $attributes[ 'themeOSM' ] : 'default';
        $attributes[ 'dataType' ] = $attributes[ 'dataType' ] ? $attributes[ 'dataType' ] : 'markers';
        $attributes[ 'dynamicTrack' ] = $attributes[ 'dynamicTrack' ] == '' ? false : true;
        $position = $attributes[ 'position' ];
        $provider = $attributes[ 'provider' ];
        $this->enqueueMapInitializerByProvider( $provider );

        $customTheme = $this->validate_custom_theme( $attributes['customTheme'] );
        
        $endpointAtts = $attributes;
        $endpointDataType = $attributes['dataType'];
        $dataAttributes = '';

        //customize data to get correct endpoint url
        if( is_singular('citadela-item') ) {
            $endpointAtts = [];
            $endpointDataType = $attributes[ 'dataType' ] = 'all';
            $attributes[ 'dynamicTrack' ] = true;
            $dataAttributes .= 'data-single-item="true"';
        }elseif( is_search() && isset( $_REQUEST[ 'ctdl' ] ) && $_REQUEST[ 'post_type' ] == 'citadela-item'  ){
            $endpointAtts = [];
        }elseif( is_tax('citadela-item-category') ){
            $endpointAtts = [];
        }elseif( is_tax('citadela-item-location') ){
            $endpointAtts = [];
        }else{
            //define fixed-map attribute to specify endpoint and recognize it's not automatic map
            $endpointAtts['fixed-map'] = true;
        }

        if( \Citadela\Directory\AdvancedFilters::$use ){
            $endpointAtts[ 'advanced_filters' ] = true;
        }
        
        $isGeolocation = isset( $_REQUEST['rad'] ) && $_REQUEST['rad'] != '' && isset( $_REQUEST['lat'] ) && $_REQUEST['lat'] != '' && isset( $_REQUEST['lon'] ) && $_REQUEST['lon'] != '';
        if( $isGeolocation ){
            $geoData = [
                'rad' => $_REQUEST['rad'],
                'lat' => $_REQUEST['lat'],
                'lon' => $_REQUEST['lon'],
                'unit' => isset( $_REQUEST['unit'] ) && $_REQUEST['unit'] != '' ? $_REQUEST['unit'] : 'km',
            ];
            $dataAttributes .= 'data-geolocation="'. htmlspecialchars( json_encode( $geoData ) ) .'"';
            $endpointAtts[ 'geolocation' ] = $geoData;
        }
        
        $dataEndpoint = \CitadelaDirectoryFunctions::guessMapEndpoint( $endpointAtts , $endpointDataType );

        $dataAttributes .= 'data-endpoint="' . htmlspecialchars( ($dataEndpoint) ) . '"';
        $theme = $provider == "openstreetmap" ? esc_html( $attributes['themeOSM'] ) : esc_html( $attributes['theme'] );
        $dataAttributes .= 'data-theme="' . $theme . '"';
        $dataAttributes .= $provider == "google-map" && $customTheme ? 'data-custom-theme="' . htmlspecialchars( $customTheme ) . '"' : "";
        
        if( $provider == 'openstreetmap' ){
            
            $dataAttributes .= 'data-type="' . esc_html( $attributes['dataType'] ) . '"';

            if( $attributes['dynamicTrack'] ) {
                $dataAttributes .= 'data-dynamic-track="true"';
            }

            if( $attributes['trackColor'] ) $dataAttributes .= 'data-track-color="' . esc_html( $attributes['trackColor'] ) . '"';
            if( $attributes['trackEndpointsColor'] ) $dataAttributes .= 'data-track-endpoints-color="' . esc_html( $attributes['trackEndpointsColor'] ) . '"';
        }

        $dataAttributes .= 'data-no-data-behavior="' . esc_attr( $attributes['noDataBehavior'] ) . '"';
        $dataAttributes .= $attributes['noDataBehavior'] == 'empty-map' ? 'data-no-data-text="' . esc_html( $attributes['noDataText'] ) . '"' : "";
        $dataAttributes .= 'data-cluster="' . $attributes['clusterGridSize'] . '"';
        $dataAttributes .= 'data-is-half-layout-map="true"';
        ?>
            <div class='wp-block-citadela-blocks ctdl-automatic-directory-google-map loading-content'>
                <div class='citadela-<?php echo esc_attr( $provider ); ?> provider-wrapper'>
                    <div 
                        <?php echo $dataAttributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> 
                        class='component-container'
                    ></div>
                </div>
                <div class='citadela-loader'>
                    <div class='inner-wrapper'>
                        <i class='fas fa-circle-notch fa-spin'></i>
                    </div>
                </div>
            </div>

        <?php
    }

    function enqueueMapInitializerByProvider( $provider ) {
        if ( $provider == 'openstreetmap' ) {
            $this->enqueueLeaflet();
        } else {
            $this->enqueueGoogle();
        }
    }

    function enqueueGoogle() {
        \CitadelaDirectory::getInstance()->enqueueGoogleMasApi();

        $script_url       = "{$this->plugin->paths->url->blocks}/common-scripts/google-maps-initializer.js";
        $script_path       = "{$this->plugin->paths->dir->blocks}/common-scripts/google-maps-initializer.js";
        $script_asset_path = "{$this->plugin->paths->dir->blocks}/common-scripts/google-maps-initializer.asset.php";
            
        $script_asset      = file_exists( $script_asset_path )
            ? require( $script_asset_path ) 
            : [ 'dependencies' => [], 'version' => filemtime( $script_path ) ];
        
        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'wp-i18n', 'wp-polyfill', 'citadela-google-maps', 'citadela-markerwithlabel', 'citadela-overlapping-marker-spiderfier' ] );

        wp_enqueue_script( 'google-maps-initializer', $script_url, $script_dependencies, $script_asset['version'], false );
    }



    function enqueueLeaflet() {
        \CitadelaDirectory::getInstance()->enqueueLeafletAssets();

        $script_url       = "{$this->plugin->paths->url->blocks}/common-scripts/leaflet-maps-initializer.js";
        $script_path       = "{$this->plugin->paths->dir->blocks}/common-scripts/leaflet-maps-initializer.js";
        $script_asset_path = "{$this->plugin->paths->dir->blocks}/common-scripts/leaflet-maps-initializer.asset.php";

        $script_asset      = file_exists( $script_asset_path )
        ? require( $script_asset_path ) 
        : [ 'dependencies' => [], 'version' => filemtime( $script_path ) ];

        $script_dependencies = array_merge( $script_asset['dependencies'], [ 'wp-i18n', 'citadela-leaflet' ] );

        wp_enqueue_script( 'leaflet-maps-initializer', $script_url, $script_dependencies, $script_asset['version'], true );
    }

    function validate_custom_theme( $theme_string ) {
        json_decode( $theme_string );

        return (json_last_error() == JSON_ERROR_NONE ) ? $theme_string : '[]';
    }

    function enqueue_block_editor_assets(){
        $current_screen = get_current_screen();
        // enqueue settings only for editor on pages or posts
        if( $current_screen && $current_screen->id != 'widgets' ){
            wp_enqueue_script( 'citadela-directory-half-layout-map-editor-js' );
        }
    }

    function register_pages_meta() {
        $post_types = [ 'page', 'special_page' ];

        $item_detail_options = get_option('citadela_directory_item_detail');
        $allowed_editor = $this->plugin->ItemPageLayout_instance->allowed_editor;
        
        if( $allowed_editor ){
            $post_types[] = 'citadela-item';            
        }
        
        foreach ( $post_types as $post_type ) {

            register_meta( 'post', '_citadela_half_map_provider', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_theme', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_themeOSM', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_customTheme', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_dataType', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_dynamicTrack', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_trackColor', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_trackEndpointsColor', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_filterCategory', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_filterLocation', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_filterFeatured', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'boolean',
                'single' => true,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_noDataBehavior', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'default' => 'empty-map',
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_noDataText', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'string',
                'single' => true,
                'default' => '',
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );

            register_meta( 'post', '_citadela_half_map_clusterGridSize', [
                'object_subtype' => $post_type,
                'show_in_rest' => true,
                'type' => 'number',
                'single' => true,
                'default' => 80,
                'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
                }
            ] );
            
        }
        
        $path = "{$this->plugin->paths->dir->assets}/citadela/half-layout-map";
        $url = "{$this->plugin->paths->url->assets}/citadela/half-layout-map";
        
        $editor_asset_file = include( "{$path}/build/editor.asset.php" );

        wp_register_script(
            'citadela-directory-half-layout-map-editor-js',
            "{$url}/build/editor.js",
            array_merge( [ 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ], $editor_asset_file[ 'dependencies' ] ) ,
            filemtime( "{$path}/build/editor.js" ),
            true
        );
        wp_set_script_translations( 'citadela-directory-half-layout-map-editor-js', 'citadela-directory', $this->plugin->paths->dir->languages );

    }
}
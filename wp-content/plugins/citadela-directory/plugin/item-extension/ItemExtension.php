<?php

namespace Citadela\Directory;

class ItemExtension {

    protected static $plugin = null;
    public static $enabled;
    public static $options;

    public static function run() {

        self::$plugin = \CitadelaDirectory::getInstance();
        self::$options = get_option( 'citadela_directory_item_extension', [] );
        self::$enabled = self::enabled(); 

        add_action( 'init', [ __CLASS__, 'init' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ] );
        add_action( 'rest_api_init', [ __CLASS__, 'rest_api_init' ] );
    }


    public static function init() {

    }
    
    public static function admin_enqueue_scripts(){
        $assets_url = self::$plugin->paths->url->assets;
        wp_enqueue_script( 'jquery-ui-touch-punch', "{$assets_url}/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js", [ 'jquery' ], '0.2.3', true );
    }

    public static function rest_api_init(){

        register_rest_route( 'citadela-directory', '/item-extension/options', [
            'methods' => 'GET',
            'callback' =>  [ __CLASS__, 'get_options_callback' ],
            'permission_callback' => "__return_true",
        ] );

    }

    public static function get_options_callback( \WP_REST_Request $request ){
        return self::$options;
    }

    public static function validate_output( $input_data, $value, $meta_id ){


        switch ( $input_data['type'] ) {
            case 'citadela_multiselect':
                global $post;
                if( ! $post ) return '';
                $result = [];
                foreach ($input_data['choices'] as $key => $value) {
                    if( get_post_meta( $post->ID, "{$meta_id}_{$key}", true ) ) $result[] = "<span class=\"value\">{$value}</span>";
                }
                return ! empty( $result ) ? implode('', $result) : '';
                break;
            case 'select':
                //check for possible empty first select option
                $value = $value !== '' && isset( $input_data['choices'][$value] ) ? $input_data['choices'][$value] : '';
                return $value ? $value : '';
                break;
            case 'checkbox':
                $value = $value ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
                return $value ? $value : '';
                break;
            case 'textarea':
                $value = wpautop( $value );
                return $value ? $value : '';
                break;
            
            default:
                return $value ? $value : '';
                break;
        }
    }

    private static function enabled(){
        return isset( self::$options['enable'] ) && self::$options['enable'];
    }

}
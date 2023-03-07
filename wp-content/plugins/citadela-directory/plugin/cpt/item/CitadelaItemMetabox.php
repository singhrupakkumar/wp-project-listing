<?php

// ===============================================
// Citadela Listing Item Metabox
// -----------------------------------------------


class CitadelaItemMetabox
{

    protected static $manager, $plugin, $paths;
    
    public static $active_item_extension, $item_extension;

    public function __construct(){
        throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
    }

    public static function run()
    {
        self::$plugin = CitadelaDirectory::getInstance();
        self::$paths = $paths = CitadelaDirectoryPaths::getPaths();

        self::$item_extension = get_option( 'citadela_directory_item_extension', [] );
        self::$active_item_extension = ! empty( self::$item_extension ) && self::$item_extension['enable'];

        require_once $paths->dir->libs . '/butterbean/butterbean.php';

        add_action('butterbean_register', function($butterbean, $post_type){
            self::register($butterbean, $post_type);
        } , 10, 2);

        add_action( 'admin_enqueue_scripts', function( $hook ) {
            if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
                wp_enqueue_script( 'citadela-leaflet' );
        
                wp_enqueue_style( 'citadela-leaflet') ;
            }
        }, 10, 1 );

    }



    public static function get_meta_keys()
    {
        if(self::$manager){
            return array_keys(self::$manager->settings);
        }
        return [];
    }



    protected static function register($butterbean, $post_type)
    {
        $permissions = Citadela\Directory\Subscriptions\Feature::getPermissionsItem();
        $have_some_permission = in_array( true, $permissions );
        if( ! $have_some_permission ) return;
        
        self::init_manager($butterbean);

        $itemOptionsSections = CitadelaItem::$config->sections->citadela_item_options;
        foreach ($permissions as $key => $permission) {
            if (!$permission) {
                unset($itemOptionsSections[$key]);
            }
        }
        self::registerSections($itemOptionsSections);

        $itemOptionsFields = CitadelaItem::$config->fields->citadela_item_options;
        foreach ($itemOptionsFields as $key => $value) {
            if (isset($permissions[$value['section']]) && !$permissions[$value['section']]) {
                unset($itemOptionsFields[$key]);
            }
        }

        $screen = get_current_screen();
        if( $screen && $screen->post_type == 'citadela-item' && isset( $itemOptionsFields['featured_category'] ) ) {
            $post_id = isset( $_REQUEST['post'] ) ? intval( $_REQUEST['post'] ) : false;
            if( $post_id ){
                $terms = wp_get_post_terms( $post_id, $itemOptionsFields['featured_category']['taxonomy'] );
                $itemOptionsFields['featured_category']['citadela_settings']['post_id'] = $post_id;
                if( $terms ){
                    foreach ($terms as $term) {
                        $meta = get_term_meta($term->term_id, 'citadela-item-category-meta', true);
                        $itemOptionsFields['featured_category']['citadela_settings']['terms'][] = [
                            'term_name' => htmlspecialchars_decode( $term->name ),
                            'term_slug' => $term->slug,
                            'term_id'   => $term->term_id,
                            'term_meta' => $meta,
                        ];
                    }
                }
                
            }
        }


        self::registerFields($itemOptionsFields);

        // Item Extension sections
        if (self::$active_item_extension && $permissions['extension']) {
            $item_extension_meta_prefix = '_citadela_item_extension_';

            self::registerSections([
                'item_extension' => array(
                    'label' => self::$item_extension['inputs_group']['group_name'],
                    'icon'  => 'dashicons-screenoptions',
                ),
            ]);    
            
            // prepare fields to register
            $fields = [];
            foreach (self::$item_extension['inputs_group']['inputs'] as $key => $input_data) {
                $fields[$key] = [
                    'section'       => 'item_extension',
                    'type'          => $input_data['type'],
                    'label'         => $input_data['label'],
                    'description'   => $input_data['description'],
                    'choices'       => [],
                    'attr'          => [
                        'class'         => CitadelaDirectorySettingsItemExtension::get_input_type_classes( $input_data['type'] ),
                    ],
                    'sanitize_callback' => CitadelaDirectorySettingsItemExtension::get_input_type_sanitize_callback( $input_data['type'] ),
                ];
                
                // make sure to not use empty choices inputs, previously available choice values with empty label "key : " or empty key ": label"
                if( isset( $input_data['choices'] ) && is_array( $input_data['choices'] ) ) {
                    foreach ( $input_data['choices'] as $choice_key => $choice_label) {
                        if( $choice_key != "" && $choice_label != "" ) $fields[$key]['choices'][$choice_key] = $choice_label;
                    }
                }

                if( $input_data['type'] == 'text' ){
                    // make sure the Item Extension text input is considered as custom Citadela Text control
                    $fields[$key]['type'] = 'citadela_text';
                }

                if( $input_data['type'] == 'textarea' ){
                    // make sure the Item Extension text input is considered as custom Citadela Text control
                    $fields[$key]['type'] = 'citadela_textarea';
                }

                if( $input_data['type'] == 'select' ){
                    // make sure the Item Extension select is considered as custom Citadela Select control
                    $fields[$key]['type'] = 'citadela_select';
                    $fields[$key]['citadela_settings'] = [
                        'choices_label' => isset( $input_data['choices_label'] ) && $input_data['choices_label'] != '' ? $input_data['choices_label'] : esc_html__("Choose option", 'citadela-directory'),
                    ];
                    
                }
                
                if( $input_data['type'] == 'citadela_number' ){    
                    $fields[$key]['number_data'] = [
                        'unit'          => isset( $input_data['unit'] ) ? $input_data['unit'] : '',
                        'unit-position' => isset( $input_data['unit-position'] ) ? $input_data['unit-position'] : 'left',
                    ];
                }

                if( $input_data['type'] == 'citadela_url' ){  
                    
                    // include butterbean settings to register more meta using one field
                    $fields[$key]['settings'] = [
                        "url" => "{$item_extension_meta_prefix}{$key}",
                        "url_label" => "{$item_extension_meta_prefix}{$key}_label",
                    ];

                    // custom settings we need for our input
                    $fields[$key]['citadela_settings'] = [
                        'use_url_label' => isset( $input_data['use_url_label'] ) ? $input_data['use_url_label'] : false,
                    ];
                }
                
                if( $input_data['type'] == 'citadela_multiselect' ){ 
                    foreach ( $input_data['choices'] as $choice_key => $value ) {
                        $fields[$key]['settings'][$choice_key] = "{$item_extension_meta_prefix}{$key}_{$choice_key}";
                    }
                }

            }

            if( $fields ) self::registerFields( $fields, $item_extension_meta_prefix );
        }
    }



    protected static function init_manager($butterbean)
    {
        $butterbean->register_manager('citadela_item_options', [
            'label'     => esc_html__( 'Item Options', 'citadela-directory' ),
            'post_type' => 'citadela-item',
            'context'   => 'normal',
            'priority'  => 'high'
        ]);

        self::$manager = $manager = $butterbean->get_manager( 'citadela_item_options' );

        CitadelaButterbeanControls::registerCustomControls($butterbean, $manager);

    }

    

    protected static function registerSections( $sections )
    {
        foreach ($sections as $sectionName => $sectionData) {
            self::$manager->register_section($sectionName, $sectionData); 
        }
    }



    protected static function registerFields( $fields, $prefix = '_citadela_' )
    {
        foreach ($fields as $fieldName => $fieldData) {
            $args = [
                'type'          => $fieldData['type'],
                'section'       => $fieldData['section'],
                'label'         => isset($fieldData['label']) ? $fieldData['label'] : '',
                'description'   => isset($fieldData['description']) ? $fieldData['description'] : '',
                'attr'          => isset($fieldData['attr']) ? $fieldData['attr'] : [],
                'settings'      => isset($fieldData['settings']) ? $fieldData['settings'] : [],
                'choices'       => isset($fieldData['choices']) ? $fieldData['choices'] : [],
                'number_data'   => isset($fieldData['number_data']) ? $fieldData['number_data'] : [],
                'citadela_settings' => isset($fieldData['citadela_settings']) ? $fieldData['citadela_settings'] : [],
                'should_register'   => isset($fieldData['should_register']) ? $fieldData['should_register'] : true,
            ];
            
            // make sure to include 'class' parameter
            if( ! isset( $args['attr']['class'] ) ){
                $args['attr']['class'] = '';
            }

            if( $args['should_register'] ){
                self::$manager->register_field( "{$prefix}{$fieldName}", $args, [ 'sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : '' ] );
            }

            // register custom citadela settings
            
            if( $fieldData['type'] == 'citadela_map' ){
                 CitadelaButterbeanControls::registerCitadelaMapControlSettings( $args['settings'], self::$manager );
            }
            
            if( $fieldData['type'] == 'citadela_gpx_upload' ){
                 CitadelaButterbeanControls::registerCitadelaGpxControlSettings( $args['settings'], self::$manager );
            }
            if( $fieldData['type'] == 'citadela_multiselect' ){
                CitadelaButterbeanControls::registerCitadelaMultiselectControlSettings( $args['settings'], self::$manager );
            }

            if( $fieldData['type'] == 'citadela_number' ){
                 self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : ''] );
            }

            if( $fieldData['type'] == 'citadela_select' ){
                 self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : ''] );
            }

            if( $fieldData['type'] == 'citadela_text' ){
                 self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : 'wp_filter_nohtml_kses'] );
            }

            if( $fieldData['type'] == 'citadela_textarea' ){
                 self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : 'wp_kses_post'] );
            }

            if( $fieldData['type'] == 'citadela_url' ){
                // plain url value
                self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : ''] );

                // url label value
                if( isset( $args['citadela_settings'] ) && isset( $args['citadela_settings']['use_url_label'] ) && $args['citadela_settings']['use_url_label'] ){
                    self::$manager->register_setting( "{$prefix}{$fieldName}_label", ['sanitize_callback' => 'wp_filter_nohtml_kses'] );
                }
            }

            if( $fieldData['type'] == 'citadela_featured_category_select' ){
                self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : [ 'CitadelaButterbeanControls', 'sanitize_featured_category' ] ] );
            }

            if( $fieldData['type'] == 'citadela_gallery' ){
                self::$manager->register_setting( "{$prefix}{$fieldName}", ['sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : [ 'CitadelaButterbeanControls', 'sanitize_gallery' ] ] );
            }

        }
    }

}

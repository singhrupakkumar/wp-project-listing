<?php

namespace Citadela\Directory;

class AdvancedFilters {

    protected static $plugin = null;
    public static $enabled;
    public static $options;
    public static $inputs;
    
    // if filters should be used in query
    public static $use;

    // current filters from url parameters
    public static $current_filters;

    public static function run() {
        self::$plugin = \CitadelaDirectory::getInstance();
        add_action( 'init', [ __CLASS__, 'init' ] );
    }

    public static function init(){
        //feature is enabled according to enabled Item Extension functionality
        self::$enabled =  ItemExtension::$enabled;
        if(! self::$enabled ) return;

        self::$options = get_option( 'citadela_directory_advanced_filters', [] );
        self::$inputs = self::getInputs(); 
        self::$use = isset( $_GET['a_filters'] ) && $_GET['a_filters'] == 'true';
        self::$current_filters = self::getCurrentFilters();
    }

    // loop Advanced Filters blocks in page content and get operator settings
    public static function getFiltersOperator( $page ){
               
        $filters = [];
        
        if( $page ){ 
            $content = \CitadelaDirectoryLayouts::getSpecialPageContent( $page );
            $blocks = \CitadelaDirectoryFunctions::getBlocksByName( parse_blocks( $content ), 'citadela-directory/directory-advanced-filters');

            foreach ($blocks as $block) {
                $attrs = $block['attrs'];
                if( isset( $attrs['filter_operators'] ) && ! empty( $attrs['filter_operators'] ) ){
                    foreach ($attrs['filter_operators'] as $filter_group_name => $operator) {
                        if( isset( $attrs['active_filter_groups'] ) && in_array( $filter_group_name, $attrs['active_filter_groups']) ){
                            $filters[$filter_group_name] = $operator;
                        }
                    }
                }
            }
        }
       
        return $filters;
    }

    public static function getCurrentFilters(){
        $filters = [];
        if( self::$inputs && self::$use ){

            $checkbox_filters = isset( $_GET['filters'] ) ? explode( ',', $_GET['filters'] ) : [];
            foreach (self::$inputs as $key => $data) {

                if( $data['type'] == 'checkbox' && in_array( $key, $checkbox_filters ) ){
                    $group_name = $data['checkbox_filters_group_name'].'_checkbox';
                    $filters[$group_name]['type'] = $data['type'];
                    $filters[$group_name]['input_key'] = null;
                    $filters[$group_name]['keys'][] = $key;
                }

                if( $data['type'] == 'select' || $data['type'] == 'citadela_multiselect' ){
                    if( isset( $_GET[$key] ) ) {
                        $group_name = $key.'_'.$data['type'];
                        $filters[$group_name]['type'] = $data['type'];
                        $filters[$group_name]['input_key'] = $key;

                        $filters_names = explode( ',', $_GET[$key] );
                        foreach ($filters_names as $filter_name) {
                            $filters[$group_name]['keys'][] = $filter_name;
                        }
                    }

                }
            }    
        }
        return $filters;
    }

    public static function getInputs(){
        $item_extension = get_option( 'citadela_directory_item_extension', [] );
        $inputs = [];
        if( $item_extension ){
            foreach ($item_extension['inputs_group']['inputs'] as $key => $data) {
                if( isset( $data['use_as_filter'] ) && $data['use_as_filter'] ) $inputs[$key] = $data;
            };
        }
        return $inputs;
    }

    private static function enabled(){
        return isset( self::$options['enable'] ) && self::$options['enable'];
    }

}
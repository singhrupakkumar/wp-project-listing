<?php

class CitadelaPostMetabox
{
    protected static $manager, $paths;

    protected static function sections()
    {
        return [
            'address-location' => array(
                'label' => esc_html__( 'Address and Location', 'citadela-directory' ),
                'icon'  => 'dashicons-location-alt'
            ),
        ];
    }



    protected static function options()
    {
        return [
					
            

            // address section inputs
            'map' => array(
                'type'        => 'citadela_map',
                'section'     => 'address-location',
                'attr'        => array( 'class' => 'widefat' ),
                'settings'    => array(
                    'address'    => '_citadela_address',
                    'latitude'   => '_citadela_latitude',
                    'longitude'  => '_citadela_longitude',
                    'streetview' => '_citadela_streetview',
                    'swheading'  => '_citadela_swheading',
                    'swpitch'    => '_citadela_swpitch',
                    'swzoom'     => '_citadela_swzoom',
                ),
            ),

        ];
    }


    public static function run()
    {
        self::$paths = $paths = CitadelaDirectoryPaths::getPaths();

        require_once $paths->dir->libs . '/butterbean/butterbean.php';

        add_action('butterbean_register', function($butterbean, $post_type){
            self::register($butterbean, $post_type);
        } , 10, 2);
    }



    protected static function register($butterbean, $post_type)
    {
        self::init_manager($butterbean);
        self::registerSections(self::sections());
        self::registerFields(self::options());
    }



    protected static function init_manager($butterbean)
    {
        $butterbean->register_manager('citadela_post_options', [
            'label'     => esc_html__( 'Post Options', 'citadela-directory' ),
            'post_type' => 'post',
            'context'   => 'normal',
            'priority'  => 'high'
        ]);

        self::$manager = $manager = $butterbean->get_manager( 'citadela_post_options' );

        CitadelaButterbeanControls::registerCustomControls($butterbean, $manager);

    }



    protected static function registerSections( $sections )
    {
        foreach ($sections as $sectionName => $sectionData) {
            self::$manager->register_section($sectionName, $sectionData); 
        }
    }



    protected static function registerFields( $fields )
    {
        foreach ($fields as $fieldName => $fieldData) {
            self::$manager->register_field('_citadela_'.$fieldName, [
                'type'      => $fieldData['type'],
                'section'   => $fieldData['section'],
                'label'     => isset($fieldData['label']) ? $fieldData['label'] : '',
                'description'     => isset($fieldData['description']) ? $fieldData['description'] : '',
                'attr'      => isset($fieldData['attr']) ? $fieldData['attr'] : array(),
                'settings'  => isset($fieldData['settings']) ? $fieldData['settings'] : array(),
            ], [
                'sanitize_callback' => isset($fieldData['sanitize_callback']) ? $fieldData['sanitize_callback'] : '',
            ]);

            if( $fieldData['type'] == 'citadela_map' ){
                 CitadelaButterbeanControls::registerCitadelaMapControlSettings( $fieldData['settings'], self::$manager );
            }
        }
    }
}
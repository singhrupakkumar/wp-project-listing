<?php

class CitadelaPost
{
    public static $categorySlug = 'category';
    public static $categoryMetaKey = 'citadela-post-category';

    public static $locationSlug = 'citadela-post-location';
    public static $locationMetaKey = 'citadela-post-location';

    public static $categoryFormFields = [];



    public static function init() {
        self::$categoryFormFields = array(
            'category_icon' => array(
                'type'		  => 'fontawesome-select',
                'title' 	  => esc_html__('Category icon', 'citadela-directory'),
                'description' => '',
                'default'	  => 'fas fa-circle',
                'single-meta' => false
            ),
            'category_color' => array(
                'type'		  => 'colorpicker',
                'title' 	  => esc_html__('Category color', 'citadela-directory'),
                'description' => esc_html__('Category decoration color', 'citadela-directory'),
                'default'	  => "#0085ba",
                'opacity' 	  => false,
                'single-meta' => false
            ),
        );

        CitadelaPostMetabox::run();

		add_action('init', array(__CLASS__, 'registerLocation'), 10, 0);

        add_action('category_add_form_fields', array(__CLASS__, 'addCategoryFormFields'), 10, 2 );
		add_action('category_edit_form_fields', array(__CLASS__, 'editCategoryFormFields'), 10, 2 );
		add_action("edited_category", array(__CLASS__, 'saveCategoryFormFields'), 10, 2);
		add_action("created_category", array(__CLASS__, 'saveCategoryFormFields'), 10, 2);
    }



    public static function registerLocation() {
        $args = array(
            'hierarchical' 		=> true,
            'public' 			=> false,
            'show_in_nav_menus' => true,
            'show_ui' 			=> true,
            'show_admin_column' => true,
            'show_in_rest' 		=> true,
            'rewrite' => array(
                'slug' => 'location',
            ),
            'labels' => array(
                'name'              => esc_html_x('Locations', 'taxonomy general name', 'citadela-directory'),
                'menu_name'         => esc_html_x('Locations', 'taxonomy menu name', 'citadela-directory'),
                'singular_name'     => esc_html_x('Location', 'taxonomy singular name', 'citadela-directory'),
                'search_items'      => esc_html__('Search Locations', 'citadela-directory'),
                'all_items'         => esc_html__('All Locations', 'citadela-directory'),
                'parent_item'       => esc_html__('Parent Location', 'citadela-directory'),
                'parent_item_colon' => esc_html__('Parent Location:', 'citadela-directory'),
                'edit_item'         => esc_html__('Edit Location', 'citadela-directory'),
                'view_item'         => esc_html__('View Location', 'citadela-directory'),
                'update_item'       => esc_html__('Update Location', 'citadela-directory'),
                'add_new_item'      => esc_html__('Add New Location', 'citadela-directory'),
                'new_item_name'     => esc_html__('New Location Name', 'citadela-directory'),
            ),
        );
        register_taxonomy(self::$locationSlug, array('post'), $args);
    }



    public static function addCategoryFormFields($taxonomy) {
		$data = array(
            'taxonomy' 	=> $taxonomy
        );
		self::renderTaxonomyFormFields( $data, 'add-term');
    }



    public static function editCategoryFormFields($tag, $taxonomy) {
		$data = array(
            'tag' 		=> $tag,
            'taxonomy' 	=> $taxonomy
        );
		self::renderTaxonomyFormFields( $data, 'edit-term');
    }



    public static function saveCategoryFormFields($term_id) {

        $taxCodeName = self::$categoryMetaKey;
        $taxConfig = self::$categoryFormFields;
        $termMetaData = array();

        // we will save data for all inputs available in config file, to make sure all of them are saved (prevents not saved data for unchecked checkbox)
        if( isset( $_POST[$taxCodeName] ) ){
            //term is created or edited on Add/Edit Term page, we have access to selected custom data (icon, color.....)
            foreach ($taxConfig as $inputId => $inputConfig) {
                $value = CitadelaDirectoryFunctions::validate_saved_value( $_POST[$taxCodeName][$inputId], $inputConfig['type']  );
                if( isset( $inputConfig['single-meta'] ) && $inputConfig['single-meta'] ){
                    //save this meta as single meta value under unique meta key
                    update_term_meta($term_id, "{$taxCodeName}-{$inputId}", $value );
                }else{
                    //save this meta in array under general meta key
                    $termMetaData[$inputId] = $value;
                }
            }

        }else{
            //term is added via Add New Item page, we have to set default values for custom data    
            foreach ($taxConfig as $inputId => $inputConfig) {
                $value = CitadelaDirectoryFunctions::validate_saved_value( $_POST[$taxCodeName][$inputId], $inputConfig['type']  );
                if( isset( $inputConfig['single-meta'] ) && $inputConfig['single-meta'] ){
                    //save this meta as single meta value under unique meta key
                    update_term_meta($term_id, "{$taxCodeName}-{$inputId}", $value );
                }else{
                    //save this meta in array under general meta key
                    $termMetaData[$inputId] = isset($inputConfig['default']) ? $inputConfig['default'] : '';
                }
            }
        }

        // save general term meta
        update_term_meta($term_id, $taxCodeName, $termMetaData);

	}



    public static function renderTaxonomyFormFields( $data, $screenType ) {
        
        $termMeta = isset($data['tag']) ? get_term_meta($data['tag']->term_id, self::$categoryMetaKey, true) : array();

        foreach (self::$categoryFormFields as $key => $config) {
            
            if( isset( $config['single-meta'] ) && $config['single-meta'] ){
                //set term meta to single meta value
                $termMeta = isset($data['tag']) ? get_term_meta($data['tag']->term_id, "{$taxCodeName}-{$inputId}", true) : '';
                $singleMeta = true;
            }else{
                //set term meta to general meta data saved in array
                $singleMeta = false;
            }

            CitadelaControl::citadelaRenderTaxonomyInput( $key, $config, $termMeta, self::$categoryMetaKey, $screenType, $singleMeta );
        }
	}
}
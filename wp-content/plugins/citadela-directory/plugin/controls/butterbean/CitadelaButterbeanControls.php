<?php

// ===============================================
// Custom Citadela Butterbean Controls functions
// -----------------------------------------------

class CitadelaButterbeanControls {

	protected static $plugin;
	protected static $citadelaControls = [ 
		'citadela_map', 
		'citadela_gpx_upload',
		'citadela_number',
		'citadela_url',
		'citadela_multiselect',
		'citadela_select',
		'citadela_text',
		'citadela_textarea',
		'citadela_featured_category_select',
		'citadela_gallery',
	];

	public function __construct(){
        throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
    }

    public static function registerCustomControls( $butterbean, $manager ) {
    	
    	self::$plugin = CitadelaDirectory::getInstance();

    	add_filter( "butterbean_control_template", array( __CLASS__, 'getCustomControlsTemplate'), 10, 2 );
    	
    	$butterbean->register_control_type( 'citadela_map', 'CitadelaButterbeanMap' );
    	$butterbean->register_control_type( 'citadela_gpx_upload', 'CitadelaButterbeanGpxUpload' );
    	$butterbean->register_control_type( 'citadela_number', 'CitadelaButterbeanNumber' );
    	$butterbean->register_control_type( 'citadela_url', 'CitadelaButterbeanUrl' );
    	$butterbean->register_control_type( 'citadela_multiselect', 'CitadelaButterbeanMultiselect' );
    	$butterbean->register_control_type( 'citadela_select', 'CitadelaButterbeanSelect' );
    	$butterbean->register_control_type( 'citadela_text', 'CitadelaButterbeanText' );
    	$butterbean->register_control_type( 'citadela_textarea', 'CitadelaButterbeanTextarea' );
    	$butterbean->register_control_type( 'citadela_featured_category_select', 'CitadelaButterbeanFeaturedCategorySelect' );
    	$butterbean->register_control_type( 'citadela_gallery', 'CitadelaButterbeanGallery' );
		
    }

    public static function getCustomControlsTemplate( $located, $slug ) {

		if( in_array($slug, self::$citadelaControls) && file_exists( self::$plugin->paths->dir->controls . "/butterbean/templates/control-{$slug}.php" ) ){
			return self::$plugin->paths->dir->controls . "/butterbean/templates/control-{$slug}.php";
		}		
		return $located;
	}

	public static function registerCitadelaMapControlSettings( $settings, $manager ) {
		foreach ($settings as $settingName => $meta) {
			$args = [
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			];
			//customizer sanitize callback for our needs
			if($settingName == 'streetview') {
				$args = array( 'sanitize_callback' => 'butterbean_validate_boolean' );
			}
			if($settingName == 'latitude' || $settingName == 'longitude') {
				$args = array( 'sanitize_callback' => [__CLASS__, 'sanitize_callback_lat_long' ] );
			}

			$manager->register_setting( $meta, $args );
		}
	}

	public static function registerCitadelaGpxControlSettings( $settings, $manager ) {
		foreach ($settings as $settingName => $meta) {
			$sanitize_callback = 'sanitize_callback_gpx_track';
			$manager->register_setting( $meta, array( 'sanitize_callback' => [ __CLASS__, $sanitize_callback ] ) );

		}
	}

	public static function registerCitadelaMultiselectControlSettings( $settings, $manager ) {
		foreach ($settings as $settingName => $meta) {
			$manager->register_setting( $meta, array( 'sanitize_callback' => 'butterbean_validate_boolean' ) );
		}
	}

	public static function sanitize_callback_gpx_track( $data ) {
		//no validation yet
		return $data;
	}

	public static function sanitize_callback_lat_long( $data ) {
		$float = floatval($data);
		return $float ? $float : '0';
	}
	
	public static function sanitize_callback_multiselect( $data ) {
		//make sure to save in database also not checked meta values
		return $data == true ? true : false;
	}

	public static function sanitize_featured_category( $data ) {
		//make sure the featured category is stored only if post is assigned to this category, prevent saving of featured category which was disabled before save.
		global $post;
		$term_id = $data;
		if( $post->ID ){
			$ids = wp_get_post_terms( $post->ID, 'citadela-item-category', [ 'fields' => 'ids' ] );
			if( in_array( $term_id, $ids ) ){
				return $term_id;
			}
		}
		return "";
	}

	public static function sanitize_gallery( $data ) {
		// make sure the media really exists
		$result = [];
		if( $data && ! empty( $data ) ){
			foreach ($data as $media_id) {
				if( get_media_item( $media_id ) ){
					array_push($result, $media_id);
				}
			}
		}
		return $result;
	}
	

}
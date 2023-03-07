<?php

// ===============================================
// Citadela Butterbean Map Control functions
// -----------------------------------------------

class CitadelaButterbeanMap extends ButterBean_Control {

	/*
	*	The type of control.
	*/
	public $type = 'citadela_map';
	

	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		$plugin = CitadelaDirectory::getInstance();
		$google_api_key = $plugin->pluginOptions->general['google_maps_api_key'];
		
		$this->json['address'] = array(
			'label'      => esc_html__('Address', 'citadela-directory'),
			'value'      => $this->get_value( 'address' ),
			'field_name' => $this->get_field_name( 'address' ),
		);

		$this->json['latitude'] = array(
			'label'      => esc_html__('Latitude', 'citadela-directory'),
			'value'      => $this->get_value( 'latitude' ),
			'field_name' => $this->get_field_name( 'latitude' ),
			'default'	 => '0',
		);

		$this->json['longitude'] = array(
			'label'      => esc_html__('Longitude', 'citadela-directory'),
			'value'      => $this->get_value( 'longitude' ),
			'field_name' => $this->get_field_name( 'longitude' ),
			'default'	 => '0',
		);

		$this->json['streetview'] = array(
			'label'      => esc_html__('Show Streetview', 'citadela-directory'),
			'value'      => $this->get_value( 'streetview' ),
			'field_name' => $this->get_field_name( 'streetview' ),
			'description' => $google_api_key ? null : esc_html__('Streetview is available only if Google API key is enabled', 'citadela-directory'),
			'available' => (bool) $google_api_key,
		);

		$this->json['swheading'] = array(
			'value'      => $this->get_value( 'swheading' ),
			'field_name' => $this->get_field_name( 'swheading' ),
			'default'	 => '0',
		);

		$this->json['swpitch'] = array(
			'value'      => $this->get_value( 'swpitch' ),
			'field_name' => $this->get_field_name( 'swpitch' ),
			'default'	 => '0',
		);

		$this->json['swzoom'] = array(
			'value'      => $this->get_value( 'swzoom' ),
			'field_name' => $this->get_field_name( 'swzoom' ),
			'default'	 => '0',
		);
	}

	/*
	*	Adds custom attributes for html.
	*/
	public function get_attr() {
		$this->attr = parent::get_attr();
		$this->attr['class'] .= " {$this->type}-control";
		
		$plugin_instance = CitadelaDirectory::getInstance();
		$google_api_key = $plugin_instance->pluginOptions->general['google_maps_api_key'];
		$this->attr['data-map-provider'] = $google_api_key ? 'google' : 'openstreetmap';

		if( $google_api_key ){
			$plugin_instance->enqueueGoogleMasApi();
		}
		return $this->attr;
	}

}
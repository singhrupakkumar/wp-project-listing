<?php

// ===============================================
// Citadela Butterbean URL Control functions
// -----------------------------------------------

class CitadelaButterbeanUrl extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_url';
	
	public $citadela_settings = [];    
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		$this->json['url'] = array(
			'label'      => $this->label,
			'value'      => $this->get_value( 'url' ),
			'field_name' => $this->get_field_name( 'url' ),
		);
		$this->json['url_label'] = array(
			'label'      	=> esc_html__('Link label', 'citadela-directory'),
			'description'	=> esc_html__('Text displayed instead of full website url', 'citadela-directory'),
			'value'      	=> $this->get_value( 'url_label' ),
			'field_name' 	=> $this->get_field_name( 'url_label' ),
		);
		$this->json['citadela_settings'] = $this->citadela_settings;

	}

	/*
	*	Adds custom attributes for html.
	*/
	public function get_attr() {
		$this->attr = parent::get_attr();
		$this->attr['class'] .= " {$this->type}-control";
		return $this->attr;
	}

}
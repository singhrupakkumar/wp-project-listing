<?php

// ===============================================
// Citadela Butterbean Textarea Control functions
// -----------------------------------------------

class CitadelaButterbeanTextarea extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_textarea';
	
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		$this->json['value'] = wp_kses_post( $this->get_value() );

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
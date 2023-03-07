<?php

// ===============================================
// Citadela Butterbean Text Control functions
// -----------------------------------------------

class CitadelaButterbeanText extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_text';
	
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		$this->json['value'] = htmlspecialchars_decode( $this->get_value() );

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
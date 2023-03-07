<?php

// ===============================================
// Citadela Butterbean Number Control functions
// -----------------------------------------------

class CitadelaButterbeanNumber extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_number';
	
	public $number_data = [];		    
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		$this->json['unit'] = $this->number_data['unit'];
		$this->json['unit_position'] = $this->number_data['unit-position'];

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
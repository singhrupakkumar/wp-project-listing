<?php

// ===============================================
// Citadela Butterbean Select Control functions
// -----------------------------------------------

class CitadelaButterbeanSelect extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_select';
	
	public $citadela_settings = [];
	
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

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
<?php

// ===============================================
// Citadela Butterbean Featured Category Select Control functions
// -----------------------------------------------

class CitadelaButterbeanFeaturedCategorySelect extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_featured_category_select';
	public $citadela_settings = [];
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();
		$this->json['citadela_settings'] = $this->citadela_settings;
		$this->json['value'] = $this->get_value();

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
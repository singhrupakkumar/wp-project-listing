<?php

// ===============================================
// Citadela Butterbean Multiselect Control functions
// -----------------------------------------------

class CitadelaButterbeanMultiselect extends ButterBean_Control {
	/*
	*	The type of control.
	*/
	public $type = 'citadela_multiselect';
	
	/*
	*	Adds custom data to the json array. Data are passed to the Underscore template.
	*/
	public function to_json() {
		parent::to_json();

		foreach ($this->settings as $key => $meta) {
			
			$this->json['multiselect'][$key] = [
				'name' => $this->get_field_name($key),
				'value' => $this->get_value($key) ? true : false,
			];

		}

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
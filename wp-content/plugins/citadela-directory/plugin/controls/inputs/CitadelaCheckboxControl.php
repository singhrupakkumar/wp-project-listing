<?php

// ===============================================
// Citadela Text Control functions
// -----------------------------------------------

class CitadelaCheckboxControl extends CitadelaControl {

	/*
	*	Render input on Taxonomy Add/Edit page
	*	$inputConfig: 	input configuration
	*	$savedData: 	data from database or default input value if not saved yet
	*	$inputPrefix: 	
	*	$inputConfig: input configuration
	*	$inputConfig: input configuration
	*
	*/

	public static function renderTaxonomyInput( $inputConfig, $savedData, $inputPrefix, $metaId, $screenType ) {
		$inputId = $inputPrefix.'['.$metaId.']';
		$title = $inputConfig['title'];
		$description = $inputConfig['description'];		

		$label = '<label for="'.$inputId.'">'.$title.'</label>';
		$description = isset( $inputConfig['description'] ) ? $inputConfig['description'] : '';

		$option = '<div class="citadela-checkbox-container">';
		$option .= '<input type="checkbox" id="'.$inputId.'" name="'.$inputId.'" '.checked( $savedData, 1, false ).' style="margin-top: 0px;">';
		$option .= '<p class="description">'.$description.'</p>';
		$option .= '</div>';
		

		switch ($screenType) {
			
			case 'add-term':
				$result = '<div class="form-field citadela-control-box taxonomy-control citadela-control-checkbox">';
				$result .= 	$label;
				$result .= 	$option;
				$result .= '</div>';
				break;
			
			case 'edit-term':	
				$result = '<tr class="form-field citadela-control-box taxonomy-control citadela-control-checkbox">';
				$result .= 	'<th scope="row">';
				$result .= 		$label;
				$result .= 	'</th>';
				$result .= 	'<td>';	
				$result .=		$option;
				$result .= 	'</td>';
				$result .= '</tr>';				
			
				break;
			default:
				$result = '';
				break;
		}

		return $result;
	}


	public static function validate( $inputValue ) {
		$r = $inputValue ? 1 : 0;
		return $r;
	}
}

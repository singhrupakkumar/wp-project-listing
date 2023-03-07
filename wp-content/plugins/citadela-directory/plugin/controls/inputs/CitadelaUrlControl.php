<?php

// ===============================================
// Citadela Url Control functions
// -----------------------------------------------

class CitadelaUrlControl extends CitadelaControl {

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
		$option = '<div class="citadela-url-container">';
		$option .= 	'<input type="url" id="'.$inputId.'" name="'.$inputId.'" value="'.$savedData.'">';
		$option .= '</div>';
		switch ($screenType) {
			
			case 'add-term':
				$result = '<div class="form-field citadela-control-box taxonomy-control citadela-control-url">';
				$result .= 	$label;
				$result .= 	$option;
				$result .= '</div>';
				break;
			
			case 'edit-term':	
				$result = '<tr class="form-field citadela-control-box taxonomy-control citadela-control-url">';
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
		return $inputValue;
	}
}

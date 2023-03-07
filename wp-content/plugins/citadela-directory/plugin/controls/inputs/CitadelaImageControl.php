<?php

// ===============================================
// Citadela Image Control functions
// -----------------------------------------------

class CitadelaImageControl extends CitadelaControl {

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
		$option =  '<div class="citadela-image-select-container">';
		$option .= 	'<input type="text" id="'.$inputId.'" name="'.$inputId.'" value="'.$savedData.'" size="25" style="width:70%;">';
		$option .= 	'<input type="button" class="citadela-select-image-button button button-secondary" style="width:25%;" value="'.esc_html__('Select Image', CitadelaDirectory::getInstance()->pluginCodeName).'" id="'.$inputId.'-media-button">';
		$option .= '</div>';

		switch ($screenType) {
			
			case 'add-term':
				$result = '<div class="form-field citadela-control-box taxonomy-control citadela-control-image">';
				$result .= 	$label;
				$result .= 	$option;
				$result .= '</div>';
				break;
			
			case 'edit-term':	
				$result = '<tr class="form-field citadela-control-box taxonomy-control citadela-control-image">';
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
		$r = sanitize_text_field($inputValue);
		return $r;
	}
}

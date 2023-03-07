<?php

// ===============================================
// Citadela Colorpicker Control functions
// -----------------------------------------------

class CitadelaColorpickerControl extends CitadelaControl {

	/*
	*	Render input on Taxonomy Add/Edit page
	*
	*/
	public static function renderTaxonomyInput( $inputConfig, $savedData, $inputPrefix, $metaId, $screenType ) {

		$inputId = $inputPrefix.'['.$metaId.']';
		$title = $inputConfig['title'];
		$description = $inputConfig['description'];		
		$format = ( isset($inputConfig['opacity']) && $inputConfig['opacity'] ) ? 'rgba' : 'hex';
		$hex = CitadelaDirectory::rgba2hex($savedData);

		$opacityClass = ($format == "hex") ? '' : 'has-opacity';

		$label = '<label for="'.$inputId.'">'.$title.'</label>';
		$option = '<div class="citadela-colorpicker-container '.$opacityClass.'">';
		$option .= '<span class="citadela-colorpicker-preview"><i style="background-color: '.$savedData.'"></i></span>';
		$option .= '<input type="text" class="citadela-colorpicker-color" data-color-format="'.$format.'" id="'.$inputId.'" value="'.$hex->hex.'">';
		$option .= '<input type="hidden" class="citadela-colorpicker-storage" name="'.$inputId.'" value="'.$savedData.'">';

		if($format != "hex"){
			$option .= '<input type="number" step="1" min="0" max="100" class="citadela-colorpicker-opacity" value="'.$hex->opacity.'"><span class="citadela-unit"> %</span>';
		}
		
		$option .= '</div>';
		switch ($screenType) {
			
			case 'add-term':
				$result = '<div class="form-field citadela-control-box taxonomy-control citadela-control-colorpicker">';
				$result .= 	$label;
				$result .= 	$option;
				$result .= '</div>';
				break;
			
			case 'edit-term':	
				$result = '<tr class="form-field citadela-control-box taxonomy-control citadela-control-colorpicker">';
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

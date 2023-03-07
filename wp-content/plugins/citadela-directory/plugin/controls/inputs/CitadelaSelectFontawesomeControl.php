<?php

// ===============================================
// Citadela Fontawesome icon selection Control functions
// -----------------------------------------------


class CitadelaSelectFontawesomeControl extends CitadelaControl {
	
	/*
	*	Render input on Taxonomy Add/Edit page
	*/

	public static function renderTaxonomyInput( $inputConfig, $savedData, $inputPrefix, $metaId, $screenType ) {
		$inputId = $inputPrefix.'['.$metaId.']';
		$title = $inputConfig['title'];
		$description = $inputConfig['description'];

		$label = '<label for="'.$inputId.'">'.$title.'</label>';
		$option = 	'<div class="citadela-fontawesome-select-container">';
		$option .= 		'<div class="selected-icon"><i class="'.$savedData.'"></i></div>';
		$option .= 		'<div class="iconpicker-holder" style="display:none;">';
		$option .= 			'<input type="hidden" class="iconpicker" name="'.$inputId.'" id="'.$inputId.'" value="'.$savedData.'" data-search-text="'.esc_html__('Type for search...', 'citadela-directory').'" data-noresults-text="'.esc_html__('No results found.', 'citadela-directory').'" />';
		$option .= 		'</div>';
		$option .= 	'</div>';

		switch ($screenType) {
			
			case 'add-term':
				$result = '<div class="form-field citadela-control-box taxonomy-control citadela-control-fontawesome-select">';
				$result .= 	$label;
				$result .= 	$option;
				$result .= '</div>';
				break;
			
			case 'edit-term':	
				$result = '<tr class="form-field citadela-control-box taxonomy-control citadela-control-fontawesome-select">';
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

	private static function getFaIcons() {
		$path = CitadelaDirectory::getInstance()->paths->dir->assets . "/fontawesome/json/fontawesome.json";
		$icons = json_decode(file_get_contents($path));
		return $icons;
	}

	public static function validate( $inputValue ) {
		$r = sanitize_text_field($inputValue);
		return $r;
	}
}

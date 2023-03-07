<?php

// ===============================================
// Citadela Controls functions
// -----------------------------------------------

class CitadelaControl {

	/*
	*	Render input structure used on Taxonomy Add/Edit pages
	*/
	public static function citadelaRenderTaxonomyInput( $inputId, $inputConfig, $termMeta, $taxCodeName, $screenType, $singleMeta = false ){
		
		$inputType = $inputConfig['type'];
		
		if( $singleMeta ){
			//get direct single meta value
			$savedData = $termMeta;
		}else{
			//get value from saved array
			$savedData = isset($termMeta[$inputId]) ? $termMeta[$inputId] : $inputConfig['default'] ;
		}
		
		switch ($inputType) {
			case 'text':
				echo CitadelaTextControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $inputId, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'colorpicker':
				echo CitadelaColorpickerControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $inputId, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			
			case 'image':
				echo CitadelaImageControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $inputId, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			
			case 'fontawesome-select':
				echo CitadelaSelectFontawesomeControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $inputId, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'url':
				echo CitadelaUrlControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $inputId, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'checkbox':
				echo CitadelaCheckboxControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $inputId, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'map':
				$savedData = isset($termMeta[$inputId]) ? $termMeta[$inputId] : array() ;
				echo CitadelaMapControl::renderTaxonomyInput( $inputConfig, $savedData, $taxCodeName, $screenType ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			
			default:
				echo ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
		}
	}

}

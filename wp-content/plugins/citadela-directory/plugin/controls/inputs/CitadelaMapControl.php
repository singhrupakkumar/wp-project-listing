<?php

// ===============================================
// Citadela Map Control functions
// -----------------------------------------------

class CitadelaMapControl extends CitadelaControl {

	public static function defaultMapInputsConfig() {
		return array(
				'address' => array(
					'type'		=> 'text',
					'title'		=> esc_html__('Address', 'citadela-directory'),
					'default'	=> '',
				),
				'latitude' => array(
					'type'		=> 'text',
					'title'		=> esc_html__('Latitude', 'citadela-directory'),
					'default'	=> '0',
				),
				'longitude' => array(
					'type'		=> 'text',
					'title'		=> esc_html__('Longitude', 'citadela-directory'),
					'default'	=> '0',
				),
				'streetview' => array(
					'type'		=> 'checkbox',
					'title' 	=> esc_html__('Streetview', 'citadela-directory'),
					'default'	=> '',
				),
				'swheading' => array(
					'type'		=> 'text',
					'default'	=> '0',
				),
				'swpitch' => array(
					'type'		=> 'text',
					'default'	=> '0',
				),
				'swzoom' => array(
					'type'		=> 'text',
					'default'	=> '0',
				),
			);
	}

	public static function renderMetaboxInput( $inputConfig, $savedData, $inputPrefix ) {

		$title = $inputConfig['title'];
		$description = $inputConfig['description'];

		$mapInputs = self::defaultMapInputsConfig();

		$result = '';

		$result .= '<div class="citadela-opt-label">';
		$result .= 		'<label>'.$title.'</label>';
		$result .= 		($description !== '') ? '<div class="citadela-opt-description">'.$description.'</div>' : '';
		$result .= '</div>';

		$result .= '<div class="citadela-opt-data">';
		$result .= 	'<div class="citadela-opt-row citadela-opt-map-tools citadela-map-container">';

		$inputValue = isset($savedData['address']) ? $savedData['address'] : $mapInputs['address']['default'];
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-address">';
		$result .= 			'<label for="'.$inputPrefix.'-address">'.$mapInputs['address']['title'].'</label>';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="text" id="'.$inputPrefix.'-address" name="'.$inputPrefix.'-address" value="'.$inputValue.'">';
								//BUTTON FIND ADDRESS
		$result .= 				'<input type="button" class="button button-primary button-large" value="'.esc_html__('Find address', 'citadela-directory').'">';
		$result .=	 		'</div>';
		$result .= 		'</div>';

		$inputValue = isset($savedData['latitude']) ? $savedData['latitude'] : $mapInputs['latitude']['default'];
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-latitude">';
		$result .= 			'<label for="'.$inputPrefix.'-latitude">'.$mapInputs['latitude']['title'].'</label>';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="text" id="'.$inputPrefix.'-latitude" name="'.$inputPrefix.'-latitude" value="'.$inputValue.'">';
		$result .= 			'</div>';
		$result .= 		'</div>';

		$inputValue = isset($savedData['longitude']) ? $savedData['longitude'] : $mapInputs['longitude']['default'];
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-longitude">';
		$result .= 			'<label for="'.$inputPrefix.'-longitude">'.$mapInputs['longitude']['title'].'</label>';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="text" id="'.$inputPrefix.'-longitude" name="'.$inputPrefix.'-longitude" value="'.$inputValue.'">';
		$result .= 			'</div>';
		$result .= 		'</div>';

		$inputValue = isset($savedData['streetview']) ? $savedData['streetview'] : $mapInputs['streetview']['default'];
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-streetview">';
		$result .= 			'<label for="'.$inputPrefix.'-streetview">'.$mapInputs['streetview']['title'].'</label>';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="checkbox" id="'.$inputPrefix.'-streetview" name="'.$inputPrefix.'-streetview" '.checked( $inputValue, 1, false ).'>';
		$result .= 			'</div>';
		$result .= 		'</div>';

		//streetview controls

		$inputValue = isset($savedData['swheading']) ? $savedData['swheading'] : 0;
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-swheading">';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="hidden" id="'.$inputPrefix.'-swheading" name="'.$inputPrefix.'-swheading" value="'.$inputValue.'">';
		$result .= 			'</div>';
		$result .= 		'</div>';

		$inputValue = isset($savedData['swpitch']) ? $savedData['swpitch'] : 0;
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-swpitch">';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="hidden" id="'.$inputPrefix.'-swpitch" name="'.$inputPrefix.'-swpitch" value="'.$inputValue.'">';
		$result .= 			'</div>';
		$result .= 		'</div>';

		$inputValue = isset($savedData['swzoom']) ? $savedData['swzoom'] : 0;
		$result .= 		'<div class="citadela-opt-cell citadela-opt-map-swzoom">';
		$result .=	 		'<div class="citadela-opt-control-wrapper">';
		$result .= 				'<input type="hidden" id="'.$inputPrefix.'-swzoom" name="'.$inputPrefix.'-swzoom" value="'.$inputValue.'">';
		$result .= 			'</div>';
		$result .= 		'</div>';


		$result .=		'<div class="citadela-opt-map-message" style="display:none;">';
		$result .=		esc_html__("Couldn't find location, try different address.", "citadela-directory");
		$result .=		'</div>';

		$result .= 	'</div>';

		$result .= 	'<div class="citadela-opt-row citadela-opt-map-holder">';
		$result .= 		'<div class="citadela-google-map google-map-container"></div>';
		$result .= 	'</div>';
		$result .= '</div>';
		return $result;
	}

	public static function validate( $inputValue, $inputType ) {
		if( $inputType == 'text' ){
			$r = sanitize_text_field($inputValue);
		}elseif ( $inputType == 'checkbox' ) {
			$r = $inputValue ? 1 : 0;
		}
		return $r;
	}
}

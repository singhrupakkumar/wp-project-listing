<?php

namespace Citadela\Pro\Layouts\Upload;

class Feature {
	function settings() {
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}
}

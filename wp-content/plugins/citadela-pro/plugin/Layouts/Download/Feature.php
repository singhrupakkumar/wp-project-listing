<?php

namespace Citadela\Pro\Layouts\Download;

class Feature {
	function settings() {
		static $settings = null;
		if (!$settings) {
			$settings = new Settings;
		}
		return $settings;
	}
}

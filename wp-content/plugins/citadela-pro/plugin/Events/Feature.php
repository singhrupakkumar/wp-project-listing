<?php

namespace Citadela\Pro\Events;

use Citadela\Pro\Asset;
use Citadela\Pro\Template;

class Feature {
	function settings() {
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}
}

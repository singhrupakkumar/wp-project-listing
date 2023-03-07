<?php

namespace Citadela\Pro\Layout_Exporter;

class Feature
{
	function __construct()
	{
		Plugin::run();
	}
	function settings()
	{
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}
}

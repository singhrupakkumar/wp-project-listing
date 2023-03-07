<?php

namespace Citadela\Pro\Layout_Exporter;

use Citadela\Pro\Template;

class Settings
{
	function slug()
	{
		return 'layout-exporter';
	}
	function tab()
	{
		return [
			'label' => __('Layout Exporter', 'citadela-pro')
		];
	}
	function display()
	{
		Template::load('/layout-exporter/exporter');
	}
}

<?php

namespace Citadela\Pro\Layout_Exporter;

class Plugin 
{
    static function run()
    {
        add_action('admin_action_citadela-pro-download-export', [__CLASS__, 'download_export']);
	}
    static function exporter()
    {
		static $_exporter;
		if (!$_exporter) $_exporter = new Exporter;
		return $_exporter;
	}
    static function download_export()
    {
		self::exporter()->export()->zip()->download();
	}
    static function download_url()
    {
        return admin_url('admin.php?action=citadela-pro-download-export');
    }
}
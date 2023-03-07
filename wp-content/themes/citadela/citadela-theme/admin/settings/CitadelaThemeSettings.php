<?php
/**
 * Citadela Theme Settings screen
 *
 */
class Citadela_Theme_Settings {

	public static function run() {
		add_action( 'admin_menu', [ __CLASS__, 'create_menu' ], 10 );
	}



	public static function create_menu() {
		add_menu_page(
			esc_html__('Citadela Theme', 'citadela'),
			esc_html__('Citadela Theme', 'citadela'),
			'edit_dashboard',
			'citadela-settings',
			[ __CLASS__, 'render_settings_page' ],
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjAvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMtU1ZHLTIwMDEwOTA0L0RURC9zdmcxMC5kdGQiPg0KPCEtLSBDcmVhdG9yOiBDb3JlbERSQVcgLS0+DQo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6b2RtPSJodHRwOi8vcHJvZHVjdC5jb3JlbC5jb20vQ0dTLzExL2NkZG5zLyIgeG1sbnM6Y29yZWwtY2hhcnNldD0iaHR0cDovL3Byb2R1Y3QuY29yZWwuY29tL0NHUy8xMS9jZGRucy8iIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIyMDBweCIgaGVpZ2h0PSIyMDBweCIgc3R5bGU9ImZpbGw6I2E3YWFhZCINCiAgICAgdmlld0JveD0iMCAwIDIwMCAyMDAiPg0KIDxkZWZzPg0KICA8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KICAgPCFbQ0RBVEFbDQogICAgLmZpbDAge2ZpbGw6I0ZGRkZGRn0NCiAgICAuZmlsMSB7ZmlsbDojMDBBQ0UxfQ0KICAgXV0+DQogIDwvc3R5bGU+DQogPC9kZWZzPg0KIDxnIGlkPSJMYXllcl94MDAyMF8xIj4NCiAgPHBhdGggY2xhc3M9ImZpbDAiIGQ9Ik02OSA2M2MxNCwtMTIgMzEsLTE4IDQ5LC0xOCA0MiwwIDc2LDM0IDc2LDc2IDAsMzcgLTI3LDY4IC02Myw3NWwwIDBjLTExLDIgLTIyLC02IC0yNCwtMTcgLTIsLTEyIDYsLTIyIDE3LC0yNCAwLDAgMCwwIDAsMCAxNiwtMyAyOSwtMTcgMjksLTM0IDAsLTE5IC0xNiwtMzUgLTM1LC0zNSAtOCwwIC0xNiw0IC0yMiw5bDAgMGMtOSw4IC0yMiw3IC0zMCwtMiAtOCwtOCAtNywtMjIgMiwtMjkgMCwtMSAxLC0xIDEsLTF6Ii8+DQogIDxwYXRoIGNsYXNzPSJmaWwxIiBkPSJNMTMxIDEzN2MtMTQsMTIgLTMxLDE4IC00OSwxOCAtNDIsMCAtNzYsLTM0IC03NiwtNzYgMCwtMzcgMjcsLTY4IDYzLC03NWwwIDBjMTEsLTIgMjIsNiAyNCwxNyAyLDEyIC02LDIyIC0xNywyNCAwLDAgMCwwIDAsMCAtMTYsMyAtMjksMTcgLTI5LDM0IDAsMTkgMTYsMzUgMzUsMzUgOCwwIDE2LC00IDIyLC05bDAgMGM5LC04IDIyLC03IDMwLDIgOCw4IDcsMjIgLTIsMjkgMCwxIC0xLDEgLTEsMXoiLz4NCiA8L2c+DQo8L3N2Zz4NCg==',
			25
		);
	}



    public static function render_settings_page()
	{
		if ((class_exists('Citadela') && in_array(Citadela::$package, ['themeforest', 'mojo', 'themely'])) || defined('CITADELA_BLOCKS_PLUGIN') || defined('CITADELA_DIRECTORY_PLUGIN') || defined('CITADELA_PRO_PLUGIN')) {
			get_template_part('citadela-theme/admin/settings/templates/citadela-pro-screen');
		} else {
			get_template_part('citadela-theme/admin/settings/templates/citadela-free-screen');
		}
	}



	public static function get_instance()
	{
		if(!self::$instance){
			self::$instance = new self;
		}

		return self::$instance;
	}

}

<?php

/*
 Plugin Name: Citadela Pro
 Plugin URI: https://www.ait-themes.club/citadela-plugins/citadela-pro/
 Description: Ready to use layouts and blocks for Citadela with many customization options
 Version: 5.8.0
 Author: AitThemes
 Author URI: https://www.ait-themes.club/
 Text Domain: citadela-pro
 Domain Path: /languages
 License: GPLv2 or later
 */

define( 'CITADELA_PRO_PLUGIN', true );
define( 'CITADELA_PRO_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/plugin/Citadela.php';

\ctdl\pro\register_autoload( 'Citadela\Pro', __DIR__ . '/plugin' );

Citadela\Pro\Plugin::run();

<?php

/*
Plugin Name: Citadela Listing
Plugin URI:  https://www.ait-themes.club/citadela-plugins/citadela-listing/
Description: Universal listing features for any website
Version: 5.15.0
Author: AitThemes
Author URI: https://www.ait-themes.club/
Text Domain: citadela-directory
Domain Path: /languages
License: GPLv2 or later
*/

/* $WCREV$ */

define('CITADELA_DIRECTORY_PLUGIN', true);

require_once __DIR__ . '/plugin/Citadela.php';

require_once dirname(__FILE__) . '/plugin/compatibility.php';
ctdl_directory_check_lite_version();

require_once dirname(__FILE__) . '/plugin/CitadelaDirectory.php';
CitadelaDirectory::getInstance()->run(__FILE__);

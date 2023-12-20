<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jakebown.com
 * @since             1.0.0
 * @package           WP Import CSV
 *
 * @wordpress-plugin
 * Plugin Name:       WP Import CSV
 * Plugin URI:        https://jakebown.com
 * Description:       Quickly import CSV files as WP content.
 * Version:           1.0.2
 * Author:            Jake Bown
 * Author URI:        https://jakebown.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bc_import_csv
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if( ! defined( 'BC_IMPORT_CSV_PLUGIN_DIR' ) ) {
	define('BC_IMPORT_CSV_PLUGIN_DIR', __FILE__);
}


// Get the libs.

if(!class_exists('RationalOptionPages')) {
	require __DIR__ . '/libs/RationalOptionPages.php';
}

// Add the core files.
require __DIR__ . '/src/options/options.php';
require __DIR__ . '/src/options/functions.php';
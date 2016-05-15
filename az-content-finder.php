<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://anton.zaroutski.com
 * @since             0.1
 * @package           Az_Content_Finder
 *
 * @wordpress-plugin
 * Plugin Name:       AZ Content Finder
 * Plugin URI:        http://zaroutski.com
 * Description:       AZ Content Finder is a simple plugin that allows the administrators to search for specific text content in all the posts including all the meta data.
 * Version:           0.1
 * Author:            Anton Zaroutski
 * Author URI:        http://anton.zaroutski.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       az-content-finder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-az-content-finder-activator.php
 */
function activate_az_content_finder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-az-content-finder-activator.php';
	Az_Content_Finder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-az-content-finder-deactivator.php
 */
function deactivate_az_content_finder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-az-content-finder-deactivator.php';
	Az_Content_Finder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_az_content_finder' );
register_deactivation_hook( __FILE__, 'deactivate_az_content_finder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-az-content-finder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_az_content_finder() {

	$plugin = new Az_Content_Finder();
	$plugin->run();

}
run_az_content_finder();

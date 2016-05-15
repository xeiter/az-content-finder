<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://anton.zaroutski.com
 * @since      1.0.0
 *
 * @package    Az_Content_Finder
 * @subpackage Az_Content_Finder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Az_Content_Finder
 * @subpackage Az_Content_Finder/includes
 * @author     Anton Zaroutski <anton@zaroutski.com>
 */
class Az_Content_Finder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'az-content-finder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

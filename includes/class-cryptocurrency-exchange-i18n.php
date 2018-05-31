<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpressian.com
 * @since      1.0.0
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/includes
 * @author     WPressian <info@wpressian.com>
 */
class Cryptocurrency_Exchange_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cryptocurrency-exchange',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wpressian.com
 * @since      1.0.0
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/includes
 * @author     WPressian <info@wpressian.com>
 */
class Cryptocurrency_Exchange_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( __FILE__ ) . 'class-cryptocurrency-exchange.php';
		$cryptocurrency_exchange = new Cryptocurrency_Exchange();
		$cryptocurrency_exchange->update_coin_list();
	}
}

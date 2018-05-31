<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpressian.com
 * @since      1.0.0
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/public
 * @author     WPressian <info@wpressian.com>
 */
class Cryptocurrency_Exchange_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cryptocurrency_Exchange_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cryptocurrency_Exchange_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cryptocurrency-exchange-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cryptocurrency_Exchange_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cryptocurrency_Exchange_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name . '-socket-io', 'https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js');
		wp_enqueue_script( $this->plugin_name . '-ccc-utilites', plugin_dir_url( __FILE__ ) . 'js/ccc-streamer-utilities.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-ticker', plugin_dir_url( __FILE__ ) . '/js/ticker.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '/js/cryptocurrency-exchange-public.js', array( 'jquery' ), $this->version, false );

	}

	public static function cryptocurenccy_exchange_shortcode($atts = [], $content = null, $tag = '') {
		$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
		$subscription = [];
		$subscription_keys = '';
		$showcase = null;

		if( isset( $atts['code'] ) && ( $atts['code'] ) ) {
			$showcase = null;

			if ( $posts = get_posts( array( 
				'post_name' => $atts['code'], 
				'post_type' => 'wprs_cce_shortcode',
				'post_status' => 'publish',
				'posts_per_page' => 1
			) ) ) {
				$showcase = $posts[0];
			} 
		} else if( isset( $atts['id'] ) && ( $atts['id'] ) ) {
			$showcase = get_post( $atts['id'] );
		}

		$coin_list_selected_array = [];

		if( $showcase ) {
			$custom = get_post_custom($showcase->ID);
			$coin_list_selected = $custom["wprs_cce_coin_list"][0];
			if($coin_list_selected) {
				$coin_list_selected_array = explode(',', $coin_list_selected);
			}
		}

		if(count( $coin_list_selected_array ) ) {
			foreach( $coin_list_selected_array as $coin ) {
				$array_coin = explode(':', $coin);
				if( count( $array_coin) > 1 ) {
					$subscription[ $array_coin[0] ] = $array_coin[1];
					$subscription_keys .= ( $subscription_keys ? ',' : '' ) . $array_coin[0];
				}
			}
		}

		if( count( $subscription ) ) {
			$subscription = array_reverse( $subscription );
		}

		ob_start();

		include('partials/cryptocurrency-exchange-public-shortcode.php');

		$output = ob_get_clean();
		
		return $output;
	}
}

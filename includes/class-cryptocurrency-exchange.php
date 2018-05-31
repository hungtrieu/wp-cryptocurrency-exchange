<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpressian.com
 * @since      1.0.0
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/includes
 * @author     WPressian <info@wpressian.com>
 */

class Cryptocurrency_Exchange {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cryptocurrency_Exchange_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WPRSCCE_PLUGIN_NAME_VERSION' ) ) {
			$this->version = WPRSCCE_PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cryptocurrency-exchange';

		$this->load_dependencies();
		$this->register_post_types();
		// $this->loader->add_action( 'upgrader_process_complete', $this, 'update_plugin_options',10, 2);
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cryptocurrency_Exchange_Loader. Orchestrates the hooks of the plugin.
	 * - Cryptocurrency_Exchange_i18n. Defines internationalization functionality.
	 * - Cryptocurrency_Exchange_Admin. Defines all hooks for the admin area.
	 * - Cryptocurrency_Exchange_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cryptocurrency-exchange-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cryptocurrency-exchange-i18n.php';

		/**
		 * The class responsible for defining all widgets
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cryptocurrency-exchange-widget.php';

		/**
		 * The class responsible for defining all WPBakery Addons
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cryptocurrency-exchange-wpb-addons.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cryptocurrency-exchange-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cryptocurrency-exchange-public.php';

		$this->loader = new Cryptocurrency_Exchange_Loader();

	}

	private function register_post_types() {
		$plugin_admin = new Cryptocurrency_Exchange_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('init', $plugin_admin, 'register_custom_post_types' );
		$this->loader->add_action('widgets_init', $plugin_admin, 'wprs_cce_load_widget' );
		$this->loader->add_action('admin_init', $plugin_admin, 'register_meta_boxes');
		$this->loader->add_action('save_post_wprs_cce_shortcode', $plugin_admin, 'save_wprs_shortcode_metabox_details', 10, 2);
		$this->loader->add_action("manage_posts_custom_column", $plugin_admin, "wprs_cce_shortcode_custom_columns");
		
		$this->loader->add_filter("manage_edit-wprs_cce_shortcode_columns", $plugin_admin, "wprs_cce_shortcode_edit_columns");
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cryptocurrency_Exchange_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cryptocurrency_Exchange_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cryptocurrency_Exchange_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cryptocurrency_Exchange_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		add_shortcode('wprs_cce', array('Cryptocurrency_Exchange_Public', 'cryptocurenccy_exchange_shortcode'));
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cryptocurrency_Exchange_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public function update_plugin_options( $upgrader_object, $options ) {
		$current_plugin_path_name = plugin_basename( __FILE__ );

		if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
			foreach($options['plugins'] as $each_plugin){
				if ($each_plugin==$current_plugin_path_name){
					$this->update_coin_list();
				}
			}
		}
	}

	public function update_coin_list() {
		$option_name = 'wprs_cce_coin_data' ;
		$new_value = $this->get_all_coins() ;

		if ( get_option( $option_name ) !== false ) {

			// The option already exists, so we just update it.
			update_option( $option_name, $new_value );

		} else {

			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated = null;
			$autoload = 'no';
			add_option( $option_name, $new_value, $deprecated, $autoload );
		}

		wp_cache_delete ( 'alloptions', 'options' );
	}

	public function get_all_coins() {
		$all_coins = [];

		// get from api

		// json encode

		// return value
	}
}

<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpressian.com
 * @since      1.0.0
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/admin
 * @author     WPressian <info@wpressian.com>
 */
class Cryptocurrency_Exchange_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name . '-chosen', plugin_dir_url( __FILE__ ) . 'css/chosen.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cryptocurrency-exchange-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		
		wp_enqueue_script( $this->plugin_name . '-chosen', plugin_dir_url( __FILE__ ) . 'js/chosen.jquery.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cryptocurrency-exchange-admin.js', array( 'jquery' ), $this->version, false );
		wp_add_inline_script( $this->plugin_name, 'var plugin_dir = "' . plugin_dir_url( dirname( __FILE__ ) ) . '";', 'before' );

	}

	public function register_custom_post_types() {
		$posttype_args = [
			'labels' => [
				'name' => esc_html__( 'Cryptocurrencies', 'cryptocurrency-exchange' ),
				'singular_name' => esc_html__( 'Cryptocurrency', 'cryptocurrency-exchange' ),
				'all_items' => esc_html__( 'All shortcodes' )
			],
			'public' => true,
			'show_in_menu' => true,
			'has_archive' => false,
			'menu_position' => 25,
			'menu_icon' => 'dashicons-chart-line',
			'supports' => ['title'],
			'rewrite' => ['slug' => 'wprs_cce_shortcode'],
		];

		register_post_type( 'wprs_cce_shortcode', $posttype_args);
	}

	public function register_meta_boxes() {
		add_meta_box("wprs_cce_shortcode_format", 
			"Shortcode Format", [
				'Cryptocurrency_Exchange_Admin',
				'gen_shortcode_format_metabox',
			], "wprs_cce_shortcode", "normal", "low");

		add_meta_box("wprs_cce_shortcode_options", 
			"Shortcode Options", [
				'Cryptocurrency_Exchange_Admin',
				'gen_shortcode_options_metabox',
			], "wprs_cce_shortcode", "normal", "low");
	}

	public static function gen_shortcode_format_metabox() {
		global $post;
		$shortcode_format = '';
		$custom = get_post_custom($post->ID);
		if( count( $custom ) && isset( $custom["wprs_cce_shortcode_format"] ) ) {
			$shortcode_format = $custom["wprs_cce_shortcode_format"][0];
		}
		?>
		<p>
			<label for="wprs_cce_shortcode_format">Copy this shortcode and paste it into your post, page:</label> <br />
			<input type="text" id="wprs_cce_shortcode_format" name="wprs_cce_shortcode_format" onfocus="this.select();" readonly="readonly" class="regular-text code" value="<?=$shortcode_format; ?>">
		</p>
		<?php
	}

	public static function gen_shortcode_options_metabox() {
		global $post;
		$custom = get_post_custom($post->ID);
		$coin_list_selected = '';

		if( count( $custom ) && isset( $custom["wprs_cce_coin_list"] ) ) {
			$coin_list_selected = $custom["wprs_cce_coin_list"][0];
		}
		?>
		<p>
			<label for="wprs_cce_coin_list">Display Cryptocurrecies:</label> <br />
			<select id="wprs_cce_coin_list" name="wprs_cce_coin_list[]" data-placeholder="Choose coin(s)..." multiple  class="chosen-select">
			</select>
			<input type="hidden" id="wprs_cce_coin_selected" value="<?=$coin_list_selected;?>" />
		</p>
		<?php
	}

	public function save_wprs_shortcode_metabox_details( $post_id, $post) {
		// return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ){
			return $post_id;
		}

		$meta_key = 'wprs_cce_shortcode_format';

		$meta_value = get_post_meta( $post_id, $meta_key, true );

		if ( !$meta_value ) {
			$code = str_replace('.', '', microtime(true) );
			$meta_value = esc_html('[wprs_cce code="' . $code . '"]');
			update_post_meta($post_id, $meta_key, $meta_value);
		}

		$meta_key = 'wprs_cce_coin_list';

		if( isset( $_POST[$meta_key] ) ){
			// save data
			$meta_value = implode(',', $_POST[$meta_key] );
			update_post_meta( $post_id, $meta_key, sanitize_text_field( $meta_value ) );
		}else{
			// delete data
			delete_post_meta( $post_id, $meta_key );
		}
	}

	public function wprs_cce_shortcode_edit_columns( $columns ) {
		$columns = [
			"cb" => '<input type="checkbox" />',
			"title" => "Title",
			"wprs_cce_shortcode_format" => "Shortcode",
			// "wprs_cce_coin_list" => "Coin List",
		];
		 
		  return $columns;
	}

	public function wprs_cce_shortcode_custom_columns( $column ) {
		global $post;
		$custom = get_post_custom();

		switch ($column) {
			case "wprs_cce_shortcode_format":
				echo $custom["wprs_cce_shortcode_format"][0];
				break;
			case "wprs_cce_coin_list":
				// echo $custom["wprs_cce_coin_list"][0];
				break;
		}
	}

	public function wprs_cce_load_widget() {
		register_widget( 'wprs_cce_widget' );
	}
}

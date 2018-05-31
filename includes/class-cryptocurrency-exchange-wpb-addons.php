<?php
/*
Plugin Name: Cryptocurrency Exchange Addons
Description: Cryptocurrency Exchange Addons for WPBakery Page Builder.
Version: 0.0.1
Author: Wpressian
Author URI: http://wpressian.com
License: GPLv2 or later
*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class WPRS_CCE_VCExtendAddonClass {
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );

        // Register CSS and JS
        add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
    }
 
    public function integrateWithVC() {
        // Check if WPBakery Page Builder is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Extend WPBakery Page Builder is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }
 
        /*
        Add your WPBakery Page Builder logic here.
        Lets call vc_map function to "register" our custom shortcode within WPBakery Page Builder interface.

        More info: http://kb.wpbakery.com/index.php?title=Vc_map
        */

        // Use this when creating a shortcode addon
        add_shortcode( 'wprs_vc_cryptocurrency_exchange', array( $this, 'renderCryptocurrencyExchange' ) );

        $subscription = [0 => esc_html__('Select a shortcode', 'cryptocurrency-exchange')];

        $args = array(
            'post_type'=> 'wprs_cce_shortcode',
            'posts_per_page' => 50,
            'order_by'    => 'post-title',
            'order'    => 'ASC'
            ); 
        
        $the_posts = get_posts( $args );

        if( count( $the_posts ) ) : 
            foreach( $the_posts as $shortcode ) {
                $subscription[$shortcode->ID] = $shortcode->post_title;
            }
        endif;

        vc_map( array(
            "name" => esc_html__("Cryptocurrency Exchange", 'cryptocurrency-exchange'),
            "description" => esc_html__("Display cryptocurrency realtime prices", 'cryptocurrency-exchange'),
            "base" => "wprs_vc_cryptocurrency_exchange",
            "class" => "",
            "controls" => "full",
            "icon" => plugins_url('../images/cryptocoin.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
            "category" => esc_html__('Content', 'js_composer'),
            //'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
            "params" => array(
                array(
                  "type" => "dropdown",
                  "holder" => "div",
                  "class" => "",
                  "heading" => esc_html__("Cryptocurrency Exchange Shortcode", 'cryptocurrency-exchange'),
                  "param_name" => "wprs_cce_shortcode_alias",
                  "value" => $subscription,
                  'std' => '0',
              ),
            )
        ) );
    }
    
    /*
    Shortcode logic how it should be rendered
    */
    public function renderCryptocurrencyExchange( $atts, $content = null ) {
      extract( shortcode_atts( array(
        'wprs_cce_shortcode_alias' => 0,
      ), $atts ) );
      $content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content
     
      $output = Cryptocurrency_Exchange_Public::cryptocurenccy_exchange_shortcode(['code' => $atts['wprs_cce_shortcode_alias'] ]);
      return $output;
    }

    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function loadCssAndJs() {
    //   wp_register_style( 'vc_extend_style', plugins_url('assets/vc_extend.css', __FILE__) );
    //   wp_enqueue_style( 'vc_extend_style' );

      // If you need any javascript files on front end, here is how you can load them.
      //wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>'.sprintf(esc_html__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
        </div>';
    }
}
// Finally initialize code
new WPRS_CCE_VCExtendAddonClass();
<?php
/**
* Plugin Name: CloudEngage
* Plugin URI: #
* Version: 1.0.0
* Author: CloudEngage
* Author URI: https://cloudengage.com/
* Description: Official CloudEngage plugin
* License: GPL2
*/

/*  Copyright 2017 CloudEngage

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Cloudengage {

	public function __construct() {
		// Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'cloudengage'; // Plugin Folder
        $this->plugin->displayName  = 'CloudEngage'; // Plugin Name
        $this->plugin->version      = '0.1.0';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );

        // Check if the global wpb_feed_append variable exists. If not, set it.
        if ( ! array_key_exists( 'wpb_feed_append', $GLOBALS ) ) {
              $GLOBALS['wpb_feed_append'] = false;
        }

		add_action( 'admin_enqueue_scripts', array( &$this, 'insert_scripts_styles_admin'));
		add_action( 'admin_init', array( &$this, 'registerSettings' ) );
        add_action( 'admin_menu', array( &$this, 'adminPanelsAndMetaBoxes' ) );

		// Frontend Hooks
        add_action( 'wp_head', array( &$this, 'frontendHeader' ) );
		add_action( 'wp_footer', array( &$this, 'frontendFooter' ) );

        // Add filters
        add_filter( 'plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );

	}

    // insert scripts to admin menu
	function insert_scripts_styles_admin() {
		wp_enqueue_style( 'cloudengage-css', plugins_url('/css/cloudengage.css', __FILE__));
	}

	// Register Settings
	function registerSettings() {
		register_setting( $this->plugin->name, 'ce_insert_header', 'trim' );
		register_setting( $this->plugin->name, 'ce_insert_footer', 'trim' );
	}

	// Register the plugin settings panel
	function adminPanelsAndMetaBoxes() {
		add_submenu_page( 'options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'adminPanel' ) );
	}

	// Output the Administration Panel
	function adminPanel() {
		// only admin user can access this page
		if ( !current_user_can( 'administrator' ) ) {
			echo '<p>' . __( 'This page requires administrator access', $this->plugin->name ) . '</p>';
			return;
		}

		//when script is inject, run this code
		if ( isset($_POST['headScript']) && isset($_POST['bodyScript']) && isset($_POST['_wpnonce'])) {
			if (wp_verify_nonce( $_POST['_wpnonce'], 'check_nonce')) {
				update_option( 'ce_insert_header', $_POST['headScript'] );
				update_option( 'ce_insert_footer', $_POST['bodyScript'] );
			};
		}

		// get latest settings
		$this->settings = array(
			'ce_insert_header' => esc_html( wp_unslash( get_option( 'ce_insert_header' ))),
			'ce_insert_footer' => esc_html( wp_unslash( get_option( 'ce_insert_footer' ))),
		);

		// load Settings Form
		include_once( WP_PLUGIN_DIR . '/' . $this->plugin->name . '/views/settings.php' );
	}

	// Outputs script / CSS to the frontend header
	function frontendHeader() {
		$this->output( 'ce_insert_header' );
	}

	// Outputs script / CSS to the frontend footer
	function frontendFooter() {
		$this->output( 'ce_insert_footer' );
	}

	/**
	* Outputs the given setting, if conditions are met
	* @param string $setting Setting Name
	* @return output
	*/
	function output( $setting ) {
		// ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		// get meta
		$meta = get_option( $setting );

		if ( empty( $meta ) ) {
			return;
		}
		if ( trim( $meta ) == '' ) {
			return;
		}

		// output
		echo wp_unslash( $meta );
	}

	// add setting menu on plugins page
	function add_settings_link($links, $file) {
		static $this_plugin;

		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin){
			$settings_link = '<a href="options-general.php?page=cloudengage">'.__("Settings", "cloudengage").'</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}

}

$ce = new Cloudengage();

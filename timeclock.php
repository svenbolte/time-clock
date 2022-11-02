<?php
/*
Plugin Name: Time Clock
Description: a time clock and seat reservation (desksharing) plugin for WordPress
Author: Scott Paterson and PBMod
Author URI: https://github.com/svenbolte/
Plugin URI: https://github.com/svenbolte/time-clock/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: etimeclockwp
Domain Path: /languages/
Requires at least: 5.0
Tested up to: 6.1
Requires PHP: 5.7
Stable tag: 9.1.2.1.63
Version: 9.1.2.1.63
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// define common variables

if (!defined('ETIMECLOCKWP_PLUGIN_PATH')) {
	define('ETIMECLOCKWP_PLUGIN_PATH',			plugin_dir_path(__FILE__));
}
if (!defined('ETIMECLOCKWP_PLUGIN_BASENAME')) {
	define('ETIMECLOCKWP_PLUGIN_BASENAME',		plugin_basename(__FILE__));
}
if (!defined('ETIMECLOCKWP_SITE_URL')) {
	define('ETIMECLOCKWP_SITE_URL',				get_site_url());
}
if (!defined('ETIMECLOCKWP_NAME')) {
	define('ETIMECLOCKWP_NAME', 				'Time Clock');
}
if (!defined('ETIMECLOCKWP_VERSION')) {
	define('ETIMECLOCKWP_VERSION', 				'9.1.2.1.62');
}
if (!defined('ETIMECLOCKWP_SETTINGS_PAGE')) {
	define('ETIMECLOCKWP_SETTINGS_PAGE', 		'etimeclockwp_settings_page');
}

// Load plugin textdomain.
function timeclock_load_textdomain() {
  load_plugin_textdomain( 'etimeclockwp', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'timeclock_load_textdomain' );

// activate hook
function etimeclockwp_activation() {
	global $wp_rewrite,$wpdb;
	
	// activate includes
	include_once ('includes/admin/post_types.php');
	// register post types and taxonomies
	etimeclockwp_register_post_type();
	// save time that plugin was installed
	add_option( 'etimeclockwp_install_date', date('Y-m-d G:i:s'), '', 'yes');
	
	// creates raeume table in database if not exists
	$table = $wpdb->prefix . "rooms";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS " . $table . " (
		id int(11) not null auto_increment,
		datum TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		raumname varchar(50) not null,
		sitze int(4) not null,
		PRIMARY KEY (`id`) ) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	// creates teilnehmer table in database if not exists
	$table = $wpdb->prefix . "roombookings";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS " . $table . " (
		id int(11) not null auto_increment,
		verandatum TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		raum int(4) not null,
		sitz int(4) not null,
		belegung varchar(30) not null,
		PRIMARY KEY (`id`) ) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	// for debug truncate table
	//$sql="TRUNCATE TABLE ".$table;
	//$query = $wpdb->query($sql);
}

// deactivate hook
function etimeclockwp_deactivation() {
	delete_option("etimeclockwp_firstrun");
}

// uninstall hook
function etimeclockwp_uninstall() {
	
	// remove all plugin data if option is enabled
	if (etimeclockwp_get_option('uninstall') == "1") {
		etimeclockwp_uninstaller();
	}
	
}		

// register hooks
register_activation_hook(__FILE__,'etimeclockwp_activation');
register_deactivation_hook(__FILE__, 'etimeclockwp_deactivation');
register_uninstall_hook(__FILE__,'etimeclockwp_uninstall');


// public includes
include_once ('includes/admin/post_types.php');
include_once ('includes/settings/settings_api.php');
include_once ('includes/enqueue.php');
include_once ('includes/functions.php');
include_once ('includes/actions.php');
include_once ('includes/shortcodes.php');

// get settings
$etimeclockwp_options = etimeclockwp_get_options();

// admin includes
if (is_admin()) {
	include_once ('includes/admin/menu.php');
	include_once ('includes/admin/activity.php');
	include_once ('includes/admin/users.php');
	include_once ('includes/admin/menu.php');
	include_once ('includes/admin/settings/settings_page.php');
	include_once ('includes/admin/settings/settings_dashboard_items.php');
	include_once ('includes/admin/ajax_functions_admin.php');
	include_once ('includes/admin/uninstall.php');
}

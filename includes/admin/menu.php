<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// make admin menu
function etimeclockwp_plugin_menu() {
	
	// pages
	add_menu_page("Time Clock", "Time Clock", "manage_options", "etimeclockwp_menu", "etimeclockwp_dashboard",'dashicons-clock','28.5');
	add_submenu_page("etimeclockwp_menu", __( 'Settings','etimeclockwp'), __('Settings','etimeclockwp'), "manage_options", "etimeclockwp_settings_page", "etimeclockwp_settings_page");

}
add_action("admin_menu", "etimeclockwp_plugin_menu");


// fix highlighting for dashboard submenu items
function etimeclockwp_select_highlight($file) {
	global $plugin_page,$submenu_file;

	$screen = get_current_screen();
	
	if ($screen->post_type == 'etimeclockwp_clock') {
		$plugin_page = 'edit.php?post_type=etimeclockwp_clock';
		$submenu_file = 'edit.php?post_type=etimeclockwp_clock';
	}
	
	if ($screen->post_type == 'etimeclockwp_users') {
		$plugin_page = 'edit.php?post_type=etimeclockwp_users';
		$submenu_file = 'edit.php?post_type=etimeclockwp_users';
	}
	
	if ($screen->post_type == 'etimeclockwp_reports') {
		$plugin_page = 'edit.php?post_type=etimeclockwp_users';
		$submenu_file = 'time-clock_page_etimeclockwp_reports';
	}
	
}
add_filter('parent_file', 'etimeclockwp_select_highlight');



function my_special_nav_class( $classes, $item ) {
	
	echo "Error";
	
    if ( is_single() && $item->title == 'Blog' ) {
        $classes[] = 'special-class';
    }

    return $classes;

}

add_filter( 'nav_menu_css_class', 'my_special_nav_class', 10, 2 );


// plugin page links
function etimeclockwp_plugin_settings_link($links,$file) {
	
	if ($file == 'time-clock/timeclock.php') {
		
		$settings_link = 	'<a href="admin.php?page=etimeclockwp_settings_page">' . __('Settings', 'PTP_LOC') . '</a>';
		$premium_link = 	'';
		
		array_unshift($links, $settings_link);
		array_push($links, $premium_link);
	}
	
	return $links; 
}
add_filter('plugin_action_links', 'etimeclockwp_plugin_settings_link', 10, 2 );
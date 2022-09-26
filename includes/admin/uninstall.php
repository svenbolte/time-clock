<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// uninstall function - called from main php file, only called if option to remove data is selected in the settings page
// 1. deletes posts
// 2. deletes options
function etimeclockwp_uninstaller() {

	// delete posts
	$post_types = array(
		'etimeclockwp_clock',
		'etimeclockwp_users',
	);
	
	foreach ($post_types as $post_type) {
		
		$posts = get_posts(
			array(
				'post_type' 	=> $post_type,
				'post_status' 	=> 'any',
				'numberposts' 	=> -1, // return all
				'fields' 		=> 'ids' // only return id field
			)
		);
		
		if ($posts) {
			foreach ($posts as $post) {
				wp_delete_post($post, true); // this will remove posts and postmeta
			}
		}
	}
	
	// delete all options
	delete_option("etimeclockwp_firstrun");
	delete_option("etimeclockwp_install_date");
	delete_option("etimeclockwp_settings");
	
}
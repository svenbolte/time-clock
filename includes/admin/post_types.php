<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly


// register post types
function etimeclockwp_register_post_type() {

	if (current_user_can('update_plugins')) {
		
		// activity
		$activity_labels = array(
			'name' 				=> __( 'Activity', 'etimeclockwp' ),
			'singular_name' 	=> __( 'Activity', 'etimeclockwp' ),
			'add_new_item' 		=> __( 'Add New Activity', 'etimeclockwp' ),
			'search_items' 		=> __( 'Search Activity', 'etimeclockwp' ),
			'edit_item' 		=> __( 'Activity', 'etimeclockwp' ),
			'new_item' 			=> __( 'New Activity', 'etimeclockwp' ),
			'not_found' 		=> __( 'No Activity found', 'etimeclockwp' ),
			'all_items' 		=> __( 'Activity', 'etimeclockwp' )
		);
		
		$activity_args = array(
			'labels' 				=> $activity_labels,
			'public' 				=> false,
			'show_ui' 				=> true,
			'exclude_from_search' 	=> true,
			'show_in_menu' 			=> 'etimeclockwp_menu',
			'has_archive' 			=> true,
			'map_meta_cap' 			=> true,
			'capabilities' 			=> array('create_posts' => false ),
		);
		register_post_type('etimeclockwp_clock', $activity_args);
		
		
		// users
		$users_labels = array(
			'name'				=> __( 'Users', 'etimeclockwp' ),
			'singular_name' 	=> __( 'User', 'etimeclockwp' ),
			'add_new_item' 		=> __( 'Add New User', 'etimeclockwp' ),
			'search_items' 		=> __( 'Search Users', 'etimeclockwp' ),
			'edit_item' 		=> __( 'Edit User', 'etimeclockwp' ),
			'new_item' 			=> __( 'New User', 'etimeclockwp' ),
			'not_found' 		=> __( 'No Users Found', 'etimeclockwp' ),
			'all_items' 		=> __( 'Users', 'etimeclockwp' )
		);
		
		$users_args = array(
			'labels' 				=> $users_labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true,
			'show_in_menu' 			=> 'etimeclockwp_menu',
			'query_var'          	=> true,
			'map_meta_cap' 			=> true,
			'has_archive' 			=> false,
			'hierarchical'       	=> false,
			'rewrite' 				=> array('slug' => 'users','with_front' => false ),
		);
		
		register_post_type('etimeclockwp_users', $users_args);
		
	}
	
}
add_action('init','etimeclockwp_register_post_type', 1 );


// hide post metaboxes in custom post types
function etimeclockwp_hide_post_type_boxes() {
	
	// activity
	remove_post_type_support( 'etimeclockwp_users', 'title' );
	remove_post_type_support( 'etimeclockwp_users', 'editor' );
	remove_post_type_support( 'etimeclockwp_clock', 'title' );
	remove_post_type_support( 'etimeclockwp_clock', 'editor' );
	
}
add_action('init','etimeclockwp_hide_post_type_boxes');


// remove metaboxs in custom post types
function etimeclockwp_remove_metaboxs() {
	remove_meta_box('submitdiv','etimeclockwp_users',	'side');
	remove_meta_box('slugdiv','etimeclockwp_users',		'normal');
	remove_meta_box('submitdiv','etimeclockwp_clock',	'side');
	remove_meta_box('slugdiv','etimeclockwp_clock',		'normal');
}
add_action('admin_menu','etimeclockwp_remove_metaboxs');


// turn off autosave for post types
function my_admin_enqueue_scripts() {
    if ('etimeclockwp_clock' == get_post_type()) {
        wp_dequeue_script( 'autosave' );
	}
}
add_action( 'admin_enqueue_scripts', 'my_admin_enqueue_scripts' );
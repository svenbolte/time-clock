<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// load function
function etimeclockwp_load_function_callback() {
	
	$function = sanitize_text_field($_POST['function']);
	
	// only run function is it exists
	if (function_exists($function)) {
		echo call_user_func($function);
	}
	
	wp_die();
}
add_action( 'wp_ajax_etimeclockwp_load_function', 'etimeclockwp_load_function_callback' );
add_action( 'wp_ajax_nopriv_etimeclockwp_load_function', 'etimeclockwp_load_function_callback' );






















// activty page - process delete
function etimeclockwp_date_delete_callback() {

	$postid =		sanitize_text_field($_POST['postid']);
	$datepure =		sanitize_text_field($_POST['datepure']);
	$nonce =		sanitize_text_field($_POST['nonce']);
	
	
	if (!wp_verify_nonce($nonce, 'etimeclockwp_delete')) {
		wp_die('Security check fail');
	}
	
	delete_post_meta($postid, $datepure);
	
	// update total time caculation
	etimeclockwp_caculate_total_time($postid);
	
	// delete in or out	
	$key = explode('_', $datepure);
	$key = $key[0];

	if ($key == 'etimeclockwp-in') {
		delete_post_meta($postid, 'in');
	}

	if ($key == 'etimeclockwp-out') {
		delete_post_meta($postid, 'out');
	}

}
add_action( 'wp_ajax_etimeclockwp_date_delete', 'etimeclockwp_date_delete_callback' );



// activty page - process edit
function etimeclockwp_date_edit_callback() {

	global $wpdb;
	
	$postid =		sanitize_text_field($_POST['postid']);
	$puredate =		sanitize_text_field($_POST['puredate']);
	$timestamp =	sanitize_text_field($_POST['timestamp']);
	$order =		sanitize_text_field($_POST['order']);
	$nonce =		sanitize_text_field($_POST['nonce']);
	$type =		sanitize_text_field($_POST['type']);
	
	if (!wp_verify_nonce($nonce, 'etimeclockwp_edit')) {
		wp_die('Security check fail');
	}
	
	$timestamp = 	str_replace("+", "", $timestamp);
	
	// get mid
	$mid = $wpdb->get_var( $wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $postid, $puredate) );
	
	// redo puredate depending on type value - type value is the type of event
	$key = explode('_', $puredate);
	$puredate = 'etimeclockwp-'.$type.'_'.$key[1];
	
	// convert timestamp to unix
	$unix = strtotime($timestamp);
	
	// update post
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_key = %s, meta_value = %s WHERE meta_id = %d",$puredate,$unix.'|'.$order,$mid));
	
	// update total time caculation
	etimeclockwp_caculate_total_time($postid);

}
add_action( 'wp_ajax_etimeclockwp_date_edit', 'etimeclockwp_date_edit_callback' );




// activty page - process new
function etimeclockwp_date_new_callback() {
	
	$postid =		sanitize_text_field($_POST['postid']);
	$type =			sanitize_text_field($_POST['type']);
	$timestamp =	sanitize_text_field($_POST['timestamp']);
	$timestamp = 	str_replace("+", "", $timestamp);
	$nonce =        sanitize_text_field($_POST['nonce']);
    
    if (!wp_verify_nonce($nonce, 'etimeclockwp_add')) {
        wp_die('Security check fail');
    }
	
	$type_raw = $type;
	
	// convert timestamp to unix
	$unix = strtotime($timestamp);
	
	// make random
	$rand = mt_rand(); // default: 0, default: mt_getrandmax()
	
	// make type
	$type = 'etimeclockwp-'.$type.'_'.$rand;
	
	// get last order count
	$order = get_post_meta($postid,'count',true);
	
	update_post_meta($postid, $type, $unix.'|'.$order);
	
	// update total time caculation
	etimeclockwp_caculate_total_time($postid);
	
	// update count - used to keep track of the order of clock in / out, etc. events
	$order++;
	update_post_meta($postid,'count',$order);
	
	// record if the user has clocked in or out today, this is necessary as a wp_query search cannot perform a like search on a key name, which is required for allow users to work past midnight
	if ($type_raw == 'in' || $type_raw == 'out') {
		update_post_meta($postid,$type_raw,true);
	}

}
add_action( 'wp_ajax_etimeclockwp_date_new', 'etimeclockwp_date_new_callback' );
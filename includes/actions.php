<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// this page is for actions

// run action from url
function etimeclockwp_process_actions() {
	if (isset($_REQUEST['etimeclockwp_action'])) {
		do_action('etimeclockwp_' . sanitize_text_field($_REQUEST['etimeclockwp_action']),$_REQUEST);
	}
}
add_action('admin_init','etimeclockwp_process_actions');


// redirect to settings page on install
function etimeclockwp_firstrun() {
	if (!get_option('etimeclockwp_firstrun')) {
		update_option("etimeclockwp_firstrun", "true");
		exit(wp_redirect(admin_url( 'admin.php?page=etimeclockwp_settings_page')));
	}
}
add_action('admin_init', 'etimeclockwp_firstrun');


// employee timeclock action
function etimeclockwp_timeclock_action_callback() {

	$nonce =	sanitize_text_field($_POST['nonce']);
	$data =		sanitize_text_field($_POST['data']);
	$eid =		sanitize_text_field($_POST['eid']);
	$epw =		sanitize_text_field($_POST['epw']);
	$mandate =		sanitize_text_field($_POST['mandate']);
	$mantime =		sanitize_text_field($_POST['mantime']);
	
	// verify nonce
	if (!wp_verify_nonce($nonce,'etimeclock_nonce')) { die( __('Error - Nonce validation failed.','etimeclockwp')); }
	
   // validate data parameter - only allow specific values to prevent XSS
	$allowed_actions = array('in', 'out', 'breakon', 'breakoff');
	if (!in_array($data, $allowed_actions, true)) {
		die( __('Error - Invalid action.','time-clock'));
	}
	
	// check to see if login data is valid
	$args = array(
		'post_type'					=> 'etimeclockwp_users',
		'post_status'				=> 'publish',
		'update_post_term_cache'	=> false, // don't retrieve post terms
		'meta_query'			=> array(
		'relation'			=>'and',
			array(
				'key'		=> 'etimeclockwp_id',
				'value'		=> $eid,
				'compare'	=> '=',
			),
			array(
				'key'		=> 'etimeclockwp_pwd',
				'value'		=> $epw,
				'compare'	=> '=',
			)
		)
	);
	
	$posts_array = new WP_Query($args);
	
	foreach ($posts_array->posts as $post) {
		$user_id = $post->ID;
	}	
	
	
	// success - user id and password are correct
	if (!empty($user_id)) {
		
		$wp_date_format = current_time(etimeclockwp_get_option('date-format'));
		$wp_time_format = current_time(etimeclockwp_get_option('time-format'));
		$now 		= strtotime(current_time('mysql'));
		$now_part 	= current_time('Y-m-d');
		$postdate = current_time('Y-m-d H:i:s');

		// oder Datum und Zeit vorgeben manuell
		if (!empty($mandate) && !empty($mantime)) {
			$wp_date_format = date(etimeclockwp_get_option('date-format'),strtotime($mandate));
			$wp_time_format = date(etimeclockwp_get_option('time-format'),strtotime($mantime));
			$now 		= strtotime($mandate.' '.$mantime);
			$now_part 	= date('Y-m-d',$now);
			$postdate = $mandate.' '.$mantime;
		}
		$rand 		= mt_rand(); // default: 0, default: mt_getrandmax() - random numbers are needed because meta key names must be unique
		
		// set defaults
		$flag = '0';
		$clock_in = '0';
		
		// clock in event - record event for today - a post for today might already exists if the user is working a double shift, so check to see if a post exists or not before making a new one
		$args_f = array(
			'post_type'						=> 'etimeclockwp_clock',
			'update_post_term_cache'		=> false, // don't retrieve post terms
			'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
			'date_query'					=> array(
					'year'	=> date('Y',$now),
					'month'	=> date('m',$now),
					'day'	=> date('d',$now),
			),
			'meta_query'					=> array(
				'relation'					=> 'and',
				array(
					'key'           => 'uid',
					'value'         => $user_id,
					'compare'       => '=',
				),
			),
		);
		$posts_array_f = new WP_Query($args_f);
		
		foreach ($posts_array_f->posts as $post) {
			$post_exists = $post->ID;
		}
	
		if ($data == 'in') {
			$success_msg 	= __('Clock In','etimeclockwp');
			$working_status = '1';
		}
		
		if ($data == 'breakon') {
			$success_msg 	= __('Break On','etimeclockwp');
			$working_status = '0';
		}
		
		if ($data == 'breakoff') {
			$success_msg 	= __('Break Off','etimeclockwp');
			$working_status = '1';
		}
		
		if ($data == 'out') {
			$success_msg 	= __('Clock Out','etimeclockwp');
			$working_status = '0';
		}

		// date is not in db, so insert - record event for the date
		
		if (empty($post_exists)) {
			
			$new_post_id = wp_insert_post(
				array(
					'post_title'     => $user_id,
					'post_content'   => '',
					'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
					'post_author'    => 1,
					'post_date' => $postdate,
					'post_type'      => 'etimeclockwp_clock',
				)
			);
			
			// today is now in db, so add post meta
			update_post_meta($new_post_id,'etimeclockwp-'.$data.'_'.$rand,$now.'|0');
			
			// update working status
			update_post_meta($new_post_id,'status_'.$now_part,$working_status); // working status needs to have now_part due to reports
			
			// insert user id into post meta
			update_post_meta($new_post_id,'uid',$user_id);
			
			// total time
			etimeclockwp_caculate_total_time($new_post_id);
			
		} else {
			
			// date is already in db - record event for today / yesterday
			
			// get count
			$count	 			= get_post_meta($post_exists,'count', true);
			
			// today is already in db, so add post meta
			update_post_meta($post_exists,'etimeclockwp-'.$data.'_'.$rand,$now.'|'.$count);
			
			// update working status
			update_post_meta($post_exists,'status_'.$now_part,$working_status); // working status needs to have now_part due to reports
			
			// total time
			etimeclockwp_caculate_total_time($post_exists);
			
		}
		
		$message = __('Success','etimeclockwp').' - '. $success_msg.' - '.$wp_date_format.' - '.$wp_time_format;
		$color = 'green';
		
	} else {
		
		$message = __('Incorrect ID or Password.','etimeclockwp');
		$color = 'red';
		
	}
	
	// build response array
	$response = array(
		'message' 	=> $message,
		'color' 	=> $color,
	);
	
	
	// response
	echo json_encode($response);
	
	wp_die();

}
add_action( 'wp_ajax_etimeclockwp_timeclock_action', 'etimeclockwp_timeclock_action_callback' );
add_action( 'wp_ajax_nopriv_etimeclockwp_timeclock_action', 'etimeclockwp_timeclock_action_callback' );

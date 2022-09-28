<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function etimeclockwp_button_shortcode($atts) {

	global $current_user,$wp;
	if (isset ($_GET['show']) ) $showmode = sanitize_text_field($_GET['show']); else $showmode = 0;

	// get shortcode attributes
	$atts = shortcode_atts(array(
		'align' 	=> '',
	), $atts);
	
	if ($showmode == 0) {

		// set action url
		$action_url = add_query_arg('etimeclockwp-action','charge',home_url( 'index.php'));
		$result = "";
		$result .= "<div class='etimeclock-main'>";
			$result .= "<div class='etimeclock-body'>";
				
				// time section
				$result .= "<span class='etimeclock-date'></span> &nbsp; ";
				$result .= "<span class='etimeclock-time'></span><br>";
				$result .= '<input id="manualdate" name="manualdate" type="date"> &nbsp; ';
				$result .= '<input id="manualtime" name="manualtime" type="time">';
				$result .= "<br />";

				// status line section
				$result .= '<div id="etimeclock-status">Bereit</div><br />';
							
				// login section
				$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-id').": <br /><input id='etimeclock-eid' class='etimeclock-input' type='text' autocomplete='off' autocomplete='false'></div>";
				$result .= "<br /><br />";
				$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-password').": <br /><input id='etimeclock-epw' class='etimeclock-button etimeclock-input' type='password' autocomplete='off' autocomplete='false'></div>";
				$result .= "<br /><br />";
				$result .= "<input id='etimeclock-hash' type='hidden' value='false'>";
				
				// button section
				$result .= "<div class='etimeclock-in etimeclock-button' style='background-color: ".etimeclockwp_get_option('clock-in-button-color')."'><a class='etimeclock-action' data-id='in' href='#'>".etimeclockwp_get_option('clock-in')."</a></div>";
				$result .= "<div class='etimeclock-out etimeclock-button' style='background-color: ".etimeclockwp_get_option('clock-out-button-color')."'><a class='etimeclock-action' data-id='out' href='#'>".etimeclockwp_get_option('clock-out')."</a></div>";
				$result .= "<br />";
				
				$show_break_options = etimeclockwp_get_option('show_break_options');
				
				if ($show_break_options == '0') {
					$result .= "<div class='etimeclock-break-out etimeclock-button' style='background-color: ".etimeclockwp_get_option('leave-on-break-button-color')."'><a href='#' class='etimeclock-action' data-id='breakon'>".etimeclockwp_get_option('leave-on-break')."</a></div>";
					$result .= "<div class='etimeclock-break-in etimeclock-button' style='background-color: ".etimeclockwp_get_option('return-from-break-button-color')."'><a href='#' class='etimeclock-action' data-id='breakoff'>".etimeclockwp_get_option('return-from-break')."</a></div>";
				}
				
				// Admin show list
				$result .= '<div class="etimeclock-button" style="background-color:#888"><a href="'.home_url($wp->request).'?show=1" class="submit btnbutton">'.__('admin show bookings','etimeclockwp').'</a></div>';
				
				// create nonce
				$ajax_nonce = wp_create_nonce('etimeclock_nonce');
				$result .= "<input type='hidden' id='etimeclock_nonce' value='$ajax_nonce'>";
				
			$result .= "</div>";
		$result .= "</div>";
	} else if ($showmode == 1 && current_user_can('administrator') ) {
		//////   Activity-Anzeige letzte Buchungen - in Arbeit ---
			$activity = get_posts(
				array(
				'posts_per_page'	=> -1,
				'post_type'			=> 'etimeclockwp_clock'
				)
			);

		foreach($activity as $post) {
			$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users','ID' => $post->post_title ) );
			foreach($users as $user) { $usersname = $user->post_title;	}
			$result = '<table>';
			$result .= '<thead><th colspan=4>'.$usersname.' &nbsp; ID ('.$post->post_title.') &nbsp;  activity ('.$post->ID.')</th>';
			$result .= '<th><a href="'.home_url($wp->request).'?show=0" class="submit btnbutton">'.__('time clock','etimeclockwp').'</a></th></thead>';
			$metavalue = get_post_meta($post->ID);
			$wp_date_format = etimeclockwp_get_option('date-format');
			$wp_time_format = etimeclockwp_get_option('time-format');
			$wp_date_format_timestamp = 'Y-m-d';
			$wp_time_format_timestamp = 'H:i:s';
			$timestamp_now = 	date_i18n($wp_date_format_timestamp.' '.$wp_time_format_timestamp);
			$date_now = 		date_i18n($wp_date_format);
			$time_now = 		date_i18n($wp_time_format);
			// url nonnces
			$nonce_edit = 	wp_create_nonce('etimeclockwp_edit');
			$nonce_delete = wp_create_nonce('etimeclockwp_delete');
			$nonce_add = 	wp_create_nonce('etimeclockwp_add');
			$post_excerpt = $post->post_excerpt;
			
			foreach($metavalue as $key => $val) {
				if (substr($key, 0, 5) === "etime") {
					$key_pure = $key;
					$key = explode('_', $key);
					$key = $key[0];
					$key_action = $key;
					$timestamp_array = explode('|', $val[0]);			
					$timestampdb = $timestamp_array[0];
					if ($key == 'etimeclockwp-in') {
						$key = etimeclockwp_get_option('clock-in');
						$keycolor = etimeclockwp_get_option('clock-in-button-color');
						$day = $timestampdb;
					}
					
					if ($key == 'etimeclockwp-out') {
						$key = etimeclockwp_get_option('clock-out');
						$keycolor = etimeclockwp_get_option('clock-out-button-color');
					}
					
					if ($key == 'etimeclockwp-breakon') {
						$key = etimeclockwp_get_option('leave-on-break');
						$keycolor = etimeclockwp_get_option('leave-on-break-button-color');
					}
					
					if ($key == 'etimeclockwp-breakoff') {
						$key = etimeclockwp_get_option('return-from-break');
						$keycolor = etimeclockwp_get_option('return-from-break-button-color');
					}
					
					$datetime = date_i18n($wp_date_format.' '.$wp_time_format,$timestampdb);
					$date = 	date_i18n($wp_date_format,$timestampdb);
					$time = 	date_i18n($wp_time_format,$timestampdb);

					$timestamp = 		date_i18n($wp_date_format_timestamp.' '.$wp_time_format_timestamp,$timestampdb);
					if (!empty($oldtimestampdb)) $difftime = german_time_diff($oldtimestampdb,$timestampdb); else $difftime='';
					
					if (isset($timestamp_array[1])) {
						$order = $timestamp_array[1];
						$result .= "<tr><td class='etimeclockwp_cell_title_width' style='color:white;text-transform:uppercase;background-color: ".$keycolor."'>";
						$result .= $key;
						$result .= "</td><td>".$datetime.'</td><td>'.ago($timestampdb-date('Z')).'</td><td>'.$difftime."</td><td>&nbsp;";
					} else {
						$result .= "<tr><td class='etimeclockwp_cell_title_width'>";
						$result .= $key;
						$result .= ": </td><td>".$datetime."</td></tr>";
					}
					$oldtimestampdb = $timestampdb;
				}
			}
			$result .= "</tr>";
			$result .= '</table>';
		}
	} else { $result='kein Zugriff'; }  //////	Ende Showausgabe
	return $result;
}
add_shortcode('timeclock', 'etimeclockwp_button_shortcode');
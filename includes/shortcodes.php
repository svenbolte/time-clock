<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function etimeclockwp_button_shortcode($atts) {

	global $current_user,$wp;
	if (isset ($_GET['show']) ) $showmode = sanitize_text_field($_GET['show']); else $showmode = 0;

	// get shortcode attributes
	$atts = shortcode_atts(array( 'align' 	=> '',	), $atts);
	
	$result = '';
	if ($showmode == 0) {

		// set action url
		$action_url = add_query_arg('etimeclockwp-action','charge',home_url( 'index.php'));
		$result = "<div class='etimeclock-main'>";
			$result .= "<div class='etimeclock-body'>";
				
				// time section
				$result .= "<span class='etimeclock-date'></span> &nbsp; ";
				$result .= "<span class='etimeclock-time'></span><br>";
				$result .= '<input id="manualdate" name="manualdate" type="date"> &nbsp; ';
				$result .= '<input id="manualtime" name="manualtime" type="time" style="padding:6px">';
				$result .= "<br /><br>";

				// status line section
				$result .= '<div id="etimeclock-status">Bereit</div><br />';
							
				// login section
				$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-id').":<br /><input id='etimeclock-eid' class='etimeclock-button etimeclock-input' style='color:#000' type='text' autocomplete='on' autocomplete='true'></div>";
				$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-password').":<br /><input id='etimeclock-epw' class='etimeclock-button etimeclock-input' style='color:#000' type='password' autocomplete='on' autocomplete='true'></div>";
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
				if ( current_user_can('administrator') ) {
					$result .= '<div class="etimeclock-button" style="background-color:#888"><a href="'.home_url($wp->request).'?show=1" class="submit btnbutton">'.__('admin show bookings','etimeclockwp').'</a></div>';
				}	
				
				// create nonce
				$ajax_nonce = wp_create_nonce('etimeclock_nonce');
				$result .= "<input type='hidden' id='etimeclock_nonce' value='$ajax_nonce'>";
				
			$result .= "</div>";
		$result .= "</div>";
	} else if ($showmode == 1 && current_user_can('administrator') ) {
		//////   Activity-Anzeige letzte Buchungen - in Arbeit ---
			$result .= '<span style="float:right"><a href="'.home_url($wp->request).'?show=0" class="submit btnbutton"><i class="fa fa-clock-o"></i> '.__('time clock','etimeclockwp').'</a> &nbsp; ';
			$result .= '<a href="'.home_url($wp->request).'?show=2" class="submit btnbutton"><i class="fa fa-download"></i> '.__('export','etimeclockwp').'</a></span>';
			$activity = get_posts(
				array(
				'posts_per_page'	=> -1,
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
				'post_type'			=> 'etimeclockwp_clock',
				'orderby'          => 'date',
				'order'            => 'DESC',
				)
			);

		foreach($activity as $post) {
			$oldtimestampdb = '';
			$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', 'post__in' => array($post->post_title) ) );
			foreach($users as $user) { $usersname = $user->post_title; }
			$tagessumme = etimeclockwp_get_time_worked($post,$format = true);
			$result .= '<table>';
			$result .= '<thead><th colspan=3>'.$usersname.' &nbsp; ID ('.$post->post_title.') &nbsp;  activity ('.$post->ID.')</th>';
			$result .= '<th>'.get_the_date('D',$post->ID).' ';
			$result .= get_the_date(etimeclockwp_get_option('date-format'),$post->ID). '</th><th><i class="fa fa-hourglass-3"></i> '. etimeclockwp_get_time_worked($post,$format = true).'</th></thead>';
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
					if (!empty($oldtimestampdb)) {
						$difftime = german_time_diff($oldtimestampdb,$timestampdb);
						$diffsecs = round($timestampdb - $oldtimestampdb);
						$diffhhmm = sprintf('%02d:%02d:%02d', ($diffsecs / 3600),($diffsecs / 60 % 60), $diffsecs % 60);
					} else {
						$difftime='';
						$diffhhmm='';
					}	
					
					if (isset($timestamp_array[1])) {
						$order = $timestamp_array[1];
						$result .= "<tr><td class='etimeclockwp_cell_title_width' style='color:white;text-transform:uppercase;background-color: ".$keycolor."'>";
						$result .= $key;
						$result .= "</td><td>".date_i18n('D',$timestampdb).' '.$datetime.'</td><td>'.ago($timestampdb-date('Z')).'</td><td>'.$diffhhmm."</td><td>".$difftime."</td></tr>";
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
	} else if ($showmode == 2 && current_user_can('administrator') ) {
		// CSV Export ---------------------
		$filename = 'export-timeclock';
		$date = date("Y-m-d H:i:s");
		$output = fopen('php://output', 'w');
		ob_clean();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=\"" . $filename . " " . $date . ".csv\";" );
		header("Content-Transfer-Encoding: binary");	
		fputcsv( $output, array('Username', 'UserID', 'StempeltagID', 'Stempelart', 'StempelUhrzeit', 'Arbeitszeit', 'AZproTagTotal'), ';');
			$result ='';
			$activity = get_posts(
				array(
				'posts_per_page'	=> -1,
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
				'post_type'			=> 'etimeclockwp_clock',
				'orderby'          => 'date',
				'order'            => 'DESC',
				)
			);
		foreach($activity as $post) {
			$oldtimestampdb = '';
			$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', 'post__in' => array($post->post_title) ) );
			foreach($users as $user) { $usersname = $user->post_title;	}
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

				$modified_values = array(
					$usersname,
					$post->post_title,
					'',
					get_the_date(etimeclockwp_get_option('date-format'),$post->ID),
					'',
					'',
					etimeclockwp_get_time_worked($post,$format = true),
				);
			   fputcsv( $output, $modified_values, ';' );

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
					if (!empty($oldtimestampdb)) {
						$difftime = german_time_diff($oldtimestampdb,$timestampdb);
						$diffsecs = round($timestampdb - $oldtimestampdb);
						$diffhhmm = sprintf('%02d:%02d:%02d', ($diffsecs / 3600),($diffsecs / 60 % 60), $diffsecs % 60);
					} else {
						$difftime='';
						$diffhhmm='';
					}	
					
					if (isset($timestamp_array[1])) {
						$order = $timestamp_array[1];
						$modified_values = array(
							$usersname,
							$post->post_title,
							$post->ID,
							$key,
							$datetime,
							$diffhhmm,
							'',
						);
					   fputcsv( $output, $modified_values, ';' );
					} else {
					}
					$oldtimestampdb = $timestampdb;
				}
			}
		}
		exit;

	} else { $result = 'kein Zugriff'; }  //////	Ende Showausgabe
	return $result;
}
add_shortcode('timeclock', 'etimeclockwp_button_shortcode');
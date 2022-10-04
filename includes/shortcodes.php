<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function etimevaliduser() {
	global $datefilter;
	if ($_POST) {
		$eid =		sanitize_text_field($_POST['eid']);
		$epw =		sanitize_text_field($_POST['epw']);
		// Auf User filtern
		if (isset($_POST['month']) && !empty($_POST['month']) ) {
			$datefilt = sanitize_text_field($_POST['month'],true);
			$filtmon = (int) substr($datefilt,5,2);
			$filtyr = (int) substr($datefilt,0,4);
		} else {
			$datefilt='';
			$filtmon = NULL;
			$filtyr = NULL;
		}
		$datefilter = array( array( 'year' => $filtyr, 'month' => $filtmon, ), );
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
	} else {
		if (!current_user_can('administrator')) {
			echo '<div style="width:100%;text-align:center;display:block"><form method="post">';
			echo '<div class="etimeclock-text">'.etimeclockwp_get_option("employee-id").':<br /><input type="text" id="eid" name="eid"></div>';
			echo '<div class="etimeclock-text">'.etimeclockwp_get_option('employee-password').':<br /><input type="password" id="epw" name="epw"></div>';
			echo '<div class="etimeclock-text">'.__('time-filter','etimeclockwp').':<br /><input style="padding:4px 0" type="month" name="month"></div>';
			echo '<input type="submit" value="Anmelden"></form></div>';
		}	
	}
	if (!empty($user_id)) return $user_id; else return '';
}


function etimeclockwp_button_shortcode($atts) {
	global $current_user,$wp,$datefilter;
	if (isset ($_GET['show']) ) $showmode = sanitize_text_field($_GET['show']); else $showmode = 0;
	// get shortcode attributes
	$atts = shortcode_atts(array( 'align' 	=> '',	), $atts);
	$result = '';
	$validuser='admin';

	if ($showmode == 0) {
		// set action url
		$action_url = add_query_arg('etimeclockwp-action','charge',home_url( 'index.php'));
		$result = "<div class='etimeclock-main'>";
		$result .= "<div class='etimeclock-body'>";
		// show and export link
		$result .= '<div class="etimeclock-button" style="background-color:#888;margin-bottom:1em"><a href="'.home_url($wp->request).'?show=1" class="submit btnbutton">'.__('admin show bookings','etimeclockwp').'</a></div>';
		// status line section
		$result .= '<div id="etimeclock-status">Bereit</div>';
		// time section
		$result .= '<span style="font-size:1.7em">KW '.date_i18n('W').' &nbsp; '.date_i18n('D').'</span> &nbsp; ';
		$result .= '<span class="etimeclock-date"></span> &nbsp; ';
		$result .= "<span class='etimeclock-time'></span><br />";
		$result .= '<input id="manualdate" name="manualdate" type="date" value="'.date('Y-m-d').'"> &nbsp; ';
		$result .= '<input id="manualtime" name="manualtime" type="time" style="padding:6px">';
		$result .= "<br /><br>";
		// login section
		$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-id').":<br /><input id='etimeclock-eid' class='etimeclock-button etimeclock-input' style='color:#000' type='text' autocomplete='on' autocomplete='true'></div>";
		$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-password').":<br /><input id='etimeclock-epw' class='etimeclock-button etimeclock-input' style='color:#000' type='password' autocomplete='on' autocomplete='true'></div>";
		$result .= "<br /><br />";
		$result .= "<input id='etimeclock-hash' type='hidden' value='false'>";
		// button section
		$result .= '<div class="buttongrid">';
		$result .= "<div class='etimeclock-in etimeclock-button' style='background-color: ".etimeclockwp_get_option('clock-in-button-color')."'><a class='etimeclock-action' data-id='in' href='#'>".etimeclockwp_get_option('clock-in')."</a></div>";
		$result .= "<div class='etimeclock-out etimeclock-button' style='background-color: ".etimeclockwp_get_option('clock-out-button-color')."'><a class='etimeclock-action' data-id='out' href='#'>".etimeclockwp_get_option('clock-out')."</a></div>";
		$show_break_options = etimeclockwp_get_option('show_break_options');
		if ($show_break_options == '0') {
			$result .= "<div class='etimeclock-break-out etimeclock-button' style='background-color: ".etimeclockwp_get_option('leave-on-break-button-color')."'><a href='#' class='etimeclock-action' data-id='breakon'>".etimeclockwp_get_option('leave-on-break')."</a></div>";
			$result .= "<div class='etimeclock-break-in etimeclock-button' style='background-color: ".etimeclockwp_get_option('return-from-break-button-color')."'><a href='#' class='etimeclock-action' data-id='breakoff'>".etimeclockwp_get_option('return-from-break')."</a></div>";
		}
		// show list and export (only admin can export users and all activitiers, user exports his activities)
		$result .= '</div>';
		// create nonce
		$ajax_nonce = wp_create_nonce('etimeclock_nonce');
		$result .= "<input type='hidden' id='etimeclock_nonce' value='$ajax_nonce'>";
			
		$result .= "</div>";
		$result .= "</div>";
	} else if ($showmode == 1 && ( current_user_can('administrator') || !empty($validuser = etimevaliduser()) ) ) {
		// Activity-Anzeige letzte Buchungen 
		$result .= '<div style="float:right"><i class="fa fa-user"></i> <b>'.strtoupper($validuser).'</b> &nbsp; ';
		$result .= '<span class="btn"><a href="'.home_url($wp->request).'?show=0" class="submit"><i class="fa fa-clock-o"></i> '.__('time clock','etimeclockwp').'</a></span> &nbsp; ';
		$result .= '<span class="btn"><a title="'.__('export','etimeclockwp').' '.__('users','etimeclockwp').'" href="'.home_url($wp->request).'?show=2" class="submit btnbutton"><i class="fa fa-download"></i> '.__('activities','etimeclockwp').'</a></span> &nbsp; ';
		if ( current_user_can('administrator') ) $result .= '<span class="btn"><a title="'.__('export','etimeclockwp').' '.__('users','etimeclockwp').'" href="'.home_url($wp->request).'?show=3" class="submit btnbutton"><i class="fa fa-download"></i> '.__('users','etimeclockwp').'</a></span>';
		$current='';
		if (current_user_can('administrator') ) {
			$result .= ' &nbsp; <form class="noprint" style="display:inline" name="userfilter" method="post" action="'.home_url(add_query_arg(array('show'=>'1'), $wp->request)).'">';
			// filter on user for admins
			$users = get_posts(
				array(
				'posts_per_page'	=> -1,
				'post_type'			=> 'etimeclockwp_users'
				)
			);
			foreach($users as $post) {
				$values[$post->post_title] = $post->ID;
			}
			$result .= '<select name="user"><option value="">'. __('All users', 'etimeclockwp').'</option>';
			if (isset($_POST['user'])) {
				$current = sanitize_text_field($_POST['user'],true);
			} else {
				$current = '';
			}
			foreach ($values as $label => $value) {
				$result .= "<option value='$value'"; if ($current == $value) { $result .= "SELECTED"; } $result .= ">$label</option>";
			}
			$result .= '</select>';
			// Filter on month / year
			if (isset($_POST['month']) && !empty($_POST['month']) ) {
				$datefilt = sanitize_text_field($_POST['month'],true);
				$filtmon = (int) substr($datefilt,5,2);
				$filtyr = (int) substr($datefilt,0,4);
			} else {
				$datefilt='';
				$filtmon = NULL;
				$filtyr = NULL;
			}
			$result .= ' <input style="padding:4px 0;margin-bottom:4px" type="month" name="month" value="'.$datefilt.'"> ';
			$datefilter = array( array( 'year' => $filtyr, 'month' => $filtmon, ), );
			$result .= '<input type="submit" style="font-family: FontAwesome" value="&#xf0b0;"></form>';
		} 		
		$result .= '</div>';
		if ($validuser !=='admin') $userfilter=$validuser; else $userfilter=$current;
		$activity = get_posts(
			array(
			'posts_per_page'	=> -1,
			'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
			'post_type'			=> 'etimeclockwp_clock',
			'title' => $userfilter,
			'date_query' => $datefilter,
			'orderby'          => 'date',
			'order'            => 'DESC',
			)
		);
		$totpau=0;
		$totaz=0;
		$totbrutto=0;
		foreach($activity as $post) {
			$oldtimestampdb = '';
			$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', 'post__in' => array($post->post_title) ) );
			foreach($users as $user) { $usersname = $user->post_title; }
			$tagessumme = etimeclockwp_get_time_worked($post,$format = true);
			$result .= '<table>';
			$result .= '<thead><th colspan=3 style="text-align:left;width:65%">';
			$result .= get_the_date('D',$post->ID).' '.get_the_date(etimeclockwp_get_option('date-format'),$post->ID);
			$result .= ' &nbsp; '.$usersname.' &nbsp; ID ('.$post->post_title.') &nbsp;  activity ('.$post->ID.')</th>';
			$result .= '<th>ArbZeit</th><th>'.__('break','etimeclockwp').'</th><th>'.__('totaltime','etimeclockwp');
			$result .= '</th></thead>';
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
			$pausum=0;
			$azsum =0;
			
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
						$working_status = '1';
					}
					if ($key == 'etimeclockwp-out') {
						$key = etimeclockwp_get_option('clock-out');
						$keycolor = etimeclockwp_get_option('clock-out-button-color');
						$working_status = '0';
					}
					if ($key == 'etimeclockwp-breakon') {
						$key = etimeclockwp_get_option('leave-on-break');
						$keycolor = etimeclockwp_get_option('leave-on-break-button-color');
						$working_status = '0';
					}
					if ($key == 'etimeclockwp-breakoff') {
						$key = etimeclockwp_get_option('return-from-break');
						$keycolor = etimeclockwp_get_option('return-from-break-button-color');
						$working_status = '3';
					}
					$datetime = date_i18n($wp_date_format.' '.$wp_time_format,$timestampdb);
					$date = 	date_i18n($wp_date_format,$timestampdb);
					$time = 	date_i18n($wp_time_format,$timestampdb);
					$timestamp = 		date_i18n($wp_date_format_timestamp.' '.$wp_time_format_timestamp,$timestampdb);
					if (!empty($oldtimestampdb)) {
						$difftime = german_time_diff($oldtimestampdb,$timestampdb);
						$diffsecs = round($timestampdb - $oldtimestampdb);
						if ( $working_status == 3 ) {
							$pausum +=$diffsecs;
							$totpau +=$diffsecs;
							$totbrutto +=$diffsecs;
						} else {	
							$azsum +=$diffsecs;
							$totaz +=$diffsecs;
							$totbrutto +=$diffsecs;
						}	
						$diffhhmm = sprintf('%02d:%02d:%02d', ($diffsecs / 3600),($diffsecs / 60 % 60), $diffsecs % 60);
					} else {
						$difftime='';
						$diffhhmm='';
					}	
					if (isset($timestamp_array[1])) {
						$order = $timestamp_array[1];
						$result .= "<tr><td class='etimeclockwp_cell_title_width' style='color:white;text-transform:uppercase;background-color: ".$keycolor."'>";
						$result .= $working_status.' '.$key;
						$result .= "</td><td>".date_i18n('D',$timestampdb).' '.$datetime.'</td><td>'.ago($timestampdb-date('Z')).'</td><td style="text-align:center">';
						if ( $working_status == 3 ) $result .= '</td><td style="text-align:center">'.$diffhhmm.'</td><td>'; else $result .= $diffhhmm.'</td><td></td><td>';
						$result .= $difftime."</td></tr>";
					} else {
						$result .= "<tr><td class='etimeclockwp_cell_title_width'>";
						$result .= $key;
						$result .= ": </td><td>".$datetime."</td></tr>";
					}
					$oldtimestampdb = $timestampdb;
				}
			}
			$result .= "</tr>";
			if ($azsum > 60*60*10) $tenhourwarn = 'background-color:tomato;color:white'; else $tenhourwarn ='';
			$result .= '<tfoot><tr><td colspan=3 style="text-align:left"><b>Tagessummen</b></td><td style="'.$tenhourwarn.'"><b>'.sprintf('%02d:%02d:%02d', ($azsum / 3600),($azsum / 60 % 60), $azsum % 60).'</b></td><td><b>';
			$result .= sprintf('%02d:%02d:%02d', ($pausum / 3600),($pausum / 60 % 60), $pausum % 60).'</b></td><td><b>';
			$result .= etimeclockwp_get_time_worked($post,$format = true).'</b></td></tr></tfoot>';
			$result .= '</table>';
		}
		$result .= '<table><thead><th colspan=3 style="width:65%"><i class="fa fa-hourglass-3"></i> Gesamtsummen</th>';
		$result .= '<th>'.sprintf('%02d:%02d:%02d', ($totaz / 3600),($totaz / 60 % 60), $totaz % 60).'</th>';
		$result .= '<th>'.sprintf('%02d:%02d:%02d', ($totpau / 3600),($totpau / 60 % 60), $totpau % 60).'</th>';
		$result .= '<th>'.sprintf('%02d:%02d:%02d', ($totbrutto / 3600),($totbrutto / 60 % 60), $totbrutto % 60).'</th>';
		$result .= '</thead></table>';
	} else if ($showmode == 2 && ( current_user_can('administrator') || !empty($validuser = etimevaliduser()) ) ) {
		// CSV Export activities ---------------------
		$filename = 'export-timeclock-activities-'.$validuser;
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
		fputcsv( $output, array('Username', 'UserID', 'StempeltagID', 'Stempelart', 'StempelUhrzeit', 'Arbeitszeit', 'Pausenzeit', 'AZproTagTotal'), ';');
		if ($validuser !=='admin') $userfilter=$validuser; else $userfilter='';
			$result ='';
			$activity = get_posts(
				array(
				'posts_per_page'	=> -1,
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
				'post_type'			=> 'etimeclockwp_clock',
				'title' => $userfilter,
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
			$pausum=0;
			$azsum =0;

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
						$working_status = '1';
					}
					if ($key == 'etimeclockwp-out') {
						$key = etimeclockwp_get_option('clock-out');
						$keycolor = etimeclockwp_get_option('clock-out-button-color');
						$working_status = '0';
					}
					if ($key == 'etimeclockwp-breakon') {
						$key = etimeclockwp_get_option('leave-on-break');
						$keycolor = etimeclockwp_get_option('leave-on-break-button-color');
						$working_status = '0';
					}
					if ($key == 'etimeclockwp-breakoff') {
						$key = etimeclockwp_get_option('return-from-break');
						$keycolor = etimeclockwp_get_option('return-from-break-button-color');
						$working_status = '3';
					}
					$datetime = date_i18n($wp_date_format.' '.$wp_time_format,$timestampdb);
					$date = 	date_i18n($wp_date_format,$timestampdb);
					$time = 	date_i18n($wp_time_format,$timestampdb);
					$timestamp = 		date_i18n($wp_date_format_timestamp.' '.$wp_time_format_timestamp,$timestampdb);
					if (!empty($oldtimestampdb)) {
						$difftime = german_time_diff($oldtimestampdb,$timestampdb);
						$diffsecs = round($timestampdb - $oldtimestampdb);
						if ( $working_status == 3 ) $pausum +=$diffsecs; else $azsum +=$diffsecs;
						$diffhhmm = sprintf('%02d:%02d:%02d', ($diffsecs / 3600),($diffsecs / 60 % 60), $diffsecs % 60);
					} else {
						$difftime='';
						$diffhhmm='';
					}	
					if ( $working_status == 3 ) {$az = '';$pau = $diffhhmm;} else {$az = $diffhhmm;$pau = '';}
					if (isset($timestamp_array[1])) {
						$order = $timestamp_array[1];
						$modified_values = array(
							$usersname,
							$post->post_title,
							$post->ID,
							$key,
							$datetime,
							$az,
							$pau,
							'',
						);
					   fputcsv( $output, $modified_values, ';' );
					} else {
					}
					$oldtimestampdb = $timestampdb;
				}
			}
			// Tages-Footer Zwischensumme Totale Arbeitszeit
			$modified_values = array(
				$usersname,
				$post->post_title,
				'',
				get_the_date(etimeclockwp_get_option('date-format'),$post->ID),
				get_the_date('F Y',$post->ID),
				sprintf('%02d:%02d:%02d', ($azsum / 3600),($azsum / 60 % 60), $azsum % 60),
				sprintf('%02d:%02d:%02d', ($pausum / 3600),($pausum / 60 % 60), $pausum % 60),
				etimeclockwp_get_time_worked($post,$format = true),
			);
		   fputcsv( $output, $modified_values, ';' );
		}
		exit;

	} else if ($showmode == 3 && current_user_can('administrator') ) {
		// Export user table
		$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', ) );
		$filename = 'export-timeclock-users';
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
		fputcsv( $output, array('Username', 'UserID', 'Kennwort', 'RecordNo', 'created'), ';');
		foreach($users as $user) {
			$modified_values = array(
				$user->post_title,
				sanitize_text_field(get_post_meta($user->ID,'etimeclockwp_id', true)),
				sanitize_text_field(get_post_meta($user->ID,'etimeclockwp_pwd', true)),
				$user->ID,
				$user->post_date,
			);
		   fputcsv( $output, $modified_values, ';' );
		}
		exit;

	} else { $result = '<div class="newlabel yellow" style="font-size:1em;width:100%;text-align:center">Kein Zugriff. Falscher Benutzername. Bitte korrekt anmelden</div>'; }  //////	Ende Showausgabe
	return $result;
}
add_shortcode('timeclock', 'etimeclockwp_button_shortcode');
<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// This page is for general functions

// Calendar display month - draws a calendar with the bookings
if( !function_exists('timeclock_event_calendar')) {
	function timeclock_event_calendar($month,$year,$eventarray){
		global $totalsitze;
		setlocale (LC_ALL, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'); 
		$calheader = date('Y-m-d',mktime(2,0,0,$month,1,$year));
		$running_day = date('w',mktime(2,0,0,$month,1,$year));
		if ( $running_day == 0 ) { $running_day = 7; }
		$daytoday = date('d',time());
		$monthtoday = date('m',time());
		$yeartoday = date('Y',time());
		$days_in_month = date('t',mktime(2,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		$calendar = '<style>.weekline{border-bottom:1px dotted #888;font-weight:700;text-align:center}</style>';
		$calendar .= '<table style="border-bottom:1px dotted #888"><thead><th style="text-align:center" colspan=8>' . date_i18n('F Y', mktime(2,0,0,$month,1,$year) ) . '</th></thead>';
		$headings = array('MO','DI','MI','DO','FR','SA','SO','Kw');
		$calendar .= '<tr><td class="weekline">'.implode('</td><td class="weekline">',$headings).'</td></tr>';
		$calendar .= '<tr style="padding:2px">';
		for($x = 1; $x < $running_day; $x++) {
			$calendar.= '<td style="text-align:center;padding:2px"></td>';
			$days_in_this_week++;
		}
		for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
			$calendar.= '<td style="padding:2px;text-align:center;vertical-align:top"">';
			$running_week = date('W',mktime(2,0,0,$month,$list_day,$year));
			$istoday = (int) $daytoday == (int) $list_day && (int) $monthtoday == (int) $month && (int) $yeartoday == (int) $year;
			if ( $istoday ) $todaycolor='#fd08;border:1px dotted black'; else $todaycolor='transparent';
			// QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY
			$dailyevents = '';
			$onlyfirst = 0;
			foreach ($eventarray as $calevent => $fields) {
				if ( substr($fields['verandatum'],0,10) == date('Y-m-d',mktime(0,0,0,$month,$list_day,$year)) ) {
					$todaycolor = '#fd08;font-weight:700';
					$dailyevents .= '<span style="word-break:break-all;font-size:0.8em" title="'.wp_strip_all_tags($fields['veranstaltung']).'">' . $fields['veranstaltung'] . '</span><br>';
					$onlyfirst += 1;
				}
			}
			if ( $onlyfirst > 0 && $totalsitze > 0 ) {
				$totaltagbelegt = totalraumbelegung(date('Y-m-d',mktime(0,0,0,$month,$list_day,$year)));
				$dailyevents .= '<span class="newlabel white">Total | '. $totalsitze.'-'.$totaltagbelegt.' | '.$totalsitze-$totaltagbelegt.'</span>';
			}	
			$calendar.= '<div title="'.ago(mktime(2,0,0,$month,$list_day,$year)).'" style="width:100%;background-color:'.$todaycolor.'">'.$list_day.'<br><div style="font-weight:normal;line-height:1.1em">'.$dailyevents.'</div></div>';
			// Database Query ende
			$calendar.= '</td>';
			if ($running_day == 7) {
				$calendar.= '<td style="max-width:32px;width:30px;text-align:center"><span class="newlabel">'.$running_week.'</span></td></tr>';
				if(($day_counter+1) != $days_in_month) { $calendar.= '<tr>'; }
				$running_day = 0;
				$days_in_this_week = 0;
			}
			$days_in_this_week++; $running_day++; $day_counter++;
		}
		if ($days_in_this_week < 8 && $days_in_this_week > 1) {
			for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
				$calendar.= '<td style="text-align:center;padding:2px"></td>';
			}
			$calendar.= '<td style="max-width:32px;width:30px;text-align:center"><span class="newlabel">'.$running_week.'</span></td></tr>';
		}	
		$calendar.= '</table>';
		return $calendar;
	}
}

//// Abmelden und User Cookie löschen
if (isset($_GET['logout'])) add_action( 'init', 'my_setcookie_kill' );
function my_setcookie_kill() {
	unset($_COOKIE['etime_usercookie']); 
	setcookie('etime_usercookie', '', time()-3600);
	unset($_COOKIE['etime_session']); 
	setcookie('etime_session', '', time()-3600);
	echo '<script>window.location.replace("'.home_url( remove_query_arg( array ('logout','raum') ) ).'");</script>';
}

// User und Session Cookie setzen
if 	(!isset( $_COOKIE['etime_usercookie'] ) && isset($_POST['eid']) ) add_action( 'init', 'my_setcookie_sess' );
function my_setcookie_sess() {
	if (isset($_POST['eid'])) $eid = sanitize_text_field($_POST['eid']); else $eid='';
	$esession = md5( $eid . intval(date('Y-m-d H:i:s')) / 24 * 3600);
	setcookie("etime_usercookie", $eid, time()+24000);
	setcookie("etime_session", $esession, time()+24000);
}

function etimevaliduser() {
	global $eid;
	$logu='';
	$usercookie = isset( $_COOKIE['etime_usercookie'] ) ? $_COOKIE['etime_usercookie'] : '';
	$usersession = isset( $_COOKIE['etime_session'] ) ? $_COOKIE['etime_session'] : '';
	$esession = md5( $usercookie . intval(date('Y-m-d H:i:s')) / 24 * 3600);
	if ($_POST && $usersession !== $esession) {
		if (isset($_POST['eid'])) $eid = sanitize_text_field($_POST['eid']); else $eid='';
		if (isset($_POST['epw'])) $epw = sanitize_text_field($_POST['epw']); else $epw='';
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

		// success - user id and password are correct // Passwort und 1-day-hash in Cookies speichern
		if (!empty($user_id)) {
		}	

	} else {
		if ($usersession !== $esession) {
			$usercookie = isset( $_COOKIE['etime_usercookie'] ) ? $_COOKIE['etime_usercookie'] : '';
			$logu = '<div style="width:100%;text-align:center;display:block"><form method="post">';
			$logu .=  '<div class="etimeclock-text">'.etimeclockwp_get_option("employee-id").':<br /><input type="text" id="eid" name="eid" value="'.$usercookie.'"></div>';
			$logu .= ' <div class="etimeclock-text">'.etimeclockwp_get_option('employee-password').':<br /><input type="password" id="epw" name="epw"></div>';
			$logu .= ' <input type="submit" value="Anmelden"></form></div>';
			echo $logu;
		} else {
			$eid =		sanitize_text_field($usercookie);
			// valid session cookie is present - so get user from eid
			$args = array(
				'post_type'					=> 'etimeclockwp_users',
				'post_status'				=> 'publish',
				'update_post_term_cache'	=> false, // don't retrieve post terms
				'meta_query'			=> array(
					array(
						'key'		=> 'etimeclockwp_id',
						'value'		=> $eid,
						'compare'	=> '=',
					),
				)
			);
			$posts_array = new WP_Query($args);
			foreach ($posts_array->posts as $post) {
				$user_id = $post->ID;
			}	
		}
	}
	if (!empty($user_id)) return $user_id; else return '';
}


function etime_menu($selectedmenu,$validuser) {
	global $wp;
	if ($validuser=='admin')  { $mtext = '<li><a title="admin dashboard" href="'.site_url().'/wp-admin/edit.php?post_type=etimeclockwp_clock"><i class="fa fa-user"></i> '.strtoupper($validuser).'</a></li>';
		} else { $mtext = '<li><i class="fa fa-user"></i> '.strtoupper($validuser).'</li>'; }
	$mtext .= '<li><a href="'.home_url( add_query_arg( array('logout'=>'1') ) ).'" title="'.__('logout','etimeclockwp').'"><i class="fa fa-lg fa-sign-out" style="color:tomato"></i></a></li>';
	$mtext .= '<li><a href="'.home_url($wp->request).'?show=0" class="submit"><i class="fa fa-clock-o"></i> '.__('time clock','etimeclockwp').'</a></li>';
	if ( $selectedmenu !== 1 ) $mtext .= '<li><a title="'.__('admin show bookings','etimeclockwp').'" href="'.home_url($wp->request).'?show=1" class="submit"><i class="fa fa-list"></i></a></li>';
	if ( $selectedmenu !== 4 && current_user_can('administrator') ) $mtext .= '<li><a title="'.__('Panel','etimeclockwp').'" href="'.home_url($wp->request).'?show=4" class="submit"><i class="fa fa-heartbeat"></i></a></li>';
	if ( $selectedmenu !== 5 ) $mtext .= '<li><a title="'.__('view as calendar','etimeclockwp').'" href="'.home_url($wp->request).'?show=5" class="submit btnbutton"><i class="fa fa-calendar-o"></i></a></li>';
	$mtext .= '<li><a title="'.__('export','etimeclockwp').' '.__('activities','etimeclockwp').'" href="'.home_url($wp->request).'?show=2" class="submit btnbutton"><i class="fa fa-download"></i>|<i class="fa fa-list"></i></a></li>';
	if ( current_user_can('administrator') ) $mtext .= '<li><a title="'.__('export','etimeclockwp').' '.__('users','etimeclockwp').'" href="'.home_url($wp->request).'?show=3" class="submit btnbutton"><i class="fa fa-download"></i>|<i class="fa fa-users"></i></a></span>';
	return $mtext;
}

// Zeitdifferenz ermitteln und gestern/vorgestern/morgen schreiben: chartscodes, dedo, foldergallery, timeclock, w4-post-list
if( !function_exists('ago')) {
	function ago($timestamp) {
		if (empty($timestamp)) return;
		$xlang = get_bloginfo("language");
		date_default_timezone_set('Europe/Berlin');
		$now = time();
		if ($timestamp > $now) {
			$prepo = __('in', 'penguin');
			$postpo = '';
		} else {
			if ($xlang == 'de-DE') {
				$prepo = __('vor', 'penguin');
				$postpo = '';
			} else {
				$prepo = '';
				$postpo = __('ago', 'penguin');
			}
		}
		$her = date( 'd.m.Y', intval($timestamp) );
		if ($her == date('d.m.Y',$now - (24 * 3600))) {
			$hdate = __('yesterday', 'penguin');
		} else if ($her == date('d.m.Y',$now - (48 * 3600))) {
			$hdate = __('1 day before yesterday', 'penguin');
		} else if ($her == date('d.m.Y',$now + (24 * 3600))) {
			$hdate = __('tomorrow', 'penguin');
		} else if ($her == date('d.m.Y',$now + (48 * 3600))) {
			$hdate = __('1 day after tomorrow', 'penguin');
		} else {
			$hdate = ' ' . $prepo . ' ' . human_time_diff(intval($timestamp), $now) . ' ' . $postpo;
		}
		return $hdate;
	}
}	


if( !function_exists('tc_german_time_diff')) {
	function tc_german_time_diff( $from, $to ) {
		$days_old = abs(round(( $to - $from ) / 86400 , 0 ));
		if ( $days_old < 30 ) $newclass = 'yellow'; else $newclass = 'white';
		$diff = human_time_diff($from,$to);
		$longreplace = array(   // Grammatik bei Anzeige langer Differenz (Monate statt Monaten)
			'Tagen' => 'Tage',	'Monaten' => 'Monate',	'Jahren' => 'Jahre'
		);
		$replace = array(  // Auf Kurzform umstellen
			'Sekunde'  => 's', 'Sekunden'  => 's',	'Minute'  => 'm', 'Minuten'  => 'm',
			'Stunde'  => 'h', 'Stunden' => 'h',		'Tag'   => 'T', 'Tage'  => 'T',
			'Woche'  => 'W', 'Wochen'  => 'W',		'Monat'  => 'M', 'Monate'  => 'M',
			'Jahr'  => 'J', 'Jahre'  => 'J',		'n' =>''
		);
		$aetitle = __('time since previous post or visit','penguin').'&#10;'.strtr($diff,$longreplace).'&#10;'.$days_old.' Tage';
		return '<abbr title="'.$aetitle.'" class="newlabel '.$newclass.'" style="white-space: nowrap"><i title="'.$aetitle.'" class="fa fa-arrows-v"></i>&nbsp;' . strtr($diff,$replace) . '</abbr>';
	}
}

// get options with defaults - used in settings_api.php to load defaults for settings page
function etimeclockwp_get_option($key) {
	$etimeclockwp_options = get_option('etimeclockwp_settings');
	$result = '';
	
	// check if option has been saved
	if (isset($etimeclockwp_options[$key])) {
		// get option from saved options
		$result = $etimeclockwp_options[$key];
	} else {
		// get option from default in settings array
		$settings = etimeclockwp_settings();
		
		// loop through remaining values to get default
		foreach ($settings as $tabs ) {
			foreach ($tabs as $page) {
				foreach ($page as $option) {
					if ($option['name'] == $key) {
						if (isset($option['default'])) {
							$result = $option['default'];
						}
					}
				}
			}
		}
		
		// save default to we don't need to search again
		$etimeclockwp_options[$key] = $result;
		update_option('etimeclockwp_settings',$etimeclockwp_options);
		
	}
	
	return $result;
}


// load and save all options - the loop should only run on install
function etimeclockwp_get_options() {
	$etimeclockwp_options = get_option('etimeclockwp_settings');
	if (!isset($etimeclockwp_options['tab'])) {
		$settings = etimeclockwp_settings();
		foreach ($settings as $tabs ) {
			foreach ($tabs as $page) {
				foreach ($page as $option) {
						$etimeclockwp_options[$option['name']] = $option['default'];
					if (isset($option['default'])) {
						update_option('etimeclockwp_settings',$etimeclockwp_options);
					}
				}
			}
		}
		
		$etimeclockwp_options['tab'] = 'tab00';;
		update_option('etimeclockwp_settings',$etimeclockwp_options);
		
		$etimeclockwp_options = get_option('etimeclockwp_settings');
		return $etimeclockwp_options;
		
	} else {
		return $etimeclockwp_options;
	}
	exit;
}


// convert seconds time to hours / mins / secs
function etimeclockwp_convert_time($seconds) {
   if($seconds > 0) {
		$t = round($seconds);
		return sprintf('%02d:%02d:%02d', (floor($t)/3600),(floor($t/60)%60), floor($t)%60);
 	}
 	return '00:00:00';
}


// get users last booking
function user_last_booking($userfilter, $csvformat = NULL) {		
	$result='';
	$datefilter='';
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
	$latesttimestampdb = 0;
	foreach($activity as $post) {
		$metavalue = get_post_meta($post->ID);
		// get newest timestamp
		foreach($metavalue as $key => $val) {
			if (substr($key, 0, 5) === "etime") {
				$timestamp_array = explode('|', $val[0]);			
				$timestampdb = $timestamp_array[0];
				if ($timestampdb >= $latesttimestampdb ) $latesttimestampdb = $timestampdb;
			}
		}	
		//display newest timestamp
		foreach($metavalue as $key => $val) {
			if (substr($key, 0, 5) === "etime") {
				$key = explode('_', $key);
				$key = $key[0];
				$timestamp_array = explode('|', $val[0]);			
				$timestampdb = $timestamp_array[0];
				if ($timestampdb == $latesttimestampdb) {
					if ($key == 'etimeclockwp-in') {
						$key = etimeclockwp_get_option('clock-in');
						$keycolor = etimeclockwp_get_option('clock-in-button-color');
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
					if (isset($timestamp_array[1])) {
						if (!isset($csvformat)) $result .= '<span style="color:white;text-transform:uppercase;background-color:'.$keycolor.'">';
						if (empty($userfilter)) {
							$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', 'post__in' => array($post->post_title) ) );
							foreach($users as $user) { $usersname = $user->post_title; }
							$result .= $post->post_title.' | '.$usersname.' | ';
						}	
						$result .= $working_status.' | '.$key;
						$result .= ' | '.date_i18n('D j. F Y H:i:s',$timestampdb).' | '.ago(($timestampdb - date('Z')));
						if (!isset($csvformat)) $result .= '</span><br>';
					} else {
						$result .= '<span>';
						$result .= $key;
						$result .= ' '.$timestampdb.'</span>';
					}
				}
			}
		}
	}
	return $result;
}



// get total time worked
function etimeclockwp_get_time_worked($post,$format = true) {
	$total 		= get_post_meta($post->ID,'total', true);
	
	// this is needed due to removing the date part since version 1.2
	if (empty($total)) {
		$date_part 	= get_the_date('Y-m-d',$post->ID);
		$total 		= get_post_meta($post->ID,'total_'.$date_part, true);
	}
	
	if ($format == true) {
		$total 		= etimeclockwp_convert_time($total);
	}
	return $total;
}

// get notices for work day - used to display if someone forgot to clockout
function etimeclock_get_notices($post_id) {
	$notices 		= get_post_meta($post_id,'notices', true);
	if (!empty($notices)) {
		$notices = __( 'Admin Review','etimeclockwp');
	} else {
		$notices = '-';
	}
	return $notices;
}

// calculate arbeitszeit, pause und total and return in an array
function etimeclockwp_calculate_workpausetotal($post_id) {
	$oldtimestampdb = '';
	$metavalue = get_post_meta($post_id);
	$wp_date_format = etimeclockwp_get_option('date-format');
	$wp_time_format = etimeclockwp_get_option('time-format');
	$wp_date_format_timestamp = 'Y-m-d';
	$wp_time_format_timestamp = 'H:i:s';
	$timestamp_now = 	date_i18n($wp_date_format_timestamp.' '.$wp_time_format_timestamp);
	$date_now = 		date_i18n($wp_date_format);
	$time_now = 		date_i18n($wp_time_format);
	$pausum=0;
	$azsum =0;
	foreach($metavalue as $key => $val) {
		if (substr($key, 0, 5) === "etime") {
			$key = explode('_', $key);
			$key = $key[0];
			$timestamp_array = explode('|', $val[0]);			
			$timestampdb = $timestamp_array[0];
			if ($key == 'etimeclockwp-in') { $working_status = '1';	}
			if ($key == 'etimeclockwp-out') { $working_status = '0'; }
			if ($key == 'etimeclockwp-breakon') { $working_status = '0'; }
			if ($key == 'etimeclockwp-breakoff') { $working_status = '3'; }
			if (!empty($oldtimestampdb)) {
				$diffsecs = round($timestampdb - $oldtimestampdb);
				if ( $working_status == 3 ) $pausum +=$diffsecs; else $azsum +=$diffsecs;
			}	
			$oldtimestampdb = $timestampdb;
		}
	}
	$modified_values = array(
		get_the_date(etimeclockwp_get_option('date-format'),$post_id),
		get_the_date('F Y',$post_id),
		$azsum,
		$pausum,
		($azsum + $pausum),
		sprintf('%02d:%02d:%02d', floor($azsum / 3600),(floor($azsum / 60) % 60), floor($azsum) % 60),
		sprintf('%02d:%02d:%02d', floor($pausum / 3600),(floor($pausum / 60) % 60), floor($pausum) % 60),
		sprintf('%02d:%02d:%02d', floor(($azsum + $pausum) / 3600),(floor(($azsum + $pausum) / 60) % 60), floor($azsum + $pausum) % 60),
	);
	return $modified_values;
}


// calculate total time given post id
function etimeclockwp_caculate_total_time($post_id) {
	// do a full recaculation based on entry order and don't worry about the existing total time value
	$metavalue = get_post_meta($post_id);
	$total_time_array = array();
	$count = '0'; // this is used if the event does not have a working order, this should only happen if the user is between upgrading from version 1.1 to 1.2
	foreach($metavalue as $key => $val) {
		
		if (substr($key, 0, 5) === "etime") {
			
			// get key
			$key = explode('_', $key);
			$key = $key[0];
			
			// caculate working status
			if ($key == 'etimeclockwp-in') {
				$working_status = '1';
			}
			if ($key == 'etimeclockwp-breakon') {
				$working_status = '0';
			}
			if ($key == 'etimeclockwp-breakoff') {
				$working_status = '1';
			}
			if ($key == 'etimeclockwp-out') {
				$working_status = '0';	
			}
			$timestamp_array = explode('|', $val[0]);
			if (!isset($timestamp_array[1])) {
				$timestamp_array[1] = $count;
			}
			$total_time_array[$timestamp_array[1]] = $timestamp_array[0].'|'.$working_status;
			$count++;
		}
		
	}
	
	// reorder array values
	$total_time_array = array_values($total_time_array);
	$total_time = 0;
	foreach ($total_time_array as $key => $value) {
		$val = explode('|', $value);
		if ($val[1] == 0) {
			$previous = $total_time_array[$key-1];
			$previous = explode('|', $previous);
			$total_time_previous = $total_time;
			$total_time += $val[0] - $previous[0];
		}
	}
	
	// error in date - clock out is probably newer then clock in, so we should mark this as 00:00:00 with a review flag
	if ($total_time < 0) {
		$total_time = '';
		update_post_meta($post_id,'notices', true);
	} else {
		// remove notices flag if it exists
		delete_post_meta($post_id,'notices');
	}
	update_post_meta($post_id, 'total', $total_time);
}


// convert php date format to jQuery date format
// author Tristan Jahier
function etimeclockwp_dateformat_PHP_to_jQueryUI($php_format) {
    $SYMBOLS_MATCHING = array(
        // Day
        'd' => 'dd',
        'D' => 'D',
        'j' => 'd',
        'l' => 'DD',
        'N' => '',
        'S' => '',
        'w' => '',
        'z' => 'o',
        // Week
        'W' => '',
        // Month
        'F' => 'MM',
        'm' => 'mm',
        'M' => 'M',
        'n' => 'm',
        't' => '',
        // Year
        'L' => '',
        'o' => '',
        'Y' => 'yy',
        'y' => 'y',
        // Time
        'a' => 'tt',
        'A' => 'TT',
        'B' => '',
        'g' => 'h',
        'G' => 'H',
        'h' => 'h',
        'H' => 'H',
        'i' => 'mm',
        's' => 'ss',
        'u' => ''
    );
    $jqueryui_format = "";
    $escaping = false;
    for($i = 0; $i < strlen($php_format); $i++)
    {
        $char = $php_format[$i];
        if($char === '\\') // PHP date format escaping character
        {
            $i++;
            if($escaping) $jqueryui_format .= $php_format[$i];
            else $jqueryui_format .= '\'' . $php_format[$i];
            $escaping = true;
        }
        else
        {
            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
            if(isset($SYMBOLS_MATCHING[$char]))
                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
            else
                $jqueryui_format .= $char;
        }
    }
    return $jqueryui_format;
}
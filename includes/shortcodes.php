<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Calendar display month - draws a calendar with the bookings
if( !function_exists('timeclock_event_calendar')) {
	function timeclock_event_calendar($month,$year,$eventarray){
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
			if ( $istoday ) $todaycolor='#ffd80088;border:1px dotted black'; else $todaycolor='transparent';
			// QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY
			$dailyevents = '';
			$onlyfirst = 0;
			foreach ($eventarray as $calevent => $fields) {
				if ( substr($fields['verandatum'],0,10) == date('Y-m-d',mktime(0,0,0,$month,$list_day,$year)) ) {
					$todaycolor = '#ffd80088;font-weight:700';
					 $dailyevents .= '<span style="word-break:break-all;font-size:0.8em" title="'.esc_html($fields['veranstaltung']).'">' . $fields['veranstaltung'] . '</span><br>';
					$onlyfirst += 1;
				}
			}	
			if ( $onlyfirst > 0 ) $dailyevents .= ' <span class="newlabel white">'. $onlyfirst.'</span>';
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

function etimevaliduser() {
	global $datefilter;
	if ($_POST) {
		$eid =		sanitize_text_field($_POST['eid']);
		setcookie("etime_usercookie", $eid, time()+24000);
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
			$usercookie = isset( $_COOKIE['etime_usercookie'] ) ? $_COOKIE['etime_usercookie'] : '';
			echo '<div style="width:100%;text-align:center;display:block"><form method="post">';
			echo '<div class="etimeclock-text">'.etimeclockwp_get_option("employee-id").':<br /><input type="text" id="eid" name="eid" value="'.$usercookie.'"></div>';
			echo '<div class="etimeclock-text">'.etimeclockwp_get_option('employee-password').':<br /><input type="password" id="epw" name="epw"></div>';
			echo '<div class="etimeclock-text">'.__('time-filter','etimeclockwp').':<br /><input style="padding:4px 0" type="month" name="month"></div>';
			echo '<input type="submit" value="Anmelden"></form></div>';
		}	
	}
	if (!empty($user_id)) return $user_id; else return '';
}


function etime_menu($selectedmenu,$validuser) {
	global $wp;
		$mtext = '<li><a title="admin dashboard" href="'.site_url().'/wp-admin/edit.php?post_type=etimeclockwp_clock"><i class="fa fa-user"></i> '.strtoupper($validuser).'</a></li>'; 
		$mtext .= '<li><a href="'.home_url($wp->request).'?show=0" class="submit"><i class="fa fa-clock-o"></i> '.__('time clock','etimeclockwp').'</a></li>';
		if ( $selectedmenu !== 1 ) $mtext .= '<li><a title="'.__('admin show bookings','etimeclockwp').'" href="'.home_url($wp->request).'?show=1" class="submit"><i class="fa fa-list"></i></a></li>';
		if ( $selectedmenu !== 4 && current_user_can('administrator') ) $mtext .= '<li><a title="'.__('Panel','etimeclockwp').'" href="'.home_url($wp->request).'?show=4" class="submit"><i class="fa fa-heartbeat"></i></a></li>';
		if ( $selectedmenu !== 5 ) $mtext .= '<li><a title="'.__('view as calendar','etimeclockwp').'" href="'.home_url($wp->request).'?show=5" class="submit btnbutton"><i class="fa fa-calendar-o"></i></a></li>';
		$mtext .= '<li><a title="'.__('export','etimeclockwp').' '.__('activities','etimeclockwp').'" href="'.home_url($wp->request).'?show=2" class="submit btnbutton"><i class="fa fa-download"></i>|<i class="fa fa-list"></i></a></li>';
		if ( current_user_can('administrator') ) $mtext .= '<li><a title="'.__('export','etimeclockwp').' '.__('users','etimeclockwp').'" href="'.home_url($wp->request).'?show=3" class="submit btnbutton"><i class="fa fa-download"></i>|<i class="fa fa-users"></i></a></span>';
	return $mtext;
}

// Raum- oder Schreibtisch buchen Shortcode
function etimeclockwp_roombooking($atts) {
	global $wp, $wpdb;
	// get shortcode attributes
	$atts = shortcode_atts(array( 'raum' => '1', 'verandatum' => date('Y-m-d'), ), $atts);
	$raum = $atts['raum'];
	if (isset($_GET['raum'])) $raum = substr(esc_html($_GET['raum']),0,30);
	$verandatum = $atts['verandatum'];
	if (isset($_GET['verandatum'])) $verandatum = substr(esc_html($_GET['verandatum']),0,10);

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

	$html = '';

	//// Buchung löschen (nur admin) - Sitznummer übergeben
	if (current_user_can('administrator') && isset($_GET['delseat'])) {
		$postingid = (int) sanitize_text_field($_GET['delseat']);
		if ($postingid > 0) $wpdb->query("DELETE FROM ". $wpdb->prefix . "roombookings WHERE verandatum='".$verandatum." 00:00:00' AND raum = ".$raum." AND sitz = " . $postingid);
		wp_redirect( home_url( remove_query_arg( 'delseat' ) ) ); exit;
	}

	//// Raum mit buchungen löschen (nur admin) - Raumnummer übergeben
	if (current_user_can('administrator') && isset($_GET['delroom'])) {
		$table = $wpdb->prefix . "rooms";
		$postingid = (int) sanitize_text_field($_GET['delroom']);
		if ($postingid > 0) {
			$wpdb->query("DELETE FROM ". $wpdb->prefix . "rooms WHERE id = ".$postingid);
			$wpdb->query("DELETE FROM ". $wpdb->prefix . "roombookings WHERE raum = ".$postingid);
		}	
		wp_redirect( home_url( remove_query_arg( array ('delroom','raum') ) ) ); exit;
	}

	// Neuen Raum schreiben
   if (!empty($_POST['raumname'])) {
		$table = $wpdb->prefix . "rooms";
		$data = array(
			'datum' => date('Y-m-d'), 
			'raumname' => $_POST['raumname'], 
			'sitze' => (int) $_POST['sitze'], 
		);
		$success=$wpdb->insert( $table, $data );
		if($success){
			$html .= ' Raum '.$_POST['raumname'].' gespeichert' ; 
			$_POST['raumname']='';
		}
		wp_redirect( home_url( add_query_arg( NULL, NULL ) )); exit;

    }
	// Neuen Datensatz schreiben
   if (!empty($_POST['belegung'])) {
		$table = $wpdb->prefix . "roombookings";
	   $data = array(
			'verandatum' => $_POST['verandatum'], 
			'raum' => $_POST['raum'], 
			'sitz' => (int) $_POST['sitz'], 
			'belegung' => $_POST['belegung'], 
        );
		$success=$wpdb->insert( $table, $data );
        if($success){
            $html .= ' Belegung für Sitz '.$_POST['sitz'].' gespeichert' ; 
			$_POST['belegung']='';
        }
		wp_redirect(  home_url( add_query_arg( NULL, NULL ) ) ); exit;
    }

	// Belegungskalender
	$html .='<h6>Raumbelegung (freie Plätze seit 30 Tagen)</h6>';
	$customers = array();
	$xbelegung = $wpdb->get_results("SELECT wp_roombookings.verandatum, wp_rooms.raumname, wp_rooms.sitze, count(*) as belegt FROM wp_roombookings join wp_rooms on wp_rooms.id=wp_roombookings.raum WHERE wp_roombookings.verandatum >= CURDATE() - INTERVAL 30 DAY group by wp_roombookings.verandatum, wp_roombookings.raum" );
	foreach ($xbelegung as $beleg) {
		$freiesitze = ($beleg->sitze - $beleg->belegt);
		if ($freiesitze == 0) $zerofree = '#ff888888'; else if ($freiesitze <= 5) $zerofree ='#ffffff88'; else $zerofree ='#88ff8888';
		$customers[] = array ('verandatum' => $beleg->verandatum, 'veranstaltung' => '<span class="newlabel" style="line-height:10px;font-size:1em;background-color:'.$zerofree.'">' . $beleg->raumname.' | '.$beleg->sitze.'-'.$beleg->belegt.' | '.$freiesitze.'</span>' );
	}
	if ( !empty($customers)) {
		// Monatskalender mit Events zeigen
		$month=substr($customers[0]['verandatum'],5,2);
		$year=substr($customers[0]['verandatum'],0,4);
		if ( !empty($month) ) $html .= timeclock_event_calendar($month,$year,$customers);
		foreach($customers as $customer ) {
			if ( substr($customer['verandatum'],0,7) <> $year.'-'.$month ) {
				$month=substr($customer['verandatum'],5,2);
				$year=substr($customer['verandatum'],0,4);
				$html .= timeclock_event_calendar($month,$year,$customers);
			}	
		}
	} else { $html .= __('no records','etimeclockwp'); }	

	// Form Raum und datum auswählen
	$html .= '<div class="noprint">';
	$html .= '<form class="noprint" style="display:inline" method="get" name="raumauswahl">';
	$html .= '<input type="date" name="verandatum" value="'. $verandatum . '">';
	$xrooms = $wpdb->get_results("SELECT id, raumname,sitze FROM " . $wpdb->prefix . "rooms ORDER by raumname" );
	$html .= ' <select name="raum">';
	$sitzzahl = 0; $aktraumname='';
	foreach ($xrooms as $room) {
		$html .=  '<option value="'.$room->id.'"';
		if ($room->id == $raum) { $html .=  ' selected '; $sitzzahl = $room->sitze; $aktraumname = $room->raumname; }
		$html .=  '>' .$room->raumname.' ('.$room->sitze.')' . '</option>';
	}
	$html .=  '</select> ';
	$html .= '<input type="submit" name="raumauswahl" value="wählen"></form>';
	// Form Raum neu anlegen (nur Admin)
	if (current_user_can('administrator')) {
		$html .= ' oder <form class="noprint" style="display:inline" method="post" name="raumanlegen">';
		$html .= ' <input type="text" name="raumname" placeholder="neuer Raum Name">';
		$html .= ' <input type="number" name="sitze" min="1" max="999" placeholder="MaxSitze" style="width:70px"> ';
		$html .= '<input type="submit" name="raumanlegen" value="+"></form>';
	}	
	// Sitz im Raum buchen
	if ($sitzzahl > 0) {
		$html .= '<form class="noprint" method="post" name="sitzbuchung">';
		$xseats = $wpdb->get_results("SELECT id, verandatum, sitz,belegung FROM " . $wpdb->prefix . "roombookings WHERE verandatum='".$verandatum." 00:00:00' AND raum=".$raum." AND belegung <> '' ORDER by id" );
		$html .= '<select name="sitz">';
		for($i=1; $i <= $sitzzahl; $i++) {
			$found=false;
			foreach ($xseats as $seat) { if ($i == $seat->sitz) $found=true; }
			if (!$found) $html .=  '<option value="'.$i.'">' .$i. '</option>';
		}
		$html .=  '</select> ';
		$html .= '	<input type="hidden" name="verandatum" value="'.$verandatum.'">';
		$html .= '	<input type="hidden" name="raum" value="'.$raum.'">';
		$html .= '	<input type="text" id="belegung" name="belegung" placeholder="Belegung">';
		$html .= '<input type="submit" name="sitzbuchung" value="Sitz buchen"></form>';

		// Belegtplan anzeigen
		$html .='</div>';
		$xseats = $wpdb->get_results("SELECT  id, verandatum, sitz,belegung FROM " . $wpdb->prefix . "roombookings WHERE verandatum='".$verandatum." 00:00:00' AND raum=".$raum." ORDER by id" );
		$html .= '<blockquote style="margin-top:1em"><h6 class="widget-title" style="margin: -8px -15px 8px">';
		if (current_user_can('administrator')) $html .= '<a onclick="return confirm(\'Sind Sie sicher, den Raum zu entfernen?\');" href="'.home_url( add_query_arg( array ('delroom' => $raum) ) ).'"><i class="fa fa-trash"></i></a>';
		$belegung = count( $xseats );
		$prozent = round($belegung / $sitzzahl *100,1);
		$html .= ' Raum '.$raum.' '.$aktraumname.' | '.count( $xseats ).'/'.$sitzzahl.' belegt';
		$html .= ' <progress max=100 style="width:100px" value="'.$prozent.'"></progress>';
		$html .= ' | ' . date_i18n('D, d. M Y, \K\w W',$verandatum).'</h6>';
		$html .='<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr">';
		$belegt = 0;
		for($i=1; $i <= $sitzzahl; $i++) {
			$cc = 0; $found =- 1;
			foreach ($xseats as $seat) { $cc += 1; if ($i == $seat->sitz) $found = $cc-1; }
			if ($found >=0) {
				$belegt +=1;
				$html .= '<div style="background-color:#f42c2c;color:#fff;margin:4px;padding:5px;width:auto;display:inline-block;border:1px solid #888" title="Buchungs-ID: '.$found.'">'; 
				if (current_user_can('administrator')) $html .= '<a onclick="return confirm(\'Sind Sie sicher, den Sitz zu entfernen?\');" style="color:white" href="'.home_url( add_query_arg( array ('delseat' => $i) ) ).'"><i class="fa fa-trash"></i></a> &nbsp; ';
				$html .='<b>'.$i.'</b> - '.$xseats[$found]->belegung.'</div>'; 
			} else {
				$html .= '<div onclick="javascript:document.sitzbuchung.sitz.value = '.$i.';document.getElementById(\'belegung\').focus();" style="cursor:pointer;background-color:#4ecbab;color:#fff;margin:4px;padding:5px;display:inline-block;width:auto;border:1px solid #888" title="unbelegt, klicken zum Belegen">'; 
				$html .= '<b>'.$i.'</b> - frei</a></div>'; 
			}
		}
		$html .= '</div></blockquote>';

	}	
	return $html;
}
add_shortcode('roombooking', 'etimeclockwp_roombooking');

// Auswertungen, Listen und Exports der Zeiten
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
		$result .= '<div class="buttongrid"><div class="etimeclock-button" style="background-color:#888;margin-bottom:1em"><a href="'.home_url($wp->request).'?show=1" class="submit btnbutton">'.__('admin show bookings','etimeclockwp').'</a></div>';
		if ( current_user_can('administrator') ) $result .= '<div class="etimeclock-button" style="background-color:#666;margin-bottom:1em"><a href="'.home_url($wp->request).'?show=4" class="submit btnbutton">'.__('employee status','etimeclockwp').'</a></div>';
		$result .= '</div>';
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
		$usercookie = isset( $_COOKIE['etime_usercookie'] ) ? $_COOKIE['etime_usercookie'] : '';
		$result .= '<div class="etimeclock-text">'.etimeclockwp_get_option('employee-id').'<br><input id="etimeclock-eid" class="etimeclock-button etimeclock-input" style="color:#000" type="text" value="'.$usercookie.'" autocomplete="off" autocomplete="false"></div>';
		$result .= "<div class='etimeclock-text'>".etimeclockwp_get_option('employee-password').":<br /><input id='etimeclock-epw' class='etimeclock-button etimeclock-input' style='color:#000' type='password' autocomplete='off' autocomplete='false'></div>";
		$result .= "<br><br>";
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

		// ---------------------------- Activity-Anzeige letzte Buchungen -------------------------------------
		$result .= '<div style="text-align:right"><ul class="footer-menu">';
		$result .= etime_menu(1,$validuser);
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
		$result .= '</ul></div>';
		if ($validuser !=='admin') $userfilter=$validuser; else $userfilter=$current;
		$result .= '<strong>'.__('last booking','etimeclockwp').':</strong><br>'. user_last_booking($userfilter);
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
			$pausum=0;
			$azsum =0;
			foreach($metavalue as $key => $val) {
				if (substr($key, 0, 5) === "etime") {
					$key = explode('_', $key);
					$key = $key[0];
					$timestamp_array = explode('|', $val[0]);			
					$timestampdb = $timestamp_array[0];
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
						$result .= "</td><td>".date_i18n('D',$timestampdb).' '.$datetime.'</td><td>'.ago( $timestampdb - date('Z') ).'</td><td style="text-align:center">';
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

		// -------------------------------------------CSV Export activities ---------------------
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
			$pausum=0;
			$azsum =0;
			foreach($metavalue as $key => $val) {
				if (substr($key, 0, 5) === "etime") {
					$key = explode('_', $key);
					$key = $key[0];
					$timestamp_array = explode('|', $val[0]);			
					$timestampdb = $timestamp_array[0];
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

		// -------------------------------------------CSV Export users for admin ---------------------
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
		fputcsv( $output, array('Username', 'UserID', 'Kennwort', 'RecordNo', 'created', 'lastBooking'), ';');
		foreach($users as $user) {
			$modified_values = array(
				$user->post_title,
				sanitize_text_field(get_post_meta($user->ID,'etimeclockwp_id', true)),
				sanitize_text_field(get_post_meta($user->ID,'etimeclockwp_pwd', true)),
				$user->ID,
				$user->post_date,
				user_last_booking($user->ID, true),
			);
		   fputcsv( $output, $modified_values, ';' );
		}
		exit;

	} else if ($showmode == 4 && current_user_can('administrator') ) {

		// ------------------------------------------- show admin panel with last user status ---------------------
		$result .= '<div style="text-align:right"><ul class="footer-menu"><ul class="footer-menu">' . etime_menu(4,$validuser) . '</ul></div>';
		$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', ) );
		$result .= '<blockquote><h6 class="widget-title" style="margin: -8px -16px 10px -16px">'.__('employee status','etimeclockwp').'</h6>';
		foreach($users as $user) {
			$usersname = $user->post_title;
			$result .= '<strong style="font-size:1.2em">'.__('last booking','etimeclockwp').': '.$usersname.'</strong><br>'. user_last_booking($user->ID);
		}
		$result .= '</blockquote>';

	} else if ($showmode == 5 && ( current_user_can('administrator') || !empty($validuser = etimevaliduser()) ) ) {

		// ---------------------------- Activity-Anzeige im Kalender wenn calendar func --------------------------------
		$result .= '<div style="text-align:right"><ul class="footer-menu"><li><i class="fa fa-user"></i> <b>'.strtoupper($validuser).'</b></li>';
		$result .= etime_menu(5,$validuser);
		$current='';
		if (current_user_can('administrator') ) {
			$result .= ' &nbsp; <form class="noprint" style="display:inline" name="userfilter" method="post" action="'.home_url(add_query_arg(array('show'=>'5'), $wp->request)).'">';
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
		$result .= '</ul></div>';
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
		$customers = array();
		foreach($activity as $post) {
			$users = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'etimeclockwp_users', 'post__in' => array($post->post_title) ) );
			foreach($users as $user) { $usersname = $user->post_title; }
			$tagessumme = etimeclockwp_get_time_worked($post,$format = true);
			// Datum ins Array for Calendar
			$customers[] = array ('verandatum' => get_the_date('Y-m-d H:i:s',$post->ID), 'veranstaltung' => $usersname.' '.etimeclockwp_get_time_worked($post,$format = true) );
		}
		if ( !empty($customers)) {
			// Monatskalender mit Events zeigen
			$month=substr($customers[0]['verandatum'],5,2);
			$year=substr($customers[0]['verandatum'],0,4);
			if ( !empty($month) ) $result .= timeclock_event_calendar($month,$year,$customers);
			foreach($customers as $customer ) {
				if ( substr($customer['verandatum'],0,7) <> $year.'-'.$month ) {
					$month=substr($customer['verandatum'],5,2);
					$year=substr($customer['verandatum'],0,4);
					$result .= timeclock_event_calendar($month,$year,$customers);
				}	
			}
		} else { $result .= __('no records','etimeclockwp'); }	
	} else {

		// kein Zugriff, Meldung anzeigen -------------------------------
		$result = '<div class="newlabel yellow" style="font-size:1em;width:100%;text-align:center">'.__('access denied, wrong username or password','etimeclockwp').'</div>';

	} //////	Ende Showausgabe
	return $result;
}
add_shortcode('timeclock', 'etimeclockwp_button_shortcode');
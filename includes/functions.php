<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// This page is for general functions

// Zeitdifferenz ermitteln und gestern/vorgestern/morgen schreiben
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
		$her = intval($now) - intval($timestamp);
		if ($her > 86400 and $her < 172800) {
			$hdate = __('yesterday', 'penguin');
		} else if ($her > 172800 and $her < 259200) {
			$hdate = __('1 day before yesterday', 'penguin');
		} else if ($her < - 86400 and $her > - 172800) {
			$hdate = __('tomorrow', 'penguin');
		} else if ($her < - 172800 and $her > - 259200) {
			$hdate = __('1 day after tomorrow', 'penguin');
		} else {
			$hdate = ' ' . $prepo . ' ' . human_time_diff(intval($timestamp), $now) . ' ' . $postpo;
		}
		return $hdate;
	}
}	

// Differenz zwischen 2 Beiträgen (kurz)
if( !function_exists('german_time_diff')) {
	function german_time_diff( $from, $to ) {
		$days_old = abs(round(( $to - $from ) / 86400 , 0 ));
		if ( $days_old < 30 ) $newclass = 'yellow'; else $newclass = 'white';
		$diff = human_time_diff($from,$to);
		$longreplace = array(   // Grammatik bei Anzeige langer Differenz (Monate statt Monaten)
			'Tagen' => 'Tage',	'Monaten' => 'Monate',	'Jahren' => 'Jahre'
		);
		$replace = array(  // Auf Kurzform umstellen
			'Sekunde'  => 's', 'Sekunden'  => 's',
			'Minute'  => 'm', 'Minuten'  => 'm',
			'Stunde'  => 'h', 'Stunden' => 'h',
			'Tag'   => 'T', 'Tage'  => 'T',
			'Woche'  => 'W', 'Wochen'  => 'W',
			'Monat'  => 'M', 'Monate'  => 'M',
			'Jahr'  => 'J', 'Jahre'  => 'J',
			'n' =>''
		);
		$aetitle = __('time since previous post','penguin').'&#10;'.strtr($diff,$longreplace).'&#10;'.$days_old.' Tage';
		return '<abbr title="'.$aetitle.'" class="newlabel '.$newclass.'"><i title="'.$aetitle.'" class="fa fa-arrows-v"></i>&nbsp;' . strtr($diff,$replace) . '</abbr>';
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
	$t = round((int) $seconds);
	return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
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
		sprintf('%02d:%02d:%02d', ($azsum / 3600),($azsum / 60 % 60), $azsum % 60),
		sprintf('%02d:%02d:%02d', ($pausum / 3600),($pausum / 60 % 60), $pausum % 60),
		sprintf('%02d:%02d:%02d', (($azsum + $pausum) / 3600),(($azsum + $pausum) / 60 % 60), ($azsum + $pausum) % 60),
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
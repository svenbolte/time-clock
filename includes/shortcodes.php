<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function etimeclockwp_button_shortcode($atts) {

	global $current_user;
	

	// get shortcode attributes
	$atts = shortcode_atts(array(
		'align' 	=> '',
	), $atts);
	
	// set action url
	$action_url = add_query_arg('etimeclockwp-action','charge',home_url( 'index.php'));
	
	$result = "";
	
	$result .= "<div class='etimeclock-main'>";
		$result .= "<div class='etimeclock-body'>";
			
			// time section
			$result .= "<span class='etimeclock-date'></span>";
			$result .= "<br />";
			$result .= "<span class='etimeclock-time'></span>";
			$result .= "<br /><br />";
			
			
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
			
			// create nonce
			$ajax_nonce = wp_create_nonce('etimeclock_nonce');
			$result .= "<input type='hidden' id='etimeclock_nonce' value='$ajax_nonce'>";
			
		$result .= "</div>";
	$result .= "</div>";
	
	
	return $result;
}
add_shortcode('timeclock', 'etimeclockwp_button_shortcode');
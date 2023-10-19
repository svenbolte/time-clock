<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// admin enqueue
function etimeclockwp_admin_enqueue() {

	// admin css
	wp_register_style('etimeclockwp-admin-css',plugins_url('/assets/css/etimeclockwp-admin.css',dirname(__FILE__)),false,ETIMECLOCKWP_VERSION);
	wp_enqueue_style('etimeclockwp-admin-css');
	
	// admin js
	wp_enqueue_script('etimeclockwp-admin',plugins_url('/assets/js/etimeclockwp-admin.js',dirname(__FILE__)),array('jquery'),ETIMECLOCKWP_VERSION);
	wp_localize_script('etimeclockwp-admin', 'etimeclockwp_admin_ajax_object', array(
		'ajax_url' 			=> admin_url('admin-ajax.php'),
		'dateformat' 		=> etimeclockwp_dateformat_PHP_to_jQueryUI(etimeclockwp_get_option('date-format')),
		'timeformat' 		=> etimeclockwp_dateformat_PHP_to_jQueryUI(etimeclockwp_get_option('time-format'))
	));
	
	// admin tabs js - used on the settings page
	wp_enqueue_script('etimeclockwp-admin-tabs',plugins_url('/assets/js/etimeclockwp-admin_tabs.js',dirname(__FILE__)),array('jquery'),ETIMECLOCKWP_VERSION);
	
	// jquery datepicker
	wp_enqueue_script( 'jquery-ui-datepicker');
	wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css'); // wordpress does not include datepicker css in core
	wp_enqueue_style( 'jquery-ui' );
	
	// jquery timepicker addon
	wp_enqueue_script('etimeclockwp-timepicker-js',plugins_url('/assets/js/etimeclockwp-jquery-ui-timepicker-addon.js',dirname(__FILE__)),array('jquery-ui-core' ,'jquery-ui-datepicker'));
	wp_register_style('etimeclockwp-timepicker-css',plugins_url('/assets/css/etimeclockwp-jquery-ui-timepicker-addon.css',dirname(__FILE__)));
	wp_enqueue_style('etimeclockwp-timepicker-css');
	
	// color picker
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('wp-color-picker');
	
}
add_action('admin_enqueue_scripts','etimeclockwp_admin_enqueue');


// public enqueue 	// clock in / out actions ajax not performed when on home page, only on posts where shortcode
function etimeclockwp_public_enqueue() {
	if ( !is_home() && ! is_front_page() ) {
	
		// public css
		wp_register_style('etimeclockwp-public-css',plugins_url('/assets/css/etimeclockwp-public.css',dirname(__FILE__)),false,ETIMECLOCKWP_VERSION);
		wp_enqueue_style('etimeclockwp-public-css');
		
		// date time
		wp_register_script('etimeclockwp-date-time',plugins_url('/assets/js/etimeclockwp-date_time.js',dirname(__FILE__)),array('jquery'),ETIMECLOCKWP_VERSION);
		wp_localize_script('etimeclockwp-date-time', 'ajax_object_date_time', array(
			'date_format' 		=> etimeclockwp_get_option('date-format'),
			'time_format' 		=> etimeclockwp_get_option('time-format')
			)
		);
		
		// clock in / out actions ajax 
		wp_enqueue_script('etimeclockwp-clock-action',plugins_url('/assets/js/etimeclockwp-clock_action.js',dirname(__FILE__)),array('jquery'),ETIMECLOCKWP_VERSION);
		wp_localize_script('etimeclockwp-clock-action', 'ajax_object_clock_action', array(
			'ajax_url' 			=> admin_url('admin-ajax.php')
			)
		);
		
		// moment library
		wp_enqueue_script('etimeclockwp-moment',plugins_url('/assets/js/etimeclockwp-moment.min.js',dirname(__FILE__)),array('jquery'),ETIMECLOCKWP_VERSION);
		wp_enqueue_script('etimeclockwp-moment-php',plugins_url('/assets/js/etimeclockwp-moment.phpDateFormat.js',dirname(__FILE__)),array('jquery'),ETIMECLOCKWP_VERSION);
	}
}
add_action('wp_enqueue_scripts','etimeclockwp_public_enqueue',10);
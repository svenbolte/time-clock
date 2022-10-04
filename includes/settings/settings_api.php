<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// settings array
function etimeclockwp_settings() {

	$etimeclockwp_settings_array = apply_filters('etimeclockwp_settings_top_level', array(
		apply_filters('etimeclockwp_settings_etimeclockwp_tab', array(
			__('Dashboard','etimeclockwp') => apply_filters('etimeclockwp_settings_getting_started_page', array(
				array(
					'title' 		=> __( 'Dashboard', 'etimeclockwp' ),
					'name' 			=> '',
					'type' 			=> 'dashboard'
				),
			)),
		)),
		apply_filters('etimeclockwp_settings_general_tab', array(
			__('General','etimeclockwp') =>
			array(
				__('Main','etimeclockwp') => array(
					'title' 		=> __( 'Show Break In / Break Out Options', 'etimeclockwp' ),
					'name' 			=> 'show_break_options',
					'type' 			=> 'dropdown',
					'options' 		=> array (
						__('Yes (Default)','etimeclockwp'),
						__('No','etimeclockwp'),
					),
					'default'		=> '0',
					'help' 			=> __('When the timeclock shortcode is displayed, the Clock In / Clock Out Buttons are displayed. Should the Leave On Break / Return From Break buttons also be displayed?','etimeclockwp'),
				),
			),
			array(
				__('Default Colors','etimeclockwp') => array(
					'title' 		=> __( 'Clock In - Button Color', 'etimeclockwp' ),
					'name' 			=> 'clock-in-button-color',
					'type' 			=> 'color',
					'default'		=> '#3EBB9C',
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Clock Out - Button Color', 'etimeclockwp' ),
					'name' 			=> 'clock-out-button-color',
					'type' 			=> 'color',
					'default'		=> '#3894D1',
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Leave On Break - Button Color', 'etimeclockwp' ),
					'name' 			=> 'leave-on-break-button-color',
					'type' 			=> 'color',
					'default'		=> '#FFC655',
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Return From Break - Button Color', 'etimeclockwp' ),
					'name' 			=> 'return-from-break-button-color',
					'type' 			=> 'color',
					'default'		=> '#FF5555',
					'help' 			=> '',
				),
			),
			array(
				__('Default Text','etimeclockwp') 		=>
				array(
					'title' 		=> __( 'Employee ID', 'etimeclockwp' ),
					'name' 			=> 'employee-id',
					'type' 			=> 'input',
					'default'		=> __( 'Employee ID', 'etimeclockwp' ),
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Employee Password', 'etimeclockwp' ),
					'name' 			=> 'employee-password',
					'type' 			=> 'input',
					'default'		=> __( 'Employee Password', 'etimeclockwp' ),
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Clock In', 'etimeclockwp' ),
					'name' 			=> 'clock-in',
					'type' 			=> 'input',
					'default'		=> __( 'Clock In', 'etimeclockwp' ),
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Clock Out', 'etimeclockwp' ),
					'name' 			=> 'clock-out',
					'type' 			=> 'input',
					'default'		=> __( 'Clock Out', 'etimeclockwp' ),
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Leave On Break', 'etimeclockwp' ),
					'name' 			=> 'leave-on-break',
					'type' 			=> 'input',
					'default'		=> __( 'Leave On Break', 'etimeclockwp' ),
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Return From Break', 'etimeclockwp' ),
					'name' 			=> 'return-from-break',
					'type' 			=> 'input',
					'default'		=> __( 'Return From Break', 'etimeclockwp' ),
					'help' 			=> '',
				),
			),
			array(
				__('Date & Time Format','etimeclockwp') 		=>
				array(
					'title' 		=> __( 'Date Format', 'etimeclockwp' ),
					'name' 			=> 'date-format',
					'type' 			=> 'date',
					'default'		=> 'j. M Y',
					'help' 			=> '',
				),
				array(
					'title' 		=> __( 'Time Format', 'etimeclockwp' ),
					'name' 			=> 'time-format',
					'type' 			=> 'time',
					'default'		=> 'h:i:s',
					'help' 			=> '',
				),
			),
		)),
		
		
		
		apply_filters('etimeclockwp_settings_advanced_tab', array(
			__('Advanced','etimeclockwp') => array(
				__('Plugin Data','etimeclockwp') => array(
					'title' 		=> __( 'Remove all plugin data on uninstall', 'etimeclockwp' ),
					'name' 			=> 'uninstall',
					'type' 			=> 'dropdown',
					'options' 		=> array (
						__('No (Default)','etimeclockwp'),
						__('Yes','etimeclockwp'),
					),
					'default'		=> '0',
					'help' 			=> '',
				),
			),
		)),
	));
	
	return $etimeclockwp_settings_array;
}

// render menu
function etimeclockwp_settings_render_menu() {

	global $etimeclockwp_options;
	
	// get settings
	$settings = etimeclockwp_settings();
	
	// make array
	$tabs_array = [];
	$tabs_array_level1 = [];
	$tabs_array_level2 = [];
	
	$level = "0";
	foreach ($settings as $tab) {
		
		$tabs_array_level1[] = key($tab);
		
		foreach ($tab as $element) {
			
			$tabs_array_level2[$level][] = key($element);
			
		}
		$level++;
		
		
	}
		
	$tabs_array = array_merge(array($tabs_array_level1),$tabs_array_level2);

	// make tabs
	if (!empty($etimeclockwp_options['tab'])) {
		$etimeclockwp_active_tab =  $etimeclockwp_options['tab'];
	} else {
		$etimeclockwp_active_tab = "tab00";
	}
	
	if (isset($_GET['tab'])) {
		$etimeclockwp_active_tab = "tab".intval($_GET['tab']);
	}
	
	$etimeclockwp_active_tab_top = substr($etimeclockwp_active_tab, 0, -1);
	
	echo "<table width='100%'><tr><td width='90%'>";
	
	echo "<span class='dashicons dashicons-admin-generic'></span><span id='etimeclockwp-menu-title'>&nbsp;"; echo ETIMECLOCKWP_NAME; echo " "; echo __('Settings','etimeclockwp'); echo "</span><br /><span class='etimeclockwp-menu-sub-title'>";
	echo __(' Version ','etimeclockwp'); echo ETIMECLOCKWP_VERSION; echo "</span><br />";
	//echo "<br /><span class='etimeclockwp-menu-sub-title'> &nbsp; &nbsp; &nbsp; &nbsp;"; echo __('eCommerce built to perfection.','etimeclockwp'); echo "</span>";
	
	
	echo "</td><td width='10%' valign='bottom'>";
	echo "<input name='submit' id='submit' class='button button-primary etimeclockwp-settings-button' value='Save Changes' type='submit'>";
	echo "</td></tr></table>";

	// menu div
	echo "<div id='etimeclockwp-menu-div'>";
	
	
	// menu level 1
	echo "<h1 class='nav-tab-wrapper'>";
	
	$counter = "0";
	foreach ($tabs_array as $tabs => $tab) {
		
		if (!empty($tab[0])) {
			if ($tabs == 0) {
				echo "<ul id='etimeclockwp-tabs'>";
				foreach ($tab as $count => $title) {
					echo "<li><a href='#' id='tab$count$counter' class='nav-tab"; if ($etimeclockwp_active_tab_top == "tab".$count) { echo " nav-tab-active'"; } echo "'>$title</a></li>";
				}
				echo "</ul>";
			}
		}
		
	}
	echo "</h1>";
	
	
	// menu level 2
	$counter = "0";
	
	foreach ($tabs_array as $tabs => $tab) {
		if ($tabs > 0) {
			
			// remove any empty elements - this is necessary for extensions that add a new top level tab
			$tab = array_filter($tab);
			$tab = array_values($tab);
			
			echo "<ul id='etimeclockwp-tabs-more' class='subsubsub etimeclockwp-more etimeclockwp-more-tab$counter'"; if ($etimeclockwp_active_tab_top == "tab".$counter) { echo "style='display: block;'"; } echo ">";
			$tab_count = count( $tab );
			$tab_count--;
			foreach ($tab as $count => $title) {
				if (!empty($title)) {
					echo "<li><a href='#' id='tab$counter$count' class='tab-more tab"; echo $counter.$count; echo "T"; if ($etimeclockwp_active_tab == "tab".$counter.$count) { echo " current '"; } echo "'>$title</a>";
					
					if ($tab_count > $count) {
					echo "|";
					}
					
					echo "</li>";
				}
			}
			echo "</ul>";
			$counter++;
			
		}
	}
	
	echo "</div>";
	
	settings_errors();
	
	return;
}


// dashboard items array
function etimeclockwp_dashboard_api() {
	$etimeclockwp_dashboard_array = apply_filters('etimeclockwp_dashboard_array', array());
	return $etimeclockwp_dashboard_array;
}

// license items array
function etimeclockwp_licenses_list() {
	$etimeclockwp_license_list_array = apply_filters('etimeclockwp_licenses_list', array());
	return $etimeclockwp_license_list_array;
}



// render option types
// allowed types
// -------------
// text - 						plain text - 						public use
// dropdown - 					dropdown menu - 					public use
// input - 						input field - 						public use
// color - 						color picker - 						public use
// image - 						image picker - 						public use
// date - 						date format - 						public use
// time - 						time picker - 						public use
// editor - 					wordpress rich editor - 			public use
// textarea - 					plain textarea - 					public use
// license_list - 				list of licenses, can be added to - public use
// hr - 						horzontal rule - 					public use
// title - 						bold text in title column - 		public use
// pages - 						pages installed at install -		public use
// categories - 				product categories -				public use

function etimeclockwp_settings_render_option($item) {

	global $etimeclockwp_options;
	
	extract($item);
	
	if (!isset($default)) { $default = ''; }
	if (!isset($class)) { $class = ''; }
	if (!isset($class_div)) { $class_div = ''; }

	// text
	if ($type == "text") {
		echo "<table><tr><td  class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		echo $options;
		echo "</td></tr></table>";
	}
	
	// dropdown
	if ($type == "dropdown") {
		
		echo "<table><tr><td  class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		echo "<div class='$class_div'><select name='etimeclockwp_settings[$name]' class='etimeclockwp_cell_width $class'>";
		
		$count = "0";
		foreach($options as $key => $value) {
			echo "<option ";
			if (!empty($etimeclockwp_options[$name])) {
				if ($etimeclockwp_options[$name] == $count || $etimeclockwp_options[$name] == $key) { echo " SELECTED "; }
			} else {
				if ($count == $default || $key == $default) { echo " SELECTED "; }
			}
			echo "value='$key'>"; echo $value; echo "</option>";
			$count++;
		}
		
		echo "</select></div></td><td>";
		if (!empty($help)) {
			echo "<span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
		}
		echo "</td></tr></table>";
	}
	
	// image
	if ($type == "image") {
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		
		echo "<input type='text' class='etimeclockwp_cell_width' name='etimeclockwp_settings[$name]' id='image_url' value=' "; if (!empty($etimeclockwp_options[$name])) { echo $etimeclockwp_options[$name]; } else { echo $default; } echo " ' >";
		echo "<a class='etimeclockwp_mediauploader button'>Add or Upload Image</a>";
		
		if (!empty($help)) {
			echo "<span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
		}
		
		echo "</td></tr></table>";
	}
	
	// pages
	if ($type == "pages") {
		
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		
		echo "<select name='etimeclockwp_settings[$name]' class='etimeclockwp_cell_width'>";
		
		$args = array(
			'sort_order' 	=> 'asc',
			'sort_column' 	=> 'post_title',
			'post_type' 	=> 'page',
			'post_status' 	=> 'publish'
		); 
		$pages = get_pages($args);
		
		foreach($pages as $page) {
			echo "<option ";
			if (!empty($etimeclockwp_options[$name])) {
				if ($etimeclockwp_options[$name] == $page->ID) { echo " SELECTED "; }
			} else {
				if ($page->ID == $default) { echo " SELECTED "; }
			}
			echo "value='"; echo $page->ID; echo "'>"; echo $page->post_title; echo "</option>";
		}
		
		echo "</select>";
		if (!empty($help)) {
			echo "<span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
		}
		echo "</td></tr></table>";
	}
	
	// categories
	if ($type == "categories") {
		
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		
		echo "<select name='etimeclockwp_settings[$name]' class='etimeclockwp_cell_width'><option></option>";
		
		$args = array (
		'taxonomy'		=> 'product_category',
		'orderby'		=> 'name',
		'order'			=> 'ASC',
		);
		$categories = get_categories($args);
		
		foreach($categories as $category) {
			
			echo "<option ";
			if (!empty($etimeclockwp_options[$name])) {
				if ($etimeclockwp_options[$name] == $category->slug) { echo " SELECTED "; }
			} else {
				if ($category->slug == $default) { echo " SELECTED "; }
			}
			echo "value='"; echo $category->slug; echo "'>"; echo $category->name; echo "</option>";
		}
		
		echo "</select>";
		if (!empty($help)) {
			echo "<span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
		}
		echo "</td></tr></table>";
	}
	
	// input
	if ($type == "input") {
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		echo "<input class='etimeclockwp_cell_width' type='text' name='etimeclockwp_settings[$name]' value="; echo '"'; if (!empty($etimeclockwp_options[$name])) { echo $etimeclockwp_options[$name]; } else { echo $default; } echo '"'; echo ">";
		
		if (!empty($help)) {
			echo " <span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
		}
		
		echo "</td></tr></table>";
	}
	
	// hr
	if ($type == "hr") {
		echo "<hr>";
	}
	
	
	
	// date
	if ($type == "date") {
		
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		echo "<select name='etimeclockwp_settings[$name]' class='etimeclockwp_cell_width'>";
				echo "<option value='j. M Y'"; 	 if (etimeclockwp_get_option($name) == 'j. M Y') 	{ 	echo " SELECTED "; }  	echo ">".date_i18n('j').". ".date_i18n('M')." ".date_i18n('Y')."</option>";
				echo "<option value='j. F Y'"; 	 if (etimeclockwp_get_option($name) == 'j. F Y') 	{ 	echo " SELECTED "; }  	echo ">".date_i18n('j').". ".date_i18n('F')." ".date_i18n('Y')."</option>";
				echo "<option value='F j, Y'"; 	 if (etimeclockwp_get_option($name) == 'F j, Y') 	{ 	echo " SELECTED "; }  	echo ">".date_i18n('F')." ".date_i18n('j').", ".date_i18n('Y')."</option>";
                echo "<option value='M j, Y'"; 	 if (etimeclockwp_get_option($name) == 'M j, Y') 	{ 	echo " SELECTED "; } 	echo ">".date_i18n('M')." ".date_i18n('j').", ".date_i18n('Y')."</option>";
                echo "<option value='M j'"; 	 if (etimeclockwp_get_option($name) == 'M j') 		{ 	echo " SELECTED "; }	echo ">".date_i18n('M')." ".date_i18n('j')."</option>";
                echo "<option value='j.n.Y'"; 	 if (etimeclockwp_get_option($name) == 'n.j.Y') 	{ 	echo " SELECTED "; }	echo ">".date_i18n('n').".".date_i18n('j').".".date_i18n('Y')."</option>";
                echo "<option value='j.n.y'"; 	 if (etimeclockwp_get_option($name) == 'n.j.y') 	{ 	echo " SELECTED "; }	echo ">".date_i18n('n').".".date_i18n('j').".".date_i18n('y')."</option>";
                echo "<option value='n.j.'"; 	 if (etimeclockwp_get_option($name) == 'n.j.') 		{ 	echo " SELECTED "; }	echo ">".date_i18n('n').".".date_i18n('j')."."."</option>";
		echo "</select>";
		echo "</td></tr></table>";
	}
	
	
	// time
	if ($type == "time") {
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		echo "<select name='etimeclockwp_settings[$name]' class='etimeclockwp_cell_width'>";
			echo "<option value='H:i:s'"; 	 	if (etimeclockwp_get_option($name) == 'H:i:s') 		{ 	echo " SELECTED "; }	echo ">".date_i18n('H:i:s')."</option>";		
			echo "<option value='H:i'"; 	 	if (etimeclockwp_get_option($name) == 'H:i') 		{ 	echo " SELECTED "; }	echo ">".date_i18n('H:i')."</option>";	
			echo "<option value='g:i:s a'"; 	if (etimeclockwp_get_option($name) == 'g:i:s a') 	{ 	echo " SELECTED "; }  	echo ">".date_i18n('g:i:s a')."</option>";
			echo "<option value='g:i:s A'"; 	if (etimeclockwp_get_option($name) == 'g:i:s A') 	{ 	echo " SELECTED "; } 	echo ">".date_i18n('g:i:s A')."</option>";
			echo "<option value='g:i a'"; 	 	if (etimeclockwp_get_option($name) == 'g:i a') 		{ 	echo " SELECTED "; } 	echo ">".date_i18n('g:i a')."</option>";
			echo "<option value='g:i A'"; 	 	if (etimeclockwp_get_option($name) == 'g:i A') 		{ 	echo " SELECTED "; } 	echo ">".date_i18n('g:i A')."</option>";
		echo "</select>";
		echo "</td></tr></table>";
	}
	
	
	
	
	
	// title
	if ($type == "title") {
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "<b>$title</b></td><td>";
		echo "</td></tr></table>";
	}
	
	// dashboard
	if ($type == "dashboard") {
		
		$dashboard_array = etimeclockwp_dashboard_api();
		
		foreach ($dashboard_array as $box) {
			echo "<div class='postbox etimeclockwp_settings_dashboard_item'>";
				
				echo "<h2 class='hndle'><span>";
					echo $box['title'];
				echo "</span></h2>";
				
				echo "<div class='inside'>";
					echo $box['body'];
				echo "</div>";
				
			echo "</div>";
		}
		
	}
	
	// color
	if ($type == "color") {
		echo "<table><tr><td class='etimeclockwp_cell_title_width'>";
		echo "$title:</td><td>";
		echo "<input class='etimeclockwp_cell_width etimeclockwp_colorpicker' type='text' name='etimeclockwp_settings[$name]' value='"; if (!empty($etimeclockwp_options[$name])) { echo $etimeclockwp_options[$name]; } else { echo $default; } echo "'>";
		
		if (!empty($help)) {
			echo "<span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
		}
		
		echo "</td></tr></table>";
	}
	
	// editor
	if ($type == "editor") {
		echo "<table width='70%'><tr><td class='etimeclockwp_cell_title_width' valign='top'>";
		echo "<br />$title:</td><td>";
		$content = "";
		if (empty($etimeclockwp_options[$name])) { $content = $default; } else { $content = $etimeclockwp_options[$name]; }
		$content = stripslashes($content);
		$editor_id = $id;
		wp_editor($content, $editor_id, $settings = array(
			'textarea_name' => "etimeclockwp_settings[$name]"
		));
		echo "</td></tr><tr><td></td><td>";
		
		echo $desc;
		
		
		echo "</td></tr></table>";
	}
	
	// textarea sysytem info
	if ($type == "textarea") {
		
		if (isset($load)) {
			echo "<table width='80%'><tr><td class='etimeclockwp_cell_title_width' valign='top'>";
			echo "$title:</td><td>";
			echo "<a href='#' id='etimeclockwp_load_function' class='etimeclockwp_$name' data-placeholder='$load'>"; echo __('Generate Report','etimeclockwp'); echo "</a>";
			echo "<textarea style='width:100%;display:none;' rows='20' class='Tetimeclockwp_$name' readonly>";
			echo"</textarea>";
		} else {
			echo "<table><tr><td class='etimeclockwp_cell_title_width' valign='top'>";
			echo "$title:</td><td>";
			if (!isset($cols)) { $cols = '34'; }
			if (!isset($rows)) { $rows = '5'; }
			echo "<textarea rows='$rows' cols='$cols' name='etimeclockwp_settings[$name]'>";
				if (!empty($etimeclockwp_options[$name])) { echo $etimeclockwp_options[$name]; } else { echo $default; }
			echo"</textarea></td><td valign='top'>";
			if (!empty($help)) {
				echo "<span alt='f223' class='etimeclockwp-help-tip dashicons dashicons-editor-help' title='$help'></span>";
			}
		}
		
		echo "</td></tr></table>";
	}
	
	
	
	
	
	
	// license_list
	if ($type == "license_list") {
		$licenses = etimeclockwp_licenses_list();
		foreach ($licenses as $license) {
			foreach ($license as $license_details) {
				
				echo "<table>";
				
				echo "<tr><td><b>";
				echo $license_details['name'];
				echo "</b></td></tr>";
				
				echo "<tr><td class='etimeclockwp_cell_title_width'>";
				echo __('Key','etimeclockwp'),":";
				echo "</td><td>";
				
				echo "<input name='etimeclockwp_settings[".$license_details['slug']."]' size='45' type='text' value='"; if (!empty($license_details['key'])) { echo $license_details['key']; } echo "'>";
				
				// license nonce
				wp_nonce_field($license_details['slug'].'_nonce', $license_details['slug'].'_nonce');
				
				
				if ($license_details['status'] !== false && $license_details['status'] == 'valid' ) {
					// active
					echo "<input type='submit' class='button-secondary' name='".$license_details['slug']."_license_deactivate' value='"; echo __('Deactivate License','etimeclockwp'); echo "'>";
				} else {
					// inactive
					echo "<input type='submit' class='button-secondary' name='".$license_details['slug']."_license_activate' value='"; echo __('Activate License','etimeclockwp'); echo "'>";
				}
				
				echo "</td></tr>";
				
				
				echo "<tr><td class='etimeclockwp_cell_title_width'>";
				echo __('Status','etimeclockwp'),":";
				echo "</td><td>";
				
				if($license_details['status'] !== false && $license_details['status'] == 'valid' ) {
					
					echo "<span style='color:green;'>"; echo __('Active','test_plugin'); echo "</span> </td></tr><tr><td>";
					
					echo "</td></tr>";
					
				} else {
					echo "<span style='color:red;'>"; echo __('Inactive','test_plugin'); echo "</span> </td></tr><tr><td>";
					
					if (!empty($license_details['message'])) {
						echo __('Message','test_plugin');
						
						echo ": </td><td>";
						
						echo $license_details['message'];
					}
				}
				
				echo "</td></tr>";
				
				
				echo "</table>";
			}
		}
	}
	

	
	
}




// render settings page div's
function etimeclockwp_settings_render() {

	global $etimeclockwp_active_tab,$etimeclockwp_options;
	
	$settings = etimeclockwp_settings();
	
	// make tabs
	if (!empty($etimeclockwp_options['tab'])) {
		$etimeclockwp_active_tab =  $etimeclockwp_options['tab'];
	} else {
		$etimeclockwp_active_tab = "tab00";
	}
	
	if (isset($_GET['tab'])) {
		$etimeclockwp_active_tab = "tab".intval($_GET['tab']);
	}
	
	echo "<div class='metabox-holder'>";

	$tab_id = "0";
	foreach ($settings as $tab) {
		
		// remove any empty elements - this is necessary for extensions that add a new top level tab
		$tab = array_filter($tab);
		$tab = array_values($tab);
		
		$page_id = "0";
		foreach ($tab as $element) {
			
			echo "<div class='"; if ($tab_id != '0') { echo "postbox"; } echo " etimeclockwp-container' "; if ($etimeclockwp_active_tab == "tab$tab_id$page_id") { echo "style='display:block;'"; } else { echo "style='display:none;'"; } echo " id='tab"; echo $tab_id; echo $page_id;  echo "C'>";
				
				// Show title of tab
				$title = key($element);
				if (!empty($title)) {
					if ($tab_id != '0') {
						echo "<h2 class='hndle'><span>";
							echo key($element);
						echo "</span></h2>";
					}
				}
				
				echo "<div class='inside'>";
				
				foreach ($element as $item) {
					etimeclockwp_settings_render_option($item);
				}
				echo "</div>";
				
			echo "</div>";
			$page_id++;
		}
		$tab_id++;
	}
	
	echo "</div>";
}


// register settings
function etimeclockwp_register_settings () {
	register_setting( 'etimeclockwp_settings_group','etimeclockwp_settings','etimeclockwp_settings_sanatize');
}
add_action('admin_init','etimeclockwp_register_settings');


// sanatize settings
function etimeclockwp_settings_sanatize ($input) {

	$keys = array_keys($input);

	// get settings
	$settings = etimeclockwp_settings();
	
	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ($settings as $setting) {
		foreach ($setting as $element) {
			foreach ($element as $item) {
				foreach($keys as $key) {
					
					if ($item['name'] == $key) {
						$type = $item['type'];
						
						// sanatize
						if ($type == "editor" || $type == "textarea") {
							$input[$item['name']] = wp_kses_post($input[$item['name']]);
						} else {
							$input[$item['name']] = sanitize_text_field($input[$item['name']]);
						}
						
						// validate settings
						$input = etimeclockwp_settings_validate($input,$item);
						
						// save settings hook
						do_action('etimeclockwp_save_settings',$input);
						
					}
				}
			}
		}
	}	
	
	return $input;

}


// validate settings
function etimeclockwp_settings_validate($input,$item) {
	
	return $input;

}


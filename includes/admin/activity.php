<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// register meta boxs
function etimeclockwp_clock_register_meta_boxes() {
	global $post;
	
	add_meta_box('meta-box-id-activity', 		__('Day Details', 'etimeclockwp'), 	'etimeclockwp_callback_activity', 	'etimeclockwp_clock','normal');
	add_meta_box('meta-box-id-status', 			__( 'Details', 'etimeclockwp' ), 		'etimeclockwp_callback_status', 	'etimeclockwp_clock','side');
}
add_action('add_meta_boxes', 'etimeclockwp_clock_register_meta_boxes');


// activity metabox
function etimeclockwp_callback_activity($post) {
	global $meta_box, $post;
	
	// see if new post
	if ($post->post_status == 'auto-draft') {
	
		echo "<table><tr><td>";
			echo __('User: ', 'etimeclockwp');
		echo "</td><td>";
		
		$users = get_posts(
			array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'etimeclockwp_users'
			)
		);
		
		foreach($users as $post) {
			$values[$post->post_title] = $post->ID;
		}
		
		echo "<select name='user_id'>";
		
		if (isset($_GET['user'])) {
			$current = esc_html($_GET['user']);
		} else {
			$current = '';
		}
		
		foreach ($values as $label => $value) {
			echo "<option value='$value'"; if ($current == $value) { echo "SELECTED"; } echo ">$label</option>";
		}
		echo "</select>";
		echo "</td></tr><tr><td>";
		
		echo __('Date: ', 'etimeclockwp');
		echo "</td><td>";
		
		$wp_date_format = 	etimeclockwp_get_option('date-format');
		$date_now = 		date_i18n($wp_date_format);
		
		echo "<input type='text' id='etimeclockwp-new-activity' class='etimeclockwp_cell_width' value='$date_now'><input type='hidden' id='etimeclockwp-new-activity-real' name='real-date' value=''>";
		echo '<input type="hidden" name="etimeclockwp_MetaNonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
		echo '<input type="hidden" name="etimeclockwp_submit" value="1" />';
		echo "</td></tr><tr><td></td><td><input id='publish' class='button-primary' type='submit' value='"; echo __('Save','etimeclockwp'); echo "' accesskey='p' tabindex='5' name='save'>";
		echo "</td></tr></table>";
		
		
	// existing post
	} else {
		
		echo "<div class='etimeclockwp_meta_box'>";
			
			echo "<style>#post-body-content { margin-bottom: 0px; }</style>";
			
			
			// Use nonce for verification
			echo '<input type="hidden" name="etimeclockwp_MetaNonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
			
			echo '<input type="hidden" name="etimeclockwp_submit_activity" value="1" />';
			
			echo '<table>';
				
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

						if (!empty($oldtimestampdb)) $difftime = tc_german_time_diff($oldtimestampdb,$timestampdb); else $difftime='';
						
						if (isset($timestamp_array[1])) {
							$order = $timestamp_array[1];
                            echo "<tr><td class='etimeclockwp_cell_title_width' style='color:white;text-transform:uppercase;background-color: ".$keycolor."'>";
							echo $working_status.' &nbsp; '.$key;
							echo "</td><td>".date_i18n('D',$timestampdb).' '.$datetime.'</td><td>';
							if (function_exists('ago')) { echo ago($timestampdb-date('Z')); }
							echo '</td><td>'.$difftime."</td><td>&nbsp;";
							echo "</td><td><a href='#' class='etimeclockwp-entry-new' data-nonce='$nonce_add' data-timestamp='".$timestamp."' data-date='".$date."' data-time='".$time."' data-action='".$key_action."' data-pure='".$key_pure."'>".__('New','etimeclockwp')."</a> &nbsp; ";
							echo "</td><td><a href='#' class='etimeclockwp-entry-edit' data-nonce='$nonce_edit' data-timestamp='".$timestamp."' data-date='".$date."' data-time='".$time."' data-order='".$order."' data-action='".$key_action."' data-pure='".$key_pure."'>".__('Edit','etimeclockwp')."</a> &nbsp; ";
							echo "</td><td><a href='#' class='etimeclockwp-entry-delete' data-nonce='$nonce_delete' data-date='".$date."' data-time='".$time."' data-pure='".$key_pure."'>".__('Delete','etimeclockwp')."</a></td></tr>";
						} else {
							echo "<tr><td class='etimeclockwp_cell_title_width'>"; echo $key; echo ": </td><td>".$datetime."</td></tr>";
						}
						$oldtimestampdb = $timestampdb;
					}
					
				}
				
				echo "</tr>";
				
			echo '</table>';
		echo "</div>";
		
		
		
		// entry - delete
		echo "<div class='etimeclockwp-holder'>";
			echo "<div id='etimeclockwp-div-delete' class='etimeclockwp-div'>";
				
				echo "<div class='etimeclockwp-menu'>";
					echo __('Delete Entry','etimeclockwp');
				echo "</div><br />";
				
				echo "<div id='etimeclockwp-date'></div>";
				echo "<input type='hidden' id='etimeclockwp-datepure' value=''>";
				echo "<input type='hidden' class='etimeclockwp-postid' value='".$post->ID."'>";
				echo "<input type='hidden' id='etimeclockwp_delete_nonce' value=''>";
				
				echo "<div class='etimeclockwp-entry'><br />";
				echo "<input type='submit' class='button button-primary etimeclockwp-entry-button etimeclockwp-date-confirm-delete' value=' ". __('Confirm','etimeclockwp')." '>";
				echo "<input type='submit' class='button button-secondary etimeclockwp-entry-button etimeclockwp-cancel' value=' ". __('Cancel','etimeclockwp')." '>";
				echo "</div>";
				
				echo "<br />";
				
			echo "</div>";
		echo "</div>";
		
		// entry - edit
		echo "<div class='etimeclockwp-holder'>";
			echo "<div id='etimeclockwp-div-edit' class='etimeclockwp-div'>";
				
				echo "<div class='etimeclockwp-menu'>";
					echo __('Edit Entry','etimeclockwp');
				echo "</div><br />";
				
                echo "<center>";
                    echo "<select id='etimeclockwp-entry-dropdown-edit'>";
                        echo "<option value='in'>".etimeclockwp_get_option('clock-in')."</option>";
                        echo "<option value='out'>".etimeclockwp_get_option('clock-out')."</option>";
                        echo "<option value='breakon'>".etimeclockwp_get_option('leave-on-break')."</option>";
                        echo "<option value='breakoff'>".etimeclockwp_get_option('return-from-break')."</option>";
                    echo "</select><br /><br />";
                
                echo "<input type='text' id='etimeclockwp-date-edit' value=''></center>";
				echo "<input type='hidden' class='etimeclockwp-postid' value='".$post->ID."'>";
				echo "<input type='hidden' id='etimeclockwp-actualdate-edit' value=''>";
				echo "<input type='hidden' id='etimeclockwp-puredate' value=''>";
				echo "<input type='hidden' id='etimeclockwp-order' value=''>";
				echo "<input type='hidden' id='etimeclockwp_edit_nonce' value=''>";
				
				echo "<div class='etimeclockwp-entry'><br />";
				echo "<input type='submit' class='button button-primary etimeclockwp-entry-button etimeclockwp-date-confirm-edit' value=' ". __('Confirm','etimeclockwp')." '>";
				echo "<input type='submit' class='button button-secondary etimeclockwp-entry-button etimeclockwp-cancel' value=' ". __('Cancel','etimeclockwp')." '>";
				echo "</div>";
				
				echo "<br />";
				
			echo "</div>";
		echo "</div>";
		
		// entry - new
		echo "<div class='etimeclockwp-holder'>";
			echo "<div id='etimeclockwp-div-new' class='etimeclockwp-div'>";
				
				echo "<div class='etimeclockwp-menu'>";
					echo __('New Entry','etimeclockwp');
				echo "</div><br />";
				
				echo "<center>";
					echo "<select id='etimeclockwp-entry-dropdown-new'>";
						echo "<option value='in'>".etimeclockwp_get_option('clock-in')."</option>";
						echo "<option value='out'>".etimeclockwp_get_option('clock-out')."</option>";
						echo "<option value='breakon'>".etimeclockwp_get_option('leave-on-break')."</option>";
						echo "<option value='breakoff'>".etimeclockwp_get_option('return-from-break')."</option>";
                    echo "</select><br /><br />";
				
                echo "<input type='text' id='etimeclockwp-date-new' class='etimeclockwp_cell_width' value=''></center>";
				echo "<input type='hidden' class='etimeclockwp-postid' value='".$post->ID."'>";
				echo "<input type='hidden' id='etimeclockwp-actualdate-new' value=''>";
				echo "<input type='hidden' id='etimeclockwp_new_nonce' value=''>";
				
				echo "<div class='etimeclockwp-entry'><br />";
				echo "<input type='submit' class='button button-primary etimeclockwp-entry-button etimeclockwp-date-confirm-new' value=' ". __('Confirm','etimeclockwp')." '>";
				echo "<input type='submit' class='button button-secondary etimeclockwp-entry-button etimeclockwp-cancel' value=' ". __('Cancel','etimeclockwp')." '>";
				echo "</div>";
				
				echo "<br />";
				
			echo "</div>";
		echo "</div>";
		
	}
}


// status metabox
function etimeclockwp_callback_status($post) {
	global $meta_box, $post;
	
	echo "<div class='etimeclockwp_meta_box'>";
		
		echo '<table width="100%">';
		echo "<tr><td>";
		echo "<br />";
		echo __('Record #','etimeclockwp');
		echo ":";
		echo "</td><td align='right'><br />";
		echo $post->ID;
		echo "</td></tr>";

		echo "<tr><td>";
		echo "<br />";
		echo __('User','etimeclockwp');
		echo ":";
		echo "</td><td align='right'><br />";
		echo get_the_title($post->post_title);
		echo "</td></tr>";
		
		echo "</tr><tr><td><br />";
		echo __('Date Worked','etimeclockwp');
		echo ":";
		echo "</td><td align='right'><br />";
		echo get_the_date('D',$post->ID).' '.get_the_date(etimeclockwp_get_option('date-format'),$post->ID);
		if (function_exists('ago')) { echo ' '. ago(get_post_timestamp($post->ID)); }
		echo "</td></tr>";
		
		$workpause = etimeclockwp_calculate_workpausetotal($post->ID);

		echo '</tr><tr><td style="color:#fff;background-color:'.etimeclockwp_get_option('clock-in-button-color').'"><br />';
		echo __('Total Time Worked','etimeclockwp');
		echo ":";
		if ($workpause[2] >=36000) $overtime='color:#fff;background-color:tomato'; else $overtime='';
		echo '</td><td style="'.$overtime.'"align="right"><br />';
		echo $workpause[5];
		echo "</td></tr>";

		echo '</tr><tr><td style="color:#fff;background-color:'.etimeclockwp_get_option('leave-on-break-button-color').'"><br />';
		echo __('total time breaks','etimeclockwp');
		echo ":";
		echo "</td><td align='right'><br />";
		echo $workpause[6];
		echo "</td></tr>";


		echo "</tr><tr><td><br />";
		echo __('total time with breaks','etimeclockwp');
		echo ":";
		echo "</td><td align='right'><br />";
		echo etimeclockwp_get_time_worked($post);
		echo "</td></tr>";
		
		echo "</td></tr></table><br />";
		
	echo "</div>";
}


// user table - fill table columns with data
function etimeclockwp_manage_activity_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {
		
		case 'name' :
			echo $id = "<a href='post.php?post=$post_id&action=edit'><b>"; echo get_the_title($post->post_title); echo "</b></a>";
		break;
		
		case 'date_work' :
			echo get_the_date('D',$post->ID).' '.get_the_date(etimeclockwp_get_option('date-format'),$post_id);
			if (function_exists('ago')) { echo ' '. ago(get_post_timestamp($post->ID)); }
		break;

		case 'postID' :
			$etimeclockwp_postid = $post_id;
			echo $etimeclockwp_postid;
		break;
		
		case 'time_worked' :
			$workpause = etimeclockwp_calculate_workpausetotal($post_id);
			if ($workpause[2] >=36000) $overtime='color:#fff;background-color:tomato'; else $overtime='';
			echo '<span style="'.$overtime.'">'.$workpause[5].'</span>';
		break;

		case 'time_paused' :
			echo etimeclockwp_calculate_workpausetotal($post_id)[6];
		break;

		case 'time_with_pause' :
			echo etimeclockwp_get_time_worked($post);
		break;
		
		
		default :
			break;
	}
}
add_action( 'manage_etimeclockwp_clock_posts_custom_column', 'etimeclockwp_manage_activity_columns', 10, 2 );


// titles for admin button table
function etimeclockwp_clock_columns($columns) {

	$columns = array(
		'cb' => 			'<input type="checkbox" />',
		'name' => 			__( 'User Name','etimeclockwp'),
		'postID' => 		__( 'Record #','etimeclockwp'),
		'date_work' => 		__( 'Work Date','etimeclockwp'),
		'time_worked' => 	__( 'Total Time Worked','etimeclockwp'),
		'time_paused' => 	__( 'total time breaks','etimeclockwp'),
		'time_with_pause' => 	__( 'total time with breaks','etimeclockwp'),
	);

	return $columns;
}
add_filter('manage_edit-etimeclockwp_clock_columns','etimeclockwp_clock_columns');


// set default column for orders & customers table
function etimeclockwp_clock_list_table_primary_column( $column, $screen ) {
	
	if ('edit-etimeclockwp_clock' === $screen) {
        $column = 'name';
    }
    return $column;
}
add_filter( 'list_table_primary_column', 'etimeclockwp_clock_list_table_primary_column', 10, 2 );


// users table - remove quick links
function etimeclockwp_activity_quick_links($actions, $post) {
	
	if ($post->post_type =="etimeclockwp_clock") {
		unset($actions['inline hide-if-no-js']);
		unset($actions['edit']);
    }
    return $actions;
}
add_filter('post_row_actions','etimeclockwp_activity_quick_links', 10, 2);


// users table - update message
add_filter('post_updated_messages', 'codex_etimeclockwp_clock_updated_messages');
function codex_etimeclockwp_clock_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['etimeclockwp_clock'] = array(
		1 => __('User Activity updated.'),
	);

	return $messages;
}


// users table - remove subsubsub menu links
function etimeclockwp_clock_table_sub_menu_links( $views ) {

	unset($views['private']);
	unset($views['mine']);
	unset($views['publish']);

    return $views;
}
add_filter('views_edit-etimeclockwp_clock','etimeclockwp_clock_table_sub_menu_links');











// display user dropdown menu
function etimeclockwp_admin_posts_filter_activity(){
	
	// get current post type
	if (isset($_GET['post_type'])) {
		$type = $_GET['post_type'];
	} else {
		$type = '';
	}

	//only add filter to post type you want
	if ('etimeclockwp_clock' == $type) {
		
		$users = get_posts(
			array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'etimeclockwp_users'
			)
		);
		
		foreach($users as $post) {
			$values[$post->post_title] = $post->ID;
		}
		?>
		<select name="user">
		<option value=""><?php echo __('All users', 'etimeclockwp'); ?></option>
		<?php
			if (isset($_GET['user'])) {
				$current = esc_html($_GET['user']);
			} else {
				$current = '';
			}
			
	        if (isset($values)) {
                foreach ($values as $label => $value) {
                    echo "<option value='$value'"; if ($current == $value) { echo "SELECTED"; } echo ">$label</option>";
                }
            }
		?>
		</select>
		<?php
	}
}
add_action( 'restrict_manage_posts', 'etimeclockwp_admin_posts_filter_activity' );


// add filter to query for dropdown menu
function etimeclockwp_posts_filter_activity( $query ) {
    global $pagenow;
	
	// get current post type
	if (isset($_GET['post_type'])) {
		$type = sanitize_text_field($_GET['post_type']);
	} else {
		$type = '';
	}
	
	if ( 'etimeclockwp_clock' == $type && is_admin() && $pagenow =='edit.php' && isset($_GET['user']) && $_GET['user'] != '' && $query->is_main_query() ) {
		$query->query_vars['title'] = sanitize_text_field($_GET['user']);
	}

}
add_filter( 'parse_query', 'etimeclockwp_posts_filter_activity' );
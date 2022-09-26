<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// register meta box
function etimeclockwp_users_register_meta_boxes() {
	add_meta_box( 'meta-box-id-general', __( 'User Information', 'etimeclockwp' ), 'etimeclockwp_callback_general', 'etimeclockwp_users','normal');
	add_meta_box('meta-box-id-order', __( 'Save', 'etimeclockwp' ), 'etimeclockwp_callback_save', 'etimeclockwp_users','side');
}
add_action( 'add_meta_boxes', 'etimeclockwp_users_register_meta_boxes' );


// callback general
function etimeclockwp_callback_general($post) {
	global $meta_box, $post;
	
	echo "<div class='etimeclockwp_meta_box'>";
		
		echo "<style>#post-body-content { margin-bottom: 0px; }</style>";
		
		// Use nonce for verification
		echo '<input type="hidden" name="etimeclockwp_MetaNonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
		
		echo '<input type="hidden" name="etimeclockwp_submit" value="1" />';
		
		echo '<table class="form-table"><tr>';
		
		$etimeclockwp_name = sanitize_text_field(get_post_meta($post->ID,'etimeclockwp_name', true));
		echo "<td class='etimeclockwp_cell_width_product'>"; echo __('Full Name','etimeclockwp'); echo ": </td><td><input size='40' type='text' name='etimeclockwp_name' value='$etimeclockwp_name'> ("; echo __( 'Required' ); echo ")</td>";
		
		echo "</tr><tr>";
		
		$etimeclockwp_id = sanitize_text_field(get_post_meta($post->ID,'etimeclockwp_id', true));
		echo "<td class='etimeclockwp_cell_width_product'>"; echo __('User ID','etimeclockwp'); echo ": </td><td><input size='40' type='text' name='etimeclockwp_id' value='$etimeclockwp_id'> ("; echo __( 'Required. The User ID should not contains spaces. Example: JSmith87', 'etimeclockwp' ); echo ")</td>";
		
		echo "</tr><tr>";
		
		$etimeclockwp_pwd = sanitize_text_field(get_post_meta($post->ID,'etimeclockwp_pwd', true));
		echo "<td class='etimeclockwp_cell_width_product'>"; echo __('Password','etimeclockwp'); echo ": </td><td><input size='40' type='text' name='etimeclockwp_pwd' value='$etimeclockwp_pwd'> ("; echo __( 'Required', 'etimeclockwp' ); echo ")</td>";
		
		echo '</tr></table>';
		
	echo "</div>";
}


// save metabox
function etimeclockwp_callback_save($post) {
	global $meta_box, $post;
	
	$etimeclockwp_status = $post->post_status;
	
	// Use nonce for verification
	echo '<input type="hidden" name="etimeclockwp_MetaNonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	
	echo '<input type="hidden" name="etimeclockwp_submit" value="1" />';
	
	echo '<table><tr>';
	
	echo "</td><td align='right'><input id='publish' class='button-primary' type='submit' value='"; echo __('Save','etimeclockwp'); echo "' accesskey='p' tabindex='5' name='save'></td></tr><tr>";
	
	echo '</tr></table>';
}


// save
function etimeclockwp_save_meta_box_button($post_id) {	
	
	if (isset($_POST['etimeclockwp_submit']) && $_POST['etimeclockwp_submit'] == "1") {
		
		// verify nonce
		if (!wp_verify_nonce($_POST['etimeclockwp_MetaNonce'], basename(__FILE__))) {
			return $post_id;
		}
		
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		
		// update values
		
		// name
		update_post_meta($post_id,'etimeclockwp_name',sanitize_text_field($_POST['etimeclockwp_name']));
		
		// user id
		update_post_meta($post_id,'etimeclockwp_id',sanitize_text_field($_POST['etimeclockwp_id']));
		
		// password
		update_post_meta($post_id,'etimeclockwp_pwd',sanitize_text_field($_POST['etimeclockwp_pwd']));
		
		// wordpress account
		update_post_meta($post_id,'etimeclockwp_wp_account',sanitize_text_field($_POST['etimeclockwp_wp_account']));
		
		
		// to avoid infinite loop
		remove_action('save_post','etimeclockwp_save_meta_box_button');
		
		
		$action_array = array(
			'ID' 			=> $post_id,
			'post_status'	=> 'publish',
		);
		
		$result = wp_update_post($action_array);
		
		// save the meta id key as a user meta value, this makes seeing if the user has a time clock account more more efficient
		$user = get_user_by('slug',sanitize_text_field($_POST['etimeclockwp_wp_account']));
		update_user_meta($user->ID, 'etimeclockwp_meta_id' , $result);
		
	}	
}
add_action( 'save_post', 'etimeclockwp_save_meta_box_button' );


// title
function etimeclockwp_modify_title_question( $data , $postarr ) {
	
	if ($data['post_type'] == 'etimeclockwp_users') {
		if (isset($_POST['etimeclockwp_name'])) {
			$data['post_title'] = sanitize_text_field($_POST['etimeclockwp_name']);
		}
	}
	return $data;
}
add_filter('wp_insert_post_data','etimeclockwp_modify_title_question','99',2);




// user table - fill table columns with data
function etimeclockwp_manage_button_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {
		
		case 'user_id' :
			$etimeclockwp_id = sanitize_text_field(get_post_meta($post->ID,'etimeclockwp_id', true));
			echo $etimeclockwp_id;
		break;
		
		default :
			break;
	}
}
add_action( 'manage_etimeclockwp_users_posts_custom_column', 'etimeclockwp_manage_button_columns', 10, 2 );


// titles for admin button table
function etimeclockwp_users_columns($columns) {

	$columns = array(
		'cb' => 			'<input type="checkbox" />',
		'title' => 			__( 'User Name','etimeclockwp'),
		'user_id' => 		__( 'User ID','etimeclockwp'),
	);

	return $columns;
}
add_filter('manage_edit-etimeclockwp_users_columns','etimeclockwp_users_columns');


// users table - remove quick links
function etimeclockwp_quick_links($actions, $post) {
	
	if ($post->post_type =="etimeclockwp_users") {
		unset($actions['inline hide-if-no-js']);
		unset($actions['edit']);
		unset($actions['trash']);
		unset($actions['view']);
    }
    return $actions;
}
add_filter('post_row_actions','etimeclockwp_quick_links', 10, 2);


// users table - update message
add_filter('post_updated_messages', 'codex_etimeclockwp_users_updated_messages');
function codex_etimeclockwp_users_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['etimeclockwp_users'] = array(
		1 => __('User Information updated.'),
	);

	return $messages;
}


// users table - remove subsubsub menu links
function etimeclockwp_users_table_sub_menu_links( $views ) {

	unset($views['private']);
	unset($views['mine']);
	unset($views['publish']);

    return $views;
}
add_filter('views_edit-etimeclockwp_users','etimeclockwp_users_table_sub_menu_links');
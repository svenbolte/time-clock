<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// dashboard item - getting started
function etimeclockwp_dashboard_load_getting($etimeclockwp_dashboard_array) {

	$getting_started_array = array(
		'getting started' => array(
			'title'  				=> __( 'Getting Started Guide', 'etimeclockwp' ),
			'body'  				=> __( "
			
1. On any page or post, you can place the shortcode [timeclock]. This will display a timeclock.  <br /> <br  /> Note: You should only have one timeclock shortcode per page or post. <br /><br />

2. Create a new user on the <a target='_blank' href='edit.php?post_type=etimeclockwp_users'> Users page</a>. <br /><br />

3. When the user enters their username and password (which is setup on the Users page) on the timeclock, there time will be recorded. <br /><br />

4. You can view the users activity on the <a target='_blank' href='edit.php?post_type=etimeclockwp_clock'>Activity page</a>. <br /><br />

5. You may wish to change the datetime format displayed in the timeclock and on the Activity page. You can do this on the General Tab -> <a href='admin.php?page=etimeclockwp_settings_page&tab=13'>Date & Time Format section</a> <br /><br />

			", 'etimeclockwp' ),
		),
	);

	return array_merge($etimeclockwp_dashboard_array,$getting_started_array);
}
add_filter( 'etimeclockwp_dashboard_array','etimeclockwp_dashboard_load_getting');

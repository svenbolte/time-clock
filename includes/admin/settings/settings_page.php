<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function etimeclockwp_settings_page() {

	global 	$etimeclockwp_options, $etimeclockwp_active_tab;

	if ( !current_user_can( "manage_options" ) )  {
		wp_die( __( "You do not have sufficient permissions to access this page. Please sign in as an administrator.",'etimeclockwp' ));
	}

	?>
	
	<div class="etimeclockwp-wrapper">
		<br />
		<form method='POST' action='options.php'>
			
			<?php settings_fields('etimeclockwp_settings_group'); ?>
					
					<?php etimeclockwp_settings_render_menu(); ?>
					
			<?php etimeclockwp_settings_render(); ?>
			
			<input type="hidden" name="etimeclockwp_settings[tab]" id="tab" value="<?php echo $etimeclockwp_active_tab; ?>">			
			
		</form>
	</div>
	
	<script>
		jQuery("input[name=_wp_http_referer]").val('admin.php?page=etimeclockwp_settings_page');
	</script>
	<?php

}
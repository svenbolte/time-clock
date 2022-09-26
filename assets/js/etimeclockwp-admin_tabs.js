jQuery(document).ready(function() {

	// top level
	jQuery('#etimeclockwp-tabs li a').click(function(){
		var t = jQuery(this).attr('id');
		
		jQuery('#tab').val(t);
		
			jQuery('.nav-tab').removeClass('nav-tab-active');
			jQuery(this).addClass('nav-tab-active');
			
			jQuery('.etimeclockwp-container').hide();
			jQuery('#'+ t + 'C').show();
			
			jQuery('.etimeclockwp-more').hide();
			
			t = t.slice(0, -1);
			jQuery('.etimeclockwp-more-'+ t).show();
			jQuery('.tab-more').removeClass('current');
			jQuery('.'+ t + '0T').addClass('current');
			
	});
	
	// 2nd level
	jQuery('#etimeclockwp-tabs-more li a').click(function(){
		var t = jQuery(this).attr('id');
		jQuery('#tab').val(t);
		
			jQuery('.tab-more').removeClass('current');
			jQuery(this).addClass('current');
			
			jQuery('.etimeclockwp-container').hide();
			jQuery('#'+ t + 'C').show();
			
	});
	
});
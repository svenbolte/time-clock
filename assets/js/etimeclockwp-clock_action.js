jQuery(document).ready(function($) {

	// clock actions
	jQuery(".etimeclock-action").click(function(e) { 
		e.preventDefault();
		
		var data = {
			'action':	'etimeclockwp_timeclock_action', 			// php function
			'nonce':	jQuery('#etimeclock_nonce').val(), 			// form nonce
			'data':		jQuery(this).data("id"), 					// button action
			'eid':		jQuery('#etimeclock-eid').val(), 			// employee id
			'epw':		jQuery('#etimeclock-epw').val(),			// employee password
			'hash':		jQuery('#etimeclock-hash').val(),			// password hash
			'mandate':		jQuery('#manualdate').val(),			// date set manually
			'mantime':		jQuery('#manualtime').val(),			// time set manually
		};
		
		jQuery.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: ajax_object_clock_action.ajax_url,
			xhrFields: {
				withCredentials: true
			},
			success: function (result) {

				var uscookie = jQuery('#etimeclock-eid').val();
				document.cookie = "etime_usercookie=" + uscookie;
		
				if (result.color == 'red') {
					jQuery('#etimeclock-status').css('color', 'tomato');
					jQuery('#etimeclock-status').html(result.message);
				} else {
					jQuery('#etimeclock-status').html(result.message);
					// refresh
					window.setTimeout( result, 6000 ); // 6 seconds
					location.reload(); 
				}
				
			}
		});
		
	});
	
});
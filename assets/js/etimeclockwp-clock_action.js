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
				
				if (result.color == 'red') {
					alert(result.message);
				} else {
					alert(result.message);
					// refresh
					location.reload(); 
				}
				
			}
		});
		
	});
	
});
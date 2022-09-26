jQuery(document).ready(function($) {

	
	// Tooltips
	jQuery('.etimeclockwp-help-tip').tooltip( {
		content: function() {
			return jQuery(this).prop('title');
		},
		tooltipClass: 'etimeclockwp-ui-tooltip',
		position: {
			my: 'center top',
			at: 'center bottom+10',
			collision: 'flipfit',
		},
		hide: {
			duration: 200,
		},
		show: {
			duration: 200,
		},
	});
	
	
	// colorpicker
	jQuery(function () {
		jQuery('.etimeclockwp_colorpicker').wpColorPicker();
	});
	
	
	// Close entry box - used for all
	jQuery('.etimeclockwp-cancel').on('click', function(e) {
		e.preventDefault();
		jQuery(".etimeclockwp-div").css('display','none');
	});
	
	
	// add new activity datepicker
	jQuery('#etimeclockwp-new-activity').datepicker({
			dateFormat: 	etimeclockwp_admin_ajax_object.dateformat,
			controlType: 	'select',
			oneLine: 		true,
			altField: 		'#etimeclockwp-new-activity-real',
			altFormat: 		'yy-mm-dd',
			altFieldTimeOnly: false,
	});
	
	
	// Open entry box delete
	jQuery('.etimeclockwp-entry-delete').on('click', function(e) {
		
		jQuery(".etimeclockwp-div").css('display','none');
		jQuery("#etimeclockwp-div-delete").css('display','block');
		
		var date = 		jQuery(this).data('date');
		var time = 		jQuery(this).data('time');
		var datepure = 	jQuery(this).data('pure');
		var nonce = 	jQuery(this).data('nonce');
		
		jQuery("#etimeclockwp-date").html(date+' '+time);
		jQuery("#etimeclockwp-datepure").val(datepure);
		jQuery("#etimeclockwp_delete_nonce").val(nonce);
		
	});
	
	
	// Process entry box delete
	jQuery('.etimeclockwp-date-confirm-delete').on('click', function(e) {
		e.preventDefault();
		
		jQuery('.etimeclockwp-date-confirm-delete').prop("disabled",true);
		
		var data = {
			'action':	'etimeclockwp_date_delete',
			'postid':	jQuery('.etimeclockwp-postid').val(),
			'datepure':	jQuery('#etimeclockwp-datepure').val(),
			'nonce':	jQuery('#etimeclockwp_delete_nonce').val(),
		};
		
		jQuery.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: etimeclockwp_admin_ajax_object.ajax_url,
			xhrFields: {
				withCredentials: true
			},
			success: function (result) {
				
				location.reload();
				
			}
		})
	});
	
	
	// Open entry box edit
	jQuery('.etimeclockwp-entry-edit').on('click', function(e) {
		
		jQuery(".etimeclockwp-div").css('display','none');
		jQuery("#etimeclockwp-div-edit").css('display','block');
		
		var date = 		jQuery(this).data('date');
		var time = 		jQuery(this).data('time');
		var datepure = 	jQuery(this).data('pure');
		var timestamp = jQuery(this).data('timestamp');
		var order = 	jQuery(this).data('order');
		var nonce = 	jQuery(this).data('nonce');
		var action =     jQuery(this).data('action');
		
		jQuery("#etimeclockwp-date-edit").val(date+' '+time);
		jQuery("#etimeclockwp-actualdate-edit").val(timestamp);
		jQuery("#etimeclockwp-puredate").val(datepure);
		jQuery("#etimeclockwp-order").val(order);
		jQuery("#etimeclockwp_edit_nonce").val(nonce);
		
		if(action == 'etimeclockwp-in') {
            jQuery('#etimeclockwp-entry-dropdown-edit>option:eq(0)').prop('selected', true);
        }
        if(action == 'etimeclockwp-out') {
            jQuery('#etimeclockwp-entry-dropdown-edit>option:eq(1)').prop('selected', true);
        }
        if(action == 'etimeclockwp-breakon') {
            jQuery('#etimeclockwp-entry-dropdown-edit>option:eq(2)').prop('selected', true);
        }
        if(action == 'etimeclockwp-breakoff') {
            jQuery('#etimeclockwp-entry-dropdown-edit>option:eq(3)').prop('selected', true);
        }
        
		jQuery('#etimeclockwp-date-edit').datetimepicker({
			dateFormat: 	etimeclockwp_admin_ajax_object.dateformat,
			timeFormat: 	etimeclockwp_admin_ajax_object.timeformat,
			controlType: 	'select',
			oneLine: 		true,
			altField: 		'#etimeclockwp-actualdate-edit',
			altFormat: 		'yy-mm-dd',
			altFieldTimeOnly: false,
			altTimeFormat: 	'H:mm:00',
			showSecond: 	false
		});
		
	});
	
	
	
	// Process entry box edit
	jQuery('.etimeclockwp-date-confirm-edit').on('click', function(e) {
		e.preventDefault();
		
		jQuery('.etimeclockwp-date-confirm-edit').prop("disabled",true);
		
		var data = {
			'action':	'etimeclockwp_date_edit',
			'postid':	jQuery('.etimeclockwp-postid').val(),
			'puredate':	jQuery('#etimeclockwp-puredate').val(),
			'timestamp':jQuery('#etimeclockwp-actualdate-edit').val(),
			'order':	jQuery('#etimeclockwp-order').val(),
			'nonce':	jQuery('#etimeclockwp_edit_nonce').val(),
			'type':		jQuery('#etimeclockwp-entry-dropdown-edit').val(),
		};
		
		jQuery.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: etimeclockwp_admin_ajax_object.ajax_url,
			xhrFields: {
				withCredentials: true
			},
			success: function (result) {
				
				location.reload();
				
			}
		})
	});
	
	
	// Open entry box new
	jQuery('.etimeclockwp-entry-new').on('click', function(e) {
		
		jQuery(".etimeclockwp-div").css('display','none');
		jQuery("#etimeclockwp-div-new").css('display','block');
		
		var date = 		jQuery(this).data('date');
		var time = 		jQuery(this).data('time');
		var timestamp = jQuery(this).data('timestamp');
		var nonce = 	jQuery(this).data('nonce');
		
		jQuery("#etimeclockwp-actualdate-new").val(timestamp);
		jQuery("#etimeclockwp-date-new").val(date+' '+time);
		jQuery("#etimeclockwp_new_nonce").val(nonce);
		
		jQuery('#etimeclockwp-date-new').datetimepicker({
			dateFormat: 	etimeclockwp_admin_ajax_object.dateformat,
			timeFormat: 	etimeclockwp_admin_ajax_object.timeformat,
			controlType: 	'select',
			oneLine: 		true,
			altField: 		'#etimeclockwp-actualdate-new',
			altFormat: 		'yy-mm-dd',
			altFieldTimeOnly: false,
			altTimeFormat: 	'H:mm:00',
			showSecond: 	false
		});
		
	});
	
	
	
	
	// Process entry box new
	jQuery('.etimeclockwp-date-confirm-new').on('click', function(e) {
		e.preventDefault();
		
		jQuery('.etimeclockwp-date-confirm-new').prop("disabled",true);
		
		var data = {
			'action':	'etimeclockwp_date_new',
			'postid':	jQuery('.etimeclockwp-postid').val(),
			'timestamp':jQuery('#etimeclockwp-actualdate-new').val(),
			'type':		jQuery('#etimeclockwp-entry-dropdown-new').val(),
			'nonce':	jQuery('#etimeclockwp_new_nonce').val(),
		};
		
		jQuery.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: etimeclockwp_admin_ajax_object.ajax_url,
			xhrFields: {
				withCredentials: true
			},
			success: function (result) {
				
				location.reload();
				
			}
		})
	});
	
	
});



// Deactive survey form
(function($) {
	$(function() {

	var pluginSlug = 'time-clock';

	$(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .deactivate', function(e) {
		e.preventDefault();
		$('.etimeclockwp-popup-overlay').addClass('etimeclockwp-active');
		$('body').addClass('etimeclockwp-hidden');
	});
	
	$(document).on('click', '.etimeclockwp-popup-button-close', function () {
		close_popup();
	});
	
	$(document).on('click', ".etimeclockwp-serveypanel,tr[data-slug='" + pluginSlug + "'] .deactivate",function(e) {
		e.stopPropagation();
	});

	$(document).click(function() {
		close_popup();
	});
	
	$('.etimeclockwp-reason label').on('click', function() {
		if($(this).find('input[type="radio"]').is(':checked')) {
			$(this).next().next('.etimeclockwp-reason-input').show().end().end().parent().siblings().find('.etimeclockwp-reason-input').hide();
		}
	});
	
	$('input[type="radio"][name="etimeclockwp-selected-reason"]').on('click', function(event) {
		$(".etimeclockwp-popup-allow-deactivate").removeAttr('disabled');
		$('.etimeclockwp_input_field_error').removeClass('etimeclockwp_input_error');
	});
	
	$(document).on('submit', '#etimeclockwp-deactivate-form', function(event) {
		event.preventDefault();
		
		var _reason =  $(this).find('input[type="radio"][name="etimeclockwp-selected-reason"]:checked').val();
		var _reason_details = '';
		
		if ( _reason == 2 ) {
			_reason_details = $(this).find("textarea[name='better_plugin']").val();
		} else if ( _reason == 7 ) {
			_reason_details = $(this).find("textarea[name='other_reason']").val();
		} else if ( _reason == 1 ) {
			_reason_details = $(this).find("textarea[name='feature']").val();
		}
		
		if ( ( _reason == 7 || _reason == 2 || _reason == 1 ) && _reason_details == '' ) {
			$('.etimeclockwp_input_field_error').addClass('etimeclockwp_input_error');
			return ;
		}
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action        : 'etimeclockwp_deactivate_survey',
				reason        : _reason,
				reason_detail : _reason_details,
			},
			beforeSend: function(){
				$(".etimeclockwp-spinner").show();
				$(".etimeclockwp-popup-allow-deactivate").attr("disabled", "disabled");
			}
		})
		.done(function() {
			$(".etimeclockwp-spinner").hide();
			$(".etimeclockwp-popup-allow-deactivate").removeAttr("disabled");
			window.location.href =  $("tr[data-slug='"+ pluginSlug +"'] .deactivate a").attr('href');
		});
	});

	$('.loginpress-popup-skip-feedback').on('click', function(e) {
		window.location.href =  $("tr[data-slug='"+ pluginSlug +"'] .deactivate a").attr('href');
	})

	function close_popup() {
		$('.etimeclockwp-popup-overlay').removeClass('etimeclockwp-active');
		$('#etimeclockwp-deactivate-form').trigger("reset");
		$(".etimeclockwp-popup-allow-deactivate").attr('disabled', 'disabled');
		$(".etimeclockwp-reason-input").hide();
		$('body').removeClass('etimeclockwp-hidden');
		$('.etimeclockwp_input_field_error').removeClass('etimeclockwp_input_error');
	}
	});
})(jQuery);
jQuery(document).ready(function() {

	// set date and time on clock in / out shortcode
    var interval = setInterval(function() {
        var momentNow = moment();
		
        jQuery('.etimeclock-date').html(momentNow.formatPHP(ajax_object_date_time.date_format));
        jQuery('.etimeclock-time').html(momentNow.formatPHP(ajax_object_date_time.time_format));
    }, 100);
	
	
	// refresh page every 12 hours
	setInterval(function() {
		window.location.reload();
	}, 43200000);
	
});
$(document).ready(function() {
	// dequeue any non-functional, view only calendar forms on the page.
	console.log('enqueued \"dequeue-calendar-form.js\" via CC_Static_Calendar_Form');

	$('.cc-static-calendar-form').find('.wc_bookings_field_resource').remove();
	$('.cc-static-calendar-form').off();
});
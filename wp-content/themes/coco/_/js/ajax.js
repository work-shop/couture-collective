$(document).ready( function() {
	$('.dress-rentals').find('button').on('submit', function(e){e.preventDefault(); return false;});
	$('.dress-rentals').find('button').on('click', function(e){e.preventDefault(); return false;});

	function locate_bookable_units( d ) {
		var postdata = collect_booking_data( $(this) ),
		    response_target = $('#ajax-response');
		

		if ( postdata && valid( postdata ) ) {
			$.ajax({
				url: 		ajax.URL,
				type:		'POST',
				data: 	{
					action: 	'change_booking',
					data: 	postdata
				},
				success:    function( res ) {
					if ( res.success ) {
						if ( res.data.error ) {
							console.log('logical error: ' + res.data.message );
							console.log( res.data );
						} else {


							console.log( res.data );

						}
					} else {
						console.log( 'service error' );
						console.log( res );
					}
				}
			});
		}
	}

	/**
	 * This function collects and serialized the booking data so that it can be parsed by the server mechanism
	 * we need: 
	 *	requested user for action: 'user' // serverside check to see if it matches the current session uID
	 *	
	 */
	function collect_booking_data( c ) {
		if ( !c.attr('disabled') ) {
			context = c.prevAll('.wc-bookings-booking-form').find('.wc-bookings-date-picker-date-fields');
			booking = c.prevAll('.booking-requested');
			order = c.prevAll('.order-requested');

			console.log( order );
			console.log( booking );

			return {
				'user-id': user.ID,
				'booking-id': booking.attr('value'),
				'day': context.find('.booking_date_day').val(),
				'month': context.find('.booking_date_month').val(),
				'year': context.find('.booking_date_year').val()
			};
		} else {
			return false;
		}
	}

	function valid( data ) { // fix this
		return true;
	}



	function cancel_reservation() {
		var booking_ID = $(this).prev('.booking-requested').attr('value'),
		    response_target = $('#ajax-response');

		    console.log(booking_ID);

		$.ajax({
			url: ajax.URL,
			type:	'POST',
			data: {
				action: 'cancel_booking',
				data: {
					"user-id": user.ID,
					"booking-id": booking_ID
				}
			},
			success: function( res ) {
				if ( res.success ) {
					if ( res.data.error ) {
						console.log('logical error: ' + res.data.message );
						console.log( res.data );
					} else {

						console.log( res.data );

					}
				} else {
					console.log( 'service error' );
					console.log( res );
				}
			}
		});
	}




	$('.update-reservation').on('click', locate_bookable_units);
	$('.cancel-reservation').on('click', cancel_reservation);
});


<?php

if ( get_current_user_id() == ($id = $data['user-id']) ) {
	echo "matched session";
}


	// if ( get_current_user_id() == ($id = $data['user-id']) ) {
		// 	$val = find_requested_booking( WC_Bookings_Controller::get_bookings_for_user( $id ), $data['booking-id'] );

		// 	if ( $val ) {
		// 			$val->update_status('cancelled');

		// 			respond( array(
		// 				'error' => false,
		// 				'message' => 'cancelled the requested booking',
		// 				'cancelled_booking' => $val
		// 			) );
		// 	} else {
		// 		respond( array( 'error' => true, 'message' => 'requested booking does not belong to requested user' ));
		// 	}
		// } else {
		// 	respond( array( 'error' => true, 'message' => 'unmatching session and request tokens' ));
		// }

		// die();


?>

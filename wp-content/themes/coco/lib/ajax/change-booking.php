<?php
	// !! must check that the requested user is the user who owns the bookings BEFORE taking any action.
	function convert_time( $year, $month, $day ) {
		return strtotime( $year.'/'.$month.'/'.$day );
	}

	function find_requested_booking( $bookings, $target_id ) {
		foreach ($bookings as $i => $booking) {
			if ( $booking->get_ID() == $target_id ) {
				return $booking;
			} 
		}

		return false;
	}


	function cancel_booking() {
		$data = $_POST['data'];

		if ( get_current_user_id() == ($id = $data['user-id']) ) {
			$val = find_requested_booking( WC_Bookings_Controller::get_bookings_for_user( $id ), $data['booking-id'] );

			if ( $val ) {
					$val->update_status('cancelled');

					respond( array(
						'error' => false,
						'message' => 'cancelled the requested booking',
						'cancelled_booking' => $val
					) );
			} else {
				respond( array( 'error' => true, 'message' => 'requested booking does not belong to requested user' ));
			}
		} else {
			respond( array( 'error' => true, 'message' => 'unmatching session and request tokens' ));
		}

		die();
	}
	add_action('wp_ajax_cancel_booking', 'cancel_booking' );
	add_action('wp_ajax_nopriv_cancel_booking', 'cancel_booking' );

	function change_booking() {
		$data = $_POST['data'];

		if ( get_current_user_id() == ($id = $data['user-id']) ) {
			$val = find_requested_booking( WC_Bookings_Controller::get_bookings_for_user( $id ), $data['booking-id'] );

			if ( $val ) {
				$args = array(
			             'start_date'  	=> convert_time( $data['year'], $data['month'], $data['day'] ), // unix timestamp
			             'end_date'    	=> strtotime( '+12 hours', convert_time( $data['year'], $data['month'], $data['day'] ) ), // unix timestamp
			             'status' 	   	=> $val->status,
			             'user_id' 		=> $val->customer_id,
			             'order_item_id' 	=> get_post_meta( $val->get_id(), '_booking_order_item_id'),
			             'all_day' 		=> true
				);
				$bk = true;
			      	 //$bk =  create_wc_booking( $val->get_product_id(), $args, $val->status, true );
				$val->start_date = $args['start_date'];
				$val->end_date = $args['end_date'];
				$val->create( $val->status );

				if ( $bk ) {

					//$val->update_status( 'cancelled' );
					respond( array(
						'new_booking' => $bk,
						'order_item_id' =>  $args['order_item_id']
					) );

				} else {
					respond( array(
						'error' => true,
						'message' => 'Could not create a update the booking!',
					));
				}

			} else {

				respond( array(
					'error' => true,
					'message' => 'requested booking does not belong to requested user',
				));

			}
		} else {

			respond( array(
				'error' => true,
				'message' => 'unmatching session and request tokens'
			));

		}

		die();
	}

	add_action('wp_ajax_change_booking', 'change_booking' );
	add_action('wp_ajax_nopriv_change_booking', 'change_booking' );

?>
<?
/*
	Template Name: Make-Reservation
*/
?>
<?php
// if the required post-variables are not set, redirect to a 304-page.
var_dump( $_POST );
// if ( isset( $_POST['referring-page']) && isset($_POST['user-id']) && isset($_POST['booking-id']) ) {
// 	if ( get_current_user_id() == ( $id = $_POST['user-id'] ) ) {
// 		$val = find_requested_booking( WC_Bookings_Controller::get_bookings_for_user( $id ), $_POST['booking-id'] );

// 		if ( $val ) {
// 			$or = $val->get_order();
// 			$items = $or->get_items();

// 			if ( 1 == count( $items ) ) {
// 				$or_succ = $or->update_status('cancelled');
// 			} else {
// 				$or_succ = true;
// 			}

// 			$bk_succ = $val->update_status('cancelled');

// 			if ( $or_succ && $bk_succ ) {
// 				wc_add_notice( 'your booking was successfully cancelled.','success' );
// 				wp_redirect( $_POST['referring-page']);
// 				exit;
// 			} else {
// 				wc_add_notice( 'we couldn\'t cancel your booking!','notice' );
// 				wp_redirect( $_POST['referring-page'] );
// 				exit;
// 			}
// 		} else {
// 			// internal cancellation error.
// 			wc_add_notice( 'we couldn\'t cancel your booking!','notice' );
// 			wp_redirect( $_POST['referring-page'] );
// 			exit;
// 		}
// 	} else {
// 		// permission denied -- user session does not match authenticated user
// 		// add and check a nonce, too.
// 		wp_redirect( home_url() );
// 		exit;
// 	}
// } else {
// 	wp_redirect( home_url() );
// 	exit;
// }

?>

<?
/*
	Template Name: Cancel-Reservation
*/
?>
<?php
// if the required post-variables are not set, redirect to a 304-page.

if ( isset( $_POST['referring-page']) && isset($_POST['user-id']) && isset($_POST['booking-id']) ) {
	$user = wp_get_current_user();

	if ( $user->ID == ( $id = $_POST['user-id'] ) ) {
		$val = CC_Controller::validate_requested_booking( WC_Bookings_Controller::get_bookings_for_user( $id ), $_POST['booking-id'] );

		if ( $val ) {
			$or = $val->get_order();

			if ( $or->get_user_id() != $user->ID ) {
				wc_add_notice( '(ERR-3) The requesting user doesn\'t match the order owner','notice' );
				wp_redirect( $_POST['referring-page'] );
				exit;
			}


			$items = $or->get_items();
			$item = CC_Controller::get_order_item_for_booking( $val, $or );

			$refund = wc_create_refund( array(
				'amount' => CC_Controller::get_refund_amount( $item['id'], $item['set'] ),
				'reason' => $user->display_name . ' ('. $user->user_email .') canceled the booking.',
				'order_id' => $or->id,
				'refund_id' => 0
			));

			$bk_succ = $val->update_status('cancelled');
			$val->schedule_events();

			if ( $bk_succ && $refund ) {
				// if we've reached this point, let's try and refund the thing v. the Stripe API:

				if ( WC()->payment_gateways() ) {
					$payment_gateways = WC()->payment_gateways->payment_gateways();
				}

				if ( isset( $payment_gateways[ $or->payment_method ] ) && $payment_gateways[ $or->payment_method ]->supports( 'refunds' ) ) {
					$result = $payment_gateways[ $or->payment_method ]->process_refund( $or->id, $refund->get_refund_amount(), $refund->get_refund_reason() );

					if ( is_wp_error( $result ) ) {
						wc_add_notice( 'We couldn\'t refund your order automatically.','notice' );
						wp_redirect( $_POST['referring-page']);
						exit;
					} elseif ( ! $result ) {
						return new WP_Error( 'woocommerce_api_create_order_refund_api_failed', __( 'An error occurred while attempting to create the refund using the payment gateway API', 'woocommerce' ), array( 'status' => 500 ) );
					}
				}

				CC_Controller::delete_booking_meta( $val->id );
				
				wc_add_notice( 'Your '.$_POST['reservation_type'].' was successfully cancelled.','success' );
				wp_redirect( $_POST['referring-page']);
				exit;
			} else {
				wc_add_notice( '(ERR-1) we couldn\'t cancel your booking!','notice' );
				wp_redirect( $_POST['referring-page'] );
				exit;
			}
		} else {
			// internal cancellation error.
			wc_add_notice( '(ERR-2) we couldn\'t cancel your booking!','notice' );
			wp_redirect( $_POST['referring-page'] );
			exit;
		}
	} else {
		// permission denied -- user session does not match authenticated user
		// add and check a nonce, too.
		wp_redirect( home_url() );
		exit;
	}
} else {
	wp_redirect( home_url() );
	exit;
}

?>

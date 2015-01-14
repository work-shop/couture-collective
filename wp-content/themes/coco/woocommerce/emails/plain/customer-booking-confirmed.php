<?php
/**
 * Customer booking confirmed email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$dress_id = CC_Controller::get_dress_for_product( $booking->get_product()->id, "rental" );

echo $email_heading . "\n\n";

if ( $booking->get_order() ) {
	echo sprintf( __( 'Hello %s', 'woocommerce-bookings' ), $booking->get_order()->billing_first_name ) . "\n\n";
}

echo __(  'Thank you for your reservation!', 'woocommerce-bookings' ) . "\n\n";

//echo "****************************************************\n\n";

echo sprintf( __( 'Your %s of %s %s will be delivered on %s by 5pm.', 'woocommerce-bookings'), 
		cc_booking_noun_string( $booking->get_resource()->post_title ), 
		get_field('dress_designer', $dress_id),
		get_field('dress_description', $dress_id),
		date( 'F jS, Y', strtotime( CC_Controller::get_selected_date($booking->id) ) );
	) . "\n\n";
//echo sprintf( __( 'Booking ID: %s', 'woocommerce-bookings'), $booking->get_id() ) . "\n";

echo sprintf( __( 'Please have it ready to pick up on the morning of %s.', 'woocommerce-bookings'), 
		$booking->get_end_date()
	) . "\n\n";

echo "If your dress was shipped to you via Fed-Ex, it can be repackaged in the same box it came in, and the box can be affixed with the enclosed label.\n\n";

echo "Please be aware that failure to have your dress ready for pick up will result in a $100/day late fee.\n\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
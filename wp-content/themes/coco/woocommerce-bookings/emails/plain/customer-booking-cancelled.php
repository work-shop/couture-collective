<?php
/**
 * Customer booking confirmed email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

$dress_id = CC_Controller::get_dress_for_product( $booking->get_product()->id, "rental" );

if ( $booking->get_order() ) {
	echo sprintf( __( 'Hello %s', 'woocommerce-bookings' ), $booking->get_order()->billing_first_name ) . "\n\n";
}

echo sprintf( __(  'Your %s of %s on %s has been cancelled.', 'woocommerce-bookings' ),
		cc_booking_noun_string( CC_Controller::get_booking_type( $booking ) ),
		get_field( 'dress_designer', $dress_id ) . ' ' . get_field( 'dress_description', $dress_id ),
		date( 'F jS, Y', strtotime( CC_Controller::get_selected_date($booking->id) ) )
	) . "\n\n";

echo __( 'Please contact us if you have any questions or concerns.', 'woocommerce-bookings' ) . "\n\n"; 

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
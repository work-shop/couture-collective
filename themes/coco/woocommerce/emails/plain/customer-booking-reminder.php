<?php
/**
 * Customer booking reminder email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$dress_id = CC_Controller::get_dress_for_product($booking->get_product()->id, "rental");

echo $email_heading . "\n\n";

if ( $booking->get_order() ) {
	echo sprintf( __( 'Hello %s', 'woocommerce-bookings' ), $booking->get_order()->billing_first_name ) . "\n\n";
}

echo sprintf( __(  'This is a reminder that your reservation of %s will be delivered on %s.', 'woocommerce-bookings' ),
		get_field( "dress_designer", $dress_id ) . ' - ' . get_field( "dress_description", $dress_id ),
		$booking->get_start_date()
	) . "\n\n";

echo sprintf( __(  'Please have it ready for pickup on %s.', 'woocommerce-bookings' ),
		$booking->get_end_date()
	) . "\n\n";

echo "Thank you!\n\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
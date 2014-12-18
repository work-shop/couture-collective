
<?php


/**
 * Customer booking notification email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo __(  'A new dress reservation has been made.', 'woocommerce-bookings' ) . "\n\n";

echo "****************************************************\n\n";

/**
 * This prints out the reserved dress
 */
echo sprintf( __( 'Please Deliver: %s - %s', 'woocommerce-bookings'), $item_number, $item_name ) . "\n\n";

// print out the target reservation date.
echo sprintf( __( 'On: %s', 'woocommerce-bookings'), $reservation_date ) . "\n\n";

// print out the address
echo "To:\n";
echo sprintf( __('%s', 'woocommerce-bookings'), $customer_name ) . "\n";
echo sprintf( __('%s', 'woocommerce-bookings'), $customer_address ) . "\n";
echo "\n";

// print out the pick-up date
echo sprintf( __( 'Please Pick up on: %s', 'woocommerce-bookings'), $pickup_date ) . "\n\n";

// echo sprintf( __( 'Booking Start Date: %s', 'woocommerce-bookings'), $booking->get_start_date() ) . "\n";
// echo sprintf( __( 'Booking End Date: %s', 'woocommerce-bookings'), $booking->get_end_date() ) . "\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );


?>
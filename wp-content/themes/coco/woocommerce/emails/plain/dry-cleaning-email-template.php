
<?php

/**
 * Drycleaning notification email.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo __(  'A new dress reservation has been made.', 'woocommerce-bookings' ) . "\n\n";

echo "\n****************************************************\n\n";

echo "Please deliver: ".get_field("dress_id_number", $dress->ID)." - ".get_field("dress_description", $dress->ID)."\n\n";

echo sprintf( __( 'On: %s', 'woocommerce-bookings'), $reservation_date ) . "\n\n";

echo "To:\n";
echo sprintf( __('%s', 'woocommerce-bookings'), $customer_name ) . "\n";
echo sprintf( __('%s', 'woocommerce-bookings'), $customer_address ) . "\n";
echo "\n";

echo sprintf( __( 'Please Pick up on: %s', 'woocommerce-bookings'), $pickup_date ) . "\n\n";


echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );


?>
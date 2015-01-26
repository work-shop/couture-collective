<?php
/**
 * Customer processing order email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo "****************************************************\n\n";

// ---------------------- Define information needed for email.

foreach ($order->get_items() as $key => $value) {
		$product = wc_get_product( ws_fst( $value['item_meta']['_product_id'] ));
		$product_type = ( is_array($ts = get_the_terms( $product->id, 'product_cat')) ) ? ws_fst( $ts )->name : "";

		if ( $product->product_type == "booking" ) {
			$dress_id = CC_Controller::get_dress_for_product( $product->id, "rental" );
			
			echo __(  'Thank you for your reservation!', 'woocommerce-bookings' ) . "\n\n";

			echo sprintf( __( 'Your reservation of %s - %s will be delivered on %s by 5pm.', 'woocommerce-bookings'),
					get_field('dress_designer', $dress_id),
					get_field('dress_description', $dress_id),
					$value['Booking Date']
				) . "\n\n";
			//echo sprintf( __( 'Booking ID: %s', 'woocommerce-bookings'), $booking->get_id() ) . "\n";

			echo sprintf( __( 'Please have it ready to pick up on the following morning.', 'woocommerce-bookings') ) . "\n\n";

			echo "If your dress was shipped to you via Fed-Ex, it can be repackaged in the same box it came in, and the box can be affixed with the enclosed label.\n\n";

			echo "Please be aware that failure to have your dress ready for pick up will result in a $100/day late fee.\n\n";

		} else if ( $product->product_type == "simple" && $product_type == "share"  ) {
			$dress_id = CC_Controller::get_dress_for_product( $product->id, "share" );

			echo sprintf( __('Thank you for your purchase of a share in %s - %s. You can manage your dress at %s/closet.'),
				get_field('dress_designer', $dress_id),
				get_field('dress_description', $dress_id),
				home_url()
			);

		} else {

			$dress_id = CC_Controller::get_dress_for_product( $product->id, "sale" );

			echo sprintf( __('Thank you for your purchase of %s - %s. Your dress will be shipped to you at the end of the season.'),
				get_field('dress_designer', $dress_id),
				get_field('dress_description', $dress_id)
			);

		}

	echo "****************************************************\n\n";
}

// ---------------------- /

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

?>
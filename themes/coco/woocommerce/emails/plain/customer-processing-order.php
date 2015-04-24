<?php
/**
 * Customer processing order email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// ---------------------- Define information needed for email.

foreach ($order->get_items() as $key => $value) {
		$product = wc_get_product( ws_fst( $value['item_meta']['_product_id'] ));
		$product_type = ( is_array($ts = get_the_terms( $product->id, 'product_cat')) ) ? ws_fst( $ts )->name : "";

		if ( $product->product_type == "booking" ) {
			/**
			 * @todo fix the hard-coded offset interval here...
			 */

			$dress_id = CC_Controller::get_dress_for_product( $product->id, "rental" );

			echo __(  'Thank you for your reservation!', 'woocommerce-bookings' ) . "\n\n";

			if ( strtotime( $value['Booking Date'] ) > strtotime( '+1 day' )) { 

				/**
				 * This is an advance reservation.
				 */
				
				echo sprintf( __( 'Your reservation of %s - %s will be delivered on %s by 5pm.', 'woocommerce-bookings'),
						get_field('dress_designer', $dress_id),
						get_field('dress_description', $dress_id),
						date( "F d Y", strtotime( $value['Booking Date'] . " -1 days" ) )
					) . "\n\n";
				

				echo sprintf( __( 'Please have it ready to pick up on the morning of %s.', 'woocommerce-bookings'), 
						date( "F d Y", strtotime( $value['Booking Date'] . " +1 days" ) )
					) . "\n\n";

				
				echo "If you need to extend this reservation for any reason, please email your request to info@couturecollective.club. Extensions are dependent on availability. A one-week reservation can sometimes be arranged by using two of your pre-reservation dates. Please also be aware that, out of respect for the sartorial rights of all our members, failure to have an item ready for pick up on the designated date will result in a $100/day automatic late fee. Cancellations may be made on the website up to one week prior to the reservation. If you need to cancel a reservation within the week, please email your cancellation request to info@couturecollective.club. Cancellations cannot be made within 48 hours of a reservation. Your item will be delivered and your card will be charged the cleaning/handling fee.\n\n";

			} else {
				/**
				 * This is a tomorrow reservation.
				 */

				echo sprintf( __( 'Your reservation of %s - %s will be delivered tomorrow.', 'woocommerce-bookings'),
						get_field('dress_designer', $dress_id),
						get_field('dress_description', $dress_id)
					) . "\n\n";

				echo sprintf( __( 'Please have it ready to pick up on the morning of %s. Please also be aware that, out of respect for the sartorial rights of all our members, failure to have an item ready for pick up on the designated date will result in a $100/day automatic late fee. This reservation cannot be cancelled.', 'woocommerce-bookings'), 
						date( "F d Y", strtotime( $value['Booking Date'] . " +1 days" ) )
					) . "\n\n";

			}
			
 

		} else if ( $product->product_type == "simple" && $product_type == "share"  ) {
			$dress_id = CC_Controller::get_dress_for_product( $product->id, "share" );

			/**
			 * @todo fix the hard-coded offset interval here...
			 */

			echo sprintf( __('Thank you for your purchase of a share in %s - %s. You can manage your dress at %s/closet.'),
				get_field('dress_designer', $dress_id),
				get_field('dress_description', $dress_id),
				home_url()
			);

		} else {

			$dress_id = CC_Controller::get_dress_for_product( $product->id, "sale" );

			echo sprintf( __('Thank you for your purchase of %s - %s! This item will be delivered to you on April 1st, 2015.'),
				get_field('dress_designer', $dress_id),
				get_field('dress_description', $dress_id)
			);

		}

	echo "\n\n";
}

// ---------------------- /

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

?>
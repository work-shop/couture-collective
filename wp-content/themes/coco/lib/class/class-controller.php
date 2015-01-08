<?php

class CC_Controller {


	public static $maximum_prereservations = 5;

	/**
	 * This function, given an integer representing a user-id, returns an 
	 * array of dress objects that this user has a share in.
	 *
	 * @param int $id the id of the customer to retrieve dresses for
	 * @return array(WP_Post) an array of dress posts
	 */
	public static function dresses_for_customer( $id ) {
		return get_post_meta( $id, 'cc_closet_values', true);
	}

	/**
	 * Given a booking instance, finds the type of purchase that this booking is.
	 *
	 * @param WP_Booking $booking WP_Booking object to typecheck
	 * @return {"Prereservation","Rental","Update","Next-day"} <: String \/ False
	 */
	public static function get_booking_type( $booking ) {
		$post = get_post( ws_fst( $booking->custom_fields['_booking_resource_id'] ) );
		if ( $post ) return $post->post_title;

		return false;
 	}

 	/**
 	 * given a booking, and an order, choose the order item in that order that represents
 	 * the booking's line item.
 	 *
 	 * @param WC_Booking $booking
 	 * @param WC_Order $order
 	 * @return array(item_id => array())
 	 */
 	public static function get_order_item_for_booking( $booking, $order ) {
 		global $wpdb;
 		
 		$order_items = $order->get_items();
 		$order_item_id = $wpdb->get_col( $wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_booking_order_item_id' AND post_id = %d", $booking->id ) );

		$ret = array();
		if ( array_key_exists(ws_fst( $order_item_id ), $order_items) ) {
			$ret['id'] = ws_fst( $order_item_id );
			$ret['set'] = array();
			$ret['set'][ ws_fst( $order_item_id ) ] = $order_items[ ws_fst( $order_item_id ) ];
		} 

 		return $ret;
 	}

 	/**
 	 * extracts the total refund amount from a set of items, given an item id
 	 *
 	 * @param string $item_id the line item id to extract refund amount for
 	 * @param array(string => array) $items an associative array of order items
 	 * @return string line total
 	 */
 	public static function get_refund_amount( $item_id, $items ) {
 		return ws_fst( $items[ $item_id ]['item_meta']['_line_total'] );
 	}


 	/**
 	 * Given a booking, find ints metadata, and delete it from the database wp_postmeta
 	 *
 	 * @param in $booking_id the id of the booking to remove metadata for
 	 * @return array(string => bool) matches meta keys with successfully deleted true or false
 	 *
 	 */
 	public static function delete_booking_meta( $booking_id ) {
 		$success = array();
 		
 		foreach (get_post_meta( $booking_id ) as $meta_key => $meta_value) {
 			$success[ $meta_key ] = delete_post_meta( $booking_id, $meta_key );
 		}

 		return $success;
 	}

 	/**
 	 * Given a bookable product id, returns the set of prereservations for that dress.
 	 *
 	 * @param int $product_id the id of the bookable product to retrieve product ids from.
 	 * @return array(WC_Bookings)|false the set of prereservations for this dress, or false on failure.
 	 */
 	public static function get_prereservations_for_dress_rental( $product_id, $user_id ) {
 		global $wpdb;

 		$bookable = get_product( $product_id );

 		if ( !$bookable ) return false;
 		if ( $bookable->product_type !== "booking" ) return false;

 		foreach ( $bookable->get_resources() as $resource ) {
 			if ( $resource->get_title() == "Prereservation" ) {
 				$resource_id = $resource->get_id();
 				$booking_ids = get_posts( array(
					'numberposts'   => -1,
					'offset'        => 0,
					'orderby'       => 'post_date',
					'order'         => 'DESC',
					'post_type'     => 'wc_booking',
					'post_status'   => array('paid','confirmed'),
					'fields'        => 'ids',
					'no_found_rows' => true,
					'meta_query' => array(
						array(
							'key'     => '_booking_resource_id',
							'value'   => absint( $resource_id )
						),
						array(
							'key'	    => '_booking_customer_id',
							'value'   => absint( $user_id )
						)
					)
				) );

 				return array_map( function($x) { return get_wc_booking( $x ); }, $booking_ids );
 			}
 		}
 	} 


 	/**
 	 * Given a set of bookings and a booking id, returns the booking indicated by that id
 	 * (Used to ensure that the requested deletion is actually connected to a given user's inventory)
 	 *
 	 * @param array(WP_Booking) $bookings an array of booking objects
 	 * @param in $booking_id an booking id
 	 * @return WP_Booking|false
 	 *
 	 * @todo ensure that the correct booking class is selected, and that the booking is in a cancellable range.
 	 */
 	public static function validate_requested_booking( $bookings, $booking_id ) {
 		foreach ($bookings as $booking) {
 			if ( $booking->id == $booking_id ) {

 				if ( CC_Controller::booking_is_modifiable( $booking ) ) return $booking;
 				return false;

 			}
 		}
 		return false;
 	}

 	public static function booking_is_modifiable( $booking ) {
 		return $booking->start >= strtotime( '+1 weeks' );
 	}


	/**
	 * Given a product ID, this function adds the dress the appropropriate
	 * bin on a given user's meta taxonomy.
	 *
	 * @param {"share","rental"} $type, the type that the passed product id belongs to.
	 * @param int $product_id, the product to reverse lookup.
	 * @param int $customer_id, the customer to assign the dress to.
	 */
	public static function add_dress_to_customer_closet( $type, $product_id, $customer_id ) {
		if ( !in_array($type, array('share','rental')) ) return;
		if ( !$customer_id || !$product_id ) return;

		/* We've encountered a share product. 
	         0. given our post id, let's query the posts.
		  1. get our custom taxo, and see that our array is formatted properly; use true to unserialize array.
		*/
		$parent_dresses = get_posts(array(
			'post_type' => 'dress',
			'meta_query' => array(
				array(
					'key' => 'dress_'.$type.'_product_instance',
					'value' => '"'.$product_id.'"',
					'compare' => 'LIKE'
				)
			)
		));

		$parent_dress_id = $parent_dresses[0]->ID;

		$closet = get_post_meta( $customer_id, 'cc_closet_values', true);

		if ( !empty($closet) ) {
			$closet[ $type ][] = $parent_dress_id;
		} else {
			$closet[$type] = array( $parent_dress_id );
		}

		update_post_meta( $customer_id, 'cc_closet_values', $closet );

	}
}

?>
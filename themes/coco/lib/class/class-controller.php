<?php

class CC_Controller {
	public static $field_keys = array(
		'closet_values' => 'field_553f9d01909ec',
		'season_dresses' => 'field_553fe01b64ca0',
		'active_season' => 'field_553fe0e583f45'
	);

	public static $maximum_prereservations = 5;

	/**
	 * This function, given an integer representing a user-id, returns an 
	 * array of dress objects that this user has a share in.
	 *
	 * @param int $id the id of the customer to retrieve dresses for
	 * @return array(WP_Post) an array of dress posts
	 */
	public static function dresses_for_customer( $id ) {
		return get_user_meta( $id, 'cc_closet_values', true);
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
 	 * Extracts the total refund amount from a set of items, given an item id.
 	 *
 	 * @param string $item_id the line item id to extract refund amount for
 	 * @param array(string => array) $items an associative array of order items
 	 * @return string line total
 	 */
 	public static function get_refund_amount( $item_id, $items ) {
 		return ws_fst( $items[ $item_id ]['item_meta']['_line_total'] );
 	}


 	/**
 	 * Gets the date that the customer selected for a given booking id.
 	 *
 	 * @param int $booking_id the booking to retrieve the chosen date for.
 	 * @return long unix timestamp representing the date selected for $booking_id
 	 */
 	public static function get_selected_date( $booking_id ) {
 		return ws_fst( get_post_meta($booking_id, '_cc_customer_booked_date') );
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
 	 * gets the id of the currently active season.
 	 *
 	 * @return int, the id of the currently activated season.
 	 */
 	public static function get_active_season() {
 		return ws_fst( get_field(CC_Controller::$field_keys['active_season'], 'option') );
 	}


 	/**
 	 * Given the id of a season post, returns the dresses associated with that season.
 	 *
 	 * @param int $season_id 
 	 * @return array(int), the dresses in this season.
 	 *
 	 */
 	public static function get_dresses_for_season( $season_id ) {
 		$dresses = get_field( CC_Controller::$field_keys['season_dresses'], $season_id );
 		return ( $dresses ) ? $dresses : array();
 	}

 	/**
 	 * gets the shared dresses for a user, optionally updating the closed values for that user
 	 * if they are out of date.
 	 *
 	 * @param WP_User $user user to get shared dresses for
 	 * @return array(string) dress ids.
 	 */
 	public static function get_shared_dresses_for_user( $user ) {

 		// get the old dresses for this user, to prepare for a migration.
 		$old_dresses = get_user_meta( $user->ID, 'cc_closet_values', true );

 		// get the newly stored dresses for the user. Avoid the unset case.
 		$new_dresses = ($arr = get_field('closet_values', 'user_' . $user->ID )) ? $arr : array();

 		// now its time to normalize the set of closet value, based on the new configuration.
 		$shares = array_unique( array_merge( $new_dresses, (( !empty( $old_dresses ) && array_key_exists('share', $old_dresses) ) ? $old_dresses['share'] : array()) ) );


 		foreach ($shares as $i => $share) {
 			if ( $share ) {
 				$dress = get_post( $share );
	 			$product = wc_get_product( ws_fst( get_field( 'dress_share_product_instance', $share ))->ID );
	 			/*
					When do we know that this is a valid share instance?

					Inference Schema for Dress Ownership

					Old array?		New array?		Order Exists?
				1	Y 			Y 			Y					=>	Y (Theoretically Won't Happen...)
				2	Y 			Y 			_					=>   	Y (It has been manually added.)
				3	Y 			_ 			Y					=>   	Y
				4	Y 			_ 			_					=>	_
				5	_ 			Y 			Y					=>   	Y
				6	_			Y 			_					=>	Y
				7	_  			_ 			Y					=>	_
				8	_  			_ 			_					=> 	_

	 			*/

				$o = in_array( $share, $old_dresses ); // is this share in the old array?
				$n = in_array( $share, $new_dresses ); // is this share in the new array?
				$e = woocommerce_customer_bought_product( $user->user_email, $user->ID, $product->id ); // was this product purchased?

	 			if ( 	!$shares[ $i ]  

	 			   || ($o && !($n || $e)) 	// line 4 of the truth table above
	 			   || (!($o || $n) && $e) 	// line 7 of the truth table above
	 			   					// line 8 never happens.
	 			   ) {

	 				unset( $shares[ $i ] );

	 			}
 			} else {
 				unset( $shares[$i] );
 			}
 		}

 		// now it's very important that we update our invariants.
 		$new_dresses = array_values( $shares );
 		$old_dresses['share'] = array(); // mark the old array as visited – remove all entries. (migration complete)

 		update_user_meta( $user->ID, 'cc_closet_values', $old_dresses );
 		update_field( CC_Controller::$field_keys['closet_values'], $new_dresses, 'user_' . $user->ID );

 		// now that the values have been saved and computed, we need to intersect this array with the active season's dresses.


 		$season_dresses = CC_Controller::get_dresses_for_season( CC_Controller::get_active_season() );

 		return array_intersect($season_dresses, $new_dresses);
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
					'post_status'   => array('paid','confirmed','complete'),
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
 	 * given a user id, this function selects the active rentals that this user has
 	 * and returns an array of dresses mapped their rentals.
 	 *
 	 * @param int $user_id the id of the user to retrieve dresses and bookings for
 	 * @param array(string) $status the statuses to query for
 	 * @return array(array(string=>{WC_Post|WC_Booking}))
 	 */
 	public static function get_rentals_by_dress_for_user( $user_id, $status = array( 'confirmed', 'paid' ) ) {
 		global $wpdb;
 		/*
 		 The routine:

 		 	get the set of bookings for the given user WC_Bookings_Controller.
 		 	Group these by Bookable_Product, and retrieve dresses for each one.
 		 	Iterate accross the bookable products, looking up the appropria

 		 	scratch that.

 		 	Custom meta query, matching on name.
 		 */

 		$rental_ids = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_title IN (%s, %s)", "Rental", "Nextday") );

 		if ( empty( $rental_ids ) ) return $rental_ids;

 		// we now have access to all ids of rental resources, we'd like to a right join on these with the postmeta fields to recover the rental bookings
 		// for all dresses, given the customer.

 		$booking_ids = get_posts( array(
			'numberposts'   => -1,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_booking',
			'post_status'   => $status,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'meta_query' => array(
				array(
					'key'     => '_booking_resource_id',
					'value'   => $rental_ids,
					'compare' => 'IN'
				),
				array(
					'key'	    => '_booking_customer_id',
					'value'   => absint( $user_id )
				)
			)
		) );

 		// we should now have all bookings that represent rentals for a certain user
 		// the next move is to turn these into WC_Booking objects, and sort them by dress.
 		// we basically need a sorting routine, ugh. Maybe a custom query is best.

 		$return = array();
 		$products = array();
 		$bookings = array_map(function($x) { return get_wc_booking( $x ); }, $booking_ids);
 		

 		foreach ( $bookings as $booking ) {
 			$product = $booking->get_product();

 			if ( !isset( $products[ $product->id ] ) ) {
 				// cache the selected dress for future use.
 				$dress = get_posts(array(
					'post_type' => 'dress',
					'meta_query' => array(
						array(
							'key' => 'dress_rental_product_instance',
							'value' => '"'.$product->id.'"',
							'compare' => 'LIKE'
						)
					)
				));

				if ( empty( $dress ) ) continue;

				$products[ $product->id ] = ws_fst( $dress );
 			}

 			$return[ $products[ $product->id ]->ID ][] = $booking;
 		}

 		return $return;

 	}

 	/**
 	 * Determine whether a dress is available for reservation tomorrow.
 	 *
 	 * @param WC_Bookable_Product $rental the bookable product to get rentals for.
 	 * @return bool
 	 *
 	 */
 	public static function available_tomorrow( $rental ) {
 		if ( !$rental ) return false;

 		$availability = array();
 		$resources = $rental->get_resources();
 		$target = array_map( function($x) { return array(
 			'id' => $x->get_id(),
 			'type' => $x->get_title()
 		); }, $resources);

 		foreach ( $target as $resource ) {
 			$availability[] = $rental->get_available_bookings(strtotime('+1 days'), strtotime('+36 hours'), $resource['id']);
 		}

 		return array_reduce($availability, function( $a,$b ) {
 			return !is_wp_error( $b ) && $a;
 		}, true);
 	}

 	/**
 	 * Given a WC_Bookable_Product instance, returns the date if its next day rental.
 	 *
 	 * @param WC_Product_Booking the bookable product to retrieve a next day reservation for
 	 * @return int timestamp of the next available reservation.
 	 */
 	public static function get_next_day_reservation( $rental ) {
 		$availability = array();
 		$resources = $rental->get_resources();
 		$target = array_reduce( array_map( function($x) { return array(
 			'id' => $x->get_id(),
 			'type' => $x->get_title()
 		); }, $resources), function( $carry,$item ) {
 			return (( $item['type'] == 'Nextday' ) ? $item['id'] : "") . $carry;
 		});

 		$availability = $rental->get_blocks_in_range(strtotime('+1 days'), strtotime('+36 hours'), null, $target );
 		$availability = ( $availability && !empty( $availability ) ) ? ws_fst( $availability ) : false;

 		if ( !$availability ) return false;

 		return array_combine( array("year", "month", "day"), explode( " ", date("Y m d", $availability ) ));
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

		switch ( $type ) {
			case 'share':
				return CC_Controller::add_share_to_customer_closet( $parent_dress_id, $customer_id );

			case 'rental':
				return CC_Controller::add_rental_to_customer_closet( $parent_dress_id, $customer_id );
		}

	}

	/**
	 * add_share_to_customer_closet : int x int -> void
	 *
	 *
	 * @since v1.5
	 * @param int $dress_id, the dress to insert into the closet.
	 * @param int $customer_id, the customer to assign the dress to. 
	 *
	 */
	public static function add_share_to_customer_closet( $dress_id, $customer_id ) {
		/**
		 * This routine needs to: 
		 *  1. get the existing closet representation for a user,
 		 *  2. ?? branch if that value is empty
 		 *  		2.1 (YES) add the dress id to the given user's closet value ACF field
 		 *		2.2 (NO) add each of the values in the share closet and add them to
 		 *                    to the ACF field, then replace the the closet with an empty array.
 		 *
		 *
		 */

		$shares = CC_Controller::get_shared_dresses_for_user( $customer_id );

		$shares = array_unique( array_merge( $shares, array( $dress_id ) ) );

		update_field(CC_Controller::$field_keys['closet_values'], $shares, 'user_' . $customer_id );

	}



	// public static function add_share_to_customer_closet( $dress_id, $customer_id ) {
	// 	/**
	// 	 * This routine needs to: 
	// 	 *  1. get the existing closet representation for a user,
 // 		 *  2. ?? branch if that value is empty
 // 		 *  		2.1 (YES) add the dress id to the given user's closet value ACF field
 // 		 *		2.2 (NO) add each of the values in the share closet and add them to
 // 		 *                    to the ACF field, then replace the the closet with an empty array.
 // 		 *
	// 	 *
	// 	 */

	// 	// 1. get existing closet representation for the user
	// 	$closet = get_user_meta( $customer_id, "cc_closet_values", true);

	// 	// if the closet is NOT empty, and the 'share' field is set...
	// 	if ( !empty($closet) && isset( $closet[ "share" ] ) && !empty( $closet["share"] ) ) {
	// 		// check to see if the current dress id exists in the array.
	// 		$existing = array_keys( $closet[ "share" ], $dress_id );

	// 		if ( !empty( $existing ) ) {
	// 			if ( ($n = count( $existing )) > 1 ) {
	// 				for ( $i = 1; $i < $n; $i++ ) {
	// 					unset( $closet[ "share" ][ $existing[ $i ] ] );
	// 				}
	// 			} else {
	// 				return;
	// 			}
	// 		} else {
	// 			$closet[ "share" ][] = $dress_id;
	// 		}
	// 	} else {
	// 		$closet[ "share" ] = array( $dress_id );
	// 	}

	// 	// closet['share'] now contains the proper representation of shared dresses.
	// 	// we'll update the current values of the field with the proper value.
	// 	update_field(CC_Controller::$field_keys["closet_values"], $closet["share"], "user_" . $customer_id );

	// 	// unset the value at the share position.
	// 	unset( $closet[ "share" ] );

	// 	// tag the meta value with blank data to signal that the migration has taken place.
	// 	update_user_meta( $customer_id, "cc_closet_values", $closet );

	// }

	/**
	 * add_rental_to_customer_closet : int x int -> void
	 *
	 * add a rental product to a customer's closet based on the original
	 * routine specification. This method is ported over from the original
	 * dress placement routine, and is factored out to allow for the user's
	 * shares to be handled differently. The previous $type : String parameter
	 * has been inlined in this version of the routine.
	 *
	 * @since v1.5
	 * @param int $dress_id, the dress to insert into the closet.
	 * @param int $customer_id, the customer to assign the dress to. 
	 *
	 */
	public static function add_rental_to_customer_closet( $dress_id, $customer_id ) {

		$closet = get_user_meta( $customer_id, 'cc_closet_values', true);

		if ( !empty($closet) && isset( $closet[ 'rental' ] ) ) {
			// get existing keys in the array,
			$existing = array_keys( $closet[ 'rental' ], $dress_id );

			if ( !empty( $existing ) ) {
				if ( ($n = count( $existing )) > 1 ) {
					for ( $i = 1; $i < $n; $i++ ) {
						unset( $closet[ 'rental' ][ $existing[ $i ] ] );
					}
				} else {
					return;
				}
			} else {
				$closet[ 'rental' ][] = $dress_id;
			}
		} else {
			$closet[ 'rental' ] = array( $dress_id );
		}

		update_user_meta( $customer_id, 'cc_closet_values', $closet );
		
	}

	/**
	 * get the parent dress for a given product.
	 *
	 * @param int $product_id the id of the product to retrieve the dress for
	 * @return int the id of the parent dress
	 */
	public static function get_dress_for_product( $product_id, $type = "" ) {
		if ( !$product_id ) return false;

		if ( empty( $type ) ) {
			$parent_dresses = get_posts(array(
				'post_type' => 'dress',
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key' => 'dress_sale_product_instance',
						'value' => '"'.$product_id.'"',
						'compare' => 'LIKE'
					),
					array(
						'key' => 'dress_share_product_instance',
						'value' => '"'.$product_id.'"',
						'compare' => 'LIKE'
					)
				)
			));

		} else {

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
		}

		if ( !empty( $parent_dresses ) ) return $parent_dresses[0]->ID;

		return false;
	}


	public static function get_resource_name_for_cart_item( $resource_id, $resources  ) {
		foreach ($resources as $resource) {
			if ( $resource->get_id() == $resource_id ) {
				return $resource->get_title();
			}
		}
	}

	/**
	 * This routine gets all future trunk shows
	 * @return array(WP_Post) trunkshows in the future. 
	 */
	public static function get_upcoming_trunkshows() {
		
		$upcoming = self::get_trunkshows_by_date_pivot( date('Ymd') );

		return ( empty( $upcoming ) ) ? $upcoming : $upcoming[0];

	}

	/**
	 * This routine divides a 
	 * @return array(WP_Post) trunkshows selected by $comparison and $date.
	 */
	public static function get_trunkshows_by_date_pivot( $date ) {
		$args = array(
			'post_type' => 'trunkshow',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		);

		$GLOBALS['__get_trunkshows_by_date_pivot::NOW'] = $date;

		$shows = get_posts( $args );

		usort($shows, function( $a, $b ) {
			$d_a_s = get_field( 'trunk_show_date', $a->ID );
			$d_b_s = get_field( 'trunk_show_date', $b->ID );
			$d_a_e = get_field( 'trunk_show_date_end', $a->ID );
			$d_b_e = get_field( 'trunk_show_date_end', $b->ID );

			$cmp_a = ($d_a_e) ? $d_a_e : $d_a_s;
			$cmp_b = ($d_b_e) ? $d_b_e : $d_b_s;

			return ( $cmp_a > $cmp_b ) ? 1 : (( $cmp_a < $cmp_b ) ? -1 : 0);
		});

		$split = ws_array_split( $shows, function( $show ) {
			$date = get_field( 'trunk_show_date', $show->ID );
			return ($date >= $GLOBALS['__get_trunkshows_by_date_pivot::NOW']) ? 0 : 1;
		});

		unset( $GLOBALS['__get_trunkshows_by_date_pivot::NOW'] );

		return $split;
	}

}

?>
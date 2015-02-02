<?php

class CC_Actions {

	/**
	 * @var string - woocommerce date format.
	 */
	protected static $woocommerce_date_string = 'YmdHis';

	/**
	 * @var string - offset unit for bookings (cf. strtotime).
	 */
	protected static $offset_unit = 'days';

	/**
	 * @var int negative offset margin for bookings
	 */
	protected static $offset_neg = 1;

	/**
	 * @var int positive offset margin for bookings
	 */
	protected static $offset_pos = 1;

	/**
	 * @var string - namespace prefix
	 */
	protected static $prefix = 'cc_';

	/**
	 * @var array(string => string) - cc action hooks
	 */
	protected static $actions = array(
		'wp_authenticate'							=> 'empty_auth_redirect',
		'wp_login_failed' 						=> 'login_failure_redirect',
		'woocommerce_new_booking' 					=> 'compute_booking_margins_in_booking',
		'woocommerce_booking_paid'					=> 'schedule_dry_cleaning_email_events',
		'woocommerce_booking_pending_to_cancelled' 		=> 'clear_scheduled_dry_cleaning_email_events',
		'woocommerce_booking_confirmed_to_cancelled' 		=> 'clear_scheduled_dry_cleaning_email_events',
		'woocommerce_booking_paid_to_cancelled' 		=> 'clear_scheduled_dry_cleaning_email_events',
		'woocommerce_order_status_processing'			=> 'add_dress_to_customer',
		'woocommerce_order_status_completed'			=> 'add_dress_to_customer',
		'activated_subscription'						=> 'set_customer_approval_status',
		'wpau_approve'							=> 'activate_subscription',
		'wpau_unapprove'							=> 'deactivate_subscription',
		'expired_subscription'						=> 'deauthorize_user',
		'cancelled_subscription'						=> 'deauthorize_user',
		'cc_add_membership_items'					=> 'add_membership_to_cart',
		'cc_remove_membership_items'				=> 'remove_membership_from_cart'
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		foreach ( CC_Actions::$actions as $hook => $callback ) {
			add_action( $hook, array( $this, $callback ) );
		}
	}

	/**
	 * This hook expands bookings outwards in both directions when a booking selected.
	 * Currently, this allows for overlap in the margin reservations.
	 *
	 * approach A) expand the booking time for this booking, based on the selected time.
	 * @param int $booking_id the id of the newly minted booking
	 */
	public function compute_booking_margins_in_booking( $booking_id ) {
		if ( !$booking_id ) return;

		$booking = array( 
			'booking_start' 	=> (($s = get_post_meta( $booking_id, '_booking_start' )) && is_array($s)) ? ws_fst( $s ) : $s,
			'booking_end' 	=> (($e = get_post_meta( $booking_id, '_booking_end' )) && is_array($e)) ? ws_fst( $e ) : $e,
		);

		$offset_neg = '-'.((string) CC_Actions::$offset_neg ).' '.CC_Actions::$offset_unit;
		$offset_pos = '+'.((string) CC_Actions::$offset_pos ).' '.CC_Actions::$offset_unit;

		/**
		 * @todo apparently its better to use the DateTime::add() and DateTime::sub() methods to do this...
		 */
		$new_start 	= date(CC_Actions::$woocommerce_date_string, strtotime($booking['booking_start'].' '.$offset_neg));
		$new_end 	= date(CC_Actions::$woocommerce_date_string, strtotime($booking['booking_end'].' '.$offset_pos));

		update_post_meta( $booking_id, '_booking_start', $new_start );
		update_post_meta( $booking_id, '_booking_end', $new_end );

		if ( !add_post_meta( $booking_id, '_cc_customer_booked_date', $booking['booking_start'], true ) ) {
			update_post_meta( $booking_id, '_cc_customer_booked_date', $booking['booking_start'] );
		}
	}

	/**
	 * This hook schedules an email event for a given booking.
	 *
	 * @param int $booking_id the booking to schedule a dispatch event for.
	 */
	public function schedule_dry_cleaning_email_events( $booking_id ) {
		// whenever a new booking is created, we either want to
		// A -- send the email immediately, if the email is within a certain range, or
		// B -- schedule an email to be sent when the range margin is met.
		if ( !$booking_id ) return;

		$booking = array( 
			'booking_start' 	=> (($s = get_post_meta( $booking_id, '_booking_start' )) && is_array($s)) ? ws_fst( $s ) : $s,
			'booking_end' 	=> (($e = get_post_meta( $booking_id, '_booking_end' )) && is_array($e)) ? ws_fst( $e ) : $e,
		);

		if ( !$booking['booking_start'] ) return;

		$min_email_date = strtotime( $booking['booking_start'] . ' -1 weeks' );

		if ( strtotime('today') > $min_email_date ) { 
			// if today is greater than one week prior to the desired booking.
			do_action( 'cc_send_dry_cleaning_email', $booking_id );
		} else {
			// we need to schedule an event for send the email.
			wp_schedule_single_event( $min_email_date, 'cc_send_dry_cleaning_email', array( $booking_id ) );
		}
	}

	/**
	 * Clears any hooked dry-cleaning notification emails for a given $booking_id
	 *
	 * @param int #booking_id the id of the booking to clear hooks for
	 */
	public function clear_scheduled_dry_cleaning_email_events( $booking_id ) {
		if ( !$booking_id ) return;

		$booking = array( 
			'booking_start' 	=> (($s = get_post_meta( $booking_id, '_booking_start' )) && is_array($s)) ? ws_fst( $s ) : $s,
			'booking_end' 	=> (($e = get_post_meta( $booking_id, '_booking_end' )) && is_array($e)) ? ws_fst( $e ) : $e,
		);

		if ( !$booking['booking_start'] ) return;

		$min_email_date = strtotime( $booking['booking_start'] . ' -1 weeks' );

		if ( strtotime('today') > $min_email_date ) { 
			// the send date for this booking has passed, we need to send a second email to inform of the cancellation.
			do_action('cc_send_dry_cleaning_cancellation_email', $booking_id );
		} else { 
			// otherwise we can simply unhook the scheduled event and not worry about it.
			wp_clear_scheduled_hook('cc_send_dry_cleaning_email', array( $booking_id ) );
		}
	}


	/** 
	*  we also need a hook if the dates have changed on a certain booking when a post is saved. This should be a custom
	*  cc hook that gets fired at the end of the update post action.
	**/

	/**
	 * This hook fires when a customer purchases a share in a dress, and their payment has
	 * been approved. It adds the dress as an object to the customer's purchased meta field.
	 *
 	 * @param int $order_id, the id of the newly created order.
	 */
	public function add_dress_to_customer( $order_id ) {
		if ( !$order_id ) return;

		if ( $order_id && ($order = wc_get_order( $order_id )) ) {
			//$customer_id = get_current_user_id();
			$customer_id = $order->get_user_id();

			foreach ( $order->get_items() as $order_item ) {
				$product_id = (count( $order_item['item_meta']['_product_id'] ) == 1) 
						  ? $order_item['item_meta']['_product_id'][0] : "";

				if ( !is_numeric( $product_id) ) continue;

				$product_id = intval( $product_id );
				$product = wc_get_product( intval( $product_id ) );

				$terms = wp_get_object_terms( $product_id, 'product_cat', array('fields' => 'names') ); 
				foreach ($terms as $name) {
					if ( in_array($name, array('share', 'rental')) ) {
						CC_Controller::add_dress_to_customer_closet( $name, $product_id, $customer_id );
					}
				}
			}
		}
	}


	/**
	 * @hooked page-look-book.php
	 * This hook redirects the user to the login page, along with a GET-based failure message
	 * rather than the default wp-login.php template.
	 *
	 * @param string $username the name passed to the failed login form
	 */
	public function login_failure_redirect( $username ) {
		wp_redirect( home_url() . '/my-account?login=failed' );
		exit;	
	}

	/**
	 * @hooked header.php
	 * This hook redirects the user to the /my-account page instead of wp-login for
	 * for empty uses.
	 *
	 * @param string $username the name passed to the login form.
	 */
	public function empty_auth_redirect( $username ) {
		if ( empty( $username ) ) {
			wp_redirect( home_url() . '/my-account' );
			exit;	
		}
	}

	/**
	 * @hooked page-join.php
	 * This hook sets the default user-status to unapproved,
	 * logs out the user if they are are logged in,
	 * and redirects them to a place-holder page.
	 *
	 * @param int $customer_id the id of the newly-created customer
	 * @param array('username','password','email','role') $new_customer_data
	 * @param string $password_generated
	 */
	public function set_customer_approval_status( $customer_id ) {
		if ( ! get_user_meta( $customer_id, 'wp-approve-user', true) ) {
			$this->deactivate_subscription( $customer_id );
			wp_logout();
			wp_redirect( home_url() . '/my-account?login=pending' );
			exit;
		}
	}

	/**
	 * If the user has been manually approved, set the user's subscription to active.
	 *
	 * @param int $user_id the id of the user to activate a subscription for.
	 */
	public function activate_subscription( $user_id ) {
		if ( get_user_meta( $user_id, 'wp-approve-user', true ) ) {
			$subs = WC_Subscriptions_Manager::get_users_subscriptions( $user_id );

			if ( !empty( $subs ) ) {
				foreach ( $subs as $key => $sub ) {
					WC_Subscriptions_Manager::activate_subscription( $user_id, $key );
				}
				
			}
		}

	}

	/**
	 * If the user has been manually "unapproved", set the user's subscription to on-hold
	 *
	 * @param int $user_id the id of the user to deactivate the subscription for
	 */
	public function deactivate_subscription( $user_id ) {
		if ( !get_user_meta( $user_id, 'wp-approve-user', true ) ) {
			$subs = WC_Subscriptions_Manager::get_users_subscriptions( $user_id );

			if ( !empty( $subs ) ) {
				foreach ($subs as $key => $value) {
					WC_Subscriptions_Manager::put_subscription_on_hold( $user_id, $key );
				}
				
			}
		}
	}

	/**
	 * Deauthorize a user from logging in to the site.
	 *
	 * @param int $user_id id of the user to deauthorize
	 * @param string $subscription_key identifier of the source subscription.
	 */
	public function deauthorize_user($user_id, $subscription_key) {
		update_user_meta( $user_id, 'wp-approve-user', false );
	}

	/**
	 *
	 * Automatically add the membership product to a user's cart
	 * if they visit the join page
	 *
	 */
	public function add_membership_to_cart( ) {
		
		if ( ! is_admin() ) {
			global $woocommerce;
			$product_id = 45; // membership product id
			$found = false;
			//check if product already in cart
			if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
				foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->id == $product_id )
						$found = true;
				}
				// if product not found, add it
				if ( ! $found )
					$woocommerce->cart->add_to_cart( $product_id );
			} else {
				// if no products in cart, add it
				$woocommerce->cart->add_to_cart( $product_id );
			}
		}
	}

	/**
	 *
	 * Automatically remove the membership product to a user's cart
	 * if they are not on the join page
	 *
	 */
	public function remove_membership_from_cart() {
		if ( is_checkout() || is_cart() ) {
			global $woocommerce;
			$product_id = 45;
			if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
				foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->id == $product_id ) {
						$woocommerce->cart->set_quantity( $cart_item_key, 0 );
						break;
					}
				}
			}
		}
	}

}

new CC_Actions();

?>
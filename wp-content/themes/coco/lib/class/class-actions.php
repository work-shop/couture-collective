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
		'wp_login_failed' 						=> 'login_failure_redirect',
		'woocommerce_new_booking' 					=> 'compute_booking_margins_in_booking',
		'woocommerce_booking_paid'					=> 'schedule_dry_cleaning_email_events',
		'woocommerce_booking_pending_to_cancelled' 		=> 'clear_scheduled_dry_cleaning_email_events',
		'woocommerce_booking_confirmed_to_cancelled' 		=> 'clear_scheduled_dry_cleaning_email_events',
		'woocommerce_booking_paid_to_cancelled' 		=> 'clear_scheduled_dry_cleaning_email_events',
		'woocommerce_order_status_processing'			=> 'add_dress_to_customer'
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
			$customer_id = get_current_user_id();

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
		$ref = $_SERVER['HTTP_REFERER'];

		if ( !empty($ref) ) {
			wc_add_notice('Incorrect username or password','notice');
			wp_redirect( home_url() . '/my-account?login=failed' );
			exit;	
		}
	}
}

new CC_Actions();

?>
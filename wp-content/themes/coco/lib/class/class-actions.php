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
		'wp_login_failed' 				=> 'login_failure_redirect',
		'woocommerce_new_booking' 			=> 'compute_booking_margins_in_booking'
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
	}

	/**
	 * This hook expands bookings outwards in both directions when a booking selected.
	 * Currently, this allows for overlap in the margin reservations.
	 *
	 * approach B) create left and right subbookings that are children of this booking, and belong to admin.
	 * @param int $booking_id the id of the newly minted booking
	 */
	public function compute_booking_margins_in_subbookings( $booking_id ) {

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
<?php





class CC_Ajax {

	/**
	 * @var string namespace prefix for actions
	 */
	public static $ns_prefix = 'cc_';

	/**
	 * @var array string actions registered by this handler
	 */
	public static $actions = array(
		'calculate_costs', 
		'get_time_blocks', 
		'update_reservation'
	);

	/**
	 * Constructor ( sets up action handlers )
	 */
	public function __construct() {
		foreach ( CC_Ajax::$actions as $key => $action ) {
			add_action('wp_ajax_' . CC_Ajax::$ns_prefix . $action, array( $this, $action) );
			add_action('wp_ajax_nopriv_' . CC_Ajax::$ns_prefix . $action, array( $this, $action) );
		}
	}

	/**
	 *
	 * 
	 *
	 */
	public function calculate_costs() {
		$posted = array();

		parse_str( $_POST['form'], $posted );

		$booking_id = $posted['add-to-cart']; // changing this will help the whole reservation thing.
		$product    = get_product( $booking_id );

		die( json_encode( array(
			'result' => 'SUCCESS',
			'html' => __('Booking cost', 'woocommerce-bookings') . ': <strong>' . '$10' . '</strong>'
		) ) );
	}

	public function get_time_blocks() {
		die( json_encode( array(
			'result' => 'ERROR',
			'message' => 'Unimplemented',
			'html' => __('Booking cost', 'woocommerce-bookings') . ': <strong>' . 'Unimplemented' . '</strong>'
		) ) );
	}

	public function update_reservation() {
		die( json_encode( array(
			'result' => 'ERROR',
			'message' => 'Unimplemented',
			'html' => __('Booking cost', 'woocommerce-bookings') . ': <strong>' . 'Unimplemented' . '</strong>'
		) ) );
	}

}


new CC_Ajax();


?>
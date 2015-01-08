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

		$booking_id = $posted['add-to-cart']; 
		$product    = get_product( $booking_id );
		$type 		= $_POST['reservation_type'];

		if ( ! $product ) {
			die( json_encode( array(
				'result' => 'ERROR',
				'html'   => '<span class="booking-error">' . __( 'This date is unavailable.', 'woocommerce-bookings' ) . '</span>'
			) ) );
		}


		$form = new CC_Make_Reservation_Form( $product, $type );
		$cost = $form->calculate_booking_cost( $posted );

		if ( is_wp_error( $cost ) ) {
			die( json_encode( array(
				'result' => 'ERROR',
				'html'   => '<span class="booking-error">' . $cost->get_error_message() . '</span>',
				''
			) ) );
		}

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price    = $tax_display_mode == 'incl' ? $product->get_price_including_tax( 1, $cost ) : $product->get_price_excluding_tax( 1, $cost );

		die( json_encode( array(
			'result' => 'SUCCESS',
			'html'   => __( '<p class="h7 uppercase">'.cc_booking_cost_string( $type ).':', 'woocommerce-bookings' ) . ' <span class="h8 numerals">' . woocommerce_price( $display_price ) . $product->get_price_suffix() . '</span></p>'
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
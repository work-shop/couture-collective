<?php


class CC_Cart_Filters {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cc_validate_add_cart_item' ), 15, 3 ); // higher priority than bookings filter...
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'cc_add_cart_item_data' ), 15, 2 );
	}

	/**
	 * When a reservation is added to the cart, validate it based on price...
	 */
	public function cc_validate_add_cart_item( $passed, $product_id, $qty ) {
		global $woocommerce;

		$product = get_product( $product_id );

		if ( $product->product_type !== 'booking' ) {
			return $passed;
		}

		if ( $passed == false ) {

			wc_clear_notices();

			$booking_form = new CC_Make_Reservation_Form( $product, $_POST['reservation_type'] ); // gotta make sure this value is planted
			$data = $booking_form->get_posted_data();
			$validate = $booking_form->is_bookable( $data );

			if ( is_wp_error( $validate ) ) {
				wc_add_notice( $validate->get_error_message(), 'error' );
				return false;
			}

			return true; // this ignores the frame condition... potentially problematic.
		}
		return $passed;	
	}

	/**
	 * Added posted data to the specified cart item
	 */
	public function cc_add_cart_item_data( $cart_item_meta, $product_id ) {
		$product = get_product( $product_id );

		if ( 'booking' !== $product->product_type ) {
			return $cart_item_meta;
		}

		if ( !isset( $_POST['reservation_type'] ) ) return $cart_item_meta;

		$booking_form = new CC_Make_Reservation_Form( $product, $_POST['reservation_type'] );
		$cart_item_meta['booking'] = $booking_form->get_posted_data( $_POST );
		$cart_item_meta['booking']['_cost'] = $booking_form->calculate_booking_cost( $_POST );

		return $cart_item_meta;
	}

}

new CC_Cart_Filters();


?>
<?php


class CC_Filters {


	public static $filters = array(
		'woocommerce_add_to_cart_validation' => array( 'cc_validate_add_cart_item_price', 15, 3 ),
		//'woocommerce_add_to_cart_validation' => array( 'cc_validate_add_cart_item_qty', 16, 3 ),
		'woocommerce_add_cart_item_data' => array( 'cc_add_cart_item_data', 15, 2 ),
		'woocommerce_email_classes' => 'cc_add_dry_cleaner_notifications',
		//'woocommerce_email_classes' => 'cc_add_new_user',
		'authenticate' => array( 'cc_check_email_registry', 35, 3 ),
		'authenticate' => array( 'cc_check_user_approved', 30, 3 )
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		foreach ( CC_Filters::$filters as $filter => $cbdata ) {
			if ( is_array($cbdata ) ) {
				add_filter( $filter, array( $this, $cbdata[0] ), $cbdata[1], $cbdata[2] );
			} else {
				add_filter( $filter, array( $this, $cbdata ) );
			}
		}
	}

	/**
	 * When a reservation is added to the cart, validate it based on price...
	 */
	public function cc_validate_add_cart_item_price( $passed, $product_id, $qty ) {
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
	 * When bookable product is added to the cart, check to see how many of this product the customer has
	 * Already purchased (n), and how many they have in their cart (m), and deny the transaction if (n + m) > 5.
	 *
	 */
	public function cc_validate_add_cart_item_qty( $passed, $product_id, $qty ) {
		$product = get_product( $product_id );
		$user = wp_get_current_user();

		if ( $product->product_type !== "booking" ) return $passed;
		if ( $qty > 5 ) return false;

		return $passed;

		/*
		 We have found a request for a booking to be purchased by a user.

		 we want now to inspect the database for non-cancelled orders involving the given user, and given product id.
		 we will then want to check those orders for i

		 */

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

	/**
	 * @param $email_classes array( WC_Email )
	 * @return array( WC_Email )
	 */
	public function cc_add_dry_cleaner_notifications( $email_classes ) {
		// require internal the filter, so that it's loaded after WC_Email
		require_once( realpath(dirname(__FILE__) . '/../emails/init.php') );

		$email_classes['CC_Dry_Cleaning_Email'] = new CC_Dry_Cleaning_Email();
		$email_classes['WC_Email_Customer_New_Account'] = new CC_New_User_Email();
		
		//$email_classes['CC_Cancel_Dry_Cleaning_Email'] = new CC_Cancel_Dry_Cleaning_Email();

		return $email_classes;
	}


	/**
	 * @param array(WC_Email) $email_classes
	 * @return array( WC_Email )
	 */
	public function cc_add_new_user( $email_classes ) {
		require_once( realpath(dirname(__FILE__) . '/../emails/class-cc-new-user-email.php') );

		$email_classes['WC_Email_Customer_New_Account'] = new CC_New_User_Email();

		return $email_classes;
	}


	public function cc_check_user_approved( $user, $username, $password ) {
		if ( is_wp_error( $user ) ) return $user;
		
     		if ( get_user_meta( $user->ID, 'wp-approve-user', true) ) {
     			return $user;
     		} else {
     			return new WP_Error();
     		}
	}

	public function cc_check_email_registry( $user, $username, $password ) {
		if ( is_email( $username ) ) {
			$user = get_user_by_email( $username );
			if ( $user ) $username = $user->user_login;
		}

		return wp_authenticate_username_password( null, $username, $password );
	}


}

new CC_Filters();


?>
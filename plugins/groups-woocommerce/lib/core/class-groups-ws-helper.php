<?php
/**
 * class-groups-ws-helper.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package groups-woocommerce
 * @since groups-woocommerce 1.7.0
 */

/**
 * Helper class for common tasks.
 */
class Groups_WS_Helper {

	/**
	 * Retrieve an order.
	 * 
	 * @param int $order_id
	 * @return WC_Order or null
	 */
	public static function get_order( $order_id = '' ) {
		$result = null;
		$order = new WC_Order( $order_id );
		if ( $order->get_order( $order_id ) ) {
			$result = $order;
		}
		return $result;
	}

	/**
	 * Retrieve a product.
	 * 
	 * @param mixed $the_product Post object or post ID of the product
	 * @param array $args retrieval arguments
	 */
	public static function get_product( $the_product = false, $args = array() ) {
		$result = null;
		if ( function_exists( 'wc_get_product' ) ) {
			$result = wc_get_product( $the_product, $args );
		} else if ( function_exists( 'get_product' ) ) {
			$result = get_product( $the_product, $args );
		}
		return $result;
	}

	/**
	 * Returns the order status key for the $status given.
	 * Order status keys have changed in WC 2.2 and this is provided to make
	 * it easier to handle.
	 * 
	 * @param string $status key, one of 'pending', 'failed', 'on-hold', 'processing', 'completed', 'refunded', 'cancelled'
	 * @return string status key for current WC
	 */
	public static function get_order_status( $status ) {
		$result = $status;
		if ( function_exists( 'wc_get_order_statuses' ) ) { // only from WC 2.2
			if ( in_array( 'wc-' . $status, array_keys( wc_get_order_statuses() ) ) ) {
				$result = 'wc-' . $status;
			}
		}
		return $result;
	}
}

/**
 * Retrieve a product.
 * 
 * @param mixed $the_product
 * @param array $args
 * @return WC_Product or null
 */
function groups_ws_get_product( $the_product = false, $args = array() ) {
	return Groups_WS_Helper::get_product( $the_product, $args );
}

/**
 * Returns the order status key for the $status given.
 * 
 * @param string|array $status order status or statuses
 * @return string|array WooCommerce order status key or keys, null or an empty array if no valid statuses were provided
 */
function groups_ws_order_status( $status ) {
	$statuses = $status;
	if ( !is_array( $status ) ) {
		$statuses = array( $status );
	}
	$wc_statuses = array();
	foreach( $statuses as $status ) {
		$wc_status = Groups_WS_Helper::get_order_status( $status );
		if ( $wc_status !== null ) {
			$wc_statuses[] = $wc_status;
		}
	}
	$result = $wc_statuses;
	if ( count( $wc_statuses ) == 1 ) {
		$result = array_shift( $wc_statuses );
	}
	return $result;
}

/**
 * Returns true if we have WooCommerce 2.2 or later.
 * @return boolean
 */
function groups_ws_is_wc22() {
	return function_exists( 'wc_get_order_statuses' );
}

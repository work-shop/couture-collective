<?php
/**
 * Product quantity input
 *
 * Extends the WooCommerce quantity input template to include the add_to_cart data attribute.
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$input_button = woocommerce_quantity_input( array(
	'input_name'  => 'product_id',
	'input_value' => ! empty( $product->cart_item['quantity'] ) ? $product->cart_item['quantity'] : 0,
	'max_value'   => $product->backorders_allowed() ? '' : $product->get_stock_quantity(),
	'min_value'   => 0,
), $product, false );
echo str_replace( 'type="number"', 'type="number" data-add_to_cart="' . $product->add_to_cart_id . '"', $input_button ); ?>

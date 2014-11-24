<?php
/**
 * Template to display product selection fields in a list
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<label>
	<input type="radio" id="product_<?php echo $product->add_to_cart_id; ?>" name="add_to_cart" value="<?php echo $product->add_to_cart_id; ?>" data-add_to_cart="<?php echo $product->add_to_cart_id; ?>" <?php checked( $product->in_cart ); ?>/>
</label>

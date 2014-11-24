<?php
/**
 * Add to Cart Input Template
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}?>

<?php if ( $product->is_sold_individually() ) : ?>
	<?php wc_get_template( 'checkout/add-to-cart/button.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path ); ?>
<?php else : ?>
	<?php wc_get_template( 'checkout/add-to-cart/quantity-input.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path ); ?>
<?php endif; ?>

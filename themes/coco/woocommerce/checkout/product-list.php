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
<ul id="checkout-products">
	<?php foreach( $products as $product ) : ?>
	<li class="product-item <?php if ( $product->in_cart ) echo 'selected'; ?>" >
		<?php wc_get_template( 'checkout/add-to-cart/radio.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path );; ?>
		<?php echo $product->get_title(); ?>
		<?php if ( $product->is_type( 'variation' ) ) : ?>
			<?php $attribute_string = sprintf( '&nbsp;(%s)', implode( ', ', array_map( 'ucfirst', $product->get_variation_attributes() ) ) ); ?>
			<span class="attributes"><?php echo esc_html( apply_filters( 'woocommerce_attribute', $attribute_string, $product->get_variation_attributes(), $product ) ); ?></span>
		<?php else : ?>
			<?php $attributes = $product->get_attributes(); ?>
			<?php foreach ( $attributes as $attribute ) : ?>
				<?php $attribute_string = sprintf( '&nbsp;(%s)', $product->get_attribute( $attribute['name'] ) ); ?>
			<span class="attributes"><?php echo esc_html( apply_filters( 'woocommerce_attribute', $attribute_string, $attribute, $product ) ); ?></span>
			<?php endforeach; ?>
		<?php endif; ?>
		<span class="dash">&nbsp;&mdash;&nbsp;</span>
		<span itemprop="price" class="price"><?php echo $product->get_price_html(); ?></span>
	</li>
	<?php endforeach; // end of the loop. ?>
</ul>

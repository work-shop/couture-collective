<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>


		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				
							
				<div class="row m25 bordered-pink-bottom <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				
					<div class="product-thumbnail col-sm-2">
						<a href="#dress-permalink">
							img
							<img src="" />
						
						</a>
					</div>

					<div class="product-name col-sm-3">
						<a href="#dress-permalink">
							<h1 class="uppercase dress-designer">Designer</h1>
							<h6 class="dress-description m2">Description</h6>												
						</a>
	
						
						<p class="h7 uppercase product-type m1">1 Share/1 Night Rental/Pre-reservation/End of Season Sale</p>
						
						<?php // if(!share || !end-of-season-sale) ?>
						
							<p class="h7 product-reservation-date m2">Friday, Jan 9, 2015</p>
							
	
							<?php
								// i deleted a bunch of stuff but left this because it seemed like it might be useful
								// Meta data
								//echo WC()->cart->get_item_data( $cart_item );
							?>
							
							<div class="product-addresses">
							
								<p class="h7">Need to see what options we have for selecting addresses before marking it up</p>
							
							</div>
							
						<?php //endif ?>

					</div>

					<div class="product-price col-sm-5">
						<p class="h8 numerals">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
						</p>

					</div>

					<div class="product-quantity hidden">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
						?>
					</div>

					<div class="product-subtotal hidden">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</div>

					<div class="product-remove col-sm-2">
					
						<?php // i need this span element to be INSIDE the <a> element in the apply_filters function below, if not possible, let me know ?>
							<span class="icon svg small tooltip-white" data-toggle="tooltip" data-placement="bottom" title="remove item"><?php get_template_part('_icons/remove'); ?></span>
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s"></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
						?>
					</div>
					
					
				</div><!--/row cart-item-->
											
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		
		<div class="cart-collaterals row">
		
			<div class="col-sm-6 col-sm-offset-6">
				
				<?php  do_action( 'woocommerce_cart_collaterals' ); ?>
			
				<div class="m2">
					<?php woocommerce_cart_totals(); ?>
				</div>
				
				<div class="col-sm-12 col-md-10 col-md-offset-2">
				
					<p class="h8 m2">I accept these terms & conditions.</p>
				
					<input type="submit" class="checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Checkout', 'woocommerce' ); ?>" />
					
					

				<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
				
			</div>
			
				<?php // woocommerce_shipping_calculator(); ?>
		
		</div>		

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>


<?php do_action( 'woocommerce_after_cart' ); ?>

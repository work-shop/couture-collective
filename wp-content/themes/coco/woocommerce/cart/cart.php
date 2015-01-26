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

			if ( $_product->product_type == "booking" ) {
				$dress_id = CC_Controller::get_dress_for_product( $product_id, 'rental' );
				$type = CC_Controller::get_resource_name_for_cart_item( $cart_item['booking']['_resource_id'], $_product->get_resources() );
			} else {
				$dress_id = CC_Controller::get_dress_for_product( $product_id );
				$terms = get_the_terms( $product_id, 'product_cat' );
				if ( !empty($terms) ) { 
					$type = ws_fst( $terms )->name;
				} else {
					$type = "Sale";
				}
			}

			$perma = get_permalink( $dress_id );
			$description = get_field( 'dress_description', $dress_id );
			$designer = get_field( 'dress_designer', $dress_id );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				
							
				<div class="row m25 bordered-pink-bottom <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				
					<div class="product-thumbnail col-sm-2">
						<a href="<?php echo $perma; ?>">
							<?php echo get_the_post_thumbnail( $dress_id ); ?>
						
						</a>
					</div>

					<div class="product-name col-sm-3">

						<a href="<?php echo $perma; ?>">
							<h1 class="uppercase dress-designer"><?php echo $designer; ?></h1>
						</a>
						<h6 class="dress-description m2"><?php echo $description; ?></h6>

						<p class="h7 uppercase product-type m1"><?php echo $cart_item['quantity'] . ' ' .cc_booking_noun_string( $type ); ?></p>
						
						<?php if ( $_product->product_type == "booking") : ?>
						
							<p class="h7 product-reservation-date m2"><?php echo $cart_item['booking']['date']; ?></p>
							
							<div class="product-addresses hidden">
							
								<p class="h7">Need to see what options we have for selecting addresses before marking it up</p>
							
							</div>
							
						<?php endif; ?>

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

							<a href="<?php echo esc_url( WC()->cart->get_remove_url( $cart_item_key ) ); ?>" class="remove" title="Remove this item">
								<span class="icon svg small tooltip-white" data-toggle="tooltip" data-placement="bottom" title="remove item"><?php get_template_part('_icons/remove'); ?></span>
							</a>
						<?php
							//echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s"></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
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

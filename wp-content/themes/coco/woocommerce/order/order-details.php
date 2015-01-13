<?php
/**
 * Order details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$order = wc_get_order( $order_id );
?>
<p class="h7 uppercase m"><?php _e( 'Order Summary:', 'woocommerce' ); ?></p>
<div class="shop_table order_details">
	<div>
		<?php
		if ( sizeof( $order->get_items() ) > 0 ) {

			foreach( $order->get_items() as $item ) {
				$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
				$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );

				?>
				<div class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<div class="product-name">
						<h6>
						<?php
							if ( $_product && ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
							else
								echo apply_filters( 'woocommerce_order_item_name', sprintf( '', get_permalink( $item['product_id'] ), $item['name'] ), $item );

							echo apply_filters( 'woocommerce_order_item_quantity_html', ' <span class=" product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</span>', $item );

							$item_meta->display();

							if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

								$download_files = $order->get_item_downloads( $item );
								$i              = 0;
								$links          = array();

								foreach ( $download_files as $download_id => $file ) {
									$i++;

									$links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
								}

								echo '<br/>' . implode( '<br/>', $links );
							}
						?>
						</h6>
					</div>
					<div class="product-total">
						<p class="h8 numerals"><?php echo $order->get_formatted_line_subtotal( $item ); ?></p>
					</div>
				</div>
				<?php
			}
		}

		do_action( 'woocommerce_order_items_table', $order );
		?>
	</div>
	<div>
	<?php
		if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
			?>
			<p class="h7 uppercase">
				<?php echo $total['label']; ?>
				<span class=""><?php echo $total['value']; ?></span>
			</p>
			<?php
		endforeach;
	?>
	</div>
</div>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<hr />

<div class="customer_details">
<?php
	if ( $order->billing_email ) echo '<p class="h7 uppercase m">' . __( 'Email:', 'woocommerce' ) . '</p><p class="h7 m2">' . $order->billing_email . '</p>';
	if ( $order->billing_phone ) echo '<p class="h7 uppercase m">' . __( 'Telephone:', 'woocommerce' ) . '</p><p class="h7 m2">' . $order->billing_phone . '</p>';

	// Additional customer details hook
	do_action( 'woocommerce_order_details_after_customer_details', $order );
?>
</div>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

<div class="col2-set addresses">

	<div class="col-1">

<?php endif; ?>

		<header class="title">
			<p class="h7 uppercase m"><?php _e( 'Billing Address', 'woocommerce' ); ?></p>
		</header>
		<address>
			<?php
				if ( ! $order->get_formatted_billing_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_billing_address();
			?>
		</address>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

	</div><!-- /.col-1 -->

	<div class="col-2">

		<header class="title">
			<p class="h7 uppercase m"><?php _e( 'Shipping Address', 'woocommerce' ); ?></p>
		</header>
		<address>
			<?php
				if ( ! $order->get_formatted_shipping_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_shipping_address();
			?>
		</address>

	</div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<?php endif; ?>

<div class="clear"></div>

<hr />

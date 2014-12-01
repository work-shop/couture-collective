<?php
global $woocommerce;
$tv = false;

foreach ( $woocommerce->cart->get_cart() as $k => $v ) {


	$p = $v['data'];
	$tv =  $tv || $GLOBALS['CC_POST_DATA']['share']->ID === $p->id;
}

if ( $tv ) { ?>

<p class="alert gray">There's already a share of this dress in your cart.</p>

<?php } else { 

	$share = $GLOBALS['CC_POST_DATA']['share'];
	echo ws_ifdef_concat('<p class="price">SHARE: ',$share->get_price_html(),'</p>');

?>

	<form class="cart" method="post" enctype='multipart/form-data'>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
	 	<!-- <input type="hidden" name="redirect_to" value="<?php echo $url; ?>"> -->

	 	<button type="submit" class="single_add_to_cart_button button alt">PURCHASE</button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

<? } ?>


<?php // add the help tooltip ?>
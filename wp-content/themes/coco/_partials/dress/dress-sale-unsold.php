<?php
global $woocommerce;
$tv = false;

foreach ( $woocommerce->cart->get_cart() as $k => $v ) {


	$p = $v['data'];
	$tv =  $tv || $GLOBALS['CC_POST_DATA']['sale']->ID === $p->id;
}

if ( $tv ) { ?>

<p class="alert gray">The End-of-Season sale of this dress is already in your cart.</p>

<?php } else { 

	$sale = $GLOBALS['CC_POST_DATA']['sale'];
	echo ws_ifdef_concat('<p class="h7 uppercase">End of Season Sale: <span class="numerals">',$sale->get_price_html(),'</span></p>');

?>

	<form class="cart" method="post" enctype='multipart/form-data'>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $sale->id ); ?>" />
	 	<!-- <input type="hidden" name="redirect_to" value="<?php echo $url; ?>"> -->

	 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo cc_booking_prompt_string('Share'); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

<? } ?>


<?php // add the help tooltip ?>
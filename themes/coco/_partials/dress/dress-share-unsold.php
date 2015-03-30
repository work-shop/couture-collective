<?php
global $woocommerce;
$tv = false;

foreach ( $woocommerce->cart->get_cart() as $k => $v ) {


	$p = $v['data'];
	$tv =  $tv || $GLOBALS['CC_POST_DATA']['share']->ID === $p->id;
}

if ( $tv ) { ?>

<p class="h8">There's already a share of this dress in your cart.</p>

<?php } else { 

	$share = $GLOBALS['CC_POST_DATA']['share'];
	//i commented this out because i couldnt get the icon tempalte part to include in this function - and it has to be a child of the p
	//echo ws_ifdef_concat('<p class="h7 uppercase">SHARE: <span class="h8 numerals">',$share->get_price_html(),'</span></p>'); ?>
	
	<p class="h7" ><span class="uppercase">SHARE: </span>
		<span class="h8 numerals"><?php echo $share->get_price_html(); ?>&nbsp;&nbsp;&nbsp;</span>
	 	<span class="icon svg popover-white icon-small cursor-pointer" data-toggle="popover" data-placement="bottom" title="Purchase a Share" data-content="Pre-reserve up to 5 times per season, and 24 hours in advance any time the dress is available." data-trigger="focus-broken" tabindex="0"><?php get_template_part('_icons/question'); ?></span> 
	 </p>


	<form class="cart" method="post" enctype='multipart/form-data'>
	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $share->id ); ?>" />

	 	<button type="submit" class="single_add_to_cart_button button alt"><?php echo cc_booking_prompt_string('Share'); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

<? } ?>


<?php // add the help tooltip ?>
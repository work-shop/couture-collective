<?php
	$owned = wc_customer_bought_product(
		$GLOBALS['CC_POST_DATA']['user']->user_email,
		$GLOBALS['CC_POST_DATA']['user']->ID,
		$GLOBALS['CC_POST_DATA']['share']->id
	);
	
	$user = $GLOBALS['CC_POST_DATA']['user'];
	$sale = $GLOBALS['CC_POST_DATA']['sale'];

	/**
	 * @var $share_price float the price of a share in this dress. 
	 */
	$share_price = get_field('dress_share_price', get_the_ID() );

	/**
	 * @var $sale_price float the price of the end-of-season sale for this dress. 
	 */
	$sale_price = get_field('dress_sale_price', get_the_ID() );

	/**
	 * @var $rental_price float the price of a rental in the dress.
	 */
	$rental_price = get_field('dress_rental_price', get_the_ID() );
?>
<ul id="tabs-nav" class="list-inline<?php if ( $owned ) { ?> owned<?php } ?>">
	<?php if ( $owned ) { ?>
		<li class="active h7"><span class="uppercase">My Reservations</span><br /><span class="h8 numerals">&nbsp;</span></li>
	<?php } else { ?>
		<li class="active h7"><span class="uppercase">Share</span><br /><span class="h8 numerals">$<?php echo $share_price; ?></span></li>
	    <li class="h7"><span class="uppercase">Rental</span><br /><span class="h8 numerals">$<?php echo $rental_price; ?></span></li>
    <?php } ?>
    <?php if ( $sale->is_in_stock() ) { ?>
	    <li class="h7"><span class="uppercase">Sale</span><br /><span class="h8 numerals">$<?php echo $sale_price; ?></span></li>
    <?php } ?>
</ul>

<ul id="tab">
<?php
	
	if ( $owned ) {
		get_template_part('_partials/dress/dress','owned');
	} else {
		get_template_part('_partials/dress/dress','share');
		get_template_part('_partials/dress/dress','rental');
	}
	if ( $sale->is_in_stock() ) {
		get_template_part('_partials/dress/dress','sale');	
	}

?>
</ul>
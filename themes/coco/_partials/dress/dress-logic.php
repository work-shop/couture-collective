<?php
	$owned = $GLOBALS['CC_POST_DATA']['owned'];	
	$in_stock = $GLOBALS['CC_POST_DATA']['sale']->is_in_stock();
	$active = $GLOBALS['CC_POST_DATA']['active'];
	$user = $GLOBALS['CC_POST_DATA']['user'];
	$sale = $GLOBALS['CC_POST_DATA']['sale'];

	/**
	 * @var $dry_cleaning_price float the handling surcharge for this dress. 
	 */
	$dry_cleaning_price = get_field('dress_dry_cleaning_price', get_the_ID() );

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

	if ( !cc_user_is_guest() && ( $active || $in_stock ) ) {
?>



<ul id="tabs-nav" class="list-inline<?php if ( $owned ) { ?> owned<?php } ?>">


	
	<?php if ( $active ) { ?>

		<?php if ( $owned ) { ?>

			<li class="active h7"><span class="uppercase m3">Reservations</span><br/ ><span class="h8 numerals"><?php echo wc_trim_zeros( wc_price( $dry_cleaning_price ) ); ?></span></li>

		<?php } else { ?>

			<li class="active h7"><span class="uppercase">Share</span><br /><span class="h8 numerals"><?php echo wc_trim_zeros( wc_price( $share_price) ); ?></span></li>
			<li class="h7"><span class="uppercase">Rental</span><br /><span class="h8 numerals"><?php echo wc_trim_zeros(wc_price( $rental_price)); ?></span></li>

		<?php } ?>

		<?php if ( $in_stock ) { ?>

			<li class="h7"><span class="uppercase">Sale</span><br /><span class="h8 numerals small-xs"><?php echo wc_trim_zeros( wc_price( $sale_price)); ?></span></li>

		<?php } ?>

	<?php } else if ( $in_stock ) { ?>

		<li class="active h7"><span class="uppercase">Rental</span><br /><span class="h8 numerals"><?php echo wc_trim_zeros(wc_price( $rental_price)); ?></span></li>
		<li class="h7"><span class="uppercase">Sale</span><br /><span class="h8 numerals small-xs"><?php echo wc_trim_zeros( wc_price( $sale_price)); ?></span></li>


	<?php } ?>

</ul>

<ul id="tab">

<?php
	
	if ( $active ) {


		if ( $owned ) {

			get_template_part('_partials/dress/dress','owned');

		} else {

			get_template_part('_partials/dress/dress','share');
			get_template_part('_partials/dress/dress','rental');

		}

		if ( $in_stock ) {

			get_template_part('_partials/dress/dress','sale');

		}

	} else if ( $in_stock ) {

		get_template_part('_partials/dress/dress','rental');
		get_template_part('_partials/dress/dress','sale');

	}

?>

</ul>

<?php } else { ?>





<?php } ?>
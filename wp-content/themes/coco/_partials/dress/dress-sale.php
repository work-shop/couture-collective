<div class="row">
<div class="col-sm-12 col-lg-12" style="border-top:3px solid #aaa;">
<?php
	$user = $GLOBALS['CC_POST_DATA']['user'];
	$sale = $GLOBALS['CC_POST_DATA']['sale'];


	if ( woocommerce_customer_bought_product( $user->email, $user->ID, $sale->ID ) ) {

		get_template_part('_partials/dress/dress', 'sale-sold');

	} else if ( $sale->is_in_stock() ) {

		get_template_part('_partials/dress/dress', 'sale-unsold');

	}

?>
</div>
</div>
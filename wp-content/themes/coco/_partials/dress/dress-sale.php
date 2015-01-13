
<?php

	$user = $GLOBALS['CC_POST_DATA']['user'];
	$sale = $GLOBALS['CC_POST_DATA']['sale'];


	if ( wc_customer_bought_product( $user->user_email, $user->ID, $sale->id ) ) { ?>
		
		<div class="row">
		<div class="col-sm-12">
		
			<hr />		
			<?php get_template_part('_partials/dress/dress', 'sale-sold'); ?>
			
		</div>
		</div>		
		

	<?php } else if ( $sale->is_in_stock() ) { ?>

		<div class="row">
		<div class="col-sm-12">
		
			<hr />		
			<?php get_template_part('_partials/dress/dress', 'sale-unsold'); ?>
			
		</div>
		</div>		

	<?php }
	else{
		
	}

?>

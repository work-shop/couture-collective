<li class="tab">
<?php

	$user = $GLOBALS['CC_POST_DATA']['user'];
	$sale = $GLOBALS['CC_POST_DATA']['sale'];


	if ( wc_customer_bought_product( $user->user_email, $user->ID, $sale->id ) ) { ?>
	
		<?php get_template_part('_partials/dress/dress', 'sale-sold'); ?>
	

	<?php } else if ( $sale->is_in_stock() ) { ?>
			
		<?php get_template_part('_partials/dress/dress', 'sale-unsold'); ?>	

	<?php }
	else{
		
	}

?>
</li>
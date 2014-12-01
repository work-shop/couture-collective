<?php
	$owned = woocommerce_customer_bought_product(
		$GLOBALS['CC_POST_DATA']['user']->email,
		$GLOBALS['CC_POST_DATA']['user']->ID,
		$GLOBALS['CC_POST_DATA']['share']->ID
	);

	if ( $owned ) {
		get_template_part('_partials/dress/dress','owned');
		get_template_part('_partials/dress/dress','sale');
	} else {
		get_template_part('_partials/dress/dress','share');
		get_template_part('_partials/dress/dress','rental');
		get_template_part('_partials/dress/dress','sale');
	}

	

?>
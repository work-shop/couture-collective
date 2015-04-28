<?php
	// $owned = wc_customer_bought_product(
	// 	$GLOBALS['CC_POST_DATA']['user']->user_email,
	// 	$GLOBALS['CC_POST_DATA']['user']->ID,
	// 	$GLOBALS['CC_POST_DATA']['share']->id
	// );

	$owned = $GLOBALS['CC_POST_DATA']['owned'];



	// echo '<h1>'.$GLOBALS['CC_POST_DATA']['user']->user_email.'</h1>';
	// echo '<h1>'.$GLOBALS['CC_POST_DATA']['user']->ID.'</h1>';
	// echo '<h1>'.$GLOBALS['CC_POST_DATA']['share']->id.'</h1>';
	// echo '<h1>'.$owned.'</h1>';

	if ( $owned ) {

		get_template_part('_partials/dress/dress','owned');
		get_template_part('_partials/dress/dress','sale');

	} else {

		get_template_part('_partials/dress/dress','share');
		get_template_part('_partials/dress/dress','rental');
		get_template_part('_partials/dress/dress','sale');
		
	}

	

?>
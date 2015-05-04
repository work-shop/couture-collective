<?php
	$owned = wc_customer_bought_product(
		$GLOBALS['CC_POST_DATA']['user']->user_email,
		$GLOBALS['CC_POST_DATA']['user']->ID,
		$GLOBALS['CC_POST_DATA']['share']->id
	);

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

	if ( $owned ) {

		get_template_part('_partials/dress/dress','owned');
		get_template_part('_partials/dress/dress','sale');

	} else {

		get_template_part('_partials/dress/dress','share');
		get_template_part('_partials/dress/dress','rental');
		get_template_part('_partials/dress/dress','sale');
		
	}

	

?>
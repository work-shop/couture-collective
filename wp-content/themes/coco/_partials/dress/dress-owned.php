<?php
	
	$rental = $GLOBALS['CC_POST_DATA']['rental'];

	// next_day_rental thing
	get_template_part('_partials/dress/dress', 'next-day-rental-make' );
	get_template_part('_partials/dress/dress', 'prereservation-make');
	get_template_part('_partials/dress/dress', 'prereservation-history');






?>

<?php $bookings = WC_Bookings_Controller::get_bookings_for_user( $GLOBALS['CC_POST_DATA']['user']->ID ); ?>
<?php if ( !empty($bookings) ) : ?>
<div class="row">
<div class="col-sm-12">
<h6>MY RENTALS:</h6>
<ul>
<?php
	//var_dump($bookings);

	foreach ( $bookings as $i => $booking ) {
		$GLOBALS['CC_POST_DATA']['current_booking'] = $booking;
		get_template_part( '_partials/reservation/reservation', 'line-item' );
		unset( $GLOBALS['CC_POST_DATA']['current_booking'] );
	}

?>
</ul>
</div>
</div>
<?php endif; ?>
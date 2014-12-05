
<?php 
	$bookings = array();

	$all_b = WC_Bookings_Controller::get_bookings_for_user( $GLOBALS['CC_POST_DATA']['user']->ID ); 
	foreach ($all_b as $i => $booking) {
		if ( ($booking->get_product_id() == $GLOBALS['CC_POST_DATA']['rental']->id )
		     && $booking->status != 'cancelled' ) {
			array_push( $bookings, $booking );
		}
	}

?>
<?php if ( !empty($bookings) ) : ?>
<div class="row dress-rentals">
<div class="col-sm-12">
<h6>MY RENTALS:</h6>
<ul>
<?php
	//var_dump($bookings);

	foreach ( $bookings as $i => $booking ) {
		$GLOBALS['CC_POST_DATA']['current_booking'] 	= $booking;
		$GLOBALS['CC_POST_DATA']['current_order'] 	= new WC_Order( $booking->order_id );


		get_template_part( '_partials/reservation/reservation', 'line-item' );
		

		unset( $GLOBALS['CC_POST_DATA']['current_booking'] );
		unset( $GLOBALS['CC_POST_DATA']['current_order'] );
	}

?>
</ul>
</div>
</div>
<?php endif; ?>
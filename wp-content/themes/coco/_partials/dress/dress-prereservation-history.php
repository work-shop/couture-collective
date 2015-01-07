<?php 
	$bookings = array();
	$reservation_type = 'Prereservation';

	$all_b = WC_Bookings_Controller::get_bookings_for_user( $GLOBALS['CC_POST_DATA']['user']->ID ); 
	foreach ($all_b as $i => $booking) {
		if ( ($booking->get_product_id() == $GLOBALS['CC_POST_DATA']['rental']->id )
		     && $booking->status != 'cancelled' ) {

			if ( CC_Controller::get_booking_type( $booking ) == $reservation_type ) {
				array_push( $bookings, $booking );
			}	
		}
	}

?>

<div class="row dress-prereservations">
<div class="col-sm-12">

<?php if (!empty($bookings) ) { ?>

	<?php

		foreach ($bookings as $booking) {
			$GLOBALS['CC_POST_DATA']['current_booking'] = $booking;
			$GLOBALS['CC_POST_DATA']['current_order'] = new WC_Order( $booking->order_id );
			$GLOBALS['CC_POST_DATA']['reservation_type'] = $reservation_type;

			get_template_part( '_partials/reservation/prereservation', 'line-item');

			unset($GLOBALS['CC_POST_DATA']['current_booking']);
			unset($GLOBALS['CC_POST_DATA']['current_order']);
			unset($GLOBALS['CC_POST_DATA']['reservation_type']);
		}



	?>

<?php } else { ?>

	<h3 class="serif centered">You haven't prereserved this dress yet.</h3>

<?php } ?>
</div>
</div>



<?php if (!is_single()) : ?>
	<div class="row dress-prereservations">
	<div class="col-sm-12">
	<hr class="brand" />
	<?php if ( 5 > count( $bookings ) ) { ?>

		<h3 class="pink centered">+ Add Prereservation</h3>

	<?php } else { ?>

		<h3 class="gray centered small">You've prereserved this dress 5 times.</h3>

	<?php } ?>
	</div>
	</div>
<?php endif; ?>

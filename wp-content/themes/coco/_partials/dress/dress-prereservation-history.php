<?php 
	//$bookings = array();
	

	// $all_b = WC_Bookings_Controller::get_bookings_for_user( $GLOBALS['CC_POST_DATA']['user']->ID ); 
	// foreach ($all_b as $i => $booking) {
	// 	if ( ($booking->get_product_id() == $GLOBALS['CC_POST_DATA']['rental']->id )
	// 	     && $booking->status != 'cancelled' ) {

	// 		if ( CC_Controller::get_booking_type( $booking ) == $reservation_type ) {
	// 			array_push( $bookings, $booking );
	// 		}	
	// 	}
	// }

	$reservation_type = 'Prereservation';
	$bookings = $GLOBALS['CC_POST_DATA']['prereservations'];
	

	//var_dump( CC_Controller::get_prereservations_for_dress_rental( $GLOBALS['CC_POST_DATA']['rental']->id, $GLOBALS['CC_POST_DATA']['user']->ID ) );

	//var_dump( get_post_meta( $GLOBALS['CC_POST_DATA']['rental']->id ) );
	//var_dump( $GLOBALS['CC_POST_DATA']['rental']->get_resources() );

?>

<div class="row dress-prereservations">
<div class="col-sm-12">

<?php if (!empty($bookings) ) { ?>

	<?php

		foreach ($bookings as $booking) {
			$GLOBALS['CC_POST_DATA']['current_booking'] = $booking;
			//var_dump( get_post_meta( $booking->id ) );
			$GLOBALS['CC_POST_DATA']['current_order'] = new WC_Order( $booking->order_id );
			//var_dump( $GLOBALS['CC_POST_DATA']['current_order'] );
			$GLOBALS['CC_POST_DATA']['reservation_type'] = $reservation_type;

			get_template_part( '_partials/reservation/prereservation', 'line-item');

			unset($GLOBALS['CC_POST_DATA']['current_booking']);
			unset($GLOBALS['CC_POST_DATA']['current_order']);
			unset($GLOBALS['CC_POST_DATA']['reservation_type']);
		}



	?>

<?php } else { ?>

	<h3 class="centered">You haven't prereserved this dress yet.</h3>

<?php } ?>
</div>
</div>



<?php if (!is_single()) : ?>
	<div class="row dress-prereservations">
	<div class="col-sm-12">
	<hr class="brand" />
	<?php if ( 5 > count( $bookings ) ) { 

		$perma = get_post_permalink( $GLOBALS['CC_CLOSET_DATA']['dress']->ID );
	?>

		<h3 class="centered"><a href="<?php echo $perma; ?>">+ Add Prereservation</a></h3>

	<?php } else { ?>

		<h3 class="gray centered small">You've prereserved this dress 5 times.</h3>

	<?php } ?>
	</div>
	</div>
<?php endif; ?>

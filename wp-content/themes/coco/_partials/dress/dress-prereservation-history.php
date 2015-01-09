<?php 

	$bookings = $GLOBALS['CC_POST_DATA']['prereservations'];

?>

<div class="row dress-prereservations">
<div class="col-sm-12">

<p class="h7 uppercase m">My Pre-reservations:</p>

<?php if (!empty($bookings) ) { ?>

	<?php

		foreach ($bookings as $booking) {
			$GLOBALS['CC_POST_DATA']['current_booking'] = $booking;
			//var_dump( get_post_meta( $booking->id ) );
			$GLOBALS['CC_POST_DATA']['current_order'] = new WC_Order( $booking->order_id );
			//$GLOBALS['CC_POST_DATA']['reservation_type'] = "Prereservation";
			//var_dump( $GLOBALS['CC_POST_DATA']['current_order'] );

			get_template_part( '_partials/reservation/prereservation', 'line-item');

			unset($GLOBALS['CC_POST_DATA']['current_booking']);
			unset($GLOBALS['CC_POST_DATA']['current_order']);
		}

	?>

<?php } else { ?>

	<p class="h7 uppercase">You haven't prereserved this dress yet.</p>

<?php } ?>
</div>
</div>


<?php if (!is_single()) : ?>
	<div class="row prereservation-status">
	<div class="col-sm-12">
	
	<?php if ( 5 > count( $bookings ) ) { 

		$perma = get_post_permalink( $GLOBALS['CC_CLOSET_DATA']['dress']->ID );
	?>

		<p class="h7 uppercase m2"><?php echo (5-count( $bookings )); ?> Pre-reservations remaining</p> 
		<a href="<?php echo $perma; ?>" class="button-brand">+ Add <?php echo $GLOBALS['CC_POST_DATA']['reservation_type'] ?></a>

	<?php } else { 
		
		if($GLOBALS['CC_POST_DATA']['reservation_type'] == 'rental'): ?>

		<p class="h8">This dress has been pre-reserved the maximum number of times. You may delete an order and book a new one, or <a href="<?php bloginfo('url');?>/contact" target="_blank" class="underline">contact us</a> to change your order. </p>


	<?php endif; } ?>
	</div>
	</div>
<?php endif; ?>

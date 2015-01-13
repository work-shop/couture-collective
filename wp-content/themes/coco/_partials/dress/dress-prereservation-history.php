<?php 

	$bookings = $GLOBALS['CC_POST_DATA']['prereservations'];

?>

<div class="row dress-prereservations">
<div class="col-sm-12">

<p class="h7 uppercase m2">My <?php echo $GLOBALS['CC_POST_DATA']['reservation_type'] ?>s: </p>

<?php if (!empty($bookings) ) { ?>

	<?php

		//var_dump( $bookings );

		usort( $bookings, function( $a,$b ) {
			$da = $a->custom_fields["_booking_start"];
			$db = $b->custom_fields["_booking_start"];

			return ( $da > $db ) ? 1 : (( $da < $db ) ? -1 : 0);
		});

		foreach ($bookings as $booking) {
			$GLOBALS['CC_POST_DATA']['current_booking'] = $booking;
			$GLOBALS['CC_POST_DATA']['current_order'] = new WC_Order( $booking->order_id );

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
	
	<?php if ( 5 > count( $bookings ) ){ 

		$perma = get_post_permalink( $GLOBALS['CC_CLOSET_DATA']['dress']->ID );
	?>
	
		<?php if($GLOBALS['CC_POST_DATA']['reservation_type'] != 'Rental'): ?>

			<p class="h7 uppercase m2"><?php echo (5-count( $bookings )); ?> Pre-reservations remaining</p>
		
		<?php endif; ?>
		 
		<a href="<?php echo $perma; ?>" class="button-brand">+ Add <?php echo $GLOBALS['CC_POST_DATA']['reservation_type'] ?></a>

	<?php } else { 
		
		if($GLOBALS['CC_POST_DATA']['reservation_type'] != 'Rental'): ?>

		<p class="h8">This dress has been pre-reserved the maximum number of times. You may delete an order and book a new one, or <a href="<?php bloginfo('url');?>/contact" target="_blank" class="underline">contact us</a> to change your order. </p>


	<?php endif; } ?>
	</div>
	</div>
<?php endif; ?>

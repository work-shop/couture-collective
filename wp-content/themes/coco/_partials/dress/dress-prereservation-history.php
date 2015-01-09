<?php 

	$bookings = $GLOBALS['CC_POST_DATA']['prereservations'];

?>

<div class="row dress-prereservations">
<div class="col-sm-12">

<hr class="half" />

<p class="h7 uppercase m">Pre-reserved for me:</p>

<?php if (!empty($bookings) ) { ?>

	<?php

		foreach ($bookings as $booking) {
			$GLOBALS['CC_POST_DATA']['current_booking'] = $booking;
			//var_dump( get_post_meta( $booking->id ) );
			$GLOBALS['CC_POST_DATA']['current_order'] = new WC_Order( $booking->order_id );
			//var_dump( $GLOBALS['CC_POST_DATA']['current_order'] );

			get_template_part( '_partials/reservation/prereservation', 'line-item');

			unset($GLOBALS['CC_POST_DATA']['current_booking']);
			unset($GLOBALS['CC_POST_DATA']['current_order']);
		}

	?>

<?php } else { ?>

	<!-- <p class="h7 uppercase">You haven't prereserved this dress yet.</p> -->

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

		<h3 class=""><a href="<?php echo $perma; ?>">+ Add <?php echo $GLOBALS['CC_POST_DATA']['reservation_type'] ?></a></h3>

	<?php } else { ?>

		<h3 class="">You've pre-reserved this dress 5 times.</h3>

	<?php } ?>
	</div>
	</div>
<?php endif; ?>

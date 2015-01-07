
<?php 

	$reservation_type = $GLOBALS['CC_POST_DATA']['reservation_type'];
	$booking = $GLOBALS['CC_POST_DATA']['current_booking']; 
	$order = $GLOBALS['CC_POST_DATA']['current_order'];

	$status_guard = 'wc-completed' == $order->post_status;
	$timing_guard = !CC_Controller::booking_is_modifiable( $booking );

if ( $status_guard || $timing_guard ) { ?>
	<?php // editing and deletion of this order is no longer possible... it has been processed or its margin has elapsed. ?>

	<li class="reservation-item <?php echo ( $status_guard ) ? 'complete' : ''; ?> unmodifiable">
	<div class="row">
		<div class="col-sm-12">
		<p class="reservation-date"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?> - <?php echo date( 'F jS, Y', strtotime( $booking->get_end_date() ) ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
		<p class="reservation-destination"><small>Shipping to <?php echo $order->get_shipping_address(); ?></small></p>
		<div>
	</div>
	</li>


<?php } else { ?>
	<?php // editing / deletion of the order is possible. ?>

	<li class="reservation-item modifiable incomplete">
	<div class="row">
		<div class="col-sm-12">
		<p class="reservation-date"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?> - <?php echo date( 'F jS, Y', strtotime( $booking->get_end_date() ) ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
		<p class="reservation-destination"><small>Shipping to <?php echo $order->get_shipping_address(); ?></small></p>
		<div>

		<!-- <div class="col-sm-12">
			<button class="edit-reservation button">E</button>
			<div class="edit-subscription-modal">
				<?php // get_template_part('_partials/reservation/prereservation', 'edit-modal'); ?>
			</div> 
		<div> -->

		<div class="col-sm-12">
			<form class="cc-cancel-reservation-form" method='POST' action='<?php echo home_url().'/cancel-reservation'?>'>
				<input type="hidden" class="referring-page" name="referring-page" value="<?php echo get_permalink(); ?>" />
				<input type="hidden" class="user-id" name="user-id" value="<?php echo esc_attr( $GLOBALS['CC_POST_DATA']['user']->ID ); ?>" />
				<input type="hidden" class="booking-id" name="booking-id" value="<?php echo esc_attr( $booking->get_ID() ); ?>" />
				<button type="submit" class="cancel-reservation button">Cancel</button>
			</form>
		<div>
	</div>
	</li>


<?php } ?>

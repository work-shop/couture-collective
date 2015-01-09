
<?php 

	$reservation_type = $GLOBALS['CC_POST_DATA']['reservation_type'];
	$booking = $GLOBALS['CC_POST_DATA']['current_booking']; 
	$order = $GLOBALS['CC_POST_DATA']['current_order'];
	//var_dump($booking);
	$status_guard = 'complete' == $booking->post->post_status; 
	$timing_guard = !CC_Controller::booking_is_modifiable( $booking );

if ( $status_guard || $timing_guard ) { ?>
	<?php // editing and deletion of this order is no longer possible... it has been processed or its margin has elapsed. ?>

	<div class="reservation-item <?php echo ( $status_guard ) ? 'complete' : ''; ?> unmodifiable bordered-pink-bottom m2">
		<div class="row">
			<div class="col-sm-12">
				
				<p class="reservation-date h3 <?php if(!$status_guard): ?>m0<?php endif; ?>"><?php if($status_guard): ?><span class="icon pink-darker icon-left" data-icon="%"></span><?php endif; ?><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?></p>
			</div>
		</div>
	
		<?php if (!$status_guard) : ?>

		<div class="row">
			<div class="col-sm-8 col-sm-offset-1">
				<p class="reservation-destination h9 indent">Shipping to <?php echo $order->get_shipping_address(); ?></p>
			</div>
		</div>

		<?php endif; ?>		

	</div>


<?php } else { ?>
	<?php // editing / deletion of the order is possible. ?>

	<div class="reservation-item modifiable incomplete bordered-pink-bottom m2">
	
		<div class="row">
			<div class="col-sm-12">
			<p class="reservation-date h3 m0"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?></p>
			</div>
		</div>
	
		<div class="row">
			<div class="col-sm-8 col-sm-offset-1">
				<p class="reservation-destination h9 indent">Shipping to <?php echo $order->get_shipping_address(); ?></p>
			</div>
	
			<!-- <div class="col-sm-12">
				<button class="edit-reservation button">E</button>
				<div class="edit-subscription-modal">
					<?php // get_template_part('_partials/reservation/prereservation', 'edit-modal'); ?>
				</div> 
			<div> -->
	
			<div class="col-sm-2 col-sm-offset-1">
				<form class="cc-cancel-reservation-form" method='POST' action='<?php echo home_url().'/cancel-reservation'?>'>
					<input type="hidden" class="referring-page" name="referring-page" value="<?php echo get_permalink(); ?>" />
					<input type="hidden" class="user-id" name="user-id" value="<?php echo esc_attr( $GLOBALS['CC_POST_DATA']['user']->ID ); ?>" />
					<input type="hidden" class="booking-id" name="booking-id" value="<?php echo esc_attr( $booking->get_ID() ); ?>" />
					<input type="hidden" class="booking-id" name="reservation_type" value="<?php echo esc_attr( $reservation_type ); ?>" />
					<button type="submit" class="cancel-reservation button icon-button right tooltip-white" data-toggle="tooltip" data-placement="bottom" title="cancel reservation"><span class="icon svg icon-small"><?php get_template_part('_icons/remove'); ?></span></button>
				</form>
			</div>
			
		</div>
	</div>

<?php } ?>

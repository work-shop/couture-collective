
<?php 

	$booking = $GLOBALS['CC_POST_DATA']['current_booking']; 
	$order = $GLOBALS['CC_POST_DATA']['current_order'];

if ( 'wc-completed' == $order->post_status ) { ?>
	<?php // editing and deletion of this order is no longer possible... it has been processed ?>

	<li class="reservation-item complete">
	<div class="row">
		<div class="col-sm-12">
		<p class="reservation-date"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
		<p class="reservation-destination"><small>Shipping to <?php echo $order->get_shipping_address(); ?></small></p>
		<div>

		<!-- <div class="col-sm-1">
			<button class="edit-reservation button inactive">E</button>
		<div>

		<div class="col-sm-1">
			<button class="cancel-reservation button inactive">R</button>
		<div> -->
	</div>
	</li>


<?php } else { ?>
	<?php // editing / deletion of the order is possible. ?>

	<li class="reservation-item incomplete">
	<div class="row">
		<div class="col-sm-12">
		<p class="reservation-date"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
		<p class="reservation-destination"><small>Shipping to <?php echo $order->get_shipping_address(); ?></small></p>
		<div>

		<div class="col-sm-12">
			<button class="edit-reservation button">E</button>
			<div class="edit-subscription-modal">
				<?php get_template_part('_partials/reservation/rental', 'edit-modal'); ?>
			</div> 
		<div>

		<div class="col-sm-12">
			<form class="cc-cancel-reservation-form" method='POST' action='<?php echo home_url().'/cancel-reservation'?>'>
				<input type="hidden" class="referring-page" name="referring-page" value="<?php echo get_permalink(); ?>" />
				<input type="hidden" class="user-id" name="user-id" value="<?php echo esc_attr( $GLOBALS['CC_POST_DATA']['user']->ID ); ?>" />
				<input type="hidden" class="booking-id" name="booking-id" value="<?php echo esc_attr( $booking->get_ID() ); ?>" />
				<!-- add and check a nonce -->
				<button type="submit" class="cancel-reservation button">Cancel</button>
			</form>
		<div>
	</div>
	</li>


<?php } ?>

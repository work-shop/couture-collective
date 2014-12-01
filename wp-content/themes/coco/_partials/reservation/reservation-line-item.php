
<?php 

	$booking = $GLOBALS['CC_POST_DATA']['current_booking']; 
	$order = new WC_Order( $booking->order_id );

	var_dump( $booking );
	var_dump( $order );


if ( 'wc-completed' == $order->post_status ) { ?>
	<?php // editing and deletion of this order is no longer possible... it has been processed ?>

	<li class="reservation-item">
	<div class="row">
		<div class="col-sm-12">
		<p class="reservation-date"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
		<p class="reservation-destination"><small>Shipping to <?php echo $order->get_shipping_address(); ?></small></p>
		<div>

		<div class="col-sm-1">
			<button class="edit-reservation button inactive">E</button>
		<div>

		<div class="col-sm-1">
			<button class="cancel-reservation button inactive">R</button>
		<div>
	</div>
	</li>


<?php } else { ?>
	<?php // editing / deletion of the order is possible. ?>

	<li class="reservation-item">
	<div class="row">
		<div class="col-sm-12">
		<p class="reservation-date"><?php echo date( 'F jS, Y', strtotime( $booking->get_start_date() ) ); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
		<p class="reservation-destination"><small>Shipping to <?php echo $order->get_shipping_address(); ?></small></p>
		<div>

		<div class="col-sm-1">
			<button class="edit-reservation button">E</button>
			<div class="edit-subscription-modal modal hidden">
				<?php get_template_part('_partials/reservation/reservation', 'edit-modal'); ?>
			</div>
		<div>

		<div class="col-sm-1">
			<button class="cancel-reservation button">R</button>
		<div>
	</div>
	</li>


<?php } ?>

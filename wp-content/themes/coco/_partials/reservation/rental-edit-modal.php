<?php
	
	$rental = $GLOBALS['CC_POST_DATA']['rental'];
	$booking_form = new WC_Booking_Form( $rental );
	$booking = $GLOBALS['CC_POST_DATA']['current_booking']; 
	$order = $GLOBALS['CC_POST_DATA']['current_order'];

	//var_dump($GLOBALS['CC_POST_DATA']['user']);

?>
<div class="row">
<div class="col-sm-12">
<?php 
	// echo prereserved date
	// echo ship to date.
?>
</div>

<div class="col-sm-12 edit-modal-calendar-field">

	<form class="cart" method="post" enctype='multipart/form-data'>

	 	<div id="wc-bookings-booking-form" class="wc-bookings-booking-form" style="display:none">

	 		<?php $booking_form->output(); ?>

	 		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 		<div class="wc-bookings-booking-cost" style="display:none"></div>

		</div>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $rental->id ); ?>" />
		<input type="hidden" class="user-requested" name="user-requested" value="<?php echo esc_attr( $GLOBALS['CC_POST_DATA']['user']->ID ); ?>" />
		<input type="hidden" class="booking-requested" name="booking-requested" value="<?php echo esc_attr( $booking->get_ID() ); ?>" />
		<!-- <input type="hidden" class="order-requested" name="order-requested" value="<?php echo esc_attr( $order->ID ); ?>" /> -->
		<div id="ajax-response"></div>
		<?php //wp_nonce_field('');  add this for CSRF security. ?>
	 	<button disabled="disabled" class="wc-bookings-booking-form-button single_add_to_cart_button button alt update-reservation" style="display:none"><?php echo 'UPDATE RESERVATION'; ?></button>
	 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>



<?php 
	// echo dress-metadata
	// echo save changes button
	// echo cancel button
?>
</div>
</div>
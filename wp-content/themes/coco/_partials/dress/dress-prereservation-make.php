<?php

	$rental = $GLOBALS['CC_POST_DATA']['rental'];
	$booking_form = new WC_Booking_Form( $rental );

?>

<div class="row">
<div class="col-sm-12">
<?php 
	// echo prereserved date
	// echo ship to date.
?>
</div>

<div class="col-sm-12 prereservation-calendar-field">

	<form method="post" enctype='multipart/form-data' action="<?php echo home_url().'/make-reservation'; ?>" >

	 	<div id="wc-bookings-booking-form" class="wc-bookings-booking-form" style="display:none">

	 		<?php $booking_form->output(); ?>

	 		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 		<div class="wc-bookings-booking-cost" style="display:none"></div>

		</div>

		<input type="hidden" id="pre-reservation-add-to-cart" name="add-to-cart" value="<?php echo esc_attr( $rental->id ); ?>" />
		<input type="hidden" name="booking-id" value="<?php echo esc_attr( $rental->id ); ?>" />
		<input type="hidden" class="user-requested" name="user-requested" value="<?php echo esc_attr( $GLOBALS['CC_POST_DATA']['user']->ID ); ?>" />

		<?php //wp_nonce_field('');  add this for CSRF security. ?>
	 	<button id="make-pre-reservation" class="button alt pre-reservation" ><?php echo 'PRE-RESERVE'; ?></button>
	 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>



<?php 
	// echo dress-metadata
	// echo save changes button
	// echo cancel button
?>
</div>
</div>
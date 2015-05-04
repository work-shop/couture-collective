<p class="h8 m2">Select date for your one-night rental.</p>

<?php 

global $woocommerce;

$reservation_type = 'Rental';

$rental = $GLOBALS['CC_POST_DATA']['rental'];
$booking_form = new CC_Make_Reservation_Form( $rental, $reservation_type );

do_action( 'woocommerce_before_add_to_cart_form' ); 

?>

<div class="dress-calendar">
		<form class="cart" method="post" enctype='multipart/form-data'>

		 	<div id="wc-bookings-booking-form" class="wc-bookings-booking-form cc-make-reservation-form" style="display:none">

		 		<?php $booking_form->output(); ?>

		 		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		 		<div class="wc-bookings-booking-cost" style="display:none"></div>

			</div>
			<div class="row">
				<div class="col-xs-4">
					<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $rental->id ); ?>" />
					<input type="hidden" name="reservation_type" value="<?php echo $reservation_type; ?>" />
				 	<button disabled="disabled" type="submit" class="wc-bookings-booking-form-button single_add_to_cart_button button alt" style="display:none"><?php echo cc_booking_prompt_string($reservation_type); ?></button>
				 </div>
			 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
				<p class="col-xs-8 h8 righted">Additional cleaning charges apply.</p>
			</div>
		</form>
	</div>
</div>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php

$rental = $GLOBALS['CC_POST_DATA']['rental'];
$reservation_type = "Nextday";

// int representing the number of prereservations this user has left for this dress.
$remaining_preresevations = CC_Controller::$maximum_prereservations - count( $GLOBALS['CC_POST_DATA']['prereservations'] );

?>



<div class="row">
	<div class="col-sm-12">
	<?php if ( $GLOBALS['CC_POST_DATA']['tomorrow'] ) { ?>

		<?php $date = CC_Controller::get_next_day_reservation( $rental ); ?>

		<p class="remaining-reservations h7 uppercase m2">
		<?php 
		if($remaining_preresevations > 0){
			echo $remaining_preresevations;?> reservations remaining
			<?php } else{ ?>
				<p class="h8">This dress has been reserved the maximum number of times. You may delete an order and book a new one, or <a href="<?php bloginfo('url');?>/contact" target="_blank" class="underline">contact us</a> to change your order. </p>
			<?php } ?>
		</p>
		<div class="row">
			<div class="col-sm-4 m1">
				<form class="cart" method="post" enctype="multipart/form-data">

					<input type="hidden" value="<?php echo $date['year']; ?>" name="wc_bookings_field_start_date_year" class="booking_date_year" />
					<input type="hidden" value="<?php echo $date['month']; ?>" name="wc_bookings_field_start_date_month" class="booking_date_month" />
					<input type="hidden" value="<?php echo $date['day']; ?>" name="wc_bookings_field_start_date_day" class="booking_date_day" />
					<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $rental->id ); ?>">
					<input type="hidden" name="reservation_type" value="<?php echo $reservation_type; ?>">
				 	<button type="submit" class="wc-bookings-booking-form-button single_add_to_cart_button button alt"><?php echo cc_booking_prompt_string( $reservation_type ); ?></button>
				 	
			 	</form>
		 	</div>

		 	<div class="col-sm-offset-1 col-sm-7">
			 	<p class="h8">
				 	Reserve this dress for delivery tomorrow. This doesn't count against your reservations of this dress.
				</p>
			</div>
		</div>

	<?php } else { ?>

		<p class="h8 text">This dress is not available tomorrow</p>

	<?php } ?>

	<hr />

	</div>
</div>


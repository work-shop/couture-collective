<?php

$rental = $GLOBALS['CC_POST_DATA']['rental'];
$reservation_type = "Nextday";

?>



<div class="row">
	<div class="col-sm-12">
	<?php if ( CC_Controller::available_tomorrow( $rental ) ) { ?>

		<?php $date = CC_Controller::get_next_day_reservation( $rental ); ?>

		<p class="h7" ><span class="uppercase">Reserve for Tomorrow: </span>
	 		<span class="icon svg popover-white icon-small cursor-pointer" data-toggle="popover" data-placement="bottom" title="Reserve for Tomorrow" data-content="Reserve this dress for delivery tomorrow. This doesn't count against your pre-reservations of this dress." data-trigger="focus-broken" tabindex="2"><?php get_template_part('_icons/question'); ?></span> 
		</p>

		<form class="cart" method="post" enctype="multipart/form-data">

			<input type="hidden" value="<?php echo $date['year']; ?>" name="wc_bookings_field_start_date_year" class="booking_date_year" />
			<input type="hidden" value="<?php echo $date['month']; ?>" name="wc_bookings_field_start_date_month" class="booking_date_month" />
			<input type="hidden" value="<?php echo $date['day']; ?>" name="wc_bookings_field_start_date_day" class="booking_date_day" />
			<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $rental->id ); ?>">
			<input type="hidden" name="reservation_type" value="<?php echo $reservation_type; ?>">
		 	<button type="submit" class="wc-bookings-booking-form-button single_add_to_cart_button button alt"><?php echo cc_booking_prompt_string( $reservation_type ); ?></button>
		 	
	 	</form>
		

	<?php } else { ?>

		<p class="h8 text">This dress is not available tomorrow</p>

	<?php } ?>

	<hr />

	</div>
</div>


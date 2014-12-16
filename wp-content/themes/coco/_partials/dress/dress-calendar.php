<div class="row">
<div class="col-sm-12 dress-calendar">

<?php 

global $woocommerce;

$reservation_type = 'Rental';

$rental = $GLOBALS['CC_POST_DATA']['rental'];
$booking_form = new CC_Static_Calendar_Form( $rental, $reservation_type );

// do_action( 'woocommerce_before_add_to_cart_form' ); 

?>


<form class="cart" method="post" enctype='multipart/form-data'>

 	<div id="wc-bookings-booking-form" class="wc-bookings-booking-form cc-static-calendar-form" style="display:none">

 		<?php $booking_form->output(); ?>

 		<?php // do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	</div>



 	<?php // do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php // do_action( 'woocommerce_after_add_to_cart_form' ); ?>

</div>
</div>
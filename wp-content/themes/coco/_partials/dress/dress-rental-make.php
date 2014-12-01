<div class="row">
<div class="col-sm-12">

<?php 

global $woocommerce;
$rental = $GLOBALS['CC_POST_DATA']['rental'];
$booking_form = new WC_Booking_Form( $rental );

do_action( 'woocommerce_before_add_to_cart_form' ); 

?>


<form class="cart" method="post" enctype='multipart/form-data'>

 	<div id="wc-bookings-booking-form" class="wc-bookings-booking-form" style="display:none">

 		<?php $booking_form->output(); ?>

 		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

 		<div class="wc-bookings-booking-cost" style="display:none"></div>

	</div>

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $rental->id ); ?>" />
 	<button disabled="disabled" type="submit" class="wc-bookings-booking-form-button single_add_to_cart_button button alt" style="display:none"><?php echo $rental->single_add_to_cart_text(); ?></button>

 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

</div>
</div>
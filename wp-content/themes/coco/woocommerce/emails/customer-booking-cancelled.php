<?php
/**
 * Customer booking cancelled email
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php  
	$dress_id = CC_Controller::get_dress_for_product( $booking->get_product()->id, "rental" );
	$dress_title = get_field('dress_designer', $dress_id);
	$dress_description = get_field('dress_description', $dress_id);


	$booking_type = CC_Controller::get_booking_type( $booking );
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php if ( $booking->get_order() ) : ?>
	<p><?php printf( __( 'Hello %s', 'woocommerce-bookings' ), $booking->get_order()->billing_first_name ); ?></p>
<?php endif; ?>

<p><?php _e( 'Your '.strtolower( cc_booking_noun_string( $booking_type) ).' has been cancelled. The details of the cancelled reservation can be found below.', 'woocommerce-bookings' ); ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php _e( cc_booking_noun_string( $booking_type), 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $dress_title.' - '.$dress_description; ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( cc_booking_noun_string( $booking_type).' Date', 'woocommerce-bookings' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo CC_Controller::get_selected_date( $booking->get_id() ); ?></td>
		</tr>
		<?php if ( $booking->has_persons() ) : ?>
			<?php
				foreach ( $booking->get_persons() as $id => $qty ) :
					if ( 0 === $qty ) {
						continue;
					}

					$person_type = ( 0 < $id ) ? get_the_title( $id ) : __( 'Person(s)', 'woocommerce-bookings' );
			?>
				<tr>
					<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php echo $person_type; ?></th>
					<td style="text-align:left; border: 1px solid #eee;"><?php echo $qty; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<p><?php _e( 'Please contact us if you have any questions or concerns.', 'woocommerce-bookings' ); ?></p>
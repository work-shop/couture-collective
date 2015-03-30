<?php
/**
 * Prereservation Form Class
 */
class CC_Static_Calendar_Form extends WC_Booking_Form {

	/**
	 * Constructor
	 * @param $product WC_Product_Booking
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
	}

	/**
	 *
	 * Void all the scripts that are typically enqueued with a booking form.
	 * @todo Figure out exactly what to do with the fact that the script cannot remove the res. type 
	 * from the DOM. (Accidental / faked submissions OK because no product/redirect data, but still...)
	 */
	public function scripts() {
		parent::scripts();
		wp_register_script('cc-ajax-dequeue-calendar-form', get_template_directory_uri() . '/_/js/ajax/dequeue-calendar-form.js', array( 'jquery', 'jquery-blockui' ));
		wp_enqueue_script('cc-ajax-dequeue-calendar-form');		
	}
}

?>
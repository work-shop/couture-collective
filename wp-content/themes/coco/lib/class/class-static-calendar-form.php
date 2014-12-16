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
	 */
	public function scripts() {
		wp_register_script('cc-ajax-dequeue-calendar-form', get_template_directory_uri() . '/_/js/ajax/dequeue-calendar-form.js', array( 'jquery', 'jquery-blockui' ));
		parent::scripts();
		wp_enqueue_script('cc-ajax-dequeue-calendar-form');
	}

	/**
	 * 
	 * Output the static calendar form.
	 */
	public function output() {
		// $this->add_field( array(
		// 	'name' => 'reservation_type'
		// 	'class' => array('hidden', '')
		// 	'label' =>
		// 	'type' => 'hidden'
		// ));
		parent::output();
	}
}

?>
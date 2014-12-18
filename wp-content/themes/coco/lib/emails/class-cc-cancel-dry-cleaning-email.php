<?php

if ( ! defined('ABSPATH') ) exit;

class CC_Cancel_Dry_Cleaning_Email extends WC_Email {
	/**
	 * @hooked cc_send_dry_cleaning_cancellation_email (params: $booking_id)
	 * Constructor
	 */
	public function __construct() {
		// $this->id = 'cc_dry_cleaning';
		// $this->title = 'Dry Cleaning Notifications';
		// $this->description = 'Dry Cleaning Notifications are sent when a customer rents or prereserves a dress, or updates their Rental or Prereservation.';
		// $this->heading = 'Couture Collective Delivery';
  //   		$this->subject = 'Couture Collective Delivery';

  //   		$this->template_base = get_template_directory_uri() . '/_partials/emails/';
  //   		$this->template_html = 'dry-cleaning-email-template.php';
  //   		$this->template_plain = 'plain/dry-cleaning-email-template.php';

  //   		add_action( 'cc_send_dry_cleaning_cancellation_email', array( $this, 'trigger' ) );
  //   		//add_action('woocommerce_booking_{}_to_{}_notification', array($this, 'trigger') );

  //   		parent::__construct();

  //   		$this->recipient = $this->get_option( 'recipient' );

  //   		if ( !$this->recipient ) {
  //   			$this->recipient = get_field('dry_cleaners_field', 'option');
  //   		}
	}
}


?>
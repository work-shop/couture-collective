<?php

if ( ! defined( "ABSPATH" )) exit;

/**
 * Dry Cleaning Email Template
 *
 *
 */
class CC_Dry_Cleaning_Email extends WC_Email {

	/**
	 * @hooked cc_send_dry_cleaning_email (params: $booking_id)
	 * Constructor
	 */
	public function __construct() {
		$this->id = 'cc_dry_cleaning';
		$this->title = 'Dry Cleaning Notifications';
		$this->description = 'Dry Cleaning Notifications are sent when a customer rents or prereserves a dress, or updates their Rental or Prereservation.';
		$this->heading = 'Couture Collective Delivery';
    		$this->subject = 'Couture Collective Delivery';

    		//$this->template_base = get_template_directory_uri() . '/partials/emails/';
    		$this->template_html = 'emails/dry-cleaning-email-template.php';
    		$this->template_plain = 'emails/plain/dry-cleaning-email-template.php';

    		add_action( 'cc_send_dry_cleaning_email', array( $this, 'trigger' ) );
    		//add_action('woocommerce_booking_{}_to_{}_notification', array($this, 'trigger') );

    		parent::__construct();

    		$this->recipient = $this->get_option( 'recipient' );

    		if ( !$this->recipient ) {
    			$this->recipient = get_field('dry_cleaners_field', 'option');
    		}
	}

	/**
	 * action to take when this email is triggered
	 *
	 * @param int $booking_id the id of the booking this email was triggered for.
	 *
	 */
	public function trigger( $booking_id ) {
		if ( !$booking_id ) return;

		$this->object = get_wc_booking( $booking_id );

		// if the booking doesn't have an associated order, get out.
		if ( !$this->object->get_order() ) return;
		if ( !$this->object->get_customer() ) return;  

		// rewrite behavior

		if ( !$this->is_enabled() || !$this->get_recipient() ) return;

		$this->send(
			$this->get_recipient(),
			$this->get_subject(),
			$this->get_content(),
			$this->get_headers(),
			$this->get_attachments()
		);
	}

	/**
	 * get the HTML contents for this email.
	 */
	public function get_content_html() {

	}



	/**
	 * Get the plaintext content for this email.
	 */
	public function get_content_plain() {
		$booking = $this->object;
		$order = $booking->get_order();
		$customer = $booking->get_customer();

		$name = $booking->get_product()->get_title();
		$num = ws_fst( get_post_meta( '_sku', $booking->product_id ) );

		$date_format = apply_filters( 'woocommerce_bookings_date_format', 'M jS Y' );
		$date = date_i18n( $date_format, ws_fst( get_post_meta( $booking->id, '_cc_customer_booked_date' ) ) );

		ob_start();
		wc_get_template( $this->template_plain,
			array(
				'email_heading' => $this->heading,
				'item_number' => $num,
				'item_name' => $name,
				'reservation_date' => $date,
				'pickup_date' => $booking->get_end_date(),
				'customer_name' => $customer->name,
				'customer_address' => $order->get_formatted_shipping_address()
			)
		);
		return ob_get_clean();
	}

	/**
	 * Initialize the admin form fields for handling this email template.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' => 'Enable/Disable',
				'type' => 'checkbox',
				'label' => 'Enable drycleaning notifications',
				'default' => 'yes'
			),
			'recipient' => array(
				'title' => 'Recipient',
				'type' => 'text',
				'description' => 'Enter the recipient for drycleaning notifications',
				'placeholder' => '',
				'default' => ''
			),
			'subject' => array(
				'title' => 'Subject',
				'type' => 'text',
				'description' => sprintf( __('This field controls the email subject line. Leave blank to use %s.'), $this->subject ),
				'placeholder' => '',
				'default' => $this->subject
			),
			'heading' => array(
				'title' => 'Subject',
				'type' => 'text',
				'description' => sprintf( __('This field controls the main heading used within the email. Leave blank to use %s.'), $this->heading ),
				'placeholder' => '',
				'default' => $this->heading
			),
			'email_type' => array(
				'title' => 'Email Type',
				'type' => 'select',
				'description' => 'Choose how you\'d like to format this email.',
				'default' => 'plain',
				'options' => array(
					'plain' => 'Plain text'
				)
			)
		);

	}

}


?>
<?php
/**
 * Prereservation Form Class
 */
class CC_Make_Reservation_Form extends WC_Booking_Form {

	/**
	 * Valid resource names
	 * @var string array()
	 */
	static protected $resources = array('prereservation', 'rental', 'next-day');
	
	/**
	 * the resource type for this form
	 * @var WC_Product_Booking_Resource
	 */
	protected $resource;

	/**
	 * the name of this resource
	 * @var string :> {prereservation, rental, next-day}
	 */ 
	protected $resource_name;

	/**
	 * the resource type for this form
	 * @var int :> {prereservation, rental, next-day}
	 */
	protected $resource_id;

	/**
	 * Constructor
	 * @param $product WC_Product_Booking
	 * @param $resource string :> {prereservation, rental, next-day}
	 */
	public function __construct( $product, $resource ) {
		parent::__construct( $product );
		if ( CC_Make_Reservation_Form::validate( $resource ) ) {
			$this->resource = $this->product->get_resource( $this->resource_id );
		}
		// Logic to populate the resource given this proce
	}


	/**
	 * Dequeue the parent scripts and replace them with our specialized implementations.
	 * This method replaces the parent implementation in all contexts...
	 */
	public function scripts() {
		global $wp_locale, $woocommerce;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$wc_bookings_booking_form_args = array(
			'closeText'                  => __( 'Close', 'woocommerce-bookings' ),
			'currentText'                => __( 'Today', 'woocommerce-bookings' ),
			'monthNames'                 => array_values( $wp_locale->month ),
			'monthNamesShort'            => array_values( $wp_locale->month_abbrev ),
			'dayNames'                   => array_values( $wp_locale->weekday ),
			'dayNamesShort'              => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'                => array_values( $wp_locale->weekday_initial ),
			'firstDay'                   => get_option( 'start_of_week' ),
			'current_time'               => date( 'Ymd', current_time( 'timestamp' ) ),
			'check_availability_against' => $this->product->wc_booking_check_availability_against,
			'duration_unit'              => $this->product->wc_booking_duration_unit
		);

		if ( in_array( $this->product->wc_booking_duration_unit, array( 'minute', 'hour' ) ) ) {
			$wc_bookings_booking_form_args['booking_duration'] = 1;
		} else {
			$wc_bookings_booking_form_args['booking_duration'] = $this->product->wc_booking_duration;
		}

		wp_register_script( 'cc-ajax-make-reservation', get_template_directory_uri() . '/_/js/ajax/make-reservation.js', array( 'jquery', 'jquery-blockui' ) );
		wp_enqueue_script( 'cc-ajax-make-reservation' );
		wp_localize_script( 'cc-ajax-make-reservation', 'wc_bookings_booking_form', $wc_bookings_booking_form_args );

		wp_register_script( 'wc-bookings-date-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/date-picker' . $suffix . '.js', array( 'cc-ajax-make-reservation', 'jquery-ui-datepicker' ), WC_BOOKINGS_VERSION, true );
		wp_register_script( 'wc-bookings-month-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/month-picker' . $suffix . '.js', array( 'cc-ajax-make-reservation' ), WC_BOOKINGS_VERSION, true );
		wp_register_script( 'wc-bookings-time-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/time-picker' . $suffix . '.js', array( 'cc-ajax-make-reservation' ), WC_BOOKINGS_VERSION, true );

		// Variables for JS scripts
		$booking_form_params = array(
			'ajax_url'              => $woocommerce->ajax_url(),
			'ajax_loader_url'       => apply_filters( 'woocommerce_ajax_loader_url', $woocommerce->plugin_url() . '/assets/images/ajax-loader@2x.gif' ),
			'i18n_date_unavailable' => __( 'This date is unavailable', 'woocommerce-bookings' )
		);

		wp_localize_script( 'cc-ajax-make-reservation', 'booking_form_params', apply_filters( 'booking_form_params', $booking_form_params ) );
	}


	/**
	 * Override the parent function that determines the posted data, allowing us to shim in our specific cost for a resource.
	 * The resource is not customer-chosen, but it is assigned as if it were, based on how the form is instantiated, and then
	 * it is calculated appropriately. This is used in wc-bookings-calculate-cost, but also in add-to-cart.
	 *
	 */
	public function get_posted_data( $posted = array() ) {
		$resource_id = 0;

		$data = parent::get_posted_data( $posted ); // do a first pass across the data.
		$data['_resource_id'] = $this->product->get_resource( $resource_id );
	}



	public function is_bookable( $data ) {
		$p_val = parent::is_bookable( $data );
		// at this point the resource should have been auto-set. we need to reset it.
	}


	/**
	 * Check a given string is a valid resource identifier
	 * @param $resource string
	 * @return bool
	 */
	public static function validate( $resource ) {
		return in_array($resource, CC_Make_Reservation_Form::$resources);
	}

	/**
	 * calculate costs with given resource type for posted values
	 * @param $posted array
	 * @return string
	 *
	 */
	public function calculate_booking_cost( $posted ) {
		return "10";




		// if ( !empty( $this->booking_cost ) ) { return $this->booking_cost; }

		// $base_cost = max( 0, parent::$product->wc_booking_cost );
		// $booking_cost = max( 0, parent::$product->wc_booking_base_cost );

		// $fixed_booking_cost = 0;

		// $data = parent::get_posted_data( $posted );
		// $valid = parent::is_bookable( $data );

		// if ( is_wp_error( $validate ) ) { return $validate; }

		// set the proper resource id
	}



}



?>
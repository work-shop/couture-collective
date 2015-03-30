<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_Stripe class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_Stripe extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id					= 'stripe';
		$this->method_title 		= __( 'Stripe', 'woocommerce-gateway-stripe' );
		$this->method_description   = __( 'Stripe works by adding credit card fields on the checkout and then sending the details to Stripe for verification.', 'woocommerce-gateway-stripe' );
		$this->has_fields 			= true;
		$this->api_endpoint			= 'https://api.stripe.com/';
		$this->view_transaction_url = 'https://dashboard.stripe.com/payments/%s';
		$this->supports 			= array(
			'subscriptions',
			'products',
			'refunds',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_payment_method_change',
			'subscription_date_changes',
			'pre-orders'
		);

		// Icon
		$icon       = WC()->countries->get_base_country() == 'US' ? 'cards.png' : 'eu_cards.png';
		$this->icon = apply_filters( 'wc_stripe_icon', plugins_url( '/assets/images/' . $icon, dirname( __FILE__ ) ) );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values
		$this->title                 = $this->get_option( 'title' );
		$this->description           = $this->get_option( 'description' );
		$this->enabled               = $this->get_option( 'enabled' );
		$this->testmode              = $this->get_option( 'testmode' ) === "yes" ? true : false;
		$this->capture               = $this->get_option( 'capture', "yes" ) === "yes" ? true : false;
		$this->stripe_checkout       = $this->get_option( 'stripe_checkout' ) === "yes" ? true : false;
		$this->stripe_checkout_image = $this->get_option( 'stripe_checkout_image', '' );
		$this->saved_cards           = $this->get_option( 'saved_cards' ) === "yes" ? true : false;
		$this->secret_key            = $this->testmode ? $this->get_option( 'test_secret_key' ) : $this->get_option( 'secret_key' );
		$this->publishable_key       = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );

		if ( $this->stripe_checkout ) {
			$this->order_button_text = __( 'Continue to payment', 'woocommerce-gateway-stripe' );
		}

		if ( $this->testmode ) {
			$this->description .= ' ' . __( 'TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date.', 'woocommerce-gateway-stripe' );
			$this->description  = trim( $this->description );
		}

		// Hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Get Stripe amount to pay
	 * @return float
	 */
	public function get_stripe_amount( $total ) {
		switch ( get_woocommerce_currency() ) {
			// Zero decimal currencies
			case 'BIF' :
			case 'CLP' :
			case 'DJF' :
			case 'GNF' :
			case 'JPY' :
			case 'KMF' :
			case 'KRW' :
			case 'MGA' :
			case 'PYG' :
			case 'RWF' :
			case 'VND' :
			case 'VUV' :
			case 'XAF' :
			case 'XOF' :
			case 'XPF' :
				$total = absint( $total );
				break;
			default :
				$total = round( $total, 2 ) * 100; // In cents
				break;
		}
		return $total;
	}

	/**
	 * Check if SSL is enabled and notify the user
	 */
	public function admin_notices() {
		if ( $this->enabled == 'no' ) {
			return;
		}

		// Check required fields
		if ( ! $this->secret_key ) {
			echo '<div class="error"><p>' . sprintf( __( 'Stripe error: Please enter your secret key <a href="%s">here</a>', 'woocommerce-gateway-stripe' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_stripe' ) ) . '</p></div>';
			return;

		} elseif ( ! $this->publishable_key ) {
			echo '<div class="error"><p>' . sprintf( __( 'Stripe error: Please enter your publishable key <a href="%s">here</a>', 'woocommerce-gateway-stripe' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_stripe' ) ) . '</p></div>';
			return;
		}

		// Simple check for duplicate keys
		if ( $this->secret_key == $this->publishable_key ) {
			echo '<div class="error"><p>' . sprintf( __( 'Stripe error: Your secret and publishable keys match. Please check and re-enter.', 'woocommerce-gateway-stripe' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_stripe' ) ) . '</p></div>';
			return;
		}

		// Show message if enabled and FORCE SSL is disabled and WordpressHTTPS plugin is not detected
		if ( get_option( 'woocommerce_force_ssl_checkout' ) == 'no' && ! class_exists( 'WordPressHTTPS' ) ) {
			echo '<div class="error"><p>' . sprintf( __( 'Stripe is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate - Stripe will only work in test mode.', 'woocommerce-gateway-stripe' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . '</p></div>';
		}
	}

	/**
	 * Check if this gateway is enabled
	 */
	public function is_available() {
		if ( $this->enabled == "yes" ) {
			if ( ! is_ssl() && ! $this->testmode ) {
				return false;
			}
			// Required fields check
			if ( ! $this->secret_key || ! $this->publishable_key ) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters( 'wc_stripe_settings', array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-stripe' ),
				'label'       => __( 'Enable Stripe', 'woocommerce-gateway-stripe' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-stripe' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-stripe' ),
				'default'     => __( 'Credit card (Stripe)', 'woocommerce-gateway-stripe' )
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-stripe' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-stripe' ),
				'default'     => __( 'Pay with your credit card via Stripe.', 'woocommerce-gateway-stripe')
			),
			'testmode' => array(
				'title'       => __( 'Test mode', 'woocommerce-gateway-stripe' ),
				'label'       => __( 'Enable Test Mode', 'woocommerce-gateway-stripe' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in test mode using test API keys.', 'woocommerce-gateway-stripe' ),
				'default'     => 'yes'
			),
			'secret_key' => array(
				'title'       => __( 'Live Secret Key', 'woocommerce-gateway-stripe' ),
				'type'        => 'text',
				'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
				'default'     => ''
			),
			'publishable_key' => array(
				'title'       => __( 'Live Publishable Key', 'woocommerce-gateway-stripe' ),
				'type'        => 'text',
				'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
				'default'     => ''
			),
			'test_secret_key' => array(
				'title'       => __( 'Test Secret Key', 'woocommerce-gateway-stripe' ),
				'type'        => 'text',
				'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
				'default'     => ''
			),
			'test_publishable_key' => array(
				'title'       => __( 'Test Publishable Key', 'woocommerce-gateway-stripe' ),
				'type'        => 'text',
				'description' => __( 'Get your API keys from your stripe account.', 'woocommerce-gateway-stripe' ),
				'default'     => ''
			),
			'capture' => array(
				'title'       => __( 'Capture', 'woocommerce-gateway-stripe' ),
				'label'       => __( 'Capture charge immediately', 'woocommerce-gateway-stripe' ),
				'type'        => 'checkbox',
				'description' => __( 'Whether or not to immediately capture the charge. When unchecked, the charge issues an authorization and will need to be captured later. Uncaptured charges expire in 7 days.', 'woocommerce-gateway-stripe' ),
				'default'     => 'yes'
			),
			'stripe_checkout' => array(
				'title'       => __( 'Stripe Checkout', 'woocommerce-gateway-stripe' ),
				'label'       => __( 'Enable Stripe Checkout', 'woocommerce-gateway-stripe' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, this option shows a "pay" button and modal credit card form on the checkout, instead of credit card fields directly on the page.', 'woocommerce-gateway-stripe' ),
				'default'     => 'no'
			),
			'stripe_checkout_image' => array(
				'title'       => __( 'Stripe Checkout Image', 'woocommerce-gateway-stripe' ),
				'description' => __( 'Optionally enter the URL to a 128x128px image of your brand or product. e.g. <code>https://yoursite.com/wp-content/uploads/2013/09/yourimage.jpg</code>', 'woocommerce-gateway-stripe' ),
				'type'        => 'text',
				'default'     => ''
			),
			'saved_cards' => array(
				'title'       => __( 'Saved cards', 'woocommerce-gateway-stripe' ),
				'label'       => __( 'Enable saved cards', 'woocommerce-gateway-stripe' ),
				'type'        => 'checkbox',
				'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Stripe servers, not on your store.', 'woocommerce-gateway-stripe' ),
				'default'     => 'no'
			),
		) );
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		$checked = 1;
		?>
		<fieldset>
			<?php
				if ( $this->description ) {
					echo wpautop( esc_html( $this->description ) );
				}
				if ( $this->saved_cards && is_user_logged_in() && ( $customer_id = get_user_meta( get_current_user_id(), '_stripe_customer_id', true ) ) && is_string( $customer_id ) && ( $cards = $this->get_saved_cards( $customer_id ) ) ) {
					?>
					<p class="form-row form-row-wide">
						<a class="button" style="float:right;" href="<?php echo apply_filters( 'wc_stripe_manage_saved_cards_url', get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>#saved-cards"><?php _e( 'Manage cards', 'woocommerce-gateway-stripe' ); ?></a>
						<?php if ( $cards ) : ?>
							<?php foreach ( (array) $cards as $card ) : ?>
								<label for="stripe_card_<?php echo $card->id; ?>">
									<input type="radio" id="stripe_card_<?php echo $card->id; ?>" name="stripe_card_id" value="<?php echo $card->id; ?>" <?php checked( $checked, 1 ) ?> />
									<?php printf( __( '%s card ending in %s (Expires %s/%s)', 'woocommerce-gateway-stripe' ), (isset( $card->type ) ? $card->type : $card->brand ), $card->last4, $card->exp_month, $card->exp_year ); ?>
								</label>
								<?php $checked = 0; endforeach; ?>
						<?php endif; ?>
						<label for="new">
							<input type="radio" id="new" name="stripe_card_id" <?php checked( $checked, 1 ) ?> value="new" />
							<?php _e( 'Use a new credit card', 'woocommerce-gateway-stripe' ); ?>
						</label>
					</p>
					<?php
				}
			?>
			<div class="stripe_new_card" <?php if ( $checked === 0 ) : ?>style="display:none;"<?php endif; ?>
				data-description=""
				data-amount="<?php echo $this->get_stripe_amount( WC()->cart->total ); ?>"
				data-name="<?php echo sprintf( __( '%s', 'woocommerce-gateway-stripe' ), get_bloginfo( 'name' ) ); ?>"
				data-label="<?php _e( 'Confirm and Pay', 'woocommerce-gateway-stripe' ); ?>"
				data-currency="<?php echo strtolower( get_woocommerce_currency() ); ?>"
				data-image="<?php echo $this->stripe_checkout_image; ?>"
				>
				<?php if ( ! $this->stripe_checkout ) : ?>
					<?php $this->credit_card_form( array( 'fields_have_names' => false ) ); ?>
				<?php endif; ?>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Get a customers saved cards using their Stripe ID. Cached.
	 *
	 * @param  string $customer_id
	 * @return bool|array
	 */
	public function get_saved_cards( $customer_id ) {
		if ( false === ( $cards = get_transient( 'stripe_cards_' . $customer_id ) ) ) {
			$response = $this->stripe_request( array(
				'limit'       => 100
			), 'customers/' . $customer_id . '/cards', 'GET' );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$cards = $response->data;

			set_transient( 'stripe_cards_' . $customer_id, $cards, HOUR_IN_SECONDS * 48 );
		}

		return $cards;
	}

	/**
	 * payment_scripts function.
	 *
	 * Outputs scripts used for stripe payment
	 *
	 * @access public
	 */
	public function payment_scripts() {
		if ( ! is_checkout() ) {
			return;
		}

		if ( $this->stripe_checkout ) {
			wp_enqueue_script( 'stripe', 'https://checkout.stripe.com/v2/checkout.js', '', '2.0', true );
			wp_enqueue_script( 'woocommerce_stripe', plugins_url( 'assets/js/stripe_checkout.js', dirname( __FILE__ ) ), array( 'stripe' ), WC_STRIPE_VERSION, true );
		} else {
			wp_enqueue_script( 'stripe', 'https://js.stripe.com/v1/', '', '1.0', true );
			wp_enqueue_script( 'woocommerce_stripe', plugins_url( 'assets/js/stripe.js', dirname( __FILE__ ) ), array( 'stripe' ), WC_STRIPE_VERSION, true );
		}

		$stripe_params = array(
			'key'                  => $this->publishable_key,
			'i18n_terms'           => __( 'Please accept the terms and conditions first', 'woocommerce-gateway-stripe' ),
			'i18n_required_fields' => __( 'Please fill in required checkout fields first', 'woocommerce-gateway-stripe' ),
		);

		// If we're on the pay page we need to pass stripe.js the address of the order.
		if ( is_checkout_pay_page() && isset( $_GET['order'] ) && isset( $_GET['order_id'] ) ) {
			$order_key = urldecode( $_GET['order'] );
			$order_id  = absint( $_GET['order_id'] );
			$order     = new WC_Order( $order_id );

			if ( $order->id == $order_id && $order->order_key == $order_key ) {
				$stripe_params['billing_first_name'] = $order->billing_first_name;
				$stripe_params['billing_last_name']  = $order->billing_last_name;
				$stripe_params['billing_address_1']  = $order->billing_address_1;
				$stripe_params['billing_address_2']  = $order->billing_address_2;
				$stripe_params['billing_state']      = $order->billing_state;
				$stripe_params['billing_city']       = $order->billing_city;
				$stripe_params['billing_postcode']   = $order->billing_postcode;
				$stripe_params['billing_country']    = $order->billing_country;
			}
		}

		wp_localize_script( 'woocommerce_stripe', 'wc_stripe_params', $stripe_params );
	}

	/**
	 * Process the payment
	 */
	public function process_payment( $order_id ) {
		$order        = new WC_Order( $order_id );
		$stripe_token = isset( $_POST['stripe_token'] ) ? wc_clean( $_POST['stripe_token'] ) : '';
		$card_id      = isset( $_POST['stripe_card_id'] ) ? wc_clean( $_POST['stripe_card_id'] ) : '';
		$customer_id  = is_user_logged_in() ? get_user_meta( get_current_user_id(), '_stripe_customer_id', true ) : 0;

		if ( ! $customer_id || ! is_string( $customer_id ) ) {
			$customer_id = 0;
		}

		// Use Stripe CURL API for payment
		try {
			$post_data = array();

			// Check amount
			if ( $order->order_total * 100 < 50 ) {
				throw new Exception( __( 'Sorry, the minimum allowed order total is 0.50 to use this payment method.', 'woocommerce-gateway-stripe' ) );
			}

			// Pay using a saved card!
			if ( $card_id !== 'new' && $card_id && $customer_id ) {
				$post_data['customer'] = $customer_id;
				$post_data['card']     = $card_id;
			}

			// If not using a saved card, we need a token
			elseif ( empty( $stripe_token ) ) {
				$error_msg = __( 'Please make sure your card details have been entered correctly and that your browser supports JavaScript.', 'woocommerce-gateway-stripe' );

				if ( $this->testmode ) {
					$error_msg .= ' ' . __( 'Developers: Please make sure that you are including jQuery and there are no JavaScript errors on the page.', 'woocommerce-gateway-stripe' );
				}

				throw new Exception( $error_msg );
			}

			// Use token
			else {
				// Save token if logged in
				if ( is_user_logged_in() && $this->saved_cards ) {
					if ( ! $customer_id ) {
						$customer_id = $this->add_customer( $order, $stripe_token );

						if ( is_wp_error( $customer_id ) ) {
							throw new Exception( $customer_id->get_error_message() );
						}
					} else {
						$card_id = $this->add_card( $customer_id, $stripe_token );

						if ( is_wp_error( $card_id ) ) {
							throw new Exception( $card_id->get_error_message() );
						}

						$post_data['card'] = $card_id;
					}

					$post_data['customer'] = $customer_id;
				} else {
					$post_data['card']     = $stripe_token;
				}
			}

			// Store the ID in the order
			if ( $customer_id ) {
				update_post_meta( $order_id, '_stripe_customer_id', $customer_id );
			}
			if ( $card_id ) {
				update_post_meta( $order_id, '_stripe_card_id', $card_id );
			}

			// Other charge data
			$post_data['amount']      = $this->get_stripe_amount( $order->order_total );
			$post_data['currency']    = strtolower( get_woocommerce_currency() );
			$post_data['description'] = sprintf( __( '%s - Order %s', 'woocommerce-gateway-stripe' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $order->get_order_number() );
			$post_data['capture']     = $this->capture ? 'true' : 'false';
			$post_data['expand[]']    = 'balance_transaction';

			// Make the request
			$response = $this->stripe_request( $post_data );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			// Store charge ID
			update_post_meta( $order->id, '_stripe_charge_id', $response->id );

			// Store other data such as fees
			update_post_meta( $order->id, 'Stripe Payment ID', $response->id );

			if ( isset( $response->balance_transaction ) && isset( $response->balance_transaction->fee ) ) {
				$fee = number_format( $response->balance_transaction->fee / 100, 2, '.', '' );
				update_post_meta( $order->id, 'Stripe Fee', $fee );
				update_post_meta( $order->id, 'Net Revenue From Stripe', $order->order_total - $fee );
			}

			if ( $response->captured ) {

				// Store captured value
				update_post_meta( $order->id, '_stripe_charge_captured', 'yes' );

				// Payment complete
				$order->payment_complete( $response->id );

				// Add order note
				$order->add_order_note( sprintf( __( 'Stripe charge complete (Charge ID: %s)', 'woocommerce-gateway-stripe' ), $response->id ) );

			} else {

				// Store captured value
				update_post_meta( $order->id, '_stripe_charge_captured', 'no' );
				add_post_meta( $order->id, '_transaction_id', $response->id, true );

				// Mark as on-hold
				$order->update_status( 'on-hold', sprintf( __( 'Stripe charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-stripe' ), $response->id ) );

				// Reduce stock levels
				$order->reduce_order_stock();
			}

			// Remove cart
			WC()->cart->empty_cart();

			// Return thank you page redirect
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order )
			);

		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return;
		}
	}

	/**
	 * Refund a charge
	 * @param  int $order_id
	 * @param  float $amount
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || ! $order->get_transaction_id() ) {
			return false;
		}

		$body = array();

		if ( ! is_null( $amount ) ) {
			$body['amount']	= $this->get_stripe_amount( $amount );
		}

		if ( $reason ) {
			$body['metadata'] = array(
				'reason'	=> $reason,
			);
		}

		$response = $this->stripe_request( $body, 'charges/' . $order->get_transaction_id() . '/refunds' );

		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( ! empty( $response->id ) ) {
			$order->add_order_note( sprintf( __( 'Refunded %s - Refund ID: %s - Reason: %s', 'woocommerce' ), wc_price( $response->amount / 100 ), $response->id, $reason ) );
			return true;
		}
	}

	/**
	 * Add a customer to Stripe via the API.
	 *
	 * @param int $order
	 * @param string $stripe_token
	 * @return int|WP_ERROR
	 */
	public function add_customer( $order, $stripe_token ) {
		if ( $stripe_token && is_user_logged_in() ) {
			$response = $this->stripe_request( array(
				'email'       => $order->billing_email,
				'description' => 'Customer: ' . $order->billing_first_name . ' ' . $order->billing_last_name,
				'card'        => $stripe_token,
				'expand[]'    => 'default_card'
			), 'customers' );

			if ( is_wp_error( $response ) ) {
				return $response;
			} elseif ( ! empty( $response->id ) ) {
				// Store the ID on the user account
				update_user_meta( get_current_user_id(), '_stripe_customer_id', $response->id );

				// Store the ID in the order
				update_post_meta( $order->id, '_stripe_customer_id', $response->id );

				return $response->id;
			}
		}
		return new WP_Error( 'error', __( 'Unable to add customer', 'woocommerce-gateway-stripe' ) );
	}

	/**
	 * Add a card to a customer via the API.
	 *
	 * @param int $order
	 * @param string $stripe_token
	 * @return int|WP_ERROR
	 */
	public function add_card( $customer_id, $stripe_token ) {
		if ( $stripe_token ) {
			$response = $this->stripe_request( array(
				'card'        => $stripe_token
			), 'customers/' . $customer_id . '/cards' );

			delete_transient( 'stripe_cards_' . $customer_id );

			if ( is_wp_error( $response ) ) {
				return $response;
			} elseif ( ! empty( $response->id ) ) {
				return $response->id;
			}
		}
		return new WP_Error( 'error', __( 'Unable to add card', 'woocommerce-gateway-stripe' ) );
	}

	/**
	 * Send the request to Stripe's API
	 *
	 * @param array $request
	 * @param string $api
	 * @return array|WP_Error
	 */
	public function stripe_request( $request, $api = 'charges', $method = 'POST' ) {
		$response = wp_remote_post(
			$this->api_endpoint . 'v1/' . $api,
			array(
				'method'        => $method,
				'headers'       => array(
					'Authorization'  => 'Basic ' . base64_encode( $this->secret_key . ':' ),
					'Stripe-Version' => '2014-09-08'
				),
				'body'       => apply_filters( 'wc_stripe_request_body', $request, $api ),
				'timeout'    => 70,
				'sslverify'  => false,
				'user-agent' => 'WooCommerce ' . WC()->version
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'stripe_error', __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-stripe' ) );
		}

		if ( empty( $response['body'] ) ) {
			return new WP_Error( 'stripe_error', __( 'Empty response.', 'woocommerce-gateway-stripe' ) );
		}

		$parsed_response = json_decode( $response['body'] );

		// Handle response
		if ( ! empty( $parsed_response->error ) ) {
			return new WP_Error( 'stripe_error', $parsed_response->error->message );
		} else {
			return $parsed_response;
		}
	}
}
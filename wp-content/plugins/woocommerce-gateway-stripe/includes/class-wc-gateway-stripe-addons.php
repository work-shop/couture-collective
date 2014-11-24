<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_Stripe_Addons class.
 *
 * @extends WC_Gateway_Stripe
 */
class WC_Gateway_Stripe_Addons extends WC_Gateway_Stripe {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 3 );
			add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this, 'remove_renewal_order_meta' ), 10, 4 );
			add_action( 'woocommerce_subscriptions_changed_failing_payment_method_stripe', array( $this, 'update_failing_payment_method' ), 10, 3 );
			// display the current payment method used for a subscription in the "My Subscriptions" table
			add_filter( 'woocommerce_my_subscriptions_recurring_payment_method', array( $this, 'maybe_render_subscription_payment_method' ), 10, 3 );
		}

		if ( class_exists( 'WC_Pre_Orders_Order' ) ) {
			add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this, 'process_pre_order_release_payment' ) );
		}
	}

	/**
     * Process the subscription
     *
	 * @param int $order_id
	 * @return array
     */
	public function process_subscription( $order_id ) {
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

			// Save token
			if ( ! $customer_id ) {
				$customer_id = $this->add_customer( $order, $stripe_token );

				if ( is_wp_error( $customer_id ) ) {
					throw new Exception( $customer_id->get_error_message() );
				}

				unset( $post_data['card'] );
				$post_data['customer'] = $customer_id;

			} elseif ( ! $card_id || $card_id === 'new' ) {
				$card_id = $this->add_card( $customer_id, $stripe_token );

				if ( is_wp_error( $card_id ) ) {
					throw new Exception( $card_id->get_error_message() );
				}

				$post_data['card']     = $card_id;
				$post_data['customer'] = $customer_id;
			}

			// Store the ID in the order
			update_post_meta( $order_id, '_stripe_customer_id', $customer_id );
			update_post_meta( $order_id, '_stripe_card_id', $card_id );

			$initial_payment = WC_Subscriptions_Order::get_total_initial_payment( $order );

			if ( $initial_payment > 0 ) {
				$payment_response = $this->process_subscription_payment( $order, $initial_payment );
			}

			if ( isset( $payment_response ) && is_wp_error( $payment_response ) ) {

				throw new Exception( $payment_response->get_error_message() );

			} else {

				if ( isset( $payment_response->balance_transaction ) && isset( $payment_response->balance_transaction->fee ) ) {
					$fee = number_format( $payment_response->balance_transaction->fee / 100, 2, '.', '' );
					update_post_meta( $order->id, 'Stripe Fee', $fee );
					update_post_meta( $order->id, 'Net Revenue From Stripe', $order->order_total - $fee );
				}

				// Payment complete
				$order->payment_complete( $payment_response->id );

				// Remove cart
				WC()->cart->empty_cart();

				// Activate subscriptions
				WC_Subscriptions_Manager::activate_subscriptions_for_order( $order );

				// Return thank you page redirect
				return array(
					'result' 	=> 'success',
					'redirect'	=> $this->get_return_url( $order )
				);
			}

		} catch ( Exception $e ) {
			wc_add_notice( __('Error:', 'woocommerce-gateway-stripe') . ' "' . $e->getMessage() . '"', 'error' );
			return;
		}
	}

	/**
	 * Process the pre-order
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_pre_order( $order_id ) {
		if ( WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {
			$order        = new WC_Order( $order_id );
			$stripe_token = isset( $_POST['stripe_token'] ) ? wc_clean( $_POST['stripe_token'] ) : '';
			$card_id      = isset( $_POST['stripe_card_id'] ) ? wc_clean( $_POST['stripe_card_id'] ) : '';
			$customer_id  = is_user_logged_in() ? get_user_meta( get_current_user_id(), '_stripe_customer_id', true ) : 0;

			if ( ! $customer_id || ! is_string( $customer_id ) ) {
				$customer_id = 0;
			}

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

				// Save token
				if ( ! $customer_id ) {
					$customer_id = $this->add_customer( $order, $stripe_token );

					if ( is_wp_error( $customer_id ) ) {
						throw new Exception( $customer_id->get_error_message() );
					}

					unset( $post_data['card'] );
					$post_data['customer'] = $customer_id;

				} elseif ( ! $card_id || $card_id === 'new' ) {
					$card_id = $this->add_card( $customer_id, $stripe_token );

					if ( is_wp_error( $card_id ) ) {
						throw new Exception( $card_id->get_error_message() );
					}

					$post_data['card']     = $card_id;
					$post_data['customer'] = $customer_id;
				}

				// Store the ID in the order
				update_post_meta( $order->id, '_stripe_customer_id', $customer_id );

				// Store the ID in the order
				update_post_meta( $order->id, '_stripe_card_id', $card_id );

				// Reduce stock levels
				$order->reduce_order_stock();

				// Remove cart
				WC()->cart->empty_cart();

				// Is pre ordered!
				WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

				// Return thank you page redirect
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order )
				);

			} catch ( Exception $e ) {
				WC()->add_error( $e->getMessage() );
				return;
			}
		} else {
			return parent::process_payment( $order_id );
		}
	}

	/**
	 * Process the payment
	 *
	 * @param  int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		// Processing subscription
		if ( class_exists( 'WC_Subscriptions_Order' ) && WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {
			return $this->process_subscription( $order_id );

		// Processing pre-order
		} elseif ( class_exists( 'WC_Pre_Orders_Order' ) && WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {
			return $this->process_pre_order( $order_id );

		// Processing regular product
		} else {
			return parent::process_payment( $order_id );
		}
	}

	/**
	 * scheduled_subscription_payment function.
	 *
	 * @param $amount_to_charge float The amount to charge.
	 * @param $order WC_Order The WC_Order object of the order which the subscription was purchased in.
	 * @param $product_id int The ID of the subscription product for which this payment relates.
	 * @access public
	 * @return void
	 */
	public function scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {
		$result = $this->process_subscription_payment( $order, $amount_to_charge );

		if ( is_wp_error( $result ) ) {
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
		} else {
			WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );
		}
	}

	/**
	 * process_subscription_payment function.
	 *
	 * @access public
	 * @param mixed $order
	 * @param int $amount (default: 0)
	 * @param string $stripe_token (default: '')
	 * @return void
	 */
	public function process_subscription_payment( $order = '', $amount = 0 ) {
		$order_items       = $order->get_items();
		$order_item        = array_shift( $order_items );
		$subscription_name = sprintf( __( 'Subscription for "%s"', 'woocommerce-gateway-stripe' ), $order_item['name'] ) . ' ' . sprintf( __( '(Order %s)', 'woocommerce-gateway-stripe' ), $order->get_order_number() );

		if ( $amount * 100 < 50 ) {
			return new WP_Error( 'stripe_error', __( 'Sorry, the minimum allowed order total is 0.50 to use this payment method.', 'woocommerce-gateway-stripe' ) );
		}

		// We need a customer in Stripe. First, look for the customer ID linked to the USER.
		$user_id         = $order->customer_user;
		$stripe_customer = get_user_meta( $user_id, '_stripe_customer_id', true );

		// If we couldn't find a Stripe customer linked to the account, fallback to the order meta data.
		if ( ! $stripe_customer || ! is_string( $stripe_customer ) ) {
			$stripe_customer = get_post_meta( $order->id, '_stripe_customer_id', true );
		}

		// Or fail :(
		if ( ! $stripe_customer ) {
			return new WP_Error( 'stripe_error', __( 'Customer not found', 'woocommerce-gateway-stripe' ) );
		}

		$stripe_payment_args = array(
			'amount'      => $this->get_stripe_amount( $amount ),
			'currency'    => strtolower( get_woocommerce_currency() ),
			'description' => $subscription_name,
			'customer'    => $stripe_customer,
			'expand[]'    => 'balance_transaction'
		);

		// See if we're using a particular card
		if ( $card_id = get_post_meta( $order->id, '_stripe_card_id', true ) ) {
			$stripe_payment_args['card'] = $card_id;
		}

		// Charge the customer
		$response = $this->stripe_request( $stripe_payment_args, 'charges' );

		if ( is_wp_error( $response ) ) {
			return $response;
		} else {
			$order->add_order_note( sprintf( __( 'Stripe subscription payment completed (Charge ID: %s)', 'woocommerce-gateway-stripe' ), $response->id ) );
			add_post_meta( $order->id, '_transaction_id', $response->id, true );
			return true;
		}
	}

	/**
	 * Don't transfer Stripe customer/token meta when creating a parent renewal order.
	 *
	 * @access public
	 * @param array $order_meta_query MySQL query for pulling the metadata
	 * @param int $original_order_id Post ID of the order being used to purchased the subscription being renewed
	 * @param int $renewal_order_id Post ID of the order created for renewing the subscription
	 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'
	 * @return void
	 */
	public function remove_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {
		if ( 'parent' == $new_order_role ) {
			$order_meta_query .= " AND `meta_key` NOT IN ( '_stripe_customer_id', '_stripe_card_id' ) ";
		}
		return $order_meta_query;
	}

	/**
	 * Update the customer_id for a subscription after using Stripe to complete a payment to make up for
	 * an automatic renewal payment which previously failed.
	 *
	 * @access public
	 * @param WC_Order $original_order The original order in which the subscription was purchased.
	 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
	 * @param string $subscription_key A subscription key of the form created by @see WC_Subscriptions_Manager::get_subscription_key()
	 * @return void
	 */
	public function update_failing_payment_method( $original_order, $renewal_order, $subscription_key ) {
		$new_customer_id = get_post_meta( $renewal_order->id, '_stripe_customer_id', true );
		$new_card_id     = get_post_meta( $renewal_order->id, '_stripe_card_id', true );
		update_post_meta( $original_order->id, '_stripe_customer_id', $new_customer_id );
		update_post_meta( $original_order->id, '_stripe_card_id', $new_card_id );
	}

	/**
	 * Render the payment method used for a subscription in the "My Subscriptions" table
	 *
	 * @since 1.7.5
	 * @param string $payment_method_to_display the default payment method text to display
	 * @param array $subscription_details the subscription details
	 * @param WC_Order $order the order containing the subscription
	 * @return string the subscription payment method
	 */
	public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription_details, WC_Order $order ) {
		// bail for other payment methods
		if ( $this->id !== $order->recurring_payment_method || ! $order->customer_user ) {
			return $payment_method_to_display;
		}

		$user_id         = $order->customer_user;
		$stripe_customer = get_user_meta( $user_id, '_stripe_customer_id', true );

		// If we couldn't find a Stripe customer linked to the account, fallback to the order meta data.
		if ( ! $stripe_customer || ! is_string( $stripe_customer ) ) {
			$stripe_customer = get_post_meta( $order->id, '_stripe_customer_id', true );
		}

		// Card specified?
		$stripe_card = get_post_meta( $order->id, '_stripe_card_id', true );

		// Get cards from API
		$cards       = $this->get_saved_cards( $stripe_customer );

		if ( $cards ) {
			$found_card = false;
			foreach ( $cards as $card ) {
				if ( $card->id === $stripe_card ) {
					$found_card                = true;
					$payment_method_to_display = sprintf( __( 'Via %s card ending in %s', 'woocommerce-gateway-stripe' ), ( isset( $card->type ) ? $card->type : $card->brand ), $card->last4 );
					break;
				}
			}
			if ( ! $found_card ) {
				$payment_method_to_display = sprintf( __( 'Via %s card ending in %s', 'woocommerce-gateway-stripe' ), ( isset( $cards[0]->type ) ? $cards[0]->type : $cards[0]->brand ), $cards[0]->last4 );
			}
		}

		return $payment_method_to_display;
	}

	/**
	 * Process a pre-order payment when the pre-order is released
	 *
	 * @param WC_Order $order
	 * @return void
	 */
	public function process_pre_order_release_payment( $order ) {

		try {
			$post_data['customer']    = get_post_meta( $order->id, '_stripe_customer_id', true );
			$post_data['card']        = get_post_meta( $order->id, '_stripe_card_id', true );
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
			}

		} catch ( Exception $e ) {
			$order_note = sprintf( __( 'Stripe Transaction Failed (%s)', 'woocommerce-gateway-stripe' ), $e->getMessage() );

			// Mark order as failed if not already set,
			// otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
			if ( 'failed' != $order->status ) {
				$order->update_status( 'failed', $order_note );
			} else {
				$order->add_order_note( $order_note );
			}
		}
	}
}

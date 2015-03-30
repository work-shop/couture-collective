<?php
/*
Plugin Name: WooCommerce Stripe Gateway
Plugin URI: http://www.woothemes.com/products/stripe/
Description: A payment gateway for Stripe (https://stripe.com/). A Stripe account and a server with Curl, SSL support, and a valid SSL certificate is required (for security reasons) for this gateway to function. Requires WC 2.1+
Version: 2.2.8
Author: Mike Jolley
Author URI: http://mikejolley.com

	Copyright: © 2009-2014 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html

	Stripe Docs: https://stripe.com/docs
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'b022f53cd049144bfd02586bdc0928cd', '18627' );

/**
 * Main Stripe class which sets the gateway up for us
 */
class WC_Stripe {

	/**
	 * Constructor
	 */
	public function __construct() {
		define( 'WC_STRIPE_VERSION', '2.2.8' );
		define( 'WC_STRIPE_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
		define( 'WC_STRIPE_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WC_STRIPE_MAIN_FILE', __FILE__ );

		// Actions
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'capture_payment' ) );
		add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'capture_payment' ) );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'cancel_payment' ) );
		add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'cancel_payment' ) );
	}

	/**
	 * Add relevant links to plugins page
	 * @param  array $links
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_stripe' ) . '">' . __( 'Settings', 'woocommerce-gateway-stripe' ) . '</a>',
			'<a href="http://support.woothemes.com/">' . __( 'Support', 'woocommerce-gateway-stripe' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/stripe/">' . __( 'Docs', 'woocommerce-gateway-stripe' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Init localisations and files
	 */
	public function init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		// Includes
		include_once( 'includes/class-wc-gateway-stripe.php' );
		include_once( 'includes/class-wc-gateway-stripe-saved-cards.php' );

		if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
			include_once( 'includes/class-wc-gateway-stripe-addons.php' );
		}

		// Localisation
		load_plugin_textdomain( 'woocommerce-gateway-stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register the gateway for use
	 */
	public function register_gateway( $methods ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) || class_exists( 'WC_Pre_Orders_Order' ) ) {
			$methods[] = 'WC_Gateway_Stripe_Addons';
		} else {
			$methods[] = 'WC_Gateway_Stripe';
		}

		return $methods;
	}

	/**
	 * Capture payment when the order is changed from on-hold to complete or processing
	 *
	 * @param  int $order_id
	 */
	public function capture_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( $order->payment_method == 'stripe' ) {
			$charge   = get_post_meta( $order_id, '_stripe_charge_id', true );
			$captured = get_post_meta( $order_id, '_stripe_charge_captured', true );

			if ( $charge && $captured == 'no' ) {
				$stripe = new WC_Gateway_Stripe();

				$result = $stripe->stripe_request( array(
					'amount' => $order->order_total * 100
				), 'charges/' . $charge . '/capture' );

				if ( is_wp_error( $result ) ) {
					$order->add_order_note( __( 'Unable to capture charge!', 'woocommerce-gateway-stripe' ) . ' ' . $result->get_error_message() );
				} else {
					$order->add_order_note( sprintf( __( 'Stripe charge complete (Charge ID: %s)', 'woocommerce-gateway-stripe' ), $result->id ) );
					update_post_meta( $order->id, '_stripe_charge_captured', 'yes' );

					// Store other data such as fees
					update_post_meta( $order->id, 'Stripe Payment ID', $result->id );
					update_post_meta( $order->id, 'Stripe Fee', number_format( $result->fee / 100, 2, '.', '' ) );
					update_post_meta( $order->id, 'Net Revenue From Stripe', ( $order->order_total - number_format( $result->fee / 100, 2, '.', '' ) ) );
				}
			}
		}
	}

	/**
	 * Cancel pre-auth on refund/cancellation
	 *
	 * @param  int $order_id
	 */
	public function cancel_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( $order->payment_method == 'stripe' ) {
			$charge   = get_post_meta( $order_id, '_stripe_charge_id', true );

			if ( $charge ) {
				$stripe = new WC_Gateway_Stripe();

				$result = $stripe->stripe_request( array(
					'amount' => $order->order_total * 100
				), 'charges/' . $charge . '/refund' );

				if ( is_wp_error( $result ) ) {
					$order->add_order_note( __( 'Unable to refund charge!', 'woocommerce-gateway-stripe' ) . ' ' . $result->get_error_message() );
				} else {
					$order->add_order_note( sprintf( __( 'Stripe charge refunded (Charge ID: %s)', 'woocommerce-gateway-stripe' ), $result->id ) );
					delete_post_meta( $order->id, '_stripe_charge_captured' );
					delete_post_meta( $order->id, '_stripe_charge_id' );
				}
			}
		}
	}
}

new WC_Stripe();

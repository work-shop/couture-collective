<?php
/**
 * class-groups-ws-handler.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package groups-woocommerce
 * @since groups-woocommerce 1.0.0
 */

/**
 * Product & subscription handler.
 */
class Groups_WS_Handler {

	/**
	 * Register action hooks.
	 */
	public static function init() {

		$options = get_option( 'groups-woocommerce', array() );
		$order_status = isset( $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] ) ? $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] : GROUPS_WS_DEFAULT_MEMBERSHIP_ORDER_STATUS;
		$remove_on_hold = isset( $options[GROUPS_WS_REMOVE_ON_HOLD] ) ? $options[GROUPS_WS_REMOVE_ON_HOLD] : GROUPS_WS_DEFAULT_REMOVE_ON_HOLD;

		// normal products

		// the essentials for normal order processing flow
		add_action ( 'woocommerce_order_status_cancelled',  array( __CLASS__, 'order_status_cancelled' ) );
		add_action ( 'woocommerce_order_status_completed',  array( __CLASS__, 'order_status_completed' ) );
		if ( $order_status == 'processing' ) {
			add_action ( 'woocommerce_order_status_processing', array( __CLASS__, 'order_status_completed' ) );
		} else {
			add_action ( 'woocommerce_order_status_processing', array( __CLASS__, 'order_status_processing' ) );
		}
		add_action ( 'woocommerce_order_status_refunded',   array( __CLASS__, 'order_status_refunded' ) );

		// these are of concern when manual adjustments are made (backwards in order flow) 
		add_action ( 'woocommerce_order_status_failed',     array( __CLASS__, 'order_status_failed' ) );
		add_action ( 'woocommerce_order_status_on_hold',    array( __CLASS__, 'order_status_on_hold' ) );
		add_action ( 'woocommerce_order_status_pending',    array( __CLASS__, 'order_status_pending' ) );

		// subscriptions

		// do_action( 'activated_subscription', $user_id, $subscription_key );
		add_action( 'activated_subscription', array( __CLASS__, 'activated_subscription' ), 10, 2 );
		// do_action( 'cancelled_subscription', $user_id, $subscription_key );
		add_action( 'cancelled_subscription', array( __CLASS__, 'cancelled_subscription' ), 10, 2 );
		// do_action( 'subscription_end_of_prepaid_term', $user_id, $subscription_key );
		add_action( 'subscription_end_of_prepaid_term', array( __CLASS__, 'subscription_end_of_prepaid_term' ), 10, 2 );
		// do_action( 'subscription_trashed', $user_id, $subscription_key );
		add_action( 'subscription_trashed', array( __CLASS__, 'subscription_trashed' ), 10, 2 );
		// do_action( 'subscription_expired', $user_id, $subscription_key );
		add_action( 'subscription_expired', array( __CLASS__, 'subscription_expired' ), 10, 2 );

		add_action( 'updated_users_subscription', array( __CLASS__, 'updated_users_subscription' ), 10, 2 );

		if ( $remove_on_hold ) {
			// do_action( 'subscription_put_on-hold', $user_id, $subscription_key );
			add_action( 'subscription_put_on-hold', array( __CLASS__, 'subscription_put_on_hold' ), 10, 2 );
			// do_action( 'reactivated_subscription', $user_id, $subscription_key );
			add_action( 'reactivated_subscription', array( __CLASS__, 'reactivated_subscription' ), 10, 2 );
		}

		// scheduled expirations
		add_action( 'groups_ws_subscription_expired', array( __CLASS__, 'subscription_expired' ), 10, 2 );

		// time-limited memberships
		add_action( 'groups_created_user_group', array( __CLASS__, 'groups_created_user_group' ), 10, 2 );
		add_action( 'groups_deleted_user_group', array( __CLASS__, 'groups_deleted_user_group' ), 10, 2 );

		// force registration at checkout
		add_filter( 'option_woocommerce_enable_guest_checkout', array( __CLASS__, 'option_woocommerce_enable_guest_checkout' ) );
		add_filter( 'option_woocommerce_enable_signup_and_login_from_checkout', array( __CLASS__, 'option_woocommerce_enable_signup_and_login_from_checkout' ) );
	}

	/**
	 * Cancel group memberships for the order.
	 * @param int $order_id
	 */
	public static function order_status_cancelled( $order_id ) {
		if ( $order = Groups_WS_Helper::get_order( $order_id ) ) {
			if ( $items = $order->get_items() ) {
				if ( $user_id = $order->user_id ) { // not much we can do if there isn't
					foreach ( $items as $item ) {
						if ( $product = $order->get_product_from_item( $item ) ) {
							// Don't act on subscriptions here unless it's a refund.
							// Refunded subscription orders must be handled here as well
							// to assure that group membership is terminated immediately. 
							if ( $order->status == 'refunded' || !class_exists( 'WC_Subscriptions_Product' ) || !WC_Subscriptions_Product::is_subscription( $product->id ) ) {
								$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
								if ( isset( $groups_product_groups[$order_id] ) &&
									 isset( $groups_product_groups[$order_id][$product->id] ) &&
									 isset( $groups_product_groups[$order_id][$product->id]['groups'] )
								) {
									foreach( $groups_product_groups[$order_id][$product->id]['groups'] as $group_id ) {
										self::maybe_delete( $user_id, $group_id, $order_id );
									}
								}
							}
						}
					}
				}
			}
		}

		self::unregister_order( $order_id );
	}

	/**
	 * Creates group membership for the order.
	 * @param int $order_id
	 */
	public static function order_status_completed( $order_id ) {

		$unhandled = self::register_order( $order_id );

		if ( $order = Groups_WS_Helper::get_order( $order_id ) ) {
			if ( $items = $order->get_items() ) {
				if ( $user_id = $order->user_id ) { // not much we can do if there isn't
					foreach ( $items as $item ) {
						if ( $product = $order->get_product_from_item( $item ) ) {
							// add to groups
							$product_groups = get_post_meta( $product->id, '_groups_groups', false );
							if ( $product->product_type == 'variation' ) {
								if ( isset( $product->variation_id ) ) {
									if ( $variation_product_groups = get_post_meta( $product->variation_id, '_groups_variation_groups', false ) ) {
										$product_groups = array_merge( $product_groups, $variation_product_groups );
									}
								}
							}
							if ( $product_groups ) {
								// don't act on subscriptions here
								if ( !class_exists( 'WC_Subscriptions_Product' ) || !WC_Subscriptions_Product::is_subscription( $product->id ) ) {
									if ( count( $product_groups ) > 0 ) {
										// add the groups to the user by order and product so that if the product is changed later on,
										// the data is still valid for what has been purchased
										$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
										if ( empty( $groups_product_groups ) ) {
											$groups_product_groups = array();
										}
										$start = time();
										$groups_product_groups[$order_id][$product->id]['version'] = GROUPS_WS_VERSION;
										$groups_product_groups[$order_id][$product->id]['start']   = $start;
										$groups_product_groups[$order_id][$product->id]['groups']  = $product_groups;
										update_user_meta( $user_id, '_groups_product_groups', $groups_product_groups );
										global $groups_ws_product_with_duration;
										$groups_ws_product_with_duration = Groups_WS_Product::has_duration( $product );
										if ( $groups_ws_product_with_duration ) {
											$groups_product_groups[$order_id][$product->id]['duration'] = get_post_meta( $product->id, '_groups_duration', true );
											$groups_product_groups[$order_id][$product->id]['duration_uom'] = get_post_meta( $product->id, '_groups_duration_uom', true );
											update_user_meta( $user_id, '_groups_product_groups', $groups_product_groups );
										}

										// add the user to the groups
										foreach( $product_groups as $group_id ) {
											Groups_User_Group::create(
												array(
													'user_id' => $user_id,
													'group_id' => $group_id
												)
											);
											if ( $groups_ws_product_with_duration ) {
												if ( $unhandled ) {
													Groups_WS_Terminator::schedule_termination( $start + Groups_WS_Product::get_duration( $product ), $user_id, $group_id );
												}
											} else {
												Groups_WS_Terminator::mark_as_eternal( $user_id, $group_id );
											}
										}

									}
								}
							}
							// remove from groups
							$product_groups_remove = get_post_meta( $product->id, '_groups_groups_remove', false );
							if ( $product->product_type == 'variation' ) {
								if ( isset( $product->variation_id ) ) {
									if ( $variation_product_groups_remove = get_post_meta( $product->variation_id, '_groups_variation_groups_remove', false ) ) {
										$product_groups_remove = array_merge( $product_groups_remove, $variation_product_groups_remove );
									}
								}
							}
							if ( $product_groups_remove ) {
								if ( !class_exists( 'WC_Subscriptions_Product' ) || !WC_Subscriptions_Product::is_subscription( $product->id ) ) {
									if ( count( $product_groups_remove )  > 0 ) {
										$groups_product_groups_remove = get_user_meta( $user_id, '_groups_product_groups_remove', true );
										if ( empty( $groups_product_groups_remove ) ) {
											$groups_product_groups_remove = array();
										}
										$groups_product_groups_remove[$order_id][$product->id]['groups'] = $product_groups_remove;
										update_user_meta( $user_id, '_groups_product_groups_remove', $groups_product_groups_remove );
										// remove the user from the groups
										foreach( $product_groups_remove as $group_id ) {
											self::maybe_delete( $user_id, $group_id, $order_id );
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Revokes group memberships for the order.
	 * @param int $order_id
	 */
	public static function order_status_refunded( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_failed( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_on_hold( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_pending( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Proxy for cancel.
	 * @param int $order_id
	 */
	public static function order_status_processing( $order_id ) {
		self::order_status_cancelled( $order_id );
	}

	/**
	 * Hooked on user added to group.
	 * @param int $user_id
	 * @param int $group_id
	 */
	public static function groups_created_user_group( $user_id, $group_id ) {
		global $groups_ws_product_with_duration;
		if ( !isset( $groups_ws_product_with_duration ) || !$groups_ws_product_with_duration ) {
			Groups_WS_Terminator::mark_as_eternal( $user_id, $group_id );
		}
	}

	/**
	 * Hooked on user removed from group.
	 * @param int $user_id
	 * @param int $group_id
	 */
	public static function groups_deleted_user_group( $user_id, $group_id ) {
		Groups_WS_Terminator::lift_scheduled_terminations( $user_id, $group_id, false );
	}

	/**
	 * Marks order as handled only if not already marked.
	 * @param int $order_id
	 * @return boolean true if order wasn't handled yet and could be marked as handled, otherwise false
	 */
	public static function register_order( $order_id ) {
		$registered = false;
		if ( $order = Groups_WS_Helper::get_order( $order_id ) ) {
			$r = get_post_meta( $order->id, '_groups_ws_registered', true );
			if ( empty( $r ) ) {
				$registered = update_post_meta( $order->id, '_groups_ws_registered', true );
			}
		}
		return $registered;
	}

	/**
	 * Marks order as not handled.
	 * @param int $order_id
	 * @return boolean true if order could be marked as not handled, false on failure
	 */
	public static function unregister_order( $order_id ) {
		$unregistered = false;
		if ( $order = Groups_WS_Helper::get_order( $order_id ) ) {
			$r = get_post_meta( $order->id, '_groups_ws_registered', true );
			if ( !empty( $r ) ) {
				$unregistered = delete_post_meta( $order->id, '_groups_ws_registered' );
			}
		}
		return $unregistered;
	}

	/**
	 * Handle group assignment : assign the user to the groups related to the subscription's product.
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function activated_subscription( $user_id, $subscription_key ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		if ( isset( $subscription['product_id'] ) && isset( $subscription['order_id'] ) ) {
			$product_id = $subscription['product_id'];
			$order_id = $subscription['order_id'];
			// Leasving this here for reference, it can be assumed that normally,
			// if the product's groups are modified, a reactivation should take its
			// data from the current product, not from its previous state.
			// See if the subscription was activated before and try to get subscription's groups.
			// If there are any, use these instead of those from the product.
			// This is necessary when a subscription has been cancelled and re-activated and the
			// original product groups were modified since and we do NOT want to make group
			// assignments based on the current state of the product.
			$done = false;
			//$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
			//if ( isset( $groups_product_groups[$order_id] ) && isset( $groups_product_groups[$order_id][$product_id] ) &&
			//	 isset( $groups_product_groups[$order_id][$product_id]['groups'] ) &&
			//	 isset( $groups_product_groups[$order_id][$product_id]['subscription_key'] ) &&
			//	( $groups_product_groups[$order_id][$product_id]['subscription_key'] === $subscription_key )
			//) {
			//	foreach( $groups_product_groups[$order_id][$product_id]['groups'] as $group_id ) {
			//		Groups_User_Group::create( $user_id, $group_id );
			//	}
			//	$done = true;
			//}

			// maybe unschedule pending expiration
			wp_clear_scheduled_hook(
				'groups_ws_subscription_expired',
				array(
					'user_id' => $user_id,
					'subscription_key' => $subscription_key
				)
			);

			if ( !$done ) {
				// get the product from the subscription
				$product = groups_ws_get_product( $product_id );
				if ( $product->exists() ) {
					// get the groups related to the product
					$product_groups = get_post_meta( $product_id, '_groups_groups', false );
					if ( isset( $subscription['variation_id'] ) ) {
						if ( $variation_product_groups = get_post_meta( $subscription['variation_id'], '_groups_variation_groups', false ) ) {
							$product_groups = array_merge( $product_groups, $variation_product_groups );
						}
					}
					if ( $product_groups ) {
						if ( count( $product_groups )  > 0 ) {
							// add the groups to the subscription (in case the product is changed later on, the subscription is still valid)
							$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
							if ( empty( $groups_product_groups ) ) {
								$groups_product_groups = array();
							}
							$groups_product_groups[$order_id][$product_id]['version'] = GROUPS_WS_VERSION;
							$groups_product_groups[$order_id][$product_id]['start']  = time();
							$groups_product_groups[$order_id][$product_id]['groups']  = $product_groups;
							$groups_product_groups[$order_id][$product_id]['subscription_key'] = $subscription_key;
							update_user_meta( $user_id, '_groups_product_groups', $groups_product_groups );
							// add the user to the groups
							foreach( $product_groups as $group_id ) {
								Groups_User_Group::create(
									array(
										'user_id' => $user_id,
										'group_id' => $group_id
									)
								);
							}
							Groups_WS_Terminator::mark_as_eternal( $user_id, $group_id );
						}
					}
					// remove from groups
					$product_groups_remove = get_post_meta( $product_id, '_groups_groups_remove', false );
					if ( isset( $subscription['variation_id'] ) ) {
						if ( $variation_product_groups_remove = get_post_meta( $subscription['variation_id'], '_groups_variation_groups_remove', false ) ) {
							$product_groups_remove = array_merge( $product_groups_remove, $variation_product_groups_remove );
						}
					}
					if ( $product_groups_remove ) {
						if ( count( $product_groups_remove )  > 0 ) {
							$groups_product_groups_remove = get_user_meta( $user_id, '_groups_product_groups_remove', true );
							if ( empty( $groups_product_groups_remove ) ) {
								$groups_product_groups_remove = array();
							}
							$groups_product_groups_remove[$order_id][$product_id]['groups'] = $product_groups_remove;
							update_user_meta( $user_id, '_groups_product_groups_remove', $groups_product_groups_remove );
							// remove the user from the groups
							foreach( $product_groups_remove as $group_id ) {
								self::maybe_delete( $user_id, $group_id, $order_id );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Add to groups after a subscription on hold has been reactivated.
	 * 
	 * This must NOT replicate the full action taken when a subscription is
	 * activated initially but reinstate group membership that was previously
	 * revoked.
	 * 
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function reactivated_subscription( $user_id, $subscription_key ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		if ( isset( $subscription['product_id'] ) && isset( $subscription['order_id'] ) ) {
			$product_id = $subscription['product_id'];
			$order_id = $subscription['order_id'];
			$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
			if ( isset( $groups_product_groups[$order_id] ) &&
					isset( $groups_product_groups[$order_id][$product_id] ) &&
					isset( $groups_product_groups[$order_id][$product_id]['groups'] )
			) {
				foreach( $groups_product_groups[$order_id][$product_id]['groups'] as $group_id ) {
					Groups_User_Group::create(
						array(
							'user_id' => $user_id,
							'group_id' => $group_id
						)
					);
				}
			}
		}
	}

	/**
	 * Remove the user from the subscription product's related groups.
	 * 
	 * For cancelled subscriptions that should still allow group membership
	 * until the end of the related subscription's end of term,
	 * Groups_WS_Handler::subscription_end_of_prepaid_term() is used instead.
	 * 
	 * This will only act when the related order is refunded or cancelled.
	 * 
	 * @see Groups_WS_Handler::subscription_end_of_prepaid_term()
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function cancelled_subscription( $user_id, $subscription_key ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		if ( isset( $subscription['product_id'] ) && isset( $subscription['order_id'] ) ) {
			if ( $order = Groups_WS_Helper::get_order( $subscription['order_id'] ) ) {
				switch( $order->status ) {
					case 'cancelled' :
					case 'refunded' :
						self::subscription_expired( $user_id, $subscription_key );
						break;
				}
			}
		}
	}

	/**
	 * Handle switched subscriptions to remove the user from the subscription
	 * product's related groups when a subscription has been switched.
	 * 
	 * @param string $subscription_key
	 * @param array $new_subscription_details
	 */
	public static function updated_users_subscription( $subscription_key, $new_subscription_details ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		if ( isset( $subscription['status'] ) && ( 'switched' == $subscription['status'] ) ) {
			if ( isset( $subscription['product_id'] ) && isset( $subscription['order_id'] ) ) {
				if ( $order = Groups_WS_Helper::get_order( $subscription['order_id'] ) ) {
					if ( ( $order->status == 'processing' ) || ( $order->status == 'completed' ) ) {
						if ( $user_id = $order->user_id ) {
							self::subscription_expired( $user_id, $subscription_key );
						}
					}
				}
			}
		}
	}

	/**
	 * Immediately remove the user from the subscription product's related groups.
	 * This is called when a cancelled subscription paid up period ends.
	 * The cancelled_subscription hook cannot be used because subscription is
	 * already cleared when the action is triggered and the
	 * get_next_payment_date() method will not return a payment date that
	 * we could use.
	 * @since 1.3.4
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function subscription_end_of_prepaid_term( $user_id, $subscription_key ) {
		self::subscription_expired( $user_id, $subscription_key );
	}

	/**
	 * Trashed subscriptions expire immediately.
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function subscription_trashed( $user_id, $subscription_key ) {
		self::subscription_expired( $user_id, $subscription_key );
		// unschedule pending expiration if any
		wp_clear_scheduled_hook(
			'groups_ws_subscription_expired',
			array(
				'user_id' => $user_id,
				'subscription_key' => $subscription_key
			)
		);
	}

	/**
	 * Subscription on hold => remove users from groups.
	 * 
	 * The semantics are different than those of an expired subscription,
	 * do NOT make use of Groups_WS_Handler::subscription_expired(), even
	 * though the current implementation is exactly the same, it would most
	 * probably not be appropriate if the implementation of that method was
	 * changed.
	 * 
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function subscription_put_on_hold( $user_id, $subscription_key ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		if ( isset( $subscription['product_id'] ) && isset( $subscription['order_id'] ) ) {
			$product_id = $subscription['product_id'];
			$order_id = $subscription['order_id'];
			$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
			if ( isset( $groups_product_groups[$order_id] ) &&
				 isset( $groups_product_groups[$order_id][$product_id] ) &&
				 isset( $groups_product_groups[$order_id][$product_id]['groups'] )
			) {
				foreach( $groups_product_groups[$order_id][$product_id]['groups'] as $group_id ) {
					self::maybe_delete( $user_id, $group_id, $order_id );
				}
			}
		}
	}

	/**
	 * Same as when a subscription is cancelled.
	 * @param int $user_id
	 * @param string $subscription_key
	 */
	public static function subscription_expired( $user_id, $subscription_key ) {
		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		if ( isset( $subscription['product_id'] ) && isset( $subscription['order_id'] ) ) {
			$product_id = $subscription['product_id'];
			$order_id = $subscription['order_id'];
			$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
			if ( isset( $groups_product_groups[$order_id] ) &&
				 isset( $groups_product_groups[$order_id][$product_id] ) &&
				 isset( $groups_product_groups[$order_id][$product_id]['groups'] )
			) {
				foreach( $groups_product_groups[$order_id][$product_id]['groups'] as $group_id ) {
					self::maybe_delete( $user_id, $group_id, $order_id );
				}
			}
		}
	}

	/**
	 * Force registration on checkout when a subscription is
	 * in the cart or when a product with groups assigned is in the cart.
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public static function option_woocommerce_enable_guest_checkout( $value ) {
		$options = get_option( 'groups-woocommerce', null );
		$force_registration = isset( $options[GROUPS_WS_FORCE_REGISTRATION] ) ? $options[GROUPS_WS_FORCE_REGISTRATION] : GROUPS_WS_DEFAULT_FORCE_REGISTRATION;
		if ( $force_registration ) {
			if ( self::has_groups_product_in_cart() ) {
				$value = 'no';
			}
		}
		return $value;
	}

	/**
	 * Enable login form on checkout when a subscription is
	 * in the cart or when a product with groups assigned is in the cart.
	 * 
	 * @param unknown_type $value
	 */
	public static function option_woocommerce_enable_signup_and_login_from_checkout( $value ) {
		$options = get_option( 'groups-woocommerce', null );
		$force_registration = isset( $options[GROUPS_WS_FORCE_REGISTRATION] ) ? $options[GROUPS_WS_FORCE_REGISTRATION] : GROUPS_WS_DEFAULT_FORCE_REGISTRATION;
		if ( $force_registration ) {
			if ( self::has_groups_product_in_cart() ) {
				$value = 'yes';
			}
		}
		return $value;
	}

	/**
	 * Returns true if a product with groups assigned is in the cart, false otherwise.
	 * 
	 * @return boolean whether a product with groups assigned is in the cart
	 */
	public static function has_groups_product_in_cart() {
		global $woocommerce;
		$result = false;
		if ( isset( $woocommerce ) && isset( $woocommerce->cart ) ) {
			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
				$product_groups        = get_post_meta( $values['product_id'], '_groups_groups', false );
				$product_groups_remove = get_post_meta( $values['product_id'], '_groups_groups_remove', false );
				if ( isset( $values['variation_id'] ) ) {
					if ( $variation_product_groups = get_post_meta( $values['variation_id'], '_groups_variation_groups', false ) ) {
						$product_groups = array_merge( $product_groups, $variation_product_groups );
					}
					if ( $variation_product_groups_remove = get_post_meta( $values['variation_id'], '_groups_variation_groups_remove', false ) ) {
						$product_groups_remove = array_merge( $product_groups_remove, $variation_product_groups_remove );
					}
				}
				if ( ( count( $product_groups ) > 0 ) || count( $product_groups_remove ) > 0 ) {
					$result = true;
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * Returns an array of order IDs for valid orders related to the user.
	 * Valid orders or those that are completed (and processing if the option is set).
	 * 
	 * @param int $user_id
	 * @return array of int, order IDs
	 */
	public static function get_user_valid_order_ids( $user_id ) {
		$order_ids = array();
		if ( !empty( $user_id ) ) {
			$statuses = array( 'completed' );
			$options = get_option( 'groups-woocommerce', array() );
			$order_status = isset( $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] ) ? $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] : GROUPS_WS_DEFAULT_MEMBERSHIP_ORDER_STATUS;
			if ( $order_status == 'processing' ) {
				$statuses[] = 'processing';
			}
			if ( groups_ws_is_wc22() ) {
				$wc_statuses = groups_ws_order_status( $statuses );
				$order_ids = get_posts( array(
					'fields'      => 'ids',
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $user_id,
					'post_type'   => 'shop_order',
					'post_status' => $wc_statuses
				) );
			} else {
				$order_ids = get_posts( array(
					'fields'      => 'ids',
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $user_id,
					'post_type'   => 'shop_order',
					'post_status' => 'publish',
					'tax_query'   => array( array(
						'taxonomy' => 'shop_order_status',
						'field'    => 'slug',
						'terms'    => $statuses
					) )
				) );
			}
		}
		return $order_ids;
	}

	/**
	 * Returns an array of order IDs for valid orders that grant group
	 * membership for the given group to the user related to the order.
	 * 
	 * Currently not used.
	 * 
	 * @param int $user_id
	 * @param int $group_id
	 * @return array of int, order IDs
	 */
	public static function get_valid_order_ids_granting_group_membership_from_product_groups( $user_id, $group_id ) {
		$order_ids = array();
		if ( !empty( $user_id ) ) {
			$statuses = array( 'completed' );
			$options = get_option( 'groups-woocommerce', array() );
			$order_status = isset( $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] ) ? $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] : GROUPS_WS_DEFAULT_MEMBERSHIP_ORDER_STATUS;
			if ( $order_status == 'processing' ) {
				$statuses[] = 'processing';
			}
			$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
			if ( empty( $groups_product_groups ) ) {
				$groups_product_groups = array();
			}
			foreach( $groups_product_groups as $order_id => $product_ids ) {
				if ( $order = Groups_WS_Helper::get_order( $order_id ) ) {
					if ( in_array( $order->status, $statuses ) ) {
						// this is a completed/processing order so we must consider group assignments
						foreach( $product_ids as $product_id => $group_ids ) {
							if ( in_array( $group_id, $group_ids ) ) {
								$order_ids[] = $order_id;
							}
						}
					}
				}
			}
		}
		return $order_ids;
	}

	/**
	 * Returns an array of order IDs for valid orders that grant group
	 * membership for the given group to the user related to the order.
	 * 
	 * @param int $user_id
	 * @param int $group_id
	 * @return array of int, order IDs
	 */
	public static function get_valid_order_ids_granting_group_membership_from_order_items( $user_id, $group_id ) {
		$order_ids = array();
		if ( !empty( $user_id ) ) {
			$statuses = array( 'completed' );
			$options = get_option( 'groups-woocommerce', array() );
			$order_status = isset( $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] ) ? $options[GROUPS_WS_MEMBERSHIP_ORDER_STATUS] : GROUPS_WS_DEFAULT_MEMBERSHIP_ORDER_STATUS;
			if ( $order_status == 'processing' ) {
				$statuses[] = 'processing';
			}
			$groups_product_groups = get_user_meta( $user_id, '_groups_product_groups', true );
			if ( empty( $groups_product_groups ) ) {
				$groups_product_groups = array();
			}
			foreach( $groups_product_groups as $order_id => $product_ids ) {
				if ( $order = Groups_WS_Helper::get_order( $order_id ) ) {
					// If this is a completed/processing order, consider group assignments:
					if ( in_array( $order->status, $statuses ) ) {
						// Note that for orders placed with versions up to 1.4.1, the following won't give the results we might expect if the product group-related information has changed since the order was placed.
						// As we don't store that information (WC doesn't store the whole lot of the product when purchased, nor does GW) checking the duration based on the product is the best effort at
						// finding out about the group membership duration we can make.
						// Use the order items (only existing order items are taken into account).
						if ( $items = $order->get_items() ) {
							foreach ( $items as $item ) {
								if ( $product = $order->get_product_from_item( $item ) ) {
									// Use the groups that were stored for the product when it was ordered,
									// this avoids hickups when the product's groups were changed since.
									if ( isset( $product_ids[$product->id] ) && isset( $product_ids[$product->id]['groups'] ) ) {
										$product_groups = $product_ids[$product->id]['groups'];
										if ( in_array( $group_id, $product_groups ) ) {
											// non-subscriptions
											if ( !class_exists( 'WC_Subscriptions_Product' ) || !WC_Subscriptions_Product::is_subscription( $product->id ) ) {

												if ( isset( $product_ids[$product->id] ) &&
													 isset( $product_ids[$product->id]['version'] ) // as of 1.5.0
												) {
													$has_duration =
														isset( $product_ids[$product->id]['duration'] ) &&
														$product_ids[$product->id]['duration'] &&
														isset( $product_ids[$product->id]['duration_uom'] );
												} else {
													$has_duration = Groups_WS_Product::has_duration( $product );
												}
												// unlimited membership
												if ( !$has_duration ) {
													if ( !in_array( $order_id, $order_ids ) ) {
														$order_ids[] = $order_id;
													}
												} else {
													if ( isset( $product_ids[$product->id] ) &&
													 	 isset( $product_ids[$product->id]['version'] ) // as of 1.5.0
													) {
														$duration = Groups_WS_Product::calculate_duration(
															$product_ids[$product->id]['duration'],
															$product_ids[$product->id]['duration_uom']
														);
													} else { // <= 1.4.1
														$duration = Groups_WS_Product::get_duration( $product );
													}
													// time-limited membership
													if ( $duration ) {
														$start_date = $order->order_date;
														if ( $paid_date = get_post_meta( $order_id, '_paid_date', true ) ) {
															$start_date = $paid_date;
														}
														$end = strtotime( $start_date ) + $duration;
														if ( time() < $end ) {
															if ( !in_array( $order_id, $order_ids ) ) {
																$order_ids[] = $order_id;
															}
														}
													}
												}

											} else {
												// include active subscriptions
												$subscription_key = WC_Subscriptions_Manager::get_subscription_key( $order_id, $product->id );
												$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
												if ( isset( $subscription['status'] ) ) {
													$valid = false;
													if ( $subscription['status'] == 'active' ) {
														$valid = true;
													} else if ( $subscription['status'] == 'cancelled' ) {
														$hook_args = array( 'user_id' => ( int ) $user_id, 'subscription_key' => $subscription_key );
														$end_timestamp = wp_next_scheduled( 'scheduled_subscription_end_of_prepaid_term', $hook_args );
														if ( ( $end_timestamp !== false ) && ( $end_timestamp > time() ) ) {
															$valid = true;
														}
													}
													if ( $valid ) {
														if ( !in_array( $order_id, $order_ids ) ) {
															$order_ids[] = $order_id;
														}
													}
												}
											}
										}
									}
								}
							}
						}

					}
				}
			}
		}
		return $order_ids;
	}

	/**
	 * Deletes the user from the group if no other orders than $order_id
	 * currently grant membership to that group.
	 * 
	 * @param int $user_id
	 * @param int $group_id
	 * @param int $order_id
	 */
	public static function maybe_delete( $user_id, $group_id, $order_id ) {
		$order_ids = Groups_WS_Handler::get_valid_order_ids_granting_group_membership_from_order_items( $user_id, $group_id );
		$ids = array_diff( $order_ids, array( $order_id ) );
		if ( count( $ids ) == 0 ) {
			Groups_User_Group::delete( $user_id, $group_id );
			if ( GROUPS_WS_LOG ) {
				error_log( sprintf( __METHOD__ . ' deleted membership for user ID %d with group ID %d', $user_id, $group_id ) );
			}
		} else {
			if ( GROUPS_WS_LOG ) {
				error_log( sprintf( __METHOD__ . ' membership for user ID %d with group ID %d has not been deleted due to other orders granting membership, order IDs: %s', $user_id, $group_id, implode( ',', $order_ids ) ) );
			}
		}
	}
}
Groups_WS_Handler::init();

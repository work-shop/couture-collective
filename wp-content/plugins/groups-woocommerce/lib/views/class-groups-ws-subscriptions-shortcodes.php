<?php
/**
 * class-groups-ws-subscriptions-shortcodes.php
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
 * @since groups-woocommerce 1.4.0
 */

/**
 * Subscription shortcode handlers
 */
class Groups_WS_Subscriptions_Shortcodes {

	/**
	 * Adds subscription shortcodes.
	 */
	public static function init() {
		add_shortcode( 'groups_woocommerce_subscriptions_table', array( __CLASS__, 'groups_woocommerce_subscriptions_table' ) );
	}

	/**
	 * Renders a table showing a user's subscriptions.
	 * 
	 * Attributes:
	 * 
	 * - "status"   : subscription status, defaults to active. Can be a comma-separated list or * for all subscriptions.
	 * - "user_id"  : defaults to the current user's id, accepts user id, email or login
	 * - "show_count" : If info about how many subscriptions are listed should be shown. Defaults to "true".
	 * - "count_0" : Message for 0 subscriptions listed.
	 * - "count_1" : Message for 1 subscription listed.
	 * - "count_2" : Message for n subscriptions listed, use %d as placeholder in a customized message.
	 * - "show_table" : If the table should be shown. Defaults to "true".
	 * - "columns" : Which columns should be included. Defaults to all columns. Specify one or more of subscription_id, processor, reference, status, dates, cycle, description, groups, order.
	 * @param array $atts attributes
	 * @param string $content content to render
	 * @return rendered information
	 */
	public static function groups_woocommerce_subscriptions_table( $atts, $content = null ) {
		$output = '';

		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			// for translation
			__( 'No active subscriptions.', GROUPS_WS_PLUGIN_DOMAIN );
			_n( 'One active subscription.', '%d active subscriptions.', 0, GROUPS_WS_PLUGIN_DOMAIN );
			__( 'No subscriptions.', GROUPS_WS_PLUGIN_DOMAIN );
			_n( 'One subscription.', '%d subscriptions.', 0, GROUPS_WS_PLUGIN_DOMAIN );

			$options = shortcode_atts(
				array(
					'status' => 'active',
					'user_id' => get_current_user_id(),
					'show_count' => true,
					'count_0' => 'No subscriptions.',
					'count_1' => 'One subscription.',
					'count_n' => '%d subscriptions.',
					'show_table' => true,
					'columns' => null,
					'exclude_cancelled_after_end_of_prepaid_term' => false,
					'include_cancelled_orders' => false,
					'include_refunded_orders' => false
				),
				$atts
			);

			extract( $options );

			$show_count = !( ( $show_count === false ) || ( strtolower( $show_count ) == 'no' ) || ( strtolower( $show_count ) == 'false' ) );

			if (
				( $user = get_user_by( 'id', $user_id ) ) ||
				( $user = get_user_by( 'login', $user_id ) ) ||
				( $user = get_user_by( 'email', $user_id ) )
			) {
				$user_id = $user->ID;
				if ( ( $user_id == get_current_user_id() )  || current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
					require_once( GROUPS_WS_VIEWS_LIB . '/class-groups-ws-subscriptions-table-renderer.php' );
					$table = Groups_WS_Subscriptions_Table_Renderer::render( $options, $n );
					if ( $show_count ) {
						$output .= '<div class="subscriptions-count">';
						if ( $n > 0 ) {
							$output .= sprintf( _n( $count_1, $count_n, $n, GROUPS_WS_PLUGIN_DOMAIN ), $n );
						} else {
							$output .= $count_0;
						}
						$output .= '</div>';
					}
					if ( $show_table ) {
						$output .= '<div class="subscriptions-table">';
						$output .= $table;
						$output .= '</div>';
					}
				}
			}
		}
		return $output;
	}

}
Groups_WS_Subscriptions_Shortcodes::init();

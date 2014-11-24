<?php
/**
 * class-groups-ws-shortcodes.php
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
 * @since groups-woocommerce 1.5.2
 */

/**
 * Shortcode resouce loader.
 */
class Groups_WS_Shortcodes {

	/**
	 * Handler on init:
	 * - lazy
	 * - so we know what's available
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
	}

	/**
	 * Load shortcode resources.
	 */
	public static function wp_init() {
		require_once GROUPS_WS_VIEWS_LIB . '/class-groups-ws-membership-shortcodes.php';
		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			require_once GROUPS_WS_VIEWS_LIB . '/class-groups-ws-subscriptions-shortcodes.php';
		}
	}
}
Groups_WS_Shortcodes::init();

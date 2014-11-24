<?php
/**
 * groups-woocommerce.php
 *
 * Copyright (c) 2012-2014 "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package groups-woocommerce
 * @since groups-woocommerce 1.0.0
 *
 * Plugin Name: Groups WooCommerce
 * Plugin URI: http://www.itthinx.com/plugins/groups-woocommerce
 * Description: Memberships with Groups and WooCommerce. Integrates Groups with WooCommerce and WooCommerce Subscriptions for group membership management based on product purchases and subscriptions. <a href="http://www.itthinx.com/documentation/groups-woocommerce/">Documentation</a> | <a href="http://www.itthinx.com/plugins/groups-woocommerce/">Plugin page</a>
 * Version: 1.7.1 
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 */

define( 'GROUPS_WS_VERSION', '1.7.1' );

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'aa2d455ed00566e4fb71bc9d53f2613b', '18704' );

if ( is_woocommerce_active() ) {
	define( 'GROUPS_WS_FILE', __FILE__ );
	define( 'GROUPS_WS_PLUGIN_URL',     plugin_dir_url( __FILE__ ) );
	define( 'GROUPS_WS_PLUGIN_DOMAIN', 'groups-woocommerce' );
	define( 'GROUPS_WS_LOG', false );

	if ( !defined( 'GROUPS_WS_CORE_DIR' ) ) {
		define( 'GROUPS_WS_CORE_DIR', WP_PLUGIN_DIR . '/groups-woocommerce' );
	}
	if ( !defined( 'GROUPS_WS_CORE_LIB' ) ) {
		define( 'GROUPS_WS_CORE_LIB', GROUPS_WS_CORE_DIR . '/lib/core' );
	}
	if ( !defined( 'GROUPS_WS_ADMIN_LIB' ) ) {
		define( 'GROUPS_WS_ADMIN_LIB', GROUPS_WS_CORE_DIR . '/lib/admin' );
	}
	if ( !defined( 'GROUPS_WS_VIEWS_LIB' ) ) {
		define( 'GROUPS_WS_VIEWS_LIB', GROUPS_WS_CORE_DIR . '/lib/views' );
	}
	require_once( GROUPS_WS_CORE_LIB . '/class-groups-ws.php');
}

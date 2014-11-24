<?php
/**
 * class-groups-ws.php
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
 * Boots.
 */
class Groups_WS {

	private static $admin_messages = array();

	/**
	 * Put hooks in place and activate.
	 */
	public static function init() {
		register_activation_hook(GROUPS_WS_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( GROUPS_WS_FILE, array( __CLASS__, 'deactivate' ) );
		register_uninstall_hook( GROUPS_WS_FILE, array( __CLASS__, 'uninstall' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		load_plugin_textdomain( GROUPS_WS_PLUGIN_DOMAIN, null, 'groups-woocommerce/languages' );
		if ( self::check_dependencies() ) {
			require_once GROUPS_WS_CORE_LIB . '/constants.php';
			require_once GROUPS_WS_CORE_LIB . '/class-groups-ws-helper.php';
			require_once GROUPS_WS_CORE_LIB . '/class-groups-ws-handler.php';
			require_once GROUPS_WS_CORE_LIB . '/class-groups-ws-terminator.php';
			if ( is_admin() ) {
				require_once GROUPS_WS_ADMIN_LIB . '/class-groups-ws-admin.php';
				require_once GROUPS_WS_ADMIN_LIB . '/class-groups-ws-admin-product.php';
				require_once GROUPS_WS_ADMIN_LIB . '/class-groups-ws-user.php';
			}
			require_once GROUPS_WS_ADMIN_LIB . '/class-groups-ws-product.php';
			require_once GROUPS_WS_VIEWS_LIB . '/class-groups-ws-shortcodes.php';
		}
	}

	/**
	 * Activate plugin.
	 * Reschedules pending tasks.
	 * @param boolean $network_wide
	 */
	public static function activate( $network_wide = false ) {
		$scheduled = get_option( 'groups_ws_scheduled', null );
		if ( !empty( $scheduled ) ) {
			foreach( $scheduled as $timestamp => $tasks ) {
				foreach ( $tasks as $task ) {
					if ( isset( $task['args'] ) &&
						 isset( $task['args']['user_id'] ) &&
						 isset( $task['args']['subscription_key'] )
					) {
						wp_schedule_single_event(
							$timestamp,
							'groups_ws_subscription_expired',
							array(
								'user_id' => $task['args']['user_id'],
								'subscription_key' => $task['args']['subscription_key']
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Deactivate plugin.
	 * @param boolean $network_wide
	 */
	public static function deactivate( $network_wide = false ) {
		$scheduled = self::clean_schedule();
		if ( !empty( $scheduled ) ) {
			update_option( 'groups_ws_scheduled', $scheduled );
		}
	}

	/**
	 * Uninstall plugin.
	 */
	public static function uninstall() {
		self::clean_schedule();
		delete_option( 'groups_ws_scheduled' );
	}

	/**
	 *  Clean up scheduled tasks.
	 *  @return array indexed by timestamp of array of scheduled tasks
	 */
	private static function clean_schedule() {
		$scheduled = array();
		try {
			$crons = _get_cron_array();
			if ( !empty( $crons ) ) {
				foreach ( $crons as $timestamp => $task ) {
					if ( isset( $task['groups_ws_subscription_expired'] ) ) {
						$scheduled[$timestamp] = $task['groups_ws_subscription_expired'];
						unset( $task['groups_ws_subscription_expired'] );
						if ( empty( $crons[$timestamp] ) ) {
							unset( $crons[$timestamp] );
						}
					}
				}
			}
		} catch ( Exception $e ) {
			error_log( sprintf(
					__( 'Groups WooCommerce failed to remove scheduled subscription expirations due to: %s', GROUPS_WS_PLUGIN_DOMAIN ),
					$e->getMessage()
			) );
		}
		return $scheduled;
	} 

	/**
	 * Prints admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			foreach ( self::$admin_messages as $msg ) {
				echo $msg;
			}
		}
	}

	/**
	 * Check plugin dependencies and nag if they are not met.
	 * @param boolean $disable disable the plugin if true, defaults to false
	 */
	public static function check_dependencies( $disable = false ) {
		$result = true;
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
			$active_plugins = array_merge( $active_plugins, $active_sitewide_plugins );
		}
		$groups_is_active = in_array( 'groups/groups.php', $active_plugins );
		$woocommerce_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
		if ( !$groups_is_active ) {
			self::$admin_messages[] = "<div class='error'>" . __( '<em>Groups WooCommerce</em> needs the <a href="http://www.itthinx.com/plugins/groups/" target="_blank">Groups</a> plugin. Please install and activate it.', GROUPS_WS_PLUGIN_DOMAIN ) . "</div>";
		}
		if ( !$woocommerce_is_active ) {
			self::$admin_messages[] = "<div class='error'>" . __( '<em>Groups WooCommerce</em> needs the <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> plugin. Please install and activate it.', GROUPS_WS_PLUGIN_DOMAIN ) . "</div>";
		}
		if ( !$groups_is_active || !$woocommerce_is_active ) {
			if ( $disable ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( array( __FILE__ ) );
			}
			$result = false;
		}
		return $result;
	}
}
Groups_WS::init();

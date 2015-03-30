<?php
/**
 * class-groups-ws-admin.php
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
 * @since groups-woocommerce 1.2.0
 */

/**
 * Admin section for Groups integration.
 */
class Groups_WS_Admin {

	const NONCE = 'groups-woocommerce-admin-nonce';
	const MEMBERSHIP_ORDER_STATUS = GROUPS_WS_MEMBERSHIP_ORDER_STATUS;
	const SHOW_DURATION           = GROUPS_WS_SHOW_DURATION;
	const FORCE_REGISTRATION      = GROUPS_WS_FORCE_REGISTRATION;
	const SHOW_IN_USER_PROFILE    = GROUPS_WS_SHOW_IN_USER_PROFILE;
	const SHOW_IN_EDIT_PROFILE    = GROUPS_WS_SHOW_IN_EDIT_PROFILE;

	/**
	 * Admin setup.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 40 );
	}

	/**
	 * Adds the admin section.
	 */
	public static function admin_menu() {
		$admin_page = add_submenu_page(
			'woocommerce',
			__( 'Groups', GROUPS_WS_PLUGIN_DOMAIN ),
			__( 'Groups', GROUPS_WS_PLUGIN_DOMAIN ),
			GROUPS_ADMINISTER_OPTIONS,
			'groups_woocommerce',
			array( __CLASS__, 'groups_woocommerce' )
		);
// 		add_action( 'admin_print_scripts-' . $admin_page, array( __CLASS__, 'admin_print_scripts' ) );
// 		add_action( 'admin_print_styles-' . $admin_page, array( __CLASS__, 'admin_print_styles' ) );
	}

	/**
	 * Renders the admin section.
	 */
	public static function groups_woocommerce() {

		if ( !current_user_can( GROUPS_ADMINISTER_OPTIONS ) ) {
			wp_die( __( 'Access denied.', GROUPS_WS_PLUGIN_DOMAIN ) );
		}

		$options = get_option( 'groups-woocommerce', null );
		if ( $options === null ) {
			if ( add_option( 'groups-woocommerce', array(), null, 'no' ) ) {
				$options = get_option( 'groups-woocommerce' );
			}
		}

		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], 'set' ) ) {
				$order_status = isset( $_POST[self::MEMBERSHIP_ORDER_STATUS] ) ? $_POST[self::MEMBERSHIP_ORDER_STATUS] : 'completed';
				switch ( $order_status ) {
					case 'completed' :
					case 'processing' :
						break;
					default :
						$order_status = GROUPS_WS_DEFAULT_MEMBERSHIP_ORDER_STATUS;
				}
				$options[self::MEMBERSHIP_ORDER_STATUS] = $order_status;
				$options[GROUPS_WS_REMOVE_ON_HOLD]      = isset( $_POST[GROUPS_WS_REMOVE_ON_HOLD] );
				$options[self::SHOW_DURATION]           = isset( $_POST[self::SHOW_DURATION] );
				$options[self::FORCE_REGISTRATION]      = isset( $_POST[self::FORCE_REGISTRATION] );
				$options[self::SHOW_IN_USER_PROFILE]    = isset( $_POST[self::SHOW_IN_USER_PROFILE] );
				$options[self::SHOW_IN_EDIT_PROFILE]    = isset( $_POST[self::SHOW_IN_EDIT_PROFILE] );

				update_option( 'groups-woocommerce', $options );
			}
		}

		$order_status       = isset( $options[self::MEMBERSHIP_ORDER_STATUS] ) ? $options[self::MEMBERSHIP_ORDER_STATUS] : GROUPS_WS_DEFAULT_MEMBERSHIP_ORDER_STATUS;
		$remove_on_hold     = isset( $options[GROUPS_WS_REMOVE_ON_HOLD] ) ? $options[GROUPS_WS_REMOVE_ON_HOLD] : GROUPS_WS_DEFAULT_REMOVE_ON_HOLD;
		$show_duration      = isset( $options[self::SHOW_DURATION] ) ? $options[self::SHOW_DURATION] : GROUPS_WS_DEFAULT_SHOW_DURATION;
		$force_registration = isset( $options[self::FORCE_REGISTRATION] ) ? $options[self::FORCE_REGISTRATION] : GROUPS_WS_DEFAULT_FORCE_REGISTRATION;
		$show_in_user_profile = isset( $options[self::SHOW_IN_USER_PROFILE] ) ? $options[self::SHOW_IN_USER_PROFILE] : GROUPS_WS_DEFAULT_SHOW_IN_USER_PROFILE;
		$show_in_edit_profile = isset( $options[self::SHOW_IN_EDIT_PROFILE] ) ? $options[self::SHOW_IN_EDIT_PROFILE] : GROUPS_WS_DEFAULT_SHOW_IN_EDIT_PROFILE;

		echo '<div class="groups-woocommerce">';

		echo '<h2>' . __( 'Groups', GROUPS_WS_PLUGIN_DOMAIN ) . '</h2>';

		echo
			'<form action="" name="options" method="post">' .
			'<div>' .

			'<h3>' . __( 'Group membership', GROUPS_WS_PLUGIN_DOMAIN ) . '</h3>' .
			'<h4>' . __( 'Order Status', GROUPS_WS_PLUGIN_DOMAIN ) . '</h4>' .
			'<p>' .
			'<label>' . __( 'Add users to or remove from groups as early as the order is ...', GROUPS_WS_PLUGIN_DOMAIN ) .
			'<select name="' . self::MEMBERSHIP_ORDER_STATUS . '">' .
			'<option value="completed" ' . ( $order_status == 'completed' ? ' selected="selected" ' : '' ) . '>' . __( 'Completed', GROUPS_WS_PLUGIN_DOMAIN ) . '</option>' .
			'<option value="processing" ' . ( $order_status == 'processing' ? ' selected="selected" ' : '' ) . '>' . __( 'Processing', GROUPS_WS_PLUGIN_DOMAIN ) . '</option>' .
			'</select>' .
			'</label>' .
			'</p>' .
			'<p class="description">' . __( 'Note that users will always be added to or removed from groups when an order is completed.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>' .
			'<p class="description">' . __( 'This setting does not apply to subscriptions.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>' .

			'<h4>' . __( 'Subscription Status', GROUPS_WS_PLUGIN_DOMAIN ) . '</h4>' .
			'<p>' .
			'<label>' .
			sprintf( '<input name="%s" type="checkbox" %s />', GROUPS_WS_REMOVE_ON_HOLD, $remove_on_hold ? ' checked="checked" ' : '' ) .
			' ' .
			__( 'Remove users from groups when a subscription is on hold; add them back when a subscription is reactivated.', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</label>' .
			'</p>' .
			'<p class="description">' . __( 'This setting only applies to subscriptions.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>' .

			'<h3>' . __( 'Durations', GROUPS_WS_PLUGIN_DOMAIN ) . '</h3>' .
			'<p>' .
			'<label>' .
			sprintf( '<input name="show_duration" type="checkbox" %s />', $show_duration ? ' checked="checked" ' : '' ) .
			' ' .
			__( 'Show durations', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</label>' .
			'</p>' .
			'<p class="description">' . __( 'Modifies the way product prices are displayed to show durations.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>' .

			'<h3>' . __( 'Force registration', GROUPS_WS_PLUGIN_DOMAIN ) . '</h3>' .
			'<p>' .
			'<label>' .
			'<input type="checkbox" ' . ( $force_registration ? ' checked="checked" ' : '' ) . ' name="' . GROUPS_WS_FORCE_REGISTRATION . '" />' .
			'&nbsp;' .
			__( 'Force registration on checkout', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</label>' .
			'</p>' .
			'<p class="description">' .
			__( 'Force registration on checkout when a subscription or a product which relates to groups is in the cart. The login form will also be shown to allow existing users to log in.', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</p>' .

			'<h3>' . __( 'User profiles', GROUPS_WS_PLUGIN_DOMAIN ) . '</h3>' .
			'<p>' .
			'<label>' .
			'<input type="checkbox" ' . ( $show_in_user_profile ? ' checked="checked" ' : '' ) . ' name="' . self::SHOW_IN_USER_PROFILE . '" />' .
			'&nbsp;' .
			__( 'Show membership info in user profiles', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</label>' .
			'</p>' .
			'<p class="description">' .
			__( 'If enabled, users can see information about their group memberships on the profile page.', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</p>' .
			'<p>' .
			'<label>' .
			'<input type="checkbox" ' . ( $show_in_edit_profile ? ' checked="checked" ' : '' ) . ' name="' . self::SHOW_IN_EDIT_PROFILE . '" />' .
			'&nbsp;' .
			__( 'Show membership info when editing user profiles', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</label>' .
			'</p>' .
			'<p class="description">' .
			__( 'If enabled, users who can edit other users can see and modify group memberships.', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</p>' .

			'<p>' .
			wp_nonce_field( 'set', self::NONCE, true, false ) .
			'<input class="button" type="submit" name="submit" value="' . __( 'Save', GROUPS_WS_PLUGIN_DOMAIN ) . '"/>' .
			'</p>' .
			'</div>' .
			'</form>';

		echo '</div>'; // .groups-woocommerce

		if ( GROUPS_WS_LOG ) {
			$crons = _get_cron_array();
			echo '<h2>Cron</h2>';
			echo '<pre>';
			echo var_export( $crons, true );
			echo '</pre>';
		}
	}
}
Groups_WS_Admin::init();

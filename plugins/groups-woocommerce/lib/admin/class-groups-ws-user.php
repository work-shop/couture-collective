<?php
/**
 * class-groups-ws-user.php
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
 * @since groups-woocommerce 1.3.0
 */

/**
 * Adds user membership info on profile pages.
 */
class Groups_WS_User {

	/**
	 * Adds action hooks.
	 */
	public static function init() {
		add_action( 'show_user_profile', array( __CLASS__, 'show_user_profile' ) );
		add_action( 'edit_user_profile', array( __CLASS__, 'edit_user_profile' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'personal_options_update' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'edit_user_profile_update' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Registers styles and scripts for the admin back end.
	 */
	public static function admin_enqueue_scripts() {
		wp_register_script( 'groups-ws-edit-timestamp', GROUPS_WS_PLUGIN_URL . 'js/edit-timestamp.js', array( 'jquery' ), GROUPS_WS_VERSION, true );
		wp_register_style( 'groups-ws-user-edit', GROUPS_WS_PLUGIN_URL . 'css/user-edit.css', array(), GROUPS_WS_VERSION );
	}

	/**
	 * Own profile.
	 * @param WP_User $user
	 */
	public static function show_user_profile( $user ) {
		$options = get_option( 'groups-woocommerce', null );
		$show_in_user_profile = isset( $options[GROUPS_WS_SHOW_IN_USER_PROFILE] ) ? $options[GROUPS_WS_SHOW_IN_USER_PROFILE] : GROUPS_WS_DEFAULT_SHOW_IN_USER_PROFILE;
		// also show it when editing your own profile
		$show_in_edit_profile = isset( $options[GROUPS_WS_SHOW_IN_EDIT_PROFILE] ) ? $options[GROUPS_WS_SHOW_IN_EDIT_PROFILE] : GROUPS_WS_DEFAULT_SHOW_IN_EDIT_PROFILE;
		if ( $show_in_user_profile || current_user_can('edit_users') && $show_in_edit_profile && ( $user->ID == get_current_user_id() ) ) {
			self::show_buckets( $user );
			if ( class_exists( 'WC_Subscriptions_Manager' ) ) {
				self::show_subscriptions( $user );
			}
		}
	}

	/**
	 * A user's profile.
	 * @param WP_User $user
	 */
	public static function edit_user_profile( $user ) {
		$options = get_option( 'groups-woocommerce', null );
		$show_in_edit_profile = isset( $options[GROUPS_WS_SHOW_IN_EDIT_PROFILE] ) ? $options[GROUPS_WS_SHOW_IN_EDIT_PROFILE] : GROUPS_WS_DEFAULT_SHOW_IN_EDIT_PROFILE;
		if ( $show_in_edit_profile ) {
			self::show_buckets( $user );
			if ( class_exists( 'WC_Subscriptions_Manager' ) ) {
				self::show_subscriptions( $user );
			}
		}
	}

	/**
	 * Renders group subscription info for the user.
	 * 
	 * @param object $user
	 */
	private static function show_subscriptions( $user ) {

		echo '<h3>';
		echo __( 'Group Subscriptions', GROUPS_WS_PLUGIN_DOMAIN );
		echo '</h3>';

		require_once( GROUPS_WS_VIEWS_LIB . '/class-groups-ws-subscriptions-table-renderer.php' );
			$table = Groups_WS_Subscriptions_Table_Renderer::render( array(
				'status' => 'active,cancelled',
				'exclude_cancelled_after_end_of_prepaid_term' => true,
				'user_id' => $user->ID,
				'columns' => array( 'groups', 'start_date', 'expiry_date', 'end_date' )
			),
			$n
		);
		echo apply_filters( 'groups_woocommerce_show_subscriptions_style', '<style type="text/css">div.subscriptions-count { padding: 0px 0px 1em 2px; } div.group-subscriptions table th { text-align:left; padding-right: 1em; }</style>' );
		echo '<div class="subscriptions-count">';
		if ( $n > 0 ) {
			echo sprintf( _n( 'One subscription.', '%d subscriptions.', $n, GROUPS_WS_PLUGIN_DOMAIN ), $n );
		} else {
			echo __( 'No subscriptions.', GROUPS_WS_PLUGIN_DOMAIN );
		}
		echo '</div>';
		echo '<div class="group-subscriptions">';
		echo $table;
		echo '</div>';
	} 

	/**
	 * Renders time-limited group membership info for the user.
	 * 
	 * The <code>groups_woocommerce_show_buckets_membership</code> filter can be used to modify how membership info is rendered.
	 * 
	 * @param object $user
	 */
	private static function show_buckets( $user ) {

		$user_buckets = get_user_meta( $user->ID, '_groups_buckets', true );

		if ( $user_buckets ) {

			if ( current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
				wp_enqueue_style( 'groups-ws-user-edit' );
				wp_enqueue_script( 'groups-ws-edit-timestamp' );
			}
			$timestamp_entries = array();

			echo '<h3>';
			echo __( 'Group Memberships', GROUPS_WS_PLUGIN_DOMAIN );
			echo '</h3>';

			echo '<ul>';
			uksort( $user_buckets, array( __CLASS__, 'bucket_cmp' ) );
			foreach( $user_buckets as $group_id => $timestamps ) {
				if ( $group = Groups_Group::read( $group_id ) ) {
					if ( Groups_User_Group::read( $user->ID, $group_id ) ) {
						echo '<li>';
						$ts = null;
						foreach( $timestamps as $timestamp ) {
							if ( intval( $timestamp ) === Groups_WS_Terminator::ETERNITY ) {
								$ts = Groups_WS_Terminator::ETERNITY;
								break;
							} else {
								if ( $timestamp > $ts ) {
									$ts = $timestamp;
								}
							}
						}
						if ( $ts !== null ) {
							$timestamp_entries[$group_id] = $ts;
							if ( $ts === Groups_WS_Terminator::ETERNITY ) {
								$membership_info = sprintf(
									__( '<em>%s</em> membership.', GROUPS_WS_PLUGIN_DOMAIN ),
									wp_filter_nohtml_kses( $group->name )
								);
							} else {
								$date = date_i18n( get_option( 'date_format' ), $ts );
								$time = date_i18n( get_option( 'time_format' ), $ts );
								$membership_info = sprintf(
									__( '<em>%1$s</em> membership until <span class="timestamp">%2$s at %3$s</span>.', GROUPS_WS_PLUGIN_DOMAIN ),
									wp_filter_nohtml_kses( $group->name ),
									$date,
									$time
								);
							}
							echo apply_filters( 'groups_woocommerce_show_buckets_membership', $membership_info, $group_id, $ts );
						}

						if ( GROUPS_WS_LOG ) {
							echo '<ul>';
							foreach( $timestamps as $timestamp ) {
								echo '<li>';
								if ( intval( $timestamp ) === Groups_WS_Terminator::ETERNITY ) {
									echo __( 'Unlimited', GROUPS_WS_PLUGIN_DOMAIN );
								} else {
									echo date( 'Y-m-d H:i:s', $timestamp );
								}
								echo '</li>';
							}
							echo '<ul>';
						}

						echo '</li>';
					}
				}
			}
			echo '</ul>';

			if ( current_user_can( GROUPS_ADMINISTER_GROUPS ) && ( count( $timestamp_entries ) > 0 ) ) {
				echo '<h4>';
				echo __( 'Edit Memberships', GROUPS_WS_PLUGIN_DOMAIN );
				echo '</h4>';
				echo '<table>';
				echo '<tr>';
				echo '<th>' . __( 'Group', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '<th>' . __( 'Year', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '<th>' . __( 'Month', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '<th>' . __( 'Day', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '<th>' . __( 'Hour', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '<th>' . __( 'Minute', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '<th>' . __( 'Second', GROUPS_WS_PLUGIN_DOMAIN ) . '</th>';
				echo '</tr>';
				foreach( $timestamp_entries as $group_id => $ts ) {
					if ( $group = Groups_Group::read( $group_id ) ) {
						if ( $group->name !== Groups_Registered::REGISTERED_GROUP_NAME ) {
							echo '<tr>';
							echo '<td>';
							echo wp_filter_nohtml_kses( $group->name );
							echo '</td>';
							$id_prefix = sprintf( '%d-%d', $group_id, $ts );
							if ( $ts === Groups_WS_Terminator::ETERNITY ) {
								$fields = array( 'Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'minute', 's' => 'second' );
								$size   = array( 'Y' => 4, 'm' => 2, 'd' => 2, 'H' => 2, 'i' => 2, 's' => 2 );
								$min    = array( 'Y' => 0, 'm' => 1, 'd' => 1, 'H' => 0, 'i' => 0, 's' => 0 );
								$max    = array( 'Y' => 9999, 'm' => 12, 'd' => 31, 'H' => 23, 'i' => 59, 's' => 59 );
								foreach( $fields as $format => $suffix ) {
									echo '<td>';
									printf(
										'<input name="%s" id="%s" class="timestamp-field %s" type="number" value="" size="%d" maxlength="%d" min="%d" max="%d" autocomplete="off" placeholder="%s"/>',
										sprintf( 'gw-ts[%d][%d][%s]', $group_id, $ts, $suffix ),
										$id_prefix . '_' . $suffix,
										$suffix,
										$size[$format],
										$size[$format],
										$min[$format],
										$max[$format],
										$suffix == 'year' ? '&infin;' : '-'
									);
									echo '</td>';
								}
								echo '<td>';
								echo '</td>';
							} else {
								$fields = array( 'Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'minute', 's' => 'second' );
								$size   = array( 'Y' => 4, 'm' => 2, 'd' => 2, 'H' => 2, 'i' => 2, 's' => 2 );
								$min    = array( 'Y' => 0, 'm' => 1, 'd' => 1, 'H' => 0, 'i' => 0, 's' => 0 );
								$max    = array( 'Y' => 9999, 'm' => 12, 'd' => 31, 'H' => 23, 'i' => 59, 's' => 59 );
								foreach( $fields as $format => $suffix ) {
									echo '<td>';
									printf(
										'<input name="%s" id="%s" class="timestamp-field %s" type="number" value="%d" size="%d" maxlength="%d" min="%d" max="%d" autocomplete="off" />',
										sprintf( 'gw-ts[%d][%d][%s]', $group_id, $ts, $suffix ),
										$id_prefix . '_' . $suffix,
										$suffix,
										date( $format, $ts ),
										$size[$format],
										$size[$format],
										$min[$format],
										$max[$format]
									);
									echo '</td>';
								}
								echo '<td>';
								printf(
									'<button class="eternal button" value="%s" title="%s" style="display:none;">&infin;</button>',
									$id_prefix,
									__( 'Convert to unlimited membership.', GROUPS_WS_PLUGIN_DOMAIN )
								);
								echo '<noscript>';
								echo __( 'Empty year sets unlimited membership.', GROUPS_WS_PLUGIN_DOMAIN );
								echo '</noscript>';
								echo '</td>';
							}
							echo '</tr>';
						}
					}
				}
				echo '</table>';

				echo '<p class="description">';
				echo __( 'Current memberships can be extended by modifying their time of expiration.', GROUPS_WS_PLUGIN_DOMAIN );
				echo ' ';
				echo __( 'Time-limited memberships can be converted to unlimited memberships by clearing their year.', GROUPS_WS_PLUGIN_DOMAIN );
				echo ' ';
				echo __( 'Unlimited memberships can be converted to time-limited memberships by indicating at least the year of expiration.', GROUPS_WS_PLUGIN_DOMAIN );
				echo ' ';
				echo __( 'Modifications to points in the past are not allowed.', GROUPS_WS_PLUGIN_DOMAIN );
				echo ' ';
				echo __( 'To expire a membership immediately, remove the corresponding group from the Groups field.', GROUPS_WS_PLUGIN_DOMAIN );
				echo ' ';
				echo __( 'To add a membership, first add the corresponding group to the Groups field and update the user profile; then limit the membership if desired.', GROUPS_WS_PLUGIN_DOMAIN );
				echo ' ';
				printf( __( 'Dates and times are relative to %s (GMT %s).', GROUPS_WS_PLUGIN_DOMAIN ), date( 'T', time() ), date( 'O', time() ) );
				echo '</p>';
			}
		}
	}

	/**
	 * Update own user profile.
	 * 
	 * @param int $user_id 
	 */
	public static function personal_options_update( $user_id ) {
		if ( current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
			self::edit_user_profile_update( $user_id );
		}
	}

	/**
	 * Update the user profile.
	 * 
	 * @param int $user_id
	 */
	public static function edit_user_profile_update( $user_id ) {

		global $wpdb;

		if ( current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {

			if ( !empty( $_POST['gw-ts'] ) && is_array( $_POST['gw-ts'] ) ) {
				foreach( $_POST['gw-ts'] as $group_id => $timestamps ) {
					foreach( $timestamps as $ts => $date ) {

						$year   = !empty( $date['year'] ) ? intval( $date['year'] ) : null;
						$month  = !empty( $date['month'] ) ? intval( $date['month'] ) : 1;
						$day    = !empty( $date['day'] ) ? intval( $date['day'] ) : 1;
						$hour   = !empty( $date['hour'] ) ? intval( $date['hour'] ) : 0;
						$minute = !empty( $date['minute'] ) ? intval( $date['minute'] ) : 0;
						$second = !empty( $date['second'] ) ? intval( $date['second'] ) : 0;

						if ( $year !== null ) {
							$timestamp = mktime( $hour, $minute, $second, $month, $day, $year );
							if ( $ts != $timestamp ) {
								// Only allow extensions.
								// If the time is set to a point in the past and the
								// core groups field still has the group entry (which would
								// need to be expected, then the eternity timestamp would
								// be removed form the bucket but the user-group
								// assignment would still be intact.
								// Instead of setting the time to a point in the past,
								// the group can simply be removed from the core
								// groups field (by the user who is editing the profile).
								if ( $timestamp > time() ) {
									Groups_WS_Terminator::lift_scheduled_terminations( $user_id, $group_id, false );
									Groups_WS_Terminator::schedule_termination( $timestamp, $user_id, $group_id );
								}
							}
						} else {

							if ( $ts !== Groups_WS_Terminator::ETERNITY ) {
								Groups_WS_Terminator::lift_scheduled_terminations( $user_id, $group_id );
								Groups_WS_Terminator::mark_as_eternal( $user_id, $group_id );
							}
						}

					}
				}
			}
		}
	}

	/**
	 * Sort helper - comparison by group name for given group ids.
	 * @param int $group_id1
	 * @param int $group_id2
	 * @return int
	 */
	public static function bucket_cmp( $group_id1, $group_id2 ) {
		$result = 0;
		if ( $g1 = Groups_Group::read( $group_id1 ) ) {
			if ( $g2 = Groups_Group::read( $group_id2 ) ) {
				if ( isset( $g1->name ) && isset( $g2->name ) ) {
					$result = strcmp( $g1->name, $g2->name );
				}
			}
		}
		return $result;
	}
}
Groups_WS_User::init();

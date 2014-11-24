<?php
/**
 * class-groups-ws-terminator.php
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
 * Terminates activate memberships when due.
 */
class Groups_WS_Terminator {

	const ETERNITY = 0;

	/**
	 * Add the termination hook.
	 */
	public static function init() {
		add_action( 'groups_ws_terminate_membership', array( __CLASS__, 'terminate_membership' ), 10, 2 );
	}

	/**
	 * Mark as eternal membership.
	 * @param int $user_id
	 * @param int $group_id
	 */
	public static function mark_as_eternal( $user_id, $group_id ) {
		// put eternity in the bucket
		require_once( GROUPS_WS_CORE_LIB . '/class-groups-ws-bucket.php' );
		$b = new Groups_WS_Bucket( $user_id, $group_id );
		$b->acquire();
		$B = $b->content;
		if ( !in_array( self::ETERNITY, $B ) ) {
			$B[] = self::ETERNITY;
			$b->content = $B;
		}
		$b->release();
	}

	/**
	 * Schedule termination of membership.
	 * @param int $time
	 * @param int $user_id
	 * @param int $group_id
	 */
	public static function schedule_termination( $time, $user_id, $group_id, $order_id = null ) {
		if ( GROUPS_WS_LOG ) {
			error_log( sprintf( __METHOD__ . ' scheduling termination of membership for user ID %d with group ID %d', $user_id, $group_id ) );
		}
		// put the timestamp in the bucket and schedule termination
		require_once( GROUPS_WS_CORE_LIB . '/class-groups-ws-bucket.php' );
		$b = new Groups_WS_Bucket( $user_id, $group_id );
		$b->acquire();
		$B = $b->content;
		$B[] = $time;

		// take the not-so-smart 10 minute limitation into account
		$next = wp_next_scheduled( 'groups_ws_terminate_membership', array( $user_id, $group_id ) );
		if ( $next && $next <= $time + 600 ) {
			wp_unschedule_event( $next, 'groups_ws_terminate_membership', array( $user_id, $group_id ) );
			if ( GROUPS_WS_LOG ) {
				error_log( sprintf( __METHOD__ . ' rescheduled termination of membership for user ID %d with group ID %d', $user_id, $group_id ) );
			}
		}
		// schedule removal
		wp_schedule_single_event( $time, 'groups_ws_terminate_membership', array( $user_id, $group_id ) );

		$b->content = $B;
		$b->release();
	}

	/**
	 * Clear scheduled terminations of membership.
	 * @param int $user_id
	 * @param int $group_id
	 * @param boolean $keep_eternity default true, use false to remove eternity from bucket
	 */
	public static function lift_scheduled_terminations( $user_id, $group_id, $keep_eternity = true ) {
		if ( GROUPS_WS_LOG ) {
			error_log( sprintf( __METHOD__ . ' lifting terminations of membership for user ID %d with group ID %d', $user_id, $group_id ) );
		}
		// remove the timestamps in the bucket and clear scheduled terminations
		require_once( GROUPS_WS_CORE_LIB . '/class-groups-ws-bucket.php' );
		$b = new Groups_WS_Bucket( $user_id, $group_id );
		$b->acquire();
		$B = $b->content;
		$has_eternity = false;
		foreach( $B as $timestamp ) {
			if ( $timestamp !== self::ETERNITY ) {
				wp_unschedule_event( $timestamp, 'groups_ws_terminate_membership', array( $user_id, $group_id ) );
			} else {
				$has_eternity = true;
			}
		}
		$B = array();
		if ( $has_eternity && $keep_eternity ) {
			$B[] = self::ETERNITY;
		}
		$b->content = $B;
		$b->release();
	}

	/**
	 * Execute membership termination.
	 * @param int $user_id
	 * @param int $group_id
	 */
	public static function terminate_membership( $user_id, $group_id ) {

		if ( GROUPS_WS_LOG ) {
			error_log( sprintf( __METHOD__ . ' testing for user ID %d with group ID %d', $user_id, $group_id ) );
		}

		require_once( GROUPS_WS_CORE_LIB . '/class-groups-ws-bucket.php' );

		$b = new Groups_WS_Bucket( $user_id, $group_id );
		$b->acquire();
		$B = $b->content;

		$t1 = time();

		$P = array();
		foreach( $B as $t ) {
			if ( $t <= $t1 && $t !== self::ETERNITY ) {
				$P[] = $t;
			}
		}

		$U_in_G = false;
		$groups_user = new Groups_User( $user_id );
		foreach( $groups_user->groups as $group ) {
			// Note that $group->group_id is a string, don't use === here:
			if ( $group->group_id == $group_id ) {
				$U_in_G = true;
				break;
			}
		}
		if ( $U_in_G ) {
			if ( count( $P ) > 0 ) {
				if ( count( $B ) == count( $P ) ) {
					Groups_User_Group::delete( $user_id, $group_id );
					if ( GROUPS_WS_LOG ) { error_log( sprintf( __METHOD__ . ' terminated membership for user ID %d with group ID %d', $user_id, $group_id ) ); }
					$B = array();
				} else {
					$B = array_diff( $B, $P );
				}
			}
		} else {
			$B = array_diff( $B, $P );
		}

		$b->content = $B;
		$b->release();
	}
}
Groups_WS_Terminator::init();

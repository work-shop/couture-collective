<?php
/**
 * class-groups-ws-membership-shortcodes.php
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
 * Membership shortcode handlers
 */
class Groups_WS_Membership_Shortcodes {

	/**
	 * Adds shortcodes.
	 */
	public static function init() {
		add_shortcode( 'groups_woocommerce_memberships', array( __CLASS__, 'groups_woocommerce_memberships' ) );
	}

	/**
	 * Renders time-limited group membership info for the user.
	 * 
	 * The <code>groups_woocommerce_show_membership</code> filter can be
	 * used to modify how membership info is rendered.
	 * 
	 * Possible shortcode attributes passed in $atts :
	 * 
	 * - 'exclude' : Groups to exclude can be specified using the exclude
	 *               entry for $atts. By default, the Registered group is
	 *               excluded.
	 * - 'no_memberships' : The label to be used when there are no memberships.
	 *                      This defaults to 'No memberships'.
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function groups_woocommerce_memberships( $atts, $content = null ) {

		$atts = shortcode_atts(
			array(
				'exclude'    => Groups_Registered::REGISTERED_GROUP_NAME,
				'show_count' => true,
				'count_0'    => 'No memberships.',
				'count_1'    => 'One membership.',
				'count_n'    => '%d memberships.'
			),
			$atts
		);

		extract( $atts );

		$exclude = array_map( 'trim', explode( ',', $exclude ) );

		$show_count = !( ( $show_count === false ) || ( strtolower( $show_count ) == 'no' ) || ( strtolower( $show_count ) == 'false' ) );

		// for translation
		__( 'No memberships.', GROUPS_WS_PLUGIN_DOMAIN );
		_n( 'One membership.', '%d memberships.', 0, GROUPS_WS_PLUGIN_DOMAIN );

		$output = '';
		if ( $user_id = get_current_user_id() ) {
			$user         = wp_get_current_user();
			$n            = 0;
			$list_output  = '';
			$user_buckets = get_user_meta( $user->ID, '_groups_buckets', true );
			if ( $user_buckets ) {
				$list_output .= '<ul>';
				uksort( $user_buckets, array( __CLASS__, 'bucket_cmp' ) );
				foreach( $user_buckets as $group_id => $timestamps ) {
					if ( $group = Groups_Group::read( $group_id ) ) {
						if ( !in_array( $group->name, $exclude ) && Groups_User_Group::read( $user->ID, $group_id ) ) {
							$n++;
							$list_output .= '<li>';
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
								if ( $ts === Groups_WS_Terminator::ETERNITY ) {
									$membership_info = sprintf(
										__( '<em>%s</em> membership.', GROUPS_WS_PLUGIN_DOMAIN ),
										wp_filter_nohtml_kses( $group->name )
									);
								} else {
									$date = date_i18n( get_option( 'date_format' ), $ts );
									$time = date_i18n( get_option( 'time_format' ), $ts );
									$membership_info = sprintf(
										__( '<em>%1$s</em> membership until %2$s at %3$s.', GROUPS_WS_PLUGIN_DOMAIN ),
										wp_filter_nohtml_kses( $group->name ),
										$date,
										$time
									);
								}
							}
							$list_output .= apply_filters( 'groups_woocommerce_show_membership', $membership_info, $group_id, $ts );
							$list_output .= '</li>';
						}
					}
				}
				$list_output .= '</ul>';
			}
			if ( $show_count ) {
				$output .= '<div class="membership-count">';
				if ( $n > 0 ) {
					$output .= sprintf( _n( $count_1, $count_n, $n, GROUPS_WS_PLUGIN_DOMAIN ), $n );
				} else {
					$output .= $count_0;
				}
				$output .= '</div>';
			}
			if ( $n > 0 ) {
				$output .= '<div class="membership-list">';
				$output .= $list_output;
				$output .= '</div>';
			}
		}
		return $output;
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
Groups_WS_Membership_Shortcodes::init();

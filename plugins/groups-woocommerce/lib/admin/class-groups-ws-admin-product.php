<?php
/**
 * class-groups-ws-admin-product.php
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
 * @since groups-woocommerce 1.5.0
 */

/**
 * Admin extensions to the product post type.
 */
class Groups_WS_Admin_Product {

	/**
	 * Hooks.
	 */
	public static function init() {
		if ( is_admin() ) { // yeah yeah ... just in case
			add_filter( 'manage_product_posts_columns', array( __CLASS__, 'manage_product_posts_columns' ) );
			add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'manage_product_posts_custom_column' ), 10, 2 );
			// @todo disabled for now, see below
			//add_filter( 'manage_edit-product_sortable_columns', array( __CLASS__, 'manage_product_posts_sortable_columns' ) );
			//add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		}
	}

	/**
	 * Sort by meta ... not ... see request(...)
	 */
	public static function admin_init() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && ( $screen->id == 'edit-product' ) ) {
			add_filter( 'request', array( __CLASS__, 'request' ) );
		}
	}

	/**
	 * Modify the $query_vars to sort by meta, but this is useless because
	 * we need to sort by the content of the related group names list.
	 * 
	 * @param array $query_vars
	 * @return array
	 */
	public static function request( $query_vars ) {
		if ( !isset( $query_vars['orderby'] ) || ( isset( $query_vars['orderby'] ) && 'groups' == $query_vars['orderby'] ) ) {
			$query_vars = array_merge( $query_vars, array(
				'meta_key' => '_groups_groups',
				'orderby' => 'meta_value'
			) );
		}
		return $vars;
	}

	/**
	 * Add the Groups column to the product overview screen.
	 * @param array $posts_columns
	 * @return array
	 */
	public static function manage_product_posts_columns( $posts_columns ) {
		$posts_columns['groups'] = sprintf(
			'<span title="%s">%s</span>',
			__( 'Groups to which customer is added and removed (within parenthesis).', GROUPS_WS_PLUGIN_DOMAIN ),
			__( 'Groups', GROUPS_WS_PLUGIN_DOMAIN )
		);
		return $posts_columns;
	}

	/**
	 * Adds sortable columns.
	 * @param array $posts_columns
	 * @return array
	 */
	public static function manage_product_posts_sortable_columns( $posts_columns ) {
		$posts_columns['groups'] = 'groups';
		return $posts_columns;
	}

	/**
	 * Renders the additional Groups column's content for each entry.
	 * @param string $column_name
	 * @param int $post_id
	 */
	public static function manage_product_posts_custom_column( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'groups' :
				echo self::render_group_list( $post_id );
				break;

		}
	}

	/**
	 * Render the list of groups related to a product - includes groups to which
	 * the user is added and those from which the user is removed.
	 * @param int $post_id Product's post ID
	 * @return string
	 */
	public static function render_group_list( $post_id ) {

		global $wpdb;

		$output = '';

		$product_groups        = get_post_meta( $post_id, '_groups_groups', false );
		$product_groups_remove = get_post_meta( $post_id, '_groups_groups_remove', false );

		$group_table = _groups_get_tablename( 'group' );
		$groups = $wpdb->get_results( "SELECT * FROM $group_table ORDER BY name" );

		if ( count( $groups ) > 0 ) {
			$list = array();
			foreach( $groups as $group ) {
				if ( is_array( $product_groups ) && in_array( $group->group_id, $product_groups ) ) {
					$list[] = wp_filter_nohtml_kses( $group->name );
				}
				
				if ( is_array( $product_groups_remove ) && in_array( $group->group_id, $product_groups_remove ) ) {
					$list[] = '(' . wp_filter_nohtml_kses( $group->name ) . ')';
				}
			}
			$output .= implode( ', ', $list );
		}

		return $output;
	}
}
Groups_WS_Admin_Product::init();

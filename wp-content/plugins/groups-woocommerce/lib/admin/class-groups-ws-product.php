<?php
/**
 * class-groups-ws-product.php
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
 * Product extension to integrate with Groups.
 */
class Groups_WS_Product {

	/**
	 * Register own Groups tab and handle group association with products.
	 * Register price display modifier.
	 */
	public static function init() {
		if ( is_admin() ) {
			add_action( 'woocommerce_product_write_panel_tabs', array( __CLASS__, 'product_write_panel_tabs' ) );
			add_action( 'woocommerce_product_write_panels',	    array( __CLASS__, 'product_write_panels' ) );
			add_action( 'woocommerce_process_product_meta',	    array( __CLASS__, 'process_product_meta' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
			add_action( 'woocommerce_product_after_variable_attributes', array( __CLASS__, 'woocommerce_product_after_variable_attributes'), 10, 3 );
			//add_action( 'woocommerce_variation_options', array( __CLASS__, 'woocommerce_variation_options'), 10, 3 );
		}
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'woocommerce_get_price_html' ), 10, 2 );
	}

	/**
	 * Enqueues the select script on the product screen.
	 * Groups' Access Restrictions meta box already does that as well but for
	 * the sake of consistency and in case it were not loaded by that ...
	 */
	public static function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) ) {
			switch( $screen->id ) {
				case 'product' :
					require_once GROUPS_VIEWS_LIB . '/class-groups-uie.php';
					Groups_UIE::enqueue( 'select' );
					break;
			}
		}
	}

	/**
	 * Groups tab title.
	 */
	public static function product_write_panel_tabs() {
		echo
			'<li class="attributes_tab attribute_options">' .
			'<a href="#woocommerce_groups">' .
			__( 'Groups', GROUPS_WS_PLUGIN_DOMAIN ) .
			'</a>' .
			'</li>';
	}

	/**
	 * Groups tab content.
	 */
	public static function product_write_panels() {

		global $post, $wpdb, $woocommerce;

		$groups_panel_prefix = '';
		$groups_panel_groups = '';
		$groups_panel_suffix = '';

		$is_subscription = class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $post->ID );

		$is_external = false;
		if ( $product = groups_ws_get_product( $post->ID ) ) {
			$is_external = $product->is_type( 'external' );
			unset( $product );
		}

		//
		// build the prefix
		//
		$groups_panel_prefix_simple  = sprintf( '<div class="groups-panel-item-simple" style="%s">', $is_subscription ? 'display:none;' : '' );
		$groups_panel_prefix_simple .= '<p>' . __( 'The customer will be added to or removed from the selected groups when purchasing this product.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		$groups_panel_prefix_simple .= '</div>'; // .groups-panel-item-simple

		$groups_panel_prefix_subscription  = sprintf( '<div class="groups-panel-item-subscription" style="%s">', !$is_subscription ? 'display:none;' : '' );
		$groups_panel_prefix_subscription .= '<p>' . __( 'The customer will be a member of the selected groups as long as the subscription is active. The customer will be removed from the selected groups once the subscription is active.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		$groups_panel_prefix_simple       .= '</div>'; // .groups-panel-item-subscription

		$groups_panel_prefix .= '<div id="groups-panel-prefix">';
		$groups_panel_prefix .= $groups_panel_prefix_simple;
		$groups_panel_prefix .= $groups_panel_prefix_subscription;
		$groups_panel_prefix .= '</div>'; // #groups-panel-prefix

		//
		// build the groups main section
		//
		$product_groups        = get_post_meta( $post->ID, '_groups_groups', false );
		$product_groups_remove = get_post_meta( $post->ID, '_groups_groups_remove', false );

		$group_table           = _groups_get_tablename( "group" );
		$groups                = $wpdb->get_results( "SELECT * FROM $group_table ORDER BY name" );
		$n = 0;

		$groups_panel_groups .= sprintf( '<div class="groups-panel-item-external " style="%s">', !$is_external ? 'display:none;' : '' );
		$groups_panel_groups .= '<p>' . __( 'Group assignments are not supported for external products.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		$groups_panel_groups .= '</div>'; // .groups-panel-item-external

		$groups_panel_groups .= sprintf( '<div class="groups-panel-item-non-external " id="groups-panel-groups" style="%s">', $is_external ? 'display:none;' : '' );
		if ( count( $groups ) == 0 ) {
			$groups_panel_groups .= '<p>' . __( 'There are no groups available to select. At least one group (other than <em>Registered</em>) must be created.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		} else {

			// text style
			$groups_panel_groups .= '<style type="text/css">';
			$groups_panel_groups .= '.groups-selects { padding: 0 1em; }';
			$groups_panel_groups .= '.groups-selects label { float:none; margin: 0; width: auto; }';
			$groups_panel_groups .= '.groups-selects .selectize-input { font-size: inherit; }';
			$groups_panel_groups .= '</style>';

			$groups_panel_groups .= '<div class="groups-selects">';

			// add to groups
			$groups_panel_groups .= '<label>';
			$groups_panel_groups .= __( 'Add to Groups', GROUPS_WS_PLUGIN_DOMAIN );
			$groups_panel_groups .= ' ';
			$groups_panel_groups .= sprintf(
				'<select id="product-groups-groups" class="groups-woocommerce" name="_groups_groups[]" multiple="multiple" placeholder="%s" data-placeholder="%s">',
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) ) ,
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) )
			);
			foreach( $groups as $group ) {
				$selected = is_array( $product_groups ) && in_array( $group->group_id, $product_groups );
				$groups_panel_groups .= sprintf( '<option value="%d" %s>%s</option>', Groups_Utility::id( $group->group_id ), $selected ? ' selected="selected" ' : '', wp_filter_nohtml_kses( $group->name ) );
			}
			$groups_panel_groups .= '</select>';
			$groups_panel_groups .= '</label>';
			$groups_panel_groups .= Groups_UIE::render_select( '#product-groups-groups' );

			// remove from groups
			$groups_panel_groups .= '<label>';
			$groups_panel_groups .= __( 'Remove from Groups', GROUPS_WS_PLUGIN_DOMAIN );
			$groups_panel_groups .= ' ';
			$groups_panel_groups .= sprintf(
				'<select id="product-groups-groups-remove" class="groups-woocommerce" name="_groups_groups_remove[]" multiple="multiple" placeholder="%s" data-placeholder="%s">',
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) ) ,
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) )
			);
			foreach( $groups as $group ) {
				$selected = is_array( $product_groups_remove ) && in_array( $group->group_id, $product_groups_remove );
				$groups_panel_groups .= sprintf( '<option value="%d" %s>%s</option>', Groups_Utility::id( $group->group_id ), $selected ? ' selected="selected" ' : '', wp_filter_nohtml_kses( $group->name ) );
			}
			$groups_panel_groups .= '</select>';
			$groups_panel_groups .= '</label>';
			$groups_panel_groups .= Groups_UIE::render_select( '#product-groups-groups-remove' );

			$groups_panel_groups .= '</div>'; // .groups-selects

		}
		$groups_panel_groups .= '</div>'; // #groups-panel-groups

		//
		// build the suffix
		//
		$groups_panel_suffix_simple = sprintf( '<div class="groups-panel-item-simple" id="groups-panel-suffix-simple" style="%s">', $is_subscription ? 'display:none;' : '' );
		$duration     = get_post_meta( $post->ID, '_groups_duration', true );
		$duration_uom = get_post_meta( $post->ID, '_groups_duration_uom', true );
		if ( empty( $duration_uom ) ) {
			$duration_uom = 'month';
		}
		switch( $duration_uom ) {
			case 'second' :
				$duration_uom_label = _n( 'Second', 'Seconds', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
			case 'minute' :
				$duration_uom_label = _n( 'Minute', 'Minutes', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
			case 'hour' :
				$duration_uom_label = _n( 'Hour', 'Hours', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
			case 'day' :
				$duration_uom_label = _n( 'Day', 'Days', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
			case 'week' :
				$duration_uom_label = _n( 'Week', 'Weeks', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
			case 'year' :
				$duration_uom_label = _n( 'Year', 'Years', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
			default :
				$duration_uom_label = _n( 'Month', 'Months', $duration, GROUPS_WS_PLUGIN_DOMAIN );
				break;
		}

		$duration_help =
			__( 'Leave the duration empty unless you want memberships to end after a certain amount of time.', GROUPS_WS_PLUGIN_DOMAIN ) .
			' ' .
			__( 'If the duration is empty, the customer will remain a member of the selected groups forever, unless removed explicitly.', GROUPS_WS_PLUGIN_DOMAIN ) .
			' ' .
			__( 'If the duration is set, the customer will only belong to the selected groups during the specified time, based on the <em>Duration</em> and the <em>Time unit</em>.', GROUPS_WS_PLUGIN_DOMAIN );

		$duration_help_icon = '<img class="help_tip" data-tip="' . esc_attr( $duration_help ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
		ob_start();
		woocommerce_wp_text_input(
			array(
				'id'          => '_groups_duration',
				'label'       => sprintf( __( 'Duration', GROUPS_WS_PLUGIN_DOMAIN ), $duration_help_icon ),
				'value'       => $duration,
				'description' => sprintf( __( '%s (as chosen under <em>Time unit</em>)', GROUPS_WS_PLUGIN_DOMAIN ), $duration_uom_label ),
				'placeholder' => __( 'unlimited', GROUPS_WS_PLUGIN_DOMAIN )
			)
		);
		$groups_panel_suffix_simple .= ob_get_clean();

		// data-tip is filtered out now, append it where we want it
		$groups_panel_suffix_simple .= '<script type="text/javascript">';
		$groups_panel_suffix_simple .= 'if (typeof jQuery !== "undefined"){';
		$groups_panel_suffix_simple .= 'var _groups_duration_field = jQuery("._groups_duration_field");';
		$groups_panel_suffix_simple .= '}';
		$groups_panel_suffix_simple .= 'if (typeof _groups_duration_field !== "undefined"){';
		$groups_panel_suffix_simple .= 'jQuery(_groups_duration_field).append(\'' . $duration_help_icon . '\');';
		$groups_panel_suffix_simple .= '} else {';
		$groups_panel_suffix_simple .= 'document.write(\'<p>' . $duration_help . '</p>\')';
		$groups_panel_suffix_simple .= '}';
		$groups_panel_suffix_simple .= '</script>';

		ob_start();
		woocommerce_wp_select(
			array(
				'id'          => '_groups_duration_uom',
				'label'       => __( 'Time unit', GROUPS_WS_PLUGIN_DOMAIN ),
				'value'       => $duration_uom,
				'options'     => array(
					'second' => __( 'Seconds', GROUPS_WS_PLUGIN_DOMAIN ),
					'minute' => __( 'Minutes', GROUPS_WS_PLUGIN_DOMAIN ),
					'hour'   => __( 'Hours', GROUPS_WS_PLUGIN_DOMAIN ),
					'day'    => __( 'Days', GROUPS_WS_PLUGIN_DOMAIN ),
					'week'   => __( 'Weeks', GROUPS_WS_PLUGIN_DOMAIN ),
					'month'  => __( 'Months', GROUPS_WS_PLUGIN_DOMAIN ),
					'year'   => __( 'Years', GROUPS_WS_PLUGIN_DOMAIN ),
				)
			)
		);
		$groups_panel_suffix_simple .= ob_get_clean();

		$groups_panel_suffix_simple .=
			'<noscript>' .
			'<p>' .
			$duration_help .
			'</p>' .
			'</noscript>';
		$groups_panel_suffix_simple .= '</div>'; // #groups-panel-suffix-simple

		$groups_panel_suffix_subscription  = sprintf( '<div class="groups-panel-item-subscription" id="groups-panel-suffix-subscription" style="%s">', !$is_subscription ? 'display:none;' : '' );
		$groups_panel_suffix_subscription .= '</div>';

		$groups_panel_suffix .= '<div id="groups-panel-suffix">';
		$groups_panel_suffix .= $groups_panel_suffix_simple;
		$groups_panel_suffix .= $groups_panel_suffix_subscription;
		$groups_panel_suffix .= '</div>'; // #groups-panel-suffix

		//
		// render the groups panel
		//
		echo '<div id="woocommerce_groups" class="panel woocommerce_options_panel" style="padding: 1em 0;">';
		echo $groups_panel_prefix;
		echo $groups_panel_groups;
		echo $groups_panel_suffix;
		echo '<p>' . __( 'Note that all users belong to the <em>Registered</em> group automatically.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
		echo '<br/>';
		echo '</div>'; // #woocommerce_groups

		//
		// change the panel views depending on product type
		//
		// product types : simple, grouped, external, variable, subscription, variable-subscription
		//
		?>
		<script type="text/javascript">
		if ( typeof jQuery !== "undefined" ) {
			jQuery( "select#product-type" ).change( function() {
				var productType = jQuery( "select#product-type" ).val();
				switch( productType ) {
					case "external" :
						jQuery( ".groups-panel-item-external" ).show();
						jQuery( ".groups-panel-item-non-external" ).hide();
						jQuery( ".groups-panel-item-simple" ).hide();
						jQuery( ".groups-panel-item-subscription" ).hide();
					break;
					case "subscription" :
					case "variable-subscription" :
						jQuery( ".groups-panel-item-external" ).hide();
						jQuery( ".groups-panel-item-non-external" ).show();
						jQuery( ".groups-panel-item-simple" ).hide();
						jQuery( ".groups-panel-item-subscription" ).show();
						break;
					default :
						jQuery( ".groups-panel-item-external" ).hide();
						jQuery( ".groups-panel-item-non-external" ).show();
						jQuery( ".groups-panel-item-simple" ).show();
						jQuery( ".groups-panel-item-subscription" ).hide();
				}
			});
		}
		</script>
		<?php
	}

	/**
	 * Register groups for a product.
	 * @param int $post_id product ID
	 * @param object $post product
	 */
	public static function process_product_meta( $post_id, $post ) {
		global $wpdb;

		$is_subscription = class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $post_id );

		$is_external = false;

		if ( $product = groups_ws_get_product( $post_id ) ) {
			$is_external = $product->is_type( 'external' );
			unset( $product );
		}

		// refresh groups, clear all, then assign checked
		delete_post_meta( $post_id,'_groups_groups' );
		delete_post_meta( $post_id,'_groups_groups_remove' );

		// set group assignments for supported product types
		if ( !$is_external ) {
			if ( !empty( $_POST['_groups_groups'] ) && is_array( $_POST['_groups_groups'] ) ) {
				foreach( $_POST['_groups_groups'] as $group_id ) {
					if ( $group = Groups_Group::read( $group_id ) ) {
						add_post_meta( $post_id, '_groups_groups', $group->group_id );
					}
				}
			}
			if ( !empty( $_POST['_groups_groups_remove'] ) && is_array( $_POST['_groups_groups_remove'] ) ) {
				foreach( $_POST['_groups_groups_remove'] as $group_id ) {
					if ( $group = Groups_Group::read( $group_id ) ) {
						add_post_meta( $post_id, '_groups_groups_remove', $group->group_id );
					}
				}
			}
		}

		// duration
		delete_post_meta( $post_id, '_groups_duration' );
		delete_post_meta( $post_id, '_groups_duration_uom' );

		// store duration settings for supported product types 
		if ( !$is_external && !$is_subscription ) {

			$duration  = !empty( $_POST['_groups_duration'] ) ? intval( $_POST['_groups_duration'] ) : null;
			if ( $duration <= 0 ) {
				$duration = null;
			}
			if ( $duration !== null ) {
				$duration_uom = !empty( $_POST['_groups_duration_uom'] ) ? $_POST['_groups_duration_uom'] : null;
				switch( $duration_uom ) {
					case 'second' :
					case 'minute' :
					case 'hour' :
					case 'day' :
					case 'week' :
					case 'year' :
						break;
					default :
						$duration_uom = 'month';
				}
				add_post_meta( $post_id, '_groups_duration', $duration );
				add_post_meta( $post_id, '_groups_duration_uom', $duration_uom );
			}

		}

		// variations
		$variation_post_ids = isset( $_POST['variable_post_id'] ) ? $_POST['variable_post_id'] : null;
		if ( ( $variation_post_ids !== null ) && is_array( $variation_post_ids ) ) {
			foreach( $variation_post_ids as $variation_post_id ) {
				$variation_post_id = intval( $variation_post_id );
				delete_post_meta( $variation_post_id, '_groups_variation_groups' );
				delete_post_meta( $variation_post_id, '_groups_variation_groups_remove' );
				if ( !empty( $_POST['_groups_variation_groups'] ) && is_array( $_POST['_groups_variation_groups'] ) ) {
					if ( !empty( $_POST['_groups_variation_groups'][$variation_post_id] ) && is_array( $_POST['_groups_variation_groups'][$variation_post_id] ) ) {
						foreach( $_POST['_groups_variation_groups'][$variation_post_id] as $group_id ) {
							if ( $group = Groups_Group::read( $group_id ) ) {
								add_post_meta( $variation_post_id, '_groups_variation_groups', $group->group_id );
							}
						}
					}
				}
				if ( !empty( $_POST['_groups_variation_groups_remove'] ) && is_array( $_POST['_groups_variation_groups_remove'] ) ) {
					if ( !empty( $_POST['_groups_variation_groups_remove'][$variation_post_id] ) && is_array( $_POST['_groups_variation_groups_remove'][$variation_post_id] ) ) {
						foreach( $_POST['_groups_variation_groups_remove'][$variation_post_id] as $group_id ) {
							if ( $group = Groups_Group::read( $group_id ) ) {
								add_post_meta( $variation_post_id, '_groups_variation_groups_remove', $group->group_id );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Renders group selections for variations.
	 * 
	 * @param int $loop
	 * @param array $variation_data
	 * @param WP_Post $variation
	 */
	public static function woocommerce_product_after_variable_attributes( $loop, $variation_data, $variation ) {

		global $post, $wpdb;

		$output = '';

		$output .= '<tr><td><div>';
		$variation_groups        = get_post_meta( $variation->ID, '_groups_variation_groups', false );
		$variation_groups_remove = get_post_meta( $variation->ID, '_groups_variation_groups_remove', false );
		$groups_table = _groups_get_tablename( 'group' );
		if ( $groups = $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY name" ) ) {
			// text style
			$output .= '<style type="text/css">';
			$output .= '.groups-woocommerce .selectize-input { font-size: inherit; }';
			$output .= '</style>';

			// add to groups
			$output .= '<label>';
			$output .= __( 'Add to Groups', GROUPS_WS_PLUGIN_DOMAIN );
			$output .= ' ';
			$output .= sprintf(
				'<select id="variation-groups-%d" class="groups-woocommerce" name="_groups_variation_groups[%d][]" multiple="multiple" placeholder="%s" data-placeholder="%s">',
				esc_attr( $variation->ID ),
				esc_attr( $variation->ID ),
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) ) ,
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) )
			);
			foreach( $groups as $group ) {
				$selected = is_array( $variation_groups ) && in_array( $group->group_id, $variation_groups );
				$output .= sprintf( '<option value="%d" %s>%s</option>', Groups_Utility::id( $group->group_id ), $selected ? ' selected="selected" ' : '', wp_filter_nohtml_kses( $group->name ) );
			}
			$output .= '</select>';
			$output .= '</label>';
			$output .= Groups_UIE::render_select( '#variation-groups-' . esc_attr( $variation->ID ) );
			$output .= '<p class="description">' . __( 'Add the customer to these groups when purchasing this variation.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';

			// remove from groups
			$output .= '<label>';
			$output .= __( 'Remove from Groups', GROUPS_WS_PLUGIN_DOMAIN );
			$output .= ' ';
			$output .= sprintf(
				'<select id="variation-groups-remove-%d" class="groups-woocommerce" name="_groups_variation_groups_remove[%d][]" multiple="multiple" placeholder="%s" data-placeholder="%s">',
				esc_attr( $variation->ID ),
				esc_attr( $variation->ID ),
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) ) ,
				esc_attr( __( 'Choose groups &hellip;', GROUPS_WS_PLUGIN_DOMAIN ) )
			);
			foreach( $groups as $group ) {
				$selected = is_array( $variation_groups_remove ) && in_array( $group->group_id, $variation_groups_remove );
				$output .= sprintf( '<option value="%d" %s>%s</option>', Groups_Utility::id( $group->group_id ), $selected ? ' selected="selected" ' : '', wp_filter_nohtml_kses( $group->name ) );
			}
			$output .= '</select>';
			$output .= '</label>';
			$output .= Groups_UIE::render_select( '#variation-groups-remove-' . esc_attr( $variation->ID ) );
			$output .= '<p class="description">' . __( 'Remove the customer from these groups when purchasing this variation.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';

			$is_subscription = isset( $post->ID ) && class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $post->ID );
			$output .= sprintf( '<div class="groups-panel-item-simple" style="%s">', $is_subscription ? 'display:none;' : '' );
			$output .= '<p>' . __( 'If set, the duration limitations in the <em>Groups</em> settings of the variable product apply.', GROUPS_WS_PLUGIN_DOMAIN ) . '</p>';
			$output .= '</div>';
		}
		$output .= '</div></td></tr>';
		echo $output;
	}

	/**
	 * Currently not used.
	 * 
	 * @param int $loop
	 * @param array $variation_data
	 * @param WP_Post $variation
	 */
	public static function woocommerce_variation_options( $loop, $variation_data, $variation ) {
	}

	/**
	 * Add duration info on prices.
	 * @param string $price
	 * @param WC_Product $product
	 */
	public static function woocommerce_get_price_html( $price, $product ) {
		$options = get_option( 'groups-woocommerce', null );
		$show_duration = isset( $options[GROUPS_WS_SHOW_DURATION] ) ? $options[GROUPS_WS_SHOW_DURATION] : GROUPS_WS_DEFAULT_SHOW_DURATION;
		if ( $show_duration ) {
			$duration     = get_post_meta( $product->id, '_groups_duration', true );
			if ( !empty( $duration ) ) {
				$duration_uom = get_post_meta( $product->id, '_groups_duration_uom', true );
				switch( $duration_uom ) {
					case 'second' :
						$price = sprintf( _n( '%s for 1 second', '%s for %d seconds', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'minute' :
						$price = sprintf( _n( '%s for 1 minute', '%s for %d minutes', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'hour' :
						$price = sprintf( _n( '%s for 1 hour', '%s for %d hours', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'day' :
						$price = sprintf( _n( '%s for 1 day', '%s for %d days', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'week' :
						$price = sprintf( _n( '%s for 1 week', '%s for %d weeks', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					case 'year' :
						$price = sprintf( _n( '%s for 1 year', '%s for %d years', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
					default :
						$price = sprintf( _n( '%s for 1 month', '%s for %d months', $duration, GROUPS_WS_PLUGIN_DOMAIN ), $price, $duration );
						break;
				}
			}
		}
		return $price;
	}

	/**
	 * Retruns true if the membership is limited.
	 * @param WC_Product $product
	 * @return boolean true if product group membership has duration defined, false otherwise
	 */
	public static function has_duration( $product ) {
		$duration = get_post_meta( $product->id, '_groups_duration', true );
		return $duration > 0;
	}

	/**
	 * Returns the duration of membership in seconds.
	 * @param WC_Product $product
	 * @return duration in seconds or null if there is none defined
	 */
	public static function get_duration( $product ) {
		$result = null;
		$duration     = get_post_meta( $product->id, '_groups_duration', true );
		if ( !empty( $duration ) ) {
			$duration_uom = get_post_meta( $product->id, '_groups_duration_uom', true );
			$suffix = $duration > 1 ? 's' : '';
			$result = strtotime( '+' . $duration . ' ' . $duration_uom . $suffix ) - time();
		}
		return $result;
	}

	/**
	 * Calculate the duration in seconds.
	 * 
	 * @param int|string $duration
	 * @param string $duration_uom
	 * @return seconds or null if $duration is empty
	 */
	public static function calculate_duration( $duration, $duration_uom ) {
		$result = null;
		if ( !empty( $duration ) ) {
			$suffix = $duration > 1 ? 's' : '';
			$result = strtotime( '+' . $duration . ' ' . $duration_uom . $suffix ) - time();
		}
		return $result;
	}
}
Groups_WS_Product::init();

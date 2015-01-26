<?php
// general functions for use in setting up post data, etc.

function out( $variable ) { return var_dump( $variable ); }

// Monadic functions for echoing content to the page.
function ws_ifdef_do_else( $check, $content, $else ) {
	return ( $check || $check === 0 || $check === "0" ) ? $content : $else;
}


function ws_ifdef_do( $check, $content ) {
	return ws_ifdef_do_else( $check, $content, "");
}

function ws_ifdef_show( $content ) {
	return ws_ifdef_do( $content, $content );
}

function ws_ifdef_concat($before, $content, $after) {
	return $before . ws_ifdef_show( $content ) . $after;
}

function ws_split_array_by_key( $array, $delimiter, $format_function ) {
	$accumulator = "";
	if ( $array ) {
		$count = count( $array );
		foreach ( $array as $i => $tag ) {
			if ( $i < $count - 1 ) {
				$accumulator .= $format_function($tag) . $delimiter;
			} else {
				$accumulator .= $format_function($tag);
			}
		}
	}
	return $accumulator;	
}

function ws_parity( $parity, $i, $zero, $one ) {
	return ( $i % $parity == 0 ) ? $zero : $one;
}

function ws_render_date( $datestring ) {
	$date = date_parse( $datestring );
	return $date['month'] . '/' . $date['day'] . '/' . $date['year'];
}

function ws_decide_image_type( $file ) {
		return '<img type="'.$file['mime_type'].'" src="'.$file['url'].'" />';
}


function ws_fst( $lst ) { return array_shift( $lst ); }




// functions for manipulating $_GET and $_POST data
function ws_eq_get_var( $var, $val ) {
	return isset( $_GET[$var] ) && $_GET[$var] === $val;
}

function ws_andeq_get_vars( $vars, $vals ) {
	if ( is_array( $vars ) && is_array( $vals ) ) {
		if ( count( $vars ) == count( $vals ) ) {
			return array_reduce(array_map(null, $vars, $vals), function( $x, $y ) {
				return ws_eq_get_var($x[0], $x[1]) && $y;
			});
		}
	}
	return false;
}

function ws_load_module( $module_prefix, $module_name, $args ) {
	if ( $args && is_array( $args ) ) extract( $args );
	get_template_part( $module_prefix, $module_name );
}


// general CC functions
function cc_dress( $post ) {
	return ('dress' == $post->post_type) && 
		 ('draft' != $post->post_status ) &&
		 ('trash' != $post->post_status ) &&
		 ('auto-draft' != $post->post_status );
}

function cc_dress_trash( $post ) {
	return ('dress' == $post->post_type) && 
		 ('trash' == $post->post_status );
}

function cc_compute_product_name( $basename, $suffix ) {
	return $basename . '-' . $suffix;
}

function cc_compute_product_title( $basename, $suffix ) {
	return $basename . ' ' . $suffix;
}

function cc_compute_product_sku( $sku, $suffix ) {
	return ( $sku !== null ) ? $sku.'-'.$suffix : "";
}

function cc_short_address( $address_array ) {
	var_dump( $address_array );
}

function cc_active_bookings( $x,$y ) {
	return ($x->post_status != 'cancelled' && $x->post_status != 'wc-cancelled') || $y;
}

function cc_get_dress_states( $user, $dress_id ) {
	$class_string = "";
	$share_arr = get_field( 'dress_share_product_instance', $dress_id );
	$sale_arr = get_field( 'dress_sale_product_instance', $dress_id );

	if ( $user && !empty( $share_arr ) && !empty( $sale_arr ) ) {

		$share = new WC_Product( $share_arr[0]->id );
		$sale = new WC_Product( $sale_arr[0]->id );

		// for some reason this is not turning up the correct value, although it seems to do fine on the single page?
		$owned = wc_customer_bought_product( $user->user_email, $user->ID, $share->id );

		if ( $owned ) {
			$class_string .= "owned ";
		}

		if ( $share->is_in_stock() ) {
			$class_string .= "share-available ";
		}

		if ( $sale->is_in_stock() ) {
			$class_string .= "sale-available ";
		}
	}

	return $class_string;
}

function cc_user_is_guest() {
	global $current_user;

	return is_user_logged_in() && $current_user->user_login == 'Guest';
}

function cc_can_see_admin() {
	global $current_user;

	$administrative = array('shop_manager', 'administrator');
	foreach ($current_user->roles as $role) {
		if ( in_array($role, $administrative) ) return true;
	}

	return false;
}

/**
 * writes out a cost string based on the reservation_type
 *
 * @param string $reservation_type the type of reservation
 * @return string the cost string based on $reservation_type
 */
function cc_booking_cost_string( $reservation_type ) {
	switch ( $reservation_type ) {
		case "Prereservation" :
			return "Reservation Cost";

		case "Rental" :
			return "Rental Cost";

		default:
			return "";
	}

}

/**
 * writes out a prompt string based on the reservation_type
 *
 * @param string $reservation_type the type of reservation
 * @return string the prompt based on $reservation_type
 */
function cc_booking_prompt_string( $reservation_type ) {
	switch ( $reservation_type ) {
		case "Share" :
			return "Purchase";

		case "Prereservation" :
			return "Pre-reserve";

		case "Rental" :
			return "Rent";

		case "Nextday" : 
			return "Reserve";

		default :
			return "";
	}

}

/**
 * Formats the reservation type as a human-readable noun.
 *
 * @param string $reservation_type
 * @return string formatted noun
 */
function cc_booking_noun_string( $reservation_type ) {
	$reservation_type = strtolower( $reservation_type );

	switch ( $reservation_type ) {
		case "prereservation" :
			return "Pre-reservation";

		case "rental" :
			return "Rental";

		case "nextday" : 
			return "Next-day Rental";

		case "update":
			return "Update";

		case "share":
			return "Share";

		case "sale":
			return "Dress";
		default :
			return "";
	}
}






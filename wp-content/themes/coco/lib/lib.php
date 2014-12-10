<?php
// general functions for use in setting up post data, etc.

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


function ws_fst( $lst ) { return $lst[0]; }




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








<?php

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


/*

	Register a "Dress" post type. This post-type is a union of Product Woocommerce Plumbing,
	Bookings data, and user reservation information.

*/
add_action('init','dress_init');
function dress_init() {
	$labels = array(
		'name' => 'Dresses',
		'singular_name' =>'Dress',
		'add_new' => 'Add New',
	    	'add_new_item' => 'Add New Dress',
	    	'edit_item' => 'Edit Dress',
	    	'new_item' => 'New Dress',
	    	'all_items' => 'All Dresses',
	   	'view_item' => 'View Dress',
	   	'search_items' => 'Search Dresses',
	   	'not_found' =>  'No Dresses found',
	   	'not_found_in_trash' => 'No Dresses found in Trash', 
	);

	$options = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'rewrite' => array('slug' => 'dresses'),
		'supports' => array( 'title', 'editor', 'thumbnail', 'tags' )
	);

	register_post_type( 'dress', $options );
}

/*
		GENERAL DRESS MODIFICATION SECTION
*/
add_action('save_post', 'dress_modify');
function dress_modify( $post_id ) {
	$post = get_post( $post_id );
	if ( !cc_dress($post) ) return; // return if this is not a modifiable post-type 

	$dress_share = get_field('dress_share_product_instance', $post_id );
	$dress_sale = get_field('dress_sale_product_instance', $post_id );
	$dress_rental = get_field('dress_rental_product_instance', $post_id );

	// determine whether this is a creation of deletion
	if ( !empty( $dress_share ) && !empty( $dress_sale ) && !empty( $dress_rental ) ) {
		remove_action( 'save_post', 'dress_modify');
		dress_update( $post, $post_id );
		add_action( 'save_post', 'dress_modify');
	} else if ( $dress_share || $dress_sale || $dress_rental ) {
		/*

			ERROR

		 */
		// ERROR: !($d_sh /\ $d_s /\ $d_r ) /\ ($d_sh \/ $d_s \/ $d_r )
		// ERROR: Inconsistent State, missing product indicator decoration, recover from this issue if possible,
		// and log the error. We may have missed sales, so we should handle this problem immediately.
	} else {
		remove_action( 'save_post', 'dress_modify');
		dress_create( $post, $post_id );
		add_action( 'save_post', 'dress_modify');
	}

}
/*
		END GENERAL DRESS MODIFICATION SECTION
*/

/*
		DRESS CREATION SECTION
*/
function cc_create_dress_share_product( $post, $parent_post_id ) {
	$dress_share =  array(
		'post_content' => '',
		'comment_status'  => 'closed',
		'ping_status'   => 'closed',
		'post_author'   => $post->post_author, // the author of the parent post is the author of this system post
		'post_name'   => cc_compute_product_name( $post->post_name, 'share'),
		'post_title'    => cc_compute_product_title( $post->post_title, 'Share' ),
		'post_status'   => 'publish',
		'post_type'   => 'product'
	);

	$post_id = wp_insert_post( $dress_share );

	$share_price = get_field('dress_share_price', $parent_post_id);
	$sku = get_field('dress_sku', $parent_post_id);

	// Start Consistency Checks

	// DONE
	update_post_meta( $post_id, '_virtual', 'no'); // check this for functionality
	update_post_meta( $post_id, 'total_sales', 0);
	update_post_meta( $post_id, '_stock', 5 );
	update_post_meta( $post_id, '_manage_stock', "yes" );

	update_post_meta( $post_id, '_regular_price',  $share_price );
	update_post_meta( $post_id, '_sale_price', $share_price );
	update_post_meta( $post_id, '_price', $share_price );
	update_post_meta($post_id, '_sku', cc_compute_product_sku( $sku, "SHARE") );

	// NOT DONE <= these are not used currently, but could be used to programmatically calculate shipping later.
	update_post_meta( $post_id, '_weight', "" );
	update_post_meta( $post_id, '_length', "" );
	update_post_meta( $post_id, '_width', "" );
	update_post_meta( $post_id, '_height', "" );
	
	// DEFAULTS
	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_stock_status', 'instock');
	update_post_meta( $post_id, '_downloadable', 'no');

	update_post_meta( $post_id, '_purchase_note', "" );
	update_post_meta( $post_id, '_featured', "no" );
	update_post_meta( $post_id, '_sold_individually', "" );
	update_post_meta( $post_id, '_backorders', "no" );
	update_post_meta( $post_id, '_product_attributes', array());
	update_post_meta( $post_id, '_sale_price_dates_from', "" );
	update_post_meta( $post_id, '_sale_price_dates_to', "" );

	// End Consistancy Checks

	// set relationship
	update_field( 'dress_share_product_instance', array( $post_id ), $parent_post_id );

	return $post_id;
}

function cc_create_dress_sale_product( $post, $parent_post_id ) {
	$dress_share =  array(
		'post_content' => '',
		'comment_status'  => 'closed',
		'ping_status'   => 'closed',
		'post_author'   => $post->post_author, // the author of the parent post is the author of this system post
		'post_name'   => cc_compute_product_name( $post->post_name, 'eos'),
		'post_title'    => cc_compute_product_title( $post->post_title, 'Sale' ),
		'post_status'   => 'publish',
		'post_type'   => 'product'
	);

	$post_id = wp_insert_post( $dress_share );

	$sale_price = get_field('dress_sale_price', $parent_post_id);
	$sku = get_field('dress_sku', $parent_post_id);

	// Start Consistency Checks

	// DONE
	update_post_meta( $post_id, '_virtual', 'no'); // check this for functionality
	update_post_meta( $post_id, 'total_sales', 0);
	update_post_meta( $post_id, '_stock', 1 );
	update_post_meta( $post_id, '_manage_stock', "yes" );

	update_post_meta( $post_id, '_regular_price',  $sale_price );
	update_post_meta( $post_id, '_sale_price', $sale_price );
	update_post_meta( $post_id, '_price', $sale_price );
	update_post_meta($post_id, '_sku', cc_compute_product_sku( $sku, "EOS") );

	// NOT DONE <= these are not used currently, but could be used to programmatically calculate shipping later.
	update_post_meta( $post_id, '_weight', "" );
	update_post_meta( $post_id, '_length', "" );
	update_post_meta( $post_id, '_width', "" );
	update_post_meta( $post_id, '_height', "" );
	
	// DEFAULTS
	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_stock_status', 'instock');
	update_post_meta( $post_id, '_downloadable', 'no');

	update_post_meta( $post_id, '_purchase_note', "" );
	update_post_meta( $post_id, '_featured', "no" );
	update_post_meta( $post_id, '_sold_individually', "" );
	update_post_meta( $post_id, '_backorders', "no" );
	update_post_meta( $post_id, '_product_attributes', array());
	update_post_meta( $post_id, '_sale_price_dates_from', "" );
	update_post_meta( $post_id, '_sale_price_dates_to', "" );

	// End Consistancy Checks

	// set relationship
	update_field( 'dress_sale_product_instance', array( $post_id ), $parent_post_id );

	return $post_id;
}

function cc_create_dress_rental_product( $post, $parent_post_id ) {
	$dress_share =  array(
		'post_content' => '',
		'comment_status'  => 'closed',
		'ping_status'   => 'closed',
		'post_author'   => $post->post_author, // the author of the parent post is the author of this system post
		'post_name'   => cc_compute_product_name( $post->post_name, 'rental'),
		'post_title'    => cc_compute_product_title( $post->post_title, 'Rental' ),
		'post_status'   => 'publish',
		'post_type'   => 'product'
	);

	$post_id = wp_insert_post( $dress_share );

	$rental_price = get_field('dress_rental_price', $parent_post_id);
	$sku = get_field('dress_sku', $parent_post_id);

	// Start Consistency Checks

	// Set that this product type is "bookable"
	wp_set_object_terms( $post_id, 'booking', 'product_type' );
	// Proceed to set bookable item terms.
	update_post_meta( $post_id, '_wc_booking_duration_type', 'fixed');
	update_post_meta( $post_id, '_wc_booking_duration', CC_BOOKING_DURATION);
	update_post_meta( $post_id, '_wc_booking_duration_unit', 'day');
	update_post_meta( $post_id, '_wc_booking_base_cost', $rental_price);
	update_post_meta( $post_id, '_wc_booking_min_duration', 1);
	update_post_meta( $post_id, '_wc_booking_max_duration', 1);
	update_post_meta( $post_id, '_wc_booking_qty', 1);
	update_post_meta( $post_id, '_wc_booking_max_date', 12);
	update_post_meta($post_id, '_wc_booking_max_date_unit', 'month');
	update_post_meta($post_id, '_wc_booking_min_date', 2);
	update_post_meta($post_id, '_wc_booking_min_date_unit', 'week');
	update_post_meta($post_id, '_wc_booking_default_date_availability', 'available');
	//update_post_meta($post_id, '_wc_booking_availability', ...);

	// add a custom range defining the current season.

	// DONE
	update_post_meta( $post_id, '_virtual', 'no'); // check this for functionality
	update_post_meta( $post_id, 'total_sales', 0);
	update_post_meta( $post_id, '_stock', "");
	update_post_meta( $post_id, '_manage_stock', "no" );

	update_post_meta( $post_id, '_regular_price',  $rental_price );
	update_post_meta( $post_id, '_sale_price', $rental_price );
	update_post_meta( $post_id, '_price', $rental_price );
	update_post_meta($post_id, '_sku', cc_compute_product_sku( $sku, "RENT") );

	// NOT DONE <= these are not used currently, but could be used to programmatically calculate shipping later.
	update_post_meta( $post_id, '_weight', "" );
	update_post_meta( $post_id, '_length', "" );
	update_post_meta( $post_id, '_width', "" );
	update_post_meta( $post_id, '_height', "" );
	
	// DEFAULTS
	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_stock_status', 'instock');
	update_post_meta( $post_id, '_downloadable', 'no');

	update_post_meta( $post_id, '_purchase_note', "" );
	update_post_meta( $post_id, '_featured', "no" );
	update_post_meta( $post_id, '_sold_individually', "" );
	update_post_meta( $post_id, '_backorders', "no" );
	update_post_meta( $post_id, '_product_attributes', array());
	update_post_meta( $post_id, '_sale_price_dates_from', "" );
	update_post_meta( $post_id, '_sale_price_dates_to', "" );

	// End Consistancy Checks

	// set relationship
	update_field( 'dress_rental_product_instance', array( $post_id ), $parent_post_id );

	return $post_id;
}

function dress_create( $post, $post_id ) {
	// get relevant values from post
	// Create the new posts.
	// Attach the new posts to the dress using set_field()

	// create dress
	cc_create_dress_share_product( $post, $post_id );
	cc_create_dress_sale_product( $post, $post_id );
	cc_create_dress_rental_product( $post, $post_id );

	/*

			CHECK CONSISTENCY AND PLACEMENT HERE, or REMOVE TRANSACTION.

	 */
	// at this point, the subproducts have been created, and linked. The product is live.
}


/*
		END DRESS CREATION SECTION
*/





/*
		DRESS UPDATE SECTION
*/
function cc_update_sale_product( $id, $changed ) {
	$updated = array(
		'ID' => $id,
		'post_name' => cc_compute_product_name( $changed['name'], 'eos' ),
		'post_title' => cc_compute_product_title( $changed['title'], 'Sale' )
	);

	wp_update_post( $updated );

	update_post_meta( $id, '_sku', cc_compute_product_sku( $changed['sku'], "EOS") );
	update_post_meta( $id, '_regular_price',  $changed['sale_price'] );
	update_post_meta( $id, '_sale_price', $changed['sale_price'] );
	update_post_meta( $id, '_price', $changed['sale_price'] );
}

function cc_update_share_product( $id, $changed ) {
	$updated = array(
		'ID' => $id,
		'post_name' => cc_compute_product_name( $changed['name'], 'share' ),
		'post_title' => cc_compute_product_title( $changed['title'], 'Share' )
	);

	wp_update_post( $updated );

	update_post_meta( $id, '_sku', cc_compute_product_sku( $changed['sku'], "SHARE") );
	update_post_meta( $id, '_regular_price',  $changed['share_price'] );
	update_post_meta( $id, '_sale_price', $changed['share_price'] );
	update_post_meta( $id, '_price', $changed['share_price'] );
}

function cc_update_rental_product( $id, $changed ) {
	$updated = array(
		'ID' => $id,
		'post_name' => cc_compute_product_name( $changed['name'], 'rental' ),
		'post_title' => cc_compute_product_title( $changed['title'], 'Rental' )
	);

	wp_update_post( $updated );

	update_post_meta( $id, '_sku', cc_compute_product_sku( $changed['sku'], "RENT") );
	update_post_meta( $id, '_regular_price',  $changed['rental_price'] );
	update_post_meta( $id, '_sale_price', $changed['rental_price'] );
	update_post_meta( $id, '_price', $changed['rental_price'] );
}

function dress_update( $post, $post_id ) {
	$sale_product = get_field('dress_sale_product_instance', $post_id );
	$rental_product = get_field('dress_rental_product_instance', $post_id );
	$share_product = get_field('dress_share_product_instance', $post_id );

	if ( (1 == count( $sale_product )) && (1 == count($rental_product)) && (1 == count( $share_product )) ) {
		$changed = array(
			'name' => $post->post_name,
			'title' => $post->post_title,
			'sku' => get_field('dress_sku', $post_id),
			'sale_price' => get_field('dress_sale_price', $post_id),
			'rental_price' => get_field('dress_rental_price', $post_id),
			'share_price' => get_field('dress_share_price', $post_id)
		);

		cc_update_sale_product( ws_fst( $sale_product )->ID, $changed );
		cc_update_rental_product( ws_fst( $rental_product )->ID, $changed );
		cc_update_share_product( ws_fst( $share_product )->ID, $changed );

	} else {
		// ERROR, no 1-to-3 injection.
		// recover from this state, and retry the transaction, or die()
	}
}
/*
		END DRESS UPDATE SECTION
*/


/*
		DRESS DELETE SECTION
*/

add_action('before_delete_post', 'dress_destroy' ); // prior to post deletion
function dress_destroy( $post_id ) {
	$post = get_post( $post_id );
	if ( !cc_dress_trash($post) ) return; // return if this is not a modifiable post-type

	$sale_product = get_field('dress_sale_product_instance', $post_id );
	$rental_product = get_field('dress_rental_product_instance', $post_id );
	$share_product = get_field('dress_share_product_instance', $post_id );

	if ( (1 == count( $sale_product )) && (1 == count($rental_product)) && (1 == count( $share_product )) ) {
		wp_delete_post( ws_fst( $sale_product )->ID, true );
		wp_delete_post( ws_fst( $rental_product )->ID, true );
		wp_delete_post( ws_fst( $share_product )->ID, true );

		/*
			This requires additional bookkeeping.
			1) 	Delete past bookings.
			2) 	Delete upcoming bookings; refund pending charges for these bookings.
			3)	refund sale product if it exists and has been purchased.
			4) 	Send EMAIL explaining refund to ALL associated users.
		*/

		// finish and delete the dress.

	} else {
		// ERROR, no 1-to-3 injection.
		// recover from this state, and retry the transaction, or die()
	}
}
/*
		END DRESS DELETE SECTION
*/








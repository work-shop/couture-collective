<?php
/**
 * Init Class, handles CRUD hooks
 */
class CC_Init {

	/**
	 * @var string map of custom fields names to database labels
	 */
	public static $fields = array(
		'rental' 		=> 'dress_rental_product_instance',
		'sale' 		=> 'dress_sale_product_instance',
		'share' 		=> 'dress_share_product_instance',
		'id'			=> 'dress_id_number',
		'share_price'	=> 'dress_share_price',
		'sale_price' 	=> 'dress_sale_price',
		'rent_price' 	=> 'dress_rental_price',
		'dry_price' 		=> 'dress_dry_cleaning_price',
		'description' 	=> 'dress_description'

	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array($this, 'cc_dress_init') );
		add_action('save_post', array($this, 'cc_dress_modify') );
		add_action('before_delete_post', array( $this, 'cc_dress_destroy') );
	}

	/**
	 *
	 * Register a "Dress" post type. This post-type is a union of Product Woocommerce Plumbing,
	 * Bookings data, and user reservation information.
	 *
	 */
	public function cc_dress_init() {
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

	/**
	 * General "A Dress was Modified" hook.
	 */
	public function cc_dress_modify( $post_id ) {
		$post = get_post( $post_id );
		if ( !cc_dress($post) ) return; // return if this is not a modifiable post-type 

		$dress_share = get_field(CC_Init::$fields['share'], $post_id );
		$dress_sale = get_field(CC_Init::$fields['sale'], $post_id );
		$dress_rental = get_field(CC_Init::$fields['rental'], $post_id );

		// determine whether this is a creation or modification
		if ( !empty( $dress_share ) && !empty( $dress_sale ) && !empty( $dress_rental ) ) {
			remove_action( 'save_post', array( $this, 'cc_dress_modify' ));
			$this->cc_dress_update( $post, $post_id );
			add_action( 'save_post', array( $this, 'cc_dress_modify' ));
		} else if ( !(empty( $dress_share ) && empty( $dress_sale ) && empty( $dress_rental )) ) {
			/**
 			 *
			 * @fault
			 *
			 */
			// ERROR: !($d_sh /\ $d_s /\ $d_r ) /\ ($d_sh \/ $d_s \/ $d_r )
			// ERROR: Inconsistent State, missing product indicator decoration, recover from this issue if possible,
			// and log the error. We may have missed sales, so we should handle this problem immediately.
		} else {
			remove_action( 'save_post', array( $this, 'cc_dress_modify' ) );
			$this->cc_dress_create( $post, $post_id );
			add_action( 'save_post', array( $this, 'cc_dress_modify' ));
		}		
	}

	/**
	 * create a dress share, which is product linked to the dress by a field.
	 *
	 * @param WP_Post $post, a WP_Post dress object
	 * @param int $parent_post_id, $post->ID
	 * @return int child post id
	 */
	private function cc_create_dress_share_product( $post, $parent_post_id ) {
		$dress_share = array(
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

		wp_set_object_terms( $post_id, 'share', 'product_cat' );

		$share_price = get_field( CC_Init::$fields['share_price'], $parent_post_id );
		$sku = get_field( CC_Init::$fields['id'], $parent_post_id );

		update_post_meta( $post_id, '_virtual', 'no'); // check this for functionality
		update_post_meta( $post_id, 'total_sales', 0);
		update_post_meta( $post_id, '_stock', 5 );
		update_post_meta( $post_id, '_manage_stock', "yes" );

		update_post_meta( $post_id, '_regular_price',  $share_price );
		update_post_meta( $post_id, '_sale_price', $share_price );
		update_post_meta( $post_id, '_price', $share_price );
		update_post_meta($post_id, '_sku', cc_compute_product_sku( $sku, "SHARE") );

		// NOT DONE <= these are not used currently, but could be used to programmatically calculate shipping later.
		// update_post_meta( $post_id, '_weight', "" );
		// update_post_meta( $post_id, '_length', "" );
		// update_post_meta( $post_id, '_width', "" );
		// update_post_meta( $post_id, '_height', "" );
		
		// DEFAULTS
		update_post_meta( $post_id, '_visibility', 'visible' );
		update_post_meta( $post_id, '_stock_status', 'instock');
		update_post_meta( $post_id, '_downloadable', 'no');

		// update_post_meta( $post_id, '_purchase_note', "" );
		// update_post_meta( $post_id, '_featured', "no" );
		// update_post_meta( $post_id, '_sold_individually', "" );
		// update_post_meta( $post_id, '_backorders', "no" );
		// update_post_meta( $post_id, '_product_attributes', array());
		// update_post_meta( $post_id, '_sale_price_dates_from', "" );
		// update_post_meta( $post_id, '_sale_price_dates_to', "" );

		update_field( CC_Init::$fields['share'], array( $post_id ), $parent_post_id );

		return $post_id;
	}

	/**
	 * create a dress sale product, which is product linked to the dress by a field.
	 *
	 * @param WP_Post $post, a WP_Post dress object
	 * @param int $parent_post_id, $post->ID
	 * @return int child post id
	 */
	private function cc_create_dress_sale_product( $post, $parent_post_id ) {
		$dress_sale = array(
			'post_content' => '',
			'comment_status'  => 'closed',
			'ping_status'   => 'closed',
			'post_author'   => $post->post_author, // the author of the parent post is the author of this system post
			'post_name'   => cc_compute_product_name( $post->post_name, 'eos'),
			'post_title'    => cc_compute_product_title( $post->post_title, 'Sale' ),
			'post_status'   => 'publish',
			'post_type'   => 'product'
		);

		$post_id = wp_insert_post( $dress_sale );

		wp_set_object_terms( $post_id, 'sale', 'product_cat' );

		$sale_price = get_field(CC_Init::$fields['sale_price'], $parent_post_id);
		$sku = get_field(CC_Init::$fields['id'], $parent_post_id);

		update_post_meta( $post_id, '_virtual', 'no'); // check this for functionality
		update_post_meta( $post_id, 'total_sales', 0);
		update_post_meta( $post_id, '_stock', 1 );
		update_post_meta( $post_id, '_manage_stock', "yes" );

		update_post_meta( $post_id, '_regular_price',  $sale_price );
		update_post_meta( $post_id, '_sale_price', $sale_price );
		update_post_meta( $post_id, '_price', $sale_price );
		update_post_meta($post_id, '_sku', cc_compute_product_sku( $sku, "EOS") );

		// NOT DONE <= these are not used currently, but could be used to programmatically calculate shipping later.
		// update_post_meta( $post_id, '_weight', "" );
		// update_post_meta( $post_id, '_length', "" );
		// update_post_meta( $post_id, '_width', "" );
		// update_post_meta( $post_id, '_height', "" );
		
		// DEFAULTS
		update_post_meta( $post_id, '_visibility', 'visible' );
		update_post_meta( $post_id, '_stock_status', 'instock');
		update_post_meta( $post_id, '_downloadable', 'no');

		// update_post_meta( $post_id, '_purchase_note', "" );
		// update_post_meta( $post_id, '_featured', "no" );
		// update_post_meta( $post_id, '_sold_individually', "" );
		// update_post_meta( $post_id, '_backorders', "no" );
		// update_post_meta( $post_id, '_product_attributes', array());
		// update_post_meta( $post_id, '_sale_price_dates_from', "" );
		// update_post_meta( $post_id, '_sale_price_dates_to', "" );

		update_field( CC_Init::$fields['sale'], array( $post_id ), $parent_post_id );

		return $post_id;
	}


	/**
	 * create a dress rental, which is bookable product linked to the dress by a field.
	 * this function also creates a series of resources associated with this bookable product,
	 * a Prereservation resource, Rental resource, Nextday resource, and Update resource.
	 *
	 * @param WP_Post $post, a WP_Post dress object
	 * @param int $parent_post_id, $post->ID
	 * @return int child post id
	 */
	private function cc_create_dress_rental_product( $post, $parent_post_id ) {
		$dress_rental =  array(
			'post_content' => get_field(CC_Init::$fields['description'], $parent_post_id),
			'comment_status'  => 'closed',
			'ping_status'   => 'closed',
			'post_author'   => $post->post_author, // the author of the parent post is the author of this system post
			'post_name'   => cc_compute_product_name( $post->post_name, 'rental'),
			'post_title'    => cc_compute_product_title( $post->post_title, 'Rental' ),
			'post_status'   => 'publish',
			'post_type'   => 'product'
		);

		$post_id = wp_insert_post( $dress_rental );

		wp_set_object_terms( $post_id, 'rental', 'product_cat' );

		$rental_price = get_field(CC_Init::$fields['rent_price'], $parent_post_id);
		$dry_price = get_field(CC_Init::$fields['dry_price'], $parent_post_id);
		$sku = get_field(CC_Init::$fields['id'], $parent_post_id);

		wp_set_object_terms( $post_id, 'booking', 'product_type' );

		update_post_meta($post_id, '_wc_booking_has_resources', 'yes');

		update_post_meta( $post_id, '_wc_booking_duration_type', 'fixed');
		update_post_meta( $post_id, '_wc_booking_duration', CC_BOOKING_DURATION);
		update_post_meta( $post_id, '_wc_booking_duration_unit', 'day');
		update_post_meta( $post_id, '_wc_booking_base_cost', 0);
		update_post_meta( $post_id, '_wc_booking_block_cost', 0);
		update_post_meta( $post_id, '_wc_booking_min_duration', 1);
		update_post_meta( $post_id, '_wc_booking_max_duration', 1);
		update_post_meta( $post_id, '_wc_booking_qty', 1);
		update_post_meta( $post_id, '_wc_booking_max_date', 12);
		update_post_meta($post_id, '_wc_booking_max_date_unit', 'month');
		update_post_meta($post_id, '_wc_booking_min_date', 1);
		update_post_meta($post_id, '_wc_booking_min_date_unit', 'day');
		update_post_meta($post_id, '_wc_booking_default_date_availability', 'available');
		update_post_meta($post_id, '_wc_booking_calendar_display_mode', 'always_visible');

		update_post_meta( $post_id, '_virtual', 'no'); // check this for functionality
		update_post_meta( $post_id, 'total_sales', 0);
		update_post_meta( $post_id, '_stock', "");
		update_post_meta( $post_id, '_manage_stock', "no" );

		update_post_meta( $post_id, '_regular_price',  0 );
		update_post_meta( $post_id, '_sale_price', 0 );
		update_post_meta( $post_id, '_price', 0 );
		update_post_meta($post_id, '_sku', $sku );

		// NOT DONE <= these are not used currently, but could be used to programmatically calculate shipping later.
		// update_post_meta( $post_id, '_weight', "" );
		// update_post_meta( $post_id, '_length', "" );
		// update_post_meta( $post_id, '_width', "" );
		// update_post_meta( $post_id, '_height', "" );
		
		// DEFAULTS
		update_post_meta( $post_id, '_visibility', 'visible' );
		update_post_meta( $post_id, '_stock_status', 'instock');
		update_post_meta( $post_id, '_downloadable', 'no');

		$this->cc_create_rental_resources( $post, $post_id, $rental_price, $dry_price );

		// update_post_meta( $post_id, '_purchase_note', "" );
		// update_post_meta( $post_id, '_featured', "no" );
		// update_post_meta( $post_id, '_sold_individually', "" );
		// update_post_meta( $post_id, '_backorders', "no" );
		// update_post_meta( $post_id, '_product_attributes', array());
		// update_post_meta( $post_id, '_sale_price_dates_from', "" );
		// update_post_meta( $post_id, '_sale_price_dates_to', "" );

		update_field( 'dress_rental_product_instance', array( $post_id ), $parent_post_id );

		return $post_id;
	}

	/**
	 * create the rental resources associated with a given rental bookable product.
	 *
	 * @param int $id, the id of a bookable product
	 * @param int $rental_price, the price of this rental
	 * @param int $dry_price, this dress' associated dry cleaning fee
	 */
	private function cc_create_rental_resources( $post, $id, $rental_price, $dry_price ) {
		$pre_args = array(
			'post_title' => 'Prereservation',
			'post_parent' => $id,
			'post_status' => 'publish',
			'post_type' => 'bookable_resource',
			'comment_status'  => 'closed',
			'ping_status'   => 'closed',
			'post_author'   => $post->post_author
		);

		$rent_args = array(
			'post_title' => 'Rental',
			'post_parent' => $id,
			'post_status' => 'publish',
			'post_type' => 'bookable_resource',
			'comment_status'  => 'closed',
			'ping_status'   => 'closed',
			'post_author'   => $post->post_author
		);

		$update_args = array(
			'post_title' => 'Update',
			'post_parent' => $id,
			'post_status' => 'publish',
			'post_type' => 'bookable_resource',
			'comment_status'  => 'closed',
			'ping_status'   => 'closed',
			'post_author'   => $post->post_author
		);

		$next_day_args = array(
			'post_title' => 'Nextday',
			'post_parent' => $id,
			'post_status' => 'publish',
			'post_type' => 'bookable_resource',
			'comment_status'  => 'closed',
			'ping_status'   => 'closed',
			'post_author'   => $post->post_author
		);


		$pre_id = wp_insert_post( $pre_args );
		$rent_id = wp_insert_post( $rent_args );
		$update_id = wp_insert_post( $update_args );
		$next_day_id = wp_insert_post( $next_day_args );

		update_post_meta( $pre_id, 'qty', '');
		update_post_meta( $rent_id, 'qty', '');
		update_post_meta( $update_id, 'qty', '');
		update_post_meta( $next_day_id, 'qty', '');

		$base_resources = array();
		$base_resources[ $update_id ] = 0;
		$base_resources[ $pre_id ] = $dry_price;
		$base_resources[ $next_day_id ] = $dry_price;
		$base_resources[ $rent_id ] = $dry_price + $rental_price;
		
		update_post_meta( $id, '_resource_base_costs', $base_resources );

		$block_resources = array();
		$block_resources[ $update_id ] = 0;
		$block_resources[ $pre_id ] = 0;
		$block_resources[ $next_day_id ] = 0;
		$block_resources[ $rent_id ] = 0;

		update_post_meta( $id, '_resource_block_costs', $block_resources );

		$this->link_product_and_resource( $id, $rent_id, 0 );
		$this->link_product_and_resource( $id, $pre_id, 1 );
		$this->link_product_and_resource( $id, $update_id, 2 );
		$this->link_product_and_resource( $id, $next_day_id, 3 );
	}

	/**
	 * link produces and resources together in the wc_booking_relationships table 
	 *
	 * @param int $post_id the id of the product post
	 * @param int $resource_id the id of the bookable resource
	 * @param int $order the order in which these products should display in the menu
	 */
	private function link_product_and_resource( $post_id, $resource_id, $order ) {
		global $wpdb;

		$wpdb->insert(
				"{$wpdb->prefix}wc_booking_relationships",
				array(
					'product_id'  => $post_id,
					'resource_id' => $resource_id,
					'sort_order'  => $order
				)
			);
	}

	/**
	 * delete all resources associated with a given product
	 *
	 * @param int $post_id the id of the product to delete resources for
	 */
	private function unlink_product_and_resources( $post_id ) {
		global $wpdb;

		$resource_ids = ws_fst( get_post_meta( $post_id, '_resource_base_costs' ) );

		foreach ( $resource_ids as $id => $price ) { wp_delete_post( $id, true ); }

		$wpdb->delete(
			"{$wpdb->prefix}wc_booking_relationships",
			array(
				'product_id'  => $post_id
			)
		);
	}

	/**
	 * create all of the resources required for a given dress
	 * @param WP_Post $post, the new dress post to populate
	 * @param int $post_id, $post->ID
	 */
	private function cc_dress_create( $post, $post_id ) {
		$this->cc_create_dress_share_product( $post, $post_id );
		$this->cc_create_dress_sale_product( $post, $post_id );
		$this->cc_create_dress_rental_product( $post, $post_id );

	}

	/**
	 * update the sale product with potentially changed information
	 * @param int $id, the id of the sale product to update.
	 * @param array(string => *), a map of changed values
	 */
	private function cc_update_sale_product( $id, $changed ) {
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

	/**
	 * update the share product with potentially changed information
	 * @param int $id, the id of the sale product to update.
	 * @param array(string => *), a map of changed values
	 */
	private function cc_update_share_product( $id, $changed ) {
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

 	/**
	 * update the share product with potentially changed information
	 * This function also updates all of the rental pricing for the rental's associated resources.
	 *
	 * @param int $id, the id of the sale product to update.
	 * @param array(string => *), a map of changed values
 	 */
	private function cc_update_rental_product( $id, $changed ) {
		$updated = array(
			'ID' => $id,
			'post_name' => cc_compute_product_name( $changed['name'], 'rental' ),
			'post_title' => cc_compute_product_title( $changed['title'], 'Rental' )
		);

		wp_update_post( $updated );	

		update_post_meta( $id, '_sku',  $changed['sku'] );
		
		$this->cc_update_rental_pricing( $id, $changed );	
	}

	/**
	 * updates the resources associated with this product, by recalculating all
	 * of the prices associated.
	 * @param int $id the id of the product that owns these resources...
	 * @param array(string => *) $changed, map of changed pricing
	 */
	private function cc_update_rental_pricing( $id, $changed ) {
		$product = get_product( $id );
		$pricing = array();

		foreach ( $product->get_resources() as $key => $resource ) {
			$title = $resource->get_title();
			$id = $resource->get_ID();

			switch ( $title ) {
				case "Prereservation":
					$pricing[ $id ] = $changed['dry_price'];
					break;

				case "Rental":
					$pricing[ $id ] = $changed['dry_price'] + $changed['rental_price'];
					break;

				default:
					break;
			}
		}

		update_post_meta( $id, '_resource_base_costs', $pricing );
	}

	/**
	 * check the dress for consistency and update its associated post types
	 * @param WP_Post $post, the dress object to update the references of.
	 * @param int $post_id, the id of the post to update.
	 */
	private function cc_dress_update( $post, $post_id ) {
		$sale_product = get_field( CC_Init::$fields['sale'], $post_id );
		$rental_product = get_field( CC_Init::$fields['rental'], $post_id );
		$share_product = get_field( CC_Init::$fields['share'], $post_id );

		if ( (1 == count( $sale_product )) && (1 == count( $rental_product )) && (1 == count( $share_product )) ) {
			$changed = array(
				'name' => $post->post_name,
				'title' => $post->post_title,
				'sku' => get_field( CC_Init::$fields['id'], $post_id ),
				'sale_price' => get_field( CC_Init::$fields['sale_price'], $post_id), 
				'rental_price' => get_field( CC_Init::$fields['rent_price'], $post_id ),
				'share_price' => get_field( CC_Init::$fields['share_price'], $post_id),
				'dry_price' => get_field( CC_Init::$fields['dry_price'], $post_id )
			);

			$this->cc_update_sale_product( ws_fst( $sale_product )->ID, $changed );
			$this->cc_update_rental_product( ws_fst( $rental_product )->ID, $changed );
			$this->cc_update_share_product( ws_fst( $share_product )->ID, $changed );

		} else {
			// ERROR, no 1-to-3 injection
		}

	}

	/**
	 * Hook to destroy all related references to a given dress-typed post.
	 * @param  int $post_id the id of the post to destroy and disconnect from the database
	 * 
	 */
	public function cc_dress_destroy( $post_id ) {
		$post = get_post( $post_id );
		if ( !cc_dress_trash( $post ) ) return;

		$sale_product = get_field( CC_Init::$fields['sale'], $post_id );
		$rental_product = get_field( CC_Init::$fields['rental'], $post_id );
		$share_product = get_field( CC_Init::$fields['share'], $post_id );

		if ( (1 == count( $sale_product )) && (1 == count( $rental_product )) && (1 == count( $share_product )) ) {
			
			$this->unlink_product_and_resources( ws_fst( $rental_product )->ID );

			wp_delete_post( ws_fst( $sale_product )->ID, true);
			wp_delete_post( ws_fst( $rental_product )->ID, true);
			wp_delete_post( ws_fst( $share_product )->ID, true);

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
		}
	}

}

new CC_Init();


?>
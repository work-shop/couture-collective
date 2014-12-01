<?php

$id = get_the_ID();

$GLOBALS['CC_POST_DATA'] = array();
$GLOBALS['CC_POST_DATA']['id'] = $id;
$GLOBALS['CC_POST_DATA']['logged_in'] = is_user_logged_in();
$GLOBALS['CC_POST_DATA']['title'] = apply_filters( 'the_title', get_the_title() );
$GLOBALS['CC_POST_DATA']['description'] = get_field( 'dress_description', $id );
$GLOBALS['CC_POST_DATA']['designer'] = get_field( 'dress_designer', $id );
$GLOBALS['CC_POST_DATA']['images'] = get_field('dress_images', $id);


if ( $GLOBALS['CC_POST_DATA']['logged_in'] ) {

	$GLOBALS['CC_POST_DATA']['size'] = get_field('dress_size', $id);
	$GLOBALS['CC_POST_DATA']['sale'] = new WC_Product( get_field('dress_sale_product_instance', $id )[0]->ID );
	$GLOBALS['CC_POST_DATA']['share'] = new WC_Product( get_field('dress_share_product_instance', $id )[0]->ID );
	$GLOBALS['CC_POST_DATA']['rental'] = new WC_Product_Booking( get_field('dress_rental_product_instance', $id)[0]->ID );
	$GLOBALS['CC_POST_DATA']['user'] = wp_get_current_user();

}

?>
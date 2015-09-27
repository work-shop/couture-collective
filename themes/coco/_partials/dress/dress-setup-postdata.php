<?php

$id = get_the_ID();

$GLOBALS['CC_POST_DATA'] = array();
$GLOBALS['CC_POST_DATA']['id'] = $id;
$GLOBALS['CC_POST_DATA']['logged_in'] = is_user_logged_in();
$GLOBALS['CC_POST_DATA']['title'] = apply_filters( 'the_title', get_the_title() );
$GLOBALS['CC_POST_DATA']['description'] = get_field( 'dress_description', $id );
$GLOBALS['CC_POST_DATA']['designer'] = get_field( 'dress_designer', $id );
$GLOBALS['CC_POST_DATA']['images'] = get_field('dress_images', $id);
$GLOBALS['CC_POST_DATA']['size'] = get_field('dress_size', $id);


$GLOBALS['CC_POST_DATA']['active'] = CC_Controller::dress_is_in_active_season( $id );



if ( $GLOBALS['CC_POST_DATA']['logged_in'] ) {
	$GLOBALS['CC_POST_DATA']['sale'] = new WC_Product( get_field( CC_Controller::$field_keys['sale_product'], $id )[0]->ID );
	$GLOBALS['CC_POST_DATA']['share'] = new WC_Product( get_field( CC_Controller::$field_keys['share_product'], $id )[0]->ID );
	$GLOBALS['CC_POST_DATA']['rental'] = new WC_Product_Booking( get_field( CC_Controller::$field_keys['rental_product'], $id)[0]->ID );
	$GLOBALS['CC_POST_DATA']['user'] = wp_get_current_user();
	$GLOBALS['CC_POST_DATA']['prereservations'] = CC_Controller::get_prereservations_for_dress_rental($GLOBALS['CC_POST_DATA']['rental']->id, $GLOBALS['CC_POST_DATA']['user']->ID);
	$GLOBALS['CC_POST_DATA']['tomorrow'] = CC_Controller::available_tomorrow( $GLOBALS['CC_POST_DATA']['rental'] );

	$shares = CC_Controller::get_shared_dresses_for_user( $GLOBALS['CC_POST_DATA']['user'] );
	$GLOBALS['CC_POST_DATA']['owned'] = in_array( $GLOBALS['CC_POST_DATA']['id'], $shares);

}

?>
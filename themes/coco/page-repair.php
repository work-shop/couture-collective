<?
/*
	Template Name: Repair
*/
?>

<?php

if ( get_current_user_id() == 1) {

	global $wpdb;

	$sql1 = "SELECT post_title, COUNT(*) c FROM {$wpdb->prefix}posts WHERE post_type = %s AND ((post_title LIKE %s) OR (post_title LIKE %s) OR (post_title LIKE %s)) GROUP BY post_title HAVING c > 1";

	$column1 = $wpdb->get_col( $wpdb->prepare( $sql1, 'product', '%Share%', '%Rental%', '%Sale%') );

	$column1 = '("' . join( '","', $column1 ) . '")';

	$sql2 = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title IN $column1";

	$column2 = $wpdb->get_col( $wpdb->prepare( $sql2 ) );

	$originals = array();
	$posts = array();

	foreach ( $column2 as $ID) {
		$posts[ $ID ] = get_post( $ID );

		$post = $posts[ $ID ];

		$t = $post->post_title;

		if ( !isset( $originals[ $post->post_title ] ) ) {
			$originals[ $t ] = INF;
		}

		$originals[ $t ] = ( $post->ID < $originals[ $t ] ) ? $post->ID : $originals[ $t ];

	}

	foreach ($column2 as $ID) {

		$post = $posts[ $ID ];

		if ( $post->ID != $originals[ $post->post_title ] ) {
			echo "<b>post $post->ID: $post->post_title</b><br/>";

			$type_guess = explode( ' ', $post->post_title);
			$type_guess = $type_guess[ count( $type_guess ) - 1 ];
			$type_guess = strtolower( $type_guess );

			$dress_id = CC_Controller::get_dress_for_product( $post->ID, $type_guess );

			if ( $dress_id ) {
				echo "Linked Dress: $dress_id<br/>";

				$returned_id = get_field( CC_Controller::$field_keys[ $type_guess . '_product' ], $dress_id )[0]->ID;

				echo "Retrieved $type_guess Product: $returned_id -- Selected $type_guess Product: $post->ID<br/>";

				echo "Equal: " . ($returned_id == $post->ID) . "<br/>";

			} else {

				echo "No Linked Dress... <br/>";

			}

			echo "<br/>";

			



			//wp_delete_post( $post->ID, true );
			//echo " ...deleted. <br/>";
		} 

	}

	die();

} else {

get_template_part('_partials/placeholder/placeholder', 'forward');

}

?>
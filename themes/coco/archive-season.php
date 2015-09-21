<?php
/** 
 * This page needs significant work. For now, it simply reduces the set of seasons to the currently INACTIVE season, and f
 * forwards the user to that season's single page. Ultimately, it should only do this if there is only ONE other season, and display a list of seasons
 * in any other case.
 */



$args = array(
	'post__not_in' => array( CC_Controller::get_active_season() ),
	'post_type' => 'season',
	'posts_per_page' => -1,
	'orderby' => 'menu_order',
	'order' => 'ASC'
);

$season = ws_fst( get_posts( $args ) );

wp_redirect( get_the_permalink( $season->ID ) );

?>
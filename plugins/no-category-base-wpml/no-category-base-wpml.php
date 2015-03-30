<?php
/*
Plugin Name: WP No Category Base - WPML compatible
Plugin URI: http://github.com/mines/no-category-base-wpml
Description: Removes '/category' from your category permalinks. WPML compatible.
Version: 1.1.0
Author: Mines
Author URI: http://mines.io/
*/

/*
    Based on the work by Saurabh Gupta  (email : saurabh0@gmail.com)

    Copyright 2008  Saurabh Gupta  (email : saurabh0@gmail.com)
    Copyright 2011  Mines          (email: hi@mines.io)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* hooks */
register_activation_hook(__FILE__,    'no_category_base_refresh_rules');
register_deactivation_hook(__FILE__,  'no_category_base_deactivate');

/* actions */
add_action('created_category',  'no_category_base_refresh_rules');
add_action('delete_category',   'no_category_base_refresh_rules');
add_action('edited_category',   'no_category_base_refresh_rules');
add_action('init',              'no_category_base_permastruct');

/* filters */
add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
add_filter('query_vars',             'no_category_base_query_vars');    // Adds 'category_redirect' query variable
add_filter('request',                'no_category_base_request');       // Redirects if 'category_redirect' is set

function no_category_base_refresh_rules()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function no_category_base_deactivate()
{
	remove_filter('category_rewrite_rules', 'no_category_base_rewrite_rules'); // We don't want to insert our custom rules again
	no_category_base_refresh_rules();
}

/**
 * Removes category base.
 *
 * @return void
 */
function no_category_base_permastruct()
{
	global $wp_rewrite;
  global $wp_version;
  if ($wp_version >= 3.4) {
	  $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
  } else {
    $wp_rewrite->extra_permastructs['category'][0] = '%category%';
  }
}

/**
 * Adds our custom category rewrite rules.
 *
 * @param  array $category_rewrite Category rewrite rules.
 *
 * @return array
 */
function no_category_base_rewrite_rules($category_rewrite)
{
  $category_rewrite=array();
  /* WPML is present: temporary disable terms_clauses filter to get all categories for rewrite */
  if (class_exists('Sitepress')) {
    global $sitepress;
    remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
    $categories = get_categories(array('hide_empty' => false));
    add_filter('terms_clauses', array($sitepress, 'terms_clauses'));
  } else {
    $categories = get_categories(array('hide_empty' => false));
  }

	foreach($categories as $category) {
		$category_nicename = $category->slug;
		if ( $category->parent == $category->cat_ID ) {
			$category->parent = 0;
    } elseif ($category->parent != 0 ) {
      $category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;
    }
		$category_rewrite['('.$category_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
		$category_rewrite['('.$category_nicename.')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
		$category_rewrite['('.$category_nicename.')/?$'] = 'index.php?category_name=$matches[1]';
	}
	// Redirect support from Old Category Base
	global $wp_rewrite;
	$old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
	$old_category_base = trim($old_category_base, '/');
	$category_rewrite[$old_category_base.'/(.*)$'] = 'index.php?category_redirect=$matches[1]';
	return $category_rewrite;
}

function no_category_base_query_vars($public_query_vars)
{
	$public_query_vars[] = 'category_redirect';
	return $public_query_vars;
}

/**
 * Handles category redirects.
 *
 * @param $query_vars Current query vars.
 *
 * @return array $query_vars, or void if category_redirect is present.
 */
function no_category_base_request($query_vars)
{
	if(isset($query_vars['category_redirect'])) {
		$catlink = trailingslashit(get_option( 'home' )) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
		status_header(301);
		header("Location: $catlink");
		exit();
	}
	return $query_vars;
}

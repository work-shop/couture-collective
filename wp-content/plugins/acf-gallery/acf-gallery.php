<?php
/*
Plugin Name: Advanced Custom Fields: Gallery Field
Plugin URI: http://www.advancedcustomfields.com/
Description: This premium Add-on adds a gallery field type for the Advanced Custom Fields plugin
Version: 1.1.1
Author: Elliot Condon
Author URI: http://www.elliotcondon.com/
License: GPL
Copyright: Elliot Condon
*/


add_action('acf/register_fields', 'acfgp_register_fields');

function acfgp_register_fields()
{
	include_once('gallery.php');
}


/*
*  Update
*
*  if update file exists, allow this add-on to connect and recieve updates.
*  all ACF premium Add-ons which are distributed within a plugin or theme, must have the update file removed.
*
*  @type	file
*  @date	13/07/13
*
*  @param	N/A
*  @return	N/A
*/

if( file_exists(  dirname( __FILE__ ) . '/acf-gallery-update.php' ) )
{
	include_once( dirname( __FILE__ ) . '/acf-gallery-update.php' );
}

?>

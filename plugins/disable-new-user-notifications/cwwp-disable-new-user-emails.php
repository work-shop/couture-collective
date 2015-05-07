<?php
/*
Plugin Name: Disable New User Notifications
Plugin URI: http://thomasgriffinmedia.com/
Description: Disables new user notification emails.
Author: Thomas Griffin
Author URI: http://thomasgriffinmedia.com/
Version: 1.0.1
License: GNU General Public License v2.0 or later
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/*
	Copyright 2012	 Thomas Griffin	 (email : thomas@thomasgriffinmedia.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! function_exists( 'wp_new_user_notification' ) ) :
	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
		
		/** Return early if no password is set */
		if ( empty( $plaintext_pass ) )
			return;
			
		$user 		= get_userdata( $user_id );
		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$message  = sprintf( __( 'Username: %s' ), $user_login) . "\r\n";
		$message .= sprintf( __( 'Password: %s' ), $plaintext_pass) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail( $user_email, sprintf( __( '[%s] Your username and password' ), $blogname ), $message );

	}
endif;
require plugin_dir_path( __FILE__ ) . 'utils.php';
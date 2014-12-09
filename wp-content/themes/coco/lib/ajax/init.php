<?php
	/**
	 * Sends json response to client
	 * @param Array $data, data to be serialized and sent to client.
	 */
	function respond( $data ) {
	    wp_send_json_success( $data );
	} 

	// require_once( 'change-booking.php' );
?>
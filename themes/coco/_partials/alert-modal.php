<?php 

$alert_state = 'site-alert-on';

if ( !is_page(array( 7, 35 )) ) :
	if ( is_user_logged_in() ) :
	 	global $current_user;
	 	get_currentuserinfo();
		 	if(current_user_can( 'manage_options' ) || current_user_can('manage_woocommerce') ): 
		 		$alert_state = 'site-alert-off';	 		
		 	endif;
	endif; 	
endif; 

?>
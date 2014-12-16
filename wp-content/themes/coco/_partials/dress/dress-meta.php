
	<div class="bordered-dark-top">
		<?php
			
			$id = $GLOBALS['CC_POST_DATA']['id'];
			$user = $GLOBALS['CC_POST_DATA']['logged_in'];
	
			$title = $GLOBALS['CC_POST_DATA']['title'];
			$description = $GLOBALS['CC_POST_DATA']['description'];
			$designer = $GLOBALS['CC_POST_DATA']['designer'];
	
			$size = $GLOBALS['CC_POST_DATA']['size'];
	
			echo ws_ifdef_do( $designer, ws_ifdef_concat('<h1 class="uppercase dress-designer">',$designer,'</h1>') );
			echo ws_ifdef_do( $title, ws_ifdef_concat('<h6 class="dress-description">',$description,'</h6>') );
	
			echo ws_ifdef_do( $user, ws_ifdef_do( $size, ws_ifdef_concat('<p class="h7">SIZE: <span class="numerals h8">',$size,'</span></p>') ) );
			
			?>
			
			<hr class="brand" />
			
			<p class="dress-preview h8 m">
				During our preview session, dresses are unavailable for online reservation. Browse the calendar below to see available dates for reservation for this dress, and then email <a href="mailto:info@couturecollective.club">info@couturecollective.club</a> to inquire about a reservation.
			</p>
	
	</div>
	
	<?php if ( $user ) : ?>
	<div class="col-sm-3-broken">
		<?php
	
		// CALENDAR
	
		?>
	</div>
	<?php endif; ?>

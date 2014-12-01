<div class="row">
<div class="col-sm-9">
	<?php
		
		$id = $GLOBALS['CC_POST_DATA']['id'];
		$user = $GLOBALS['CC_POST_DATA']['logged_in'];

		$title = $GLOBALS['CC_POST_DATA']['title'];
		$description = $GLOBALS['CC_POST_DATA']['description'];
		$designer = $GLOBALS['CC_POST_DATA']['designer'];

		$size = $GLOBALS['CC_POST_DATA']['size'];

		echo ws_ifdef_do( $title, ws_ifdef_concat('<h3>',$title,'</h3>') );
		echo ws_ifdef_do( $designer, ws_ifdef_concat('<h6>',$designer,'</h6>') );
		echo ws_ifdef_do( $description, ws_ifdef_concat('<h6>',$description,'</h6>') );

		echo ws_ifdef_do( $user, ws_ifdef_do( $size, ws_ifdef_concat('<h6>SIZE: ',$size,'</h6>') ) );

	?>
</div>

<?php if ( $user ) : ?>
<div class="col-sm-3">
	<?php

	// CALENDAR

	?>
</div>
<?php endif; ?>
</div>
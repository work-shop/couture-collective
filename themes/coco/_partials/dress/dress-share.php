<li class="active row">
	<div class="col-sm-12" >
	<?php
		$user = $GLOBALS['CC_POST_DATA']['user'];
		$share = $GLOBALS['CC_POST_DATA']['share'];
	
		if ( $share->is_in_stock() ) {
	
			get_template_part('_partials/dress/dress', 'share-unsold');
			
		} else {
	
			get_template_part('_partials/dress/dress', 'share-out-of-stock');
	
		}
	
	?>
	
	</div>
</li>
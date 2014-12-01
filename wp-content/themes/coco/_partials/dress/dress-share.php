<div class="row">
<div class="col-sm-12 col-lg-12" style="border-top:3px solid #aaa;">
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
</div>
<?php
$rentals = CC_Controller::get_rentals_by_dress_for_user( get_current_user_id() );

var_dump( $rentals );



?>

<div id="rented-dresses" class="row rented-dresses">
	<?php if ( empty($rentals ) ) { ?>

	<h3 class="serif centered">You haven't rented in any dresses. Dresses can be rented via the <a href="<?php echo bloginfo('url'); ?>/look-book">Look Book</a>.</h3>

	<?php } else { ?>


	<?php } ?>
	<hr class="page-header-rule"/>
</div>
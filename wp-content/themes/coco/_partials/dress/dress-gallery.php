<?php
	$images = get_field('dress_images', get_the_ID() );

	if ( $images ) {
?>

	<div class="row">
		<div class="col-sm-1">
			<div class="row">
			<!-- gallery grid -->

			</div>
		</div>
		<div class="col-sm-11">
			<!-- gallery image -->
			
		</div>
	</div>

<?php
	} else {
?>


<?php
	}
?>
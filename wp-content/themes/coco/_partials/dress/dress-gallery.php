<?php
	$images = get_field('dress_images', get_the_ID() );

	if ( $images ) {
?>

	<div class="row">
		<div class="col-sm-3">
			<div class="row">

			</div>
		</div>
		<div class="col-sm-9">
			<?php 
			if ( has_post_thumbnail() ) {
				the_post_thumbnail('gallery');
			} else {
				echo '<img src="' . get_bloginfo( 'template_directory' ) . '/_/img/thumbnail-default.jpg" />';
			}
			?>	
			
		</div>
	</div>

<?php
	} else {
?>


<?php
	}
?>
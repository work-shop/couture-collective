<?php
	$images = get_field('dress_images', get_the_ID() );

	if ( $images ) {
?>

	<div class="row">
		<div class="col-sm-2 col-xs-2">
		
		</div>

		<div class="col-sm-10 col-xs-8 main-image off">
			<?php 
			if ( has_post_thumbnail() ) {
				the_post_thumbnail('dress-large');
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
<?php
	

	if ( $images = get_field('dress_images', get_the_ID() ) ) {

?>

	<div class="row">
		<?php if (count( $images ) > 1) : ?>
		<div class="col-sm-2 p0 hidden-xs">
		<ul class="">
			<?php for ( $i = 1; $i < count( $images ); $i++ ) : ?>
				<li class="small-image main-image m">
					<a href="#">
					<img data-zoom-image="<?php echo $images[ $i ]['sizes']['full']; ?>" src-large="<?php echo $images[ $i ]['sizes']['large']; ?>" src="<?php echo $images[ $i ]['sizes']['thumbnail']; ?>" />
					</a>
				</li>

			<?php endfor; ?>
		</ul>
		</div>
	<?php endif; ?>

		<div id="main-image" class="col-sm-10 main-image off">
			<?php 
			if ( has_post_thumbnail() ) { ?>

				<!-- <div src-giant="<?php echo $images[ 0 ]['sizes']['full']; ?>" class="magnifier"></div> -->
				<img data-zoom-image="<?php echo $images[ 0 ]['sizes']['full']; ?>" src-small="<?php echo $images[ 0 ]['sizes']['thumbnail']; ?>" src="<?php echo $images[ 0 ]['sizes']['large']; ?>" />
				
			<?php } else {
				echo '<img class="no-scale" src="' . get_bloginfo( 'template_directory' ) . '/_/img/Dress-Placeholder_01.png" />';
			}
			?>	
			
		</div>
	</div>

<?php } ?>
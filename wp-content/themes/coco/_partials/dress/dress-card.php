<?php
	//PARAMETERS: WP_Query Iterator:
	$GLOBALS['LOOP']->the_post(); 
?>

<div class="col-sm-3 col-md-2 col-xs-6 product-tile <?php echo cc_get_dress_states( $GLOBALS['USER'], get_the_ID() ); ?>">
	<a href="<?php // the_permalink(); ?>">
		
		<div class="product-image">
			<?php 
			if ( has_post_thumbnail() ) {
				the_post_thumbnail();
			} else {
				echo '<img src="' . get_bloginfo( 'template_directory' ) . '/_/img/thumbnail-default.jpg" />';
			}
			?>							
		</div>
		

		<div class="product-summary centered">
			<h4><?php echo get_field('dress_designer', get_the_ID()); ?></h4>
		</div>
	
	</a>
</div>



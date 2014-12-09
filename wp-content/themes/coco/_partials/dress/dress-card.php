<?php
	//PARAMETERS: WP_Query Iterator:
	$GLOBALS['LOOP']->the_post(); 
?>


<div class="col-sm-3 col-md-3 col-xs-6 product-card card <?php // echo cc_get_dress_states( $GLOBALS['USER'], get_the_ID() ); ?>">
	<a href="<?php the_permalink(); ?>">
		
		<div class="product-image">
			<?php 
			if ( has_post_thumbnail() ) {
				the_post_thumbnail();
			} else {
				echo '<img src="' . get_bloginfo( 'template_directory' ) . '/_/img/thumbnail-default.jpg" />';
			}
			?>							
		</div>
		

		<div class="product-summary">
			<p class="h11 card-designer bordered-dark-bottom"><?php echo get_field('dress_designer', get_the_ID()); ?></p>
		</div>
	
	</a>
</div>



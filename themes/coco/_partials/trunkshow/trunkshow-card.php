
<?php $show = $GLOBALS['SHOW']; ?>

<div class="col-sm-4 col-md-4 col-xs-12 product-card card">
	<a href="<?php echo get_permalink( $show->ID ); ?>">

		<div class="product-image">
			<?php 
			if ( has_post_thumbnail( $show->ID ) ) {
				echo get_the_post_thumbnail( $show->ID, 'large' );
				// echo '<img width="178" class="attachment-post-thumbnail wp-post-image" height="372" src="' . get_the_post_thumbnail( $show->ID, 'large' ) . '" />';
				
			} else {
				echo '<img width="178" class="attachment-post-thumbnail wp-post-image" height="372" src="' . get_bloginfo( 'template_directory' ) . '/_/img/Dress-Placeholder_01.png" />';
			}
			?>							
		</div>

		<div class="product-summary">
			<p class="h11 card-designer uppercase bordered-dark-bottom"><?php echo $show->post_title; ?></p>
			<p class="h11 numerals"><?php echo ws_render_date( get_field('trunk_show_date', $show->ID) ); ?></p>
		</div>
	
	</a>
</div>
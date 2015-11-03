<?php $designer = get_field(CC_Controller::$field_keys['dress_designer'], get_the_ID()); ?>
<?php $description = get_field(CC_Controller::$field_keys['dress_description'], get_the_ID()); ?>

<div 
	class="col-sm-4 col-md-4 col-xs-6 product-card card <?php echo cc_get_dress_states( $GLOBALS['USER'], get_the_ID() ); ?>"
	<?php if ($sizes = CC_Controller::get_normalized_dress_size( get_the_ID() ) ) { ?>

			data-size-value="<?php echo implode( ',', array_merge( $sizes, array( '*' ) ) ); ?>"

	<?php } else { ?>
		
			data-size-value="*"

	<?php } ?>

	data-designer-value="<?php echo CC_Controller::normalize_name( $designer ); ?>,*"


>
	<a href="<?php the_permalink(); ?>">

		<div class="product-image">
			<?php 
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'large' );
			} else {
				echo '<img width="178" class="attachment-post-thumbnail wp-post-image" height="372" src="' . get_bloginfo( 'template_directory' ) . '/_/img/Dress-Placeholder_01.png" />';
			}
			?>							
		</div>

		<div class="product-summary">
			<p class="h11 card-designer bordered-dark-bottom m1"><?php echo $designer; ?></p>
			<p class="card-designer small bold"><?php echo strtolower( $description ); ?></p>
		</div>
	
	</a>
</div>
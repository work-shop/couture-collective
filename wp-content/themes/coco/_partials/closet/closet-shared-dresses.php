<?php
$shares = $GLOBALS['CC_CLOSET_DATA']['shares'];

?>

<div id="shared-dresses" class="row shared-dresses">
	<div class="col-sm-12">
	<?php if ( empty( $shares ) ) { ?>

	<h3 class="serif centered">You don't own shares in any dresses right now. Visit the <a href="<?php echo bloginfo('url'); ?>/look-book">Look Book</a> to purchase shares.</h3>

	<?php } else { ?>
	<?php foreach( $shares as $share ) : ?>

		<div class="shared-dress row">
			<div class="col-sm-4 col-sm-offset-1">
			<?php 
				$dress = get_post( $share );
				$GLOBALS['CC_CLOSET_DATA']['dress'] = get_post( $share );
				get_template_part('_partials/dress/closet', 'dress-summary' ); 
				unset( $GLOBALS['CC_CLOSET_DATA']['dress'] );
			?>
			</div>
			<div class="col-sm-6">
				<div class="row">
					
				</div>
				<div class="row">
					<?php
						$GLOBALS['CC_POST_DATA'] = array(
							'user' => wp_get_current_user(),
							'rental' => get_field('dress_rental_product_instance', $share )
						);

						// change this to prereservation history.
						get_template_part( '_partials/dress/dress', 'rental-history');

						unset( $GLOBALS['CC_POST_DATA'] );

					?>
				</div>
			</div>
		</div>

	<?php endforeach; ?>
	<?php } ?>

	<hr class="page-header-rule"/>
	</div>
</div>
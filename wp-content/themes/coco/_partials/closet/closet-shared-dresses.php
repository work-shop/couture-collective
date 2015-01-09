<?php

$shares = $GLOBALS['CC_CLOSET_DATA']['shares'];

?>

<div id="shared-dresses" class="row shared-dresses m3 row-m0">

	<?php if ( empty( $shares ) ) { ?>
		<div class="col-sm-5 col-sm-offset-6">

			<p class="h8">You don't own shares of any dresses right now. Visit the <a href="<?php echo bloginfo('url'); ?>/look-book">Look Book</a> to purchase shares.</p>
			
		</div>

	<?php } else { ?>
	<?php 
	$c = 0;
	foreach( $shares as $share ) : ?>

		<div class="shared-dress m3 row">
			<div class="col-sm-6 col-md-5">
			<?php 
				$dress = get_post( $share );
				$GLOBALS['CC_CLOSET_DATA']['dress'] = $dress;
				get_template_part('_partials/dress/closet', 'dress-summary' ); 
			?>
			</div>
			<div class="col-sm-5 col-md-5 col-md-offset-1">
				<?php
					$rental = get_field('dress_rental_product_instance', $share );

					if ( empty( $rental ) ) continue;

					$GLOBALS['CC_POST_DATA'] = array(
						'user' => wp_get_current_user(),
						'rental' => get_product( ws_fst( $rental ) ),
					);
					$GLOBALS['CC_POST_DATA']['prereservations'] = CC_Controller::get_prereservations_for_dress_rental($GLOBALS['CC_POST_DATA']['rental']->id, $GLOBALS['CC_POST_DATA']['user']->ID);
					$GLOBALS['CC_POST_DATA']['reservation_type'] = "Prereservation";
					// change this to prereservation history.
					get_template_part( '_partials/dress/dress', 'prereservation-history');

					unset( $GLOBALS['CC_POST_DATA'] );
					unset( $GLOBALS['CC_CLOSET_DATA']['dress']);
				?>
			</div>					
		</div>

		<?php if(!$c == count($shares) && count($shares) > 0): ?>
			<div class="col-sm-12">
				<hr />
			</div>	
		<?php endif; ?>	

	<?php $c++; endforeach; ?>

	<?php } ?>

</div>
<?php

//$shares = $GLOBALS['CC_CLOSET_DATA']['shares'];
$shares = CC_Controller::get_shared_dresses_for_user( wp_get_current_user() );

?>

<div id="shared-dresses" class="row shared-dresses m3 row-m0">


	<?php if ( empty( $shares ) ) { ?>
		<div class="col-sm-5 col-sm-offset-6">

			<p class="h8">You don't own shares of any dresses right now. Visit the <a href="<?php echo bloginfo('url'); ?>/look-book">Look Book</a> to purchase shares.</p>
			
		</div>

	<?php } else { ?>
	<?php 
	$c = 0;
	$c2 = count( $shares );
	
	foreach( $shares as $share ) : ?>
		<?php
			$dress = get_post( $share );
			$GLOBALS['CC_CLOSET_DATA']['dress'] = $dress;
			
			$rental = get_field('dress_rental_product_instance', $share );

			if ( empty( $rental ) ) continue;

			$rental = get_product( ws_fst( $rental ) );
			$user = wp_get_current_user();

			$GLOBALS['CC_POST_DATA'] = array(
				'user' => $user,
				'rental' => $rental,
				'tomorrow' => CC_Controller::available_tomorrow( $rental ),
				'prereservations' => CC_Controller::get_prereservations_for_dress_rental($rental->id, $user->ID),
				'reservation_type' => "Prereservation"
			);
		?>

		<div class="shared-dress m3 row <?php echo ( $GLOBALS['CC_POST_DATA']['tomorrow'] ) ? "available-tomorrow" : ""; ?>">
			<div class="col-sm-6 col-md-5">
			<?php 
				get_template_part('_partials/dress/closet', 'dress-summary' ); 
			?>
			</div>

			<div class="col-sm-5 col-md-5 col-md-offset-1">
				<?php
					get_template_part( '_partials/dress/dress', 'next-day-rental-make');
					get_template_part( '_partials/dress/dress', 'prereservation-history');
				?>
			</div>					
		</div>

		<?php if($c < $c2 - 1 ): ?>
			<div class="col-sm-12">
				<hr />
			</div>	
		<?php endif; ?>	


		<?php
			unset( $GLOBALS['CC_POST_DATA'] );
			unset( $GLOBALS['CC_CLOSET_DATA']['dress']);
		?>

	<?php $c++; endforeach; ?>

	<?php } ?>

</div>
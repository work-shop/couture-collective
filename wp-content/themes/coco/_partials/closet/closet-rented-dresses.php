<?php
$rentals = CC_Controller::get_rentals_by_dress_for_user( get_current_user_id() );


?>

<div id="rented-dresses" class="rented-dresses">	
			
		<div class="row">	
						
			<div class="col-sm-8">
				<p class="h7 uppercase">My Rentals</p>
			</div>
			
		</div>
		
		<div class="row">
		
			<div class="col-sm-12">
				<div class="bordered-dark-bottom m2"></div>
			</div>
		
		</div>
	
	<?php if ( empty($rentals ) ) { ?>
	
	<div class="row">
		<div class="col-sm-5 col-sm-offset-6">
	
			<p class="h8">You haven't rented in any dresses. Dresses can be rented via the <a href="<?php echo bloginfo('url'); ?>/look-book" class="underline">Look Book</a>.</p>
			
		</div>
		
	</div>

	<?php } else { ?>
	<?php foreach( $rentals as $dress_id => $rentals ) : ?>

		<div class="rented-dress row">
			<div class="col-sm-4 col-sm-offset-1">
			<?php 
				$dress = get_post( $dress_id );
				$GLOBALS['CC_CLOSET_DATA']['dress'] = $dress;
				get_template_part('_partials/dress/closet', 'dress-summary' ); 
			?>
			</div>
			<div class="col-sm-6">
				<div class="row">
					
				</div>
				<div class="row">
					<?php
						$rental = get_field('dress_rental_product_instance', $dress );

						if ( empty( $rental ) ) continue;

						$GLOBALS['CC_POST_DATA'] = array(
							'user' => wp_get_current_user(),
							'rental' => get_product( ws_fst( $rental ) ),
						);
						$GLOBALS['CC_POST_DATA']['prereservations'] = $rentals;
						$GLOBALS['CC_POST_DATA']['reservation_type'] = "Rental";

						// change this to prereservation history.
						get_template_part( '_partials/dress/dress', 'prereservation-history');

						unset( $GLOBALS['CC_POST_DATA'] );
						unset( $GLOBALS['CC_CLOSET_DATA']['dress']);
					?>
				</div>
			</div>
		</div>

	<?php endforeach; ?>


	<?php } ?>
</div>
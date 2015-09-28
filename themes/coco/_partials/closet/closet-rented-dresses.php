<?php
$rentals = CC_Controller::get_rentals_by_dress_for_user( get_current_user_id() );


?>

<div id="rented-dresses" class="rented-dresses row row-m0">	
	
	<?php if ( empty($rentals ) ) { ?>
	
	<div class="row">
		<div class="col-sm-5 col-sm-offset-6">
	
			<p class="h8">You haven't rented in any dresses. Dresses can be rented via the <a href="<?php echo bloginfo('url'); ?>/look-book" class="underline">Look Book</a>.</p>
			
		</div>
		
	</div>

	<?php } else { ?>
	<?php
	$c = 0;
	$c2 = count( $rentals );
	foreach( $rentals as $dress_id => $rentals ) : ?>

		<div class="rented-dress m3 row">
			<div class="col-sm-6 col-md-5">
			<?php 

				$dress = get_post( $dress_id );
				$GLOBALS['CC_CLOSET_DATA']['dress'] = $dress;
				get_template_part('_partials/dress/closet', 'dress-summary' ); 
			?>
			</div>
			<div class="col-sm-6 col-md-5 col-md-offset-1">
				<?php
					$rental = get_field(CC_Controller::$field_keys['rental_product'], $dress );

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
		
		<?php if(  $c < $c2 - 1 ): ?>
			<div class="col-sm-12">
				<hr />
			</div>	
		<?php endif; ?>	

	<?php $c++; endforeach; ?>


	<?php } ?>
</div>
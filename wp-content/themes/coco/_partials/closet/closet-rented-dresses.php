<?php
$rentals = $GLOBALS['CC_CLOSET_DATA']['rentals'];

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


	<?php } ?>
</div>
<?php get_header(); 

?>

<div id="my-closet" class="template template-page">	
	
	<section id="closet-introduction" class="closet-introduction block ">	
	
		<div class="container">
		
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1 wc-notices">
					<?php wc_print_notices(); ?>
				</div>	
				
			</div>
		
			<div class="row">		
								
				<div class="col-sm-8">
					<p class="h7 uppercase">My Dresses</p>
				</div>
	
				<div class="col-sm-offset-4 hidden">
					<p class="h7 uppercase">Show: <a href="#all" class="active filter-option">All </a> <a href="#tomorrow filter-option"> Available Tomorrow</a></p>
				</div>
			</div>
			<div class="row">	
				<div class="col-sm-12">
					<div class="bordered-dark-bottom m2"></div>
				</div>
		
			</div>	
	
			<?php 
				$dresses = CC_Controller::dresses_for_customer( get_current_user_id() );
	
				$GLOBALS['CC_CLOSET_DATA'] = array(
					'shares' => ( !empty( $dresses ) && array_key_exists('share', $dresses) ) ? $dresses['share'] : array(),
					'rentals' => ( !empty( $dresses ) && array_key_exists('rental', $dresses) ) ? $dresses['rental'] : array()
				);
	
			?>
	
			<?php get_template_part('_partials/closet/closet', 'shared-dresses'); ?>
				
						
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
	
			<?php get_template_part('_partials/closet/closet', 'rented-dresses'); ?>
	
			<?php unset( $GLOBALS['CC_CLOSET_DATA'] ); ?>
			
		</div>
								
	</section>	
	
</div>	

<?php get_footer(); ?>
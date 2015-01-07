<?php get_header(); 



?>

<div id="my-closet" class="template template-page">	
	
	<section id="look-book-introduction" class="look-book-introduction block">	
		<div class="row">
				
			<div class="col-sm-7 col-sm-offset-1">My Dresses</div>

			<div class="col-sm-1"><a href="#">All</a></div>

			<div class="col-sm-2"><a href="#">Available Tomorrow</a></div>

			<hr class="page-header-rule"/>

		</div>	

		<?php 
			$dresses = get_post_meta( get_current_user_id(), 'cc_closet_values', true ); 

			$GLOBALS['CC_CLOSET_DATA'] = array(
				'shares' => ( !empty( $dresses ) && array_key_exists('share', $dresses) ) ? $dresses['share'] : array(),
				'rentals' => ( !empty( $dresses ) && array_key_exists('rental', $dresses) ) ? $dresses['rental'] : array()
			);

		?>

		<?php get_template_part('_partials/closet/closet', 'shared-dresses'); ?>

		<?php get_template_part('_partials/closet/closet', 'rented-dresses'); ?>

		<?php unset( $GLOBALS['CC_CLOSET_DATA'] ); ?>
								
	</section>	
	
</div>	

<?php get_footer(); ?>
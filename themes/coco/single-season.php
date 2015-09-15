
<?php get_header();?>

<?php wc_print_notices(); ?>

<div id="season-single" class="template template-page ">	

	<section id="look-book-introduction" class="page-introduction block m2">	

		<div class="container">
				
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1">
				
					<h1 class="serif centered m"><?php echo get_the_title()?></h1>

				</div>		

			</div>
			
		</div>

		<hr class="page-header-rule"/>					

	</section>
	
	<section id="look-book-body" class="look-book block">	
	
		<div class="products">
			<div class="container">
				<?php  if ( $uli = is_user_logged_in() ) : ?>
				<div class='row filter hidden'>
					<div class='col-sm-6'></div>
					<div class='col-sm-2'><a href="#">Owned</a></div>
					<div class='col-sm-2'><a href="#">Shares Available</a></div>
					<div class='col-sm-2'><a href="#">Purchase Available</a></div>
				</div>
				<?php endif; ?>
				
				<div class="row">
				<?php

				$row_length = 4;
				$index = 0;

				$season = get_the_ID();

				$args = array(
					'post__in' => CC_Controller::get_dresses_for_season( $season ),
					'post_type' => 'dress',
					'posts_per_page' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				);

				$GLOBALS['LOOP'] = new WP_Query( $args );
				$GLOBALS['USER'] = wp_get_current_user();

				if ( $GLOBALS['LOOP']->have_posts() ) {

					while ( $GLOBALS['LOOP']->have_posts() ) : 

						$sale = new WC_Product( get_field('dress_sale_product_instance', get_the_ID() )[0]->ID );

						if ( $sale->is_in_stock() ) {

							if ( $index % $row_length == 0 ) echo '<div class="row">';

						 	get_template_part( '_partials/dress/dress', 'card' );

						 	if ( $index % $row_length == $row_length - 1 ) echo '</div>';

						 	$index++;

					 	} else {

					 		$GLOBALS['LOOP']->the_post();

					 	}	

					endwhile;

				} else { 
					echo '<h1>There aren\'t any dresses right now!</h1>';
				} 

				?>
				</div>
			</div>
		</div>
											
	</section>	
		
</div>	

<?php get_footer(); ?>

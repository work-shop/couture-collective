
<?php get_header();?>

<div id="look-book" class="template template-page">	
	
	<section id="look-book-introduction" class="look-book-introduction block">	
	
		<div class="products">
			<div class="container">
				<div class="row">
			<?php
				$args = array(
					'post_type' => 'dress',
					'posts_per_page' => 12,
					'post__not_in' => array(45)
				);
				$GLOBALS['LOOP'] = new WP_Query( $args );

				if ( $GLOBALS['LOOP']->have_posts() ) {

					while ( $GLOBALS['LOOP']->have_posts() ) : 
					 	get_template_part( '_partials/dress/dress', 'card' );
					endwhile;

				} else { 
					echo '<h1>There aren\'t any dresses right now!</h1>';
				} ?>
				</div>
			</div>
		</div>
			
								
	</section>	
	
</div>	

<?php get_footer(); ?>

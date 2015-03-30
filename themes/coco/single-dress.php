<?php get_header();?>
	

<div id="dress-single" class="template template-page template-single">	

	<article id="dress-<?php the_ID(); ?>" class="template dress m3">
		<div class="container">	
			<div class="row">

			
				<div class="col-sm-10 col-sm-offset-1 wc-notices">
					<?php wc_print_notices(); ?>
				</div>

				<?php get_template_part('_partials/dress/dress', 'base'); ?>


			</div>
		</div>
	</article>	
	
	<section id="guidepost" class="guidepost hidden">

		<div class="container">
			<div class="row">
			
				<?php 

				global $post;
				
				$prev_post = get_adjacent_post( false, '', true); 
				$prev_id = $prev_post->ID;
				$current_id = $post->ID;
				$next_post = get_adjacent_post( false, '', false); 
				$next_id = $next_post->ID;
		
				$ids = array(0 => $prev_id, 1 => $next_id);
				
				$guidepost_query = new WP_Query( array('post_type' => 'dress',  'post__in' => $ids));
				
				while ( $guidepost_query->have_posts() ) { 
					$guidepost_query->the_post();
					?>
				
				
					<div class="col-sm-6 sign">
						
						<div class="row">
							<div class="col-sm-6 col-md-4">
								<?php 
									if ( has_post_thumbnail() ) {
										the_post_thumbnail('medium');
									} else {
										echo '<img src="' . get_bloginfo( 'template_directory' ) . '/_/img/thumbnail-default.jpg" />';
									}
								?>	
								
							</div>
							
						</div>
				
					</div>
		
				<?php }
				
				wp_reset_postdata();
				
			?>			 
	
		
			</div>
		</div>
	
	</section>

</div>

<?php get_footer(); ?>

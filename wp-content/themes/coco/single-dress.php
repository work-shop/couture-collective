<?php get_header();?>
	

<div id="dress-single" class="template template-page template-single">	

	<article id="dress-<?php the_ID(); ?>" class="template dress">
		<div class="container">	
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1 wc-notices">
					<?php wc_print_notices(); ?>
				</div>

			
				<?php get_template_part('_partials/dress/dress', 'base'); ?>
			
			</div>
		</div>
	</article>	

</div>

<?php get_footer(); ?>

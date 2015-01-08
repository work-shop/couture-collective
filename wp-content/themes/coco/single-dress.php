<?php get_header();?>
	

<div id="dress-single" class="template template-page template-single">	

	<article id="dress-<?php the_ID(); ?>" class="template dress">
		<div class="container">	
			<div class="row">
			
				<?php wc_print_notices(); ?>

			
				<?php get_template_part('_partials/dress/dress', 'base'); ?>
			
			</div>
		</div>
	</article>	

</div>

<?php get_footer(); ?>

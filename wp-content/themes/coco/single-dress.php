<?php get_header();?>
	
<?php wc_print_notices(); ?>

<div id="dress-single" class="template template-page template-single">	

	<article id="dress-<?php the_ID(); ?>" class="template dress">
		<div class="container">	
			<div class="row">
			
				<?php get_template_part('_partials/dress/dress', 'base'); ?>
			
			</div>
		</div>
	</article>	

</div>

<?php get_footer(); ?>

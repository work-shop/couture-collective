<?php get_header();?>
	

<div id="dress-single" class="template template-page template-single">	

	<article id="trunkshow-<?php the_ID(); ?>" class="template trunkshow m3">
		<div class="container">	
			<div class="row">

			
				<div class="col-sm-10 col-sm-offset-1 wc-notices">
					<?php wc_print_notices(); ?>
				</div>

				<div class="col-sm-10">
					<p class="h11 numerals"><?php echo ws_render_date( get_field('trunk_show_date', get_the_ID() ) ); ?></p>
				</div>

				<?php get_template_part('_partials/trunkshow/trunkshow', 'base'); ?>

				<?php get_template_part('_partials/trunkshow/trunkshow', 'upcoming'); ?>


			</div>
		</div>
	</article>	

</div>

<?php get_footer(); ?>

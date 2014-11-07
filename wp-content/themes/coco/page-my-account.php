<?php get_header();?>

<div id="page" class="template page">

	<div class="container">
		<div class="row">
		
			<div class="col-sm-12">
				<h1><?php the_title(); ?></h1>
			</div>
			
		</div>
		
	</div>		
	
	<div class="container">
		<div class="row">
		
			<div class="col-sm-12">
				<?php echo do_shortcode('[woocommerce_my_account]') ?>
			</div>
			
		</div>
		
	</div>		
		
</div>	

<?php get_footer(); ?>

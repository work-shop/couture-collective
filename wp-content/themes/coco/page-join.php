
<?php get_header();?>

<div id="page" class="template page-cart">

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
				 <?php echo do_shortcode('[woocommerce_one_page_checkout template="product-single" product_ids="45"]'); ?>
			</div>
			
		</div>
		
	</div>		
		
</div>	

<?php get_footer(); ?>
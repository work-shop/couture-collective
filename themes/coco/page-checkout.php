<?php get_header();?>

<div id="page" class="template page-cart">

	<section id="checkout-introduction" class="block m">	
		<div class="container">
				
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1">
				
					<?php wc_print_notices(); ?>
					
				</div>

			</div>
			
		</div>
	</section>
	
	<section id="checkout-body" class="block m">
		<div class="container">
			<div class="row">
			
				<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
					<?php echo do_shortcode('[woocommerce_checkout]') ?>
				</div>
				
			</div>
			
		</div>	
	</section>	
		
</div>	

<?php get_footer(); ?>
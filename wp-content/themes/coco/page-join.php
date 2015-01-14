
<?php get_header();?>

<?php do_action('cc_add_membership_items'); ?>

<div id="page" class="template page-cart">

<hr class="page-header-rule"/>						

	<section id="join-introduction" class="join-introduction block">	
			<div class="container">
				
				<div class="row">
				
					<div class="col-sm-6 col-sm-offset-3">
					
						<p class="h3 centered m">
						Become a member for exclusive access to our curated selection of the runway's hottest looks, all available to share with a small select group for 20% of the retail price. 
						</p>
						
					</div>
					
				</div>
				
			</div>
						
	</section>
	
	<hr class="page-header-rule"/>						
	
	<section id="join-body" class="block">
		
		<div class="container">
			<div class="row">
			
				<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
					 <?php echo do_shortcode('[woocommerce_one_page_checkout template="product-single" product_ids="45"]'); ?>
				</div>
				
			</div>
			
		</div>	
	
	</section>	
		
</div>	

<?php get_footer(); ?>
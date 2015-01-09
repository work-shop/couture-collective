
<?php get_header();?>

<div id="cart" class="template template-page">	

	<section id="cart-introduction" class="block m">	
		<div class="container">
				
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1">
				
					<?php wc_print_notices(); ?>
					
				</div>

			</div>
			
		</div>
	</section>
		
	<section id="cart-body" class="block m">	
		<div class="container ">
				
			<div class="row bordered-dark-top m2">
				
			</div>

			<div id="cart-contents">
						
				<?php echo do_shortcode('[woocommerce_cart]') ?>

			</div>
			
		</div>
	</section>
	
</div>				
				

<?php get_footer(); ?>
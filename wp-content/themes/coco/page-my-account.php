<?php get_header();?>

<div id="page" class="template page">

	<section id="account-intro" class="block m3">
		
		<div class="container">
			<div class="row">
			
				<hr class="page-header-rule" />
			
				<div class="col-sm-12">
					<h1 class="centered">My Account</h1>
				</div>
				
			</div>
			
		</div>	
		
	</section>	
		
	<section id="account-information" class="block m3">
		
		<div class="container">
			<div class="row">	
			
				<div class="col-sm-12">
					<h4 class="bordered centered">My Profile</h4>
				</div>
				
				<div class="col-sm-12">
					<?php echo do_shortcode('[woocommerce_my_account]') ?>
				</div>			
				
				<div class="col-sm-3">
					<a class="h7 centered m2 bg-pink-darker display-block contact-link white uppercase" href="<?php wc_customer_edit_account_url(); ?>">Edit Profile</a>	
				</div>
				
			</div>
		</div>
	</section>

</div>	

<?php get_footer(); ?>

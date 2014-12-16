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
					<?php
					//$addr_type = "billing";
					$customer_id = get_current_user_id();
					// do_action('woocommerce_before_my_account'); // hooks my subscriptions right now
					//wc_get_template( 'myaccount/form-edit-account.php', array('user' => wp_get_current_user() ) );
					WC_Shortcode_My_Account::output( array() );
					//WC_Shortcode_My_Account::output( array() );
					// do_action('woocommerce_after_my_account'); // hooks my saved cards right now
					?>
					<?php //echo do_shortcode('[woocommerce_my_account]') ?>
				</div>			
				
				<!-- <div class="col-sm-3">
					<a class="h7 centered m2 bg-pink-darker display-block contact-link white uppercase" href="<?php wc_customer_edit_account_url(); ?>">Edit Profile</a>	
				</div> -->
				
			</div>
		</div>
	</section>

</div>	

<?php get_footer(); ?>

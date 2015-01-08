<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $current_user;
?>

<section id="account-intro" class="block m3">
	
	<div class="container">
		<div class="row">
		
			<hr class="page-header-rule" />
		
			<div class="col-sm-12">
				<h1 class="centered">My Account</h1>
			</div>
			
			<div class="col-sm-6 col-sm-offset-3">
			
				<?php wc_print_notices(); ?>
			
			</div>

			
		</div>
		
	</div>	
	
</section>	

<section id="account-information" class="block m3">
		
		<div class="container">
			
				<div class="row m3">	
				
					<div class="col-sm-12">
						<h4 class="bordered centered m25">My Profile</h4>
					</div>
					
					<div class="col-sm-6">

						<?php wc_get_template( 'myaccount/form-edit-account.php', array('user' => wp_get_current_user() ) ); ?>

					</div>
					
				</div>
					
					<?php //do_action( 'woocommerce_before_my_account' ); ?>

					<?php // wc_get_template( 'myaccount/my-downloads.php' ); ?>

					<?php // wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>

				<div class="row">	
				
					<div class="col-sm-12">
						<h4 class="bordered centered m">My Addresses</h4>
					</div>	
					
				</div>
				
				<div class="row">			
				
					<?php wc_get_template( 'myaccount/my-address.php' ); ?>
					
				</div>

					<?php do_action( 'woocommerce_after_my_account' ); ?>

				</div>			
		
			</div>
		</div>
</section>

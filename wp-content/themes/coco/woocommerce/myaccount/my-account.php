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
?>

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

					<div class="col-sm-12">
						<h4 class="bordered centered">My Profile</h4>
					</div>

					<p class="myaccount_user">
						<?php

						wc_print_notices();

						printf(
							__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'woocommerce' ) . ' ',
							$current_user->display_name,
							wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) )
						);

						// printf( __( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">edit your password and account details</a>.', 'woocommerce' ),
						// 	wc_customer_edit_account_url()
						// );
						?>
					</p>

					<?php wc_get_template( 'myaccount/form-edit-account.php', array('user' => wp_get_current_user() ) ); ?>

					<?php // do_action( 'woocommerce_before_my_account' ); ?>

					<?php // wc_get_template( 'myaccount/my-downloads.php' ); ?>

					<?php // wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>

					<?php wc_get_template( 'myaccount/my-address.php' ); ?>

					<?php // do_action( 'woocommerce_after_my_account' ); ?>

				</div>			
		
			</div>
		</div>
</section>

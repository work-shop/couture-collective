<?php get_header();?>

<div id="page" class="template page">

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

<?php get_footer(); ?>

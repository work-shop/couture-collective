<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="row">

	<div class="col-sm-4 col-sm-offset-6">
		
		<?php if ( is_user_logged_in() ) { ?>

		<p class="cart-empty h8"><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?> Return to the <a href="<?php echo site_url('/look-book'); ?>" class="underline">Look Book</a> to shop.</p>
	
		<?php } else { ?>

		<p class="cart-empty h8"> You don't have an account yet. Head to <a href="<?php echo site_url('/join'); ?>" class="underline">here</a> to join.</p>

		<?php } ?>

	</div>

</div>

<?php do_action( 'woocommerce_cart_is_empty' ); ?>

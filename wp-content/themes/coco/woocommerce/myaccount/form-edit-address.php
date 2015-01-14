<?php
/**
 * Edit address form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $current_user;

$page_title = ( $load_address === 'billing' ) ? __( 'Billing Address', 'woocommerce' ) : __( 'Shipping Address', 'woocommerce' );

get_currentuserinfo();
?>

<?php wc_print_notices(); ?>

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-account.php' ); ?>

<?php else : ?>

<section id="account-information" class="block m3">
		
		<div class="container">
			<div class="row">	
			
				<hr class="page-header-rule" />	
			
				
					<div class="col-sm-12">
						<h4 class="bordered centered m25"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h4>
					</div>

					<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

						<form method="post">
	
							<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
	
							<?php foreach ( $address as $key => $field ) : ?>
	
								<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>
	
							<?php endforeach; ?>
							
							<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
	
							<p>
								<input type="submit" class="button" name="save_address" value="<?php _e( 'Save Address', 'woocommerce' ); ?>" />
								<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
								<input type="hidden" name="action" value="edit_address" />
							</p>
	
						</form>
						
					</div>
		</div>
	</div>
</section>

<?php endif; ?>

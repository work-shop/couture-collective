<h2 id="saved-cards" style="margin-top:40px;"><?php _e( 'Saved cards', 'woocommerce-gateway-stripe' ); ?></h2>
<table class="shop_table">
	<thead>
		<tr>
			<th><?php _e( 'Card', 'woocommerce-gateway-stripe' ); ?></th>
			<th><?php _e( 'Expires', 'woocommerce-gateway-stripe' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $cards as $card ) : ?>
		<tr>
            <td><?php printf( __( '%s card ending in %s', 'woocommerce-gateway-stripe' ), ( isset( $card->type ) ? $card->type : $card->brand ), $card->last4 ); ?></td>
            <td><?php printf( __( 'Expires %s/%s', 'woocommerce-gateway-stripe' ), $card->exp_month, $card->exp_year ); ?></td>
			<td>
                <form action="" method="POST">
                    <?php wp_nonce_field ( 'stripe_del_card' ); ?>
                    <input type="hidden" name="stripe_delete_card" value="<?php echo esc_attr( $card->id ); ?>">
                    <input type="submit" class="button" value="<?php _e( 'Delete card', 'woocommerce-gateway-stripe' ); ?>">
                </form>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
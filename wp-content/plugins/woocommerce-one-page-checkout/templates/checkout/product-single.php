<?php
/**
 * Template to display a single product as per standard WooCommerce Templates
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

foreach( $products as $single_product ) : 
	$product = $single_product;
	$post = $single_product->post;
?>
<div class="opc-single-product">

	<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="summary entry-summary product-item <?php if ( $product->in_cart ) { echo 'selected'; } ?>">
			<?php
				woocommerce_template_single_title();
				woocommerce_template_single_price(); 
				woocommerce_template_single_excerpt();
				?>
		<div class="product-quantity">
			<?php wc_get_template( 'checkout/add-to-cart/add-to-cart.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path ); ?>
		</div>
			<?php
				woocommerce_template_single_meta();
				woocommerce_template_single_sharing();
			?>
		</div><!-- .summary -->

		<meta itemprop="url" content="<?php the_permalink(); ?>" />

	</div><!-- #product-<?php the_ID(); ?> -->

</div>
<?php endforeach; ?>

<?php wp_reset_postdata(); ?>
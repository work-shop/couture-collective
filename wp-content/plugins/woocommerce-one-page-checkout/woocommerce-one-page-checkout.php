<?php
/*
Plugin Name: WooCommerce One Page Checkout
Description: Super fast sales with WooCommerce. Add to cart, checkout & pay all on the one page!
Author: Prospress Inc.
Author URI: http://prospress.com/
Text Domain: wcopc
Domain Path: languages
Plugin URI: http://www.woothemes.com/products/woocommerce-one-page-checkout/
Version: 1.0.2

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * @package One Page Checkout
 * @since 1.0
 * @author Prospress Inc <wares@prospress.com>
 * @copyright Copyright (c) 2014 Prospress Inc.
 * @link http://prospress.com/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) || ! function_exists( 'is_woocommerce_active' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'c9ba8f8352cd71b5508af5161268619a', '527886' );

/**
 * Check if WooCommerce is active, and if it isn't, disable the plugin.
 *
 * @since 1.0
 */
if ( ! is_woocommerce_active() || version_compare( get_option( 'woocommerce_db_version' ), '2.1', '<' ) ) {
	add_action( 'admin_notices', 'PP_One_Page_Checkout::woocommerce_inactive_notice' );
	return;
}

if ( ! class_exists( 'PP_One_Page_Checkout' ) ) :

/**
 * Load the text domain to make the plugin's strings available for localisation.
 *
 * @since 1.0.1
 */
function wcopc_load_plugin_textdomain() {

	$locale = apply_filters( 'plugin_locale', get_locale(), 'wcopc' );

	// Allow upgrade safe, site specific language files in /wp-content/languages/woocommerce/
	load_textdomain( 'wcopc', WP_LANG_DIR . '/woocommerce/wcopc-' . $locale . '.mo' );

	// Then check for a language file in /wp-content/plugins/woocommerce-one-page-checkout/languages/ (this will be overriden by any file already loaded)
	load_plugin_textdomain( 'wcopc', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wcopc_load_plugin_textdomain' );

/**
 * So that themes and other plugins can customise the text domain, the PP_One_Page_Checkout
 * should not be initialized until after the plugins_loaded and after_setup_theme hooks.
 * However, it also needs to run early on the init hook.
 *
 * @author Brent Shepherd <brent@prospress.com>
 * @since 1.0
 */
function initialize_one_page_checkout(){
	PP_One_Page_Checkout::init();
}
add_action( 'init', 'initialize_one_page_checkout', -1 );


class PP_One_Page_Checkout {

	static $active_plugins;

	static $add_scripts = false;

	static $raw_shortcode_atts;

	static $shortcode_page_id = 0;

	static $products_to_display =  null;

	static $categories_to_display = null;

	static $template = 'checkout/product-table.php';

	static $templates;

	static $shop_variations;

	static $plugin_url;

	static $plugin_path;

	static $template_path;

	static $evaluated_shortcode;

	public static function init() {

		self::$active_plugins = get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		require_once( 'classes/class-wcopc-admin-editor.php' );

		require_once( 'classes/abstract-class-wcopc-template.php' );

		require_once( 'classes/class-wcopc-easy-pricing-tables-template.php' );

		self::$plugin_url     = untrailingslashit( plugins_url( '/', __FILE__ ) );
		self::$plugin_path    = untrailingslashit( plugin_dir_path( __FILE__ ) );
		self::$template_path  = self::$plugin_path . '/templates/';

		self::$templates   = apply_filters( 'wcopc_templates', array(
			'product-table' => array(
				'label'               => __( 'Product Table', 'wcopc' ),
				'description'         => __( 'Display a row for each product containing its thumbnail, title and price. Best for a few simple products where the thumbnails are helpful, e.g. a set of halloween masks.', 'wcopc' ),
				'supports_containers' => false,
			),
			'product-list' => array(
				'label'               => __( 'Product List', 'wcopc' ),
				'description'         => __( 'Display a list of products with a radio button for selection. Useful when the customer does not need a description or photograph to choose, e.g. versions of an eBook.', 'wcopc' ),
				'supports_containers' => false,
			),
			'product-single'  => array(
				'label'               => __( 'Single Product', 'wcopc' ),
				'description'         => __( "Display the single product template for each product. Useful when the description, images, gallery and other meta data will help the customer choose, e.g. evening gowns.", 'wcopc' ),
				'supports_containers' => false,
			),
			'pricing-table'  => array(
				'label'               => __( 'Pricing Table', 'wcopc' ),
				'description'         => __( "Display a simple pricing table with each product's attributes, weight and dimensions. Useful to allow customers to compare different, but related products, e.g. membership subscriptions.", 'wcopc' ),
				'supports_containers' => false,
			),
		) );

		add_action( 'woocommerce_checkout_before_customer_details', array( __CLASS__, 'add_product_selection_fields' ), 11 );

		// Update products from the checkout page
		add_action( 'wp_ajax_pp_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_pp_add_to_cart', array( __CLASS__, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_pp_remove_from_cart', array( __CLASS__, 'ajax_remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_pp_remove_from_cart', array( __CLASS__, 'ajax_remove_from_cart' ) );
		add_action( 'wp_ajax_pp_update_add_in_cart', array( __CLASS__, 'ajax_update_add_cart' ) );
		add_action( 'wp_ajax_nopriv_pp_update_add_in_cart', array( __CLASS__, 'ajax_update_add_cart' ) );

		// Add a shortcode to circumvent WooCommerce non-empty cart requirement for displaying the checkout
		add_shortcode( apply_filters( 'woocommerce_one_page_checkout_shortcode_tag', 'woocommerce_one_page_checkout' ), array( __CLASS__, 'get_one_page_checkout' ) );

		// Checks if a queried page contains the one page checkout shortcode, needs to happen after the "template_redirect"
		add_action( 'the_posts', array( __CLASS__, 'check_for_shortcode' ) );

		// Add JavaScript
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// Add WooCommerce body class
		add_filter( 'body_class', array( __CLASS__, 'opc_woocommerce_body_class' ) );

		// Because there is no reliable way to filter is_checkout(), we need to do a page ID hack
		add_filter( 'woocommerce_get_checkout_page_id', array( __CLASS__, 'is_checkout_hack' ) );

		add_action( 'wp_ajax_woocommerce_update_order_review', array( __CLASS__, 'short_circuit_ajax_update_order_review' ), 9 );
		add_action( 'wp_ajax_nopriv_woocommerce_update_order_review', array( __CLASS__, 'short_circuit_ajax_update_order_review' ), 9 );

		add_filter( 'woocommerce_add_error', array( __CLASS__, 'improve_empty_cart_error' ) );

		// Make sure the wc_checkout_params.is_checkout JS value is true on custom OPC pages
		add_filter( 'wc_checkout_params', array( __CLASS__, 'checkout_params' ) );

		add_action('admin_head', array( __CLASS__, 'set_tinymce_button_icon' ) );

		do_action( 'wcopc_loaded' );
	}

	/**
	 * Display product selection fields on checkout page.
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function add_product_selection_fields() {

		if ( 0 == self::$shortcode_page_id ) {
			return;
		}

		do_action( 'wcopc_product_selection_fields_before', self::$template, self::$raw_shortcode_atts );

		if ( false === apply_filters( 'wcopc_show_product_selection_fields', true, self::$template ) ) {
			return;
		}

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'visible' ),
					'compare' => 'IN'
				)
			)
		);

		// Alter query if product ids or categories specified in shortcode
		if ( self::$products_to_display ) {

			$args['post__in'] = explode( ',', self::$products_to_display );
			$args['orderby']  = 'post__in';

		} elseif ( self::$categories_to_display ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'  => 'product_cat',
					'terms'     => explode( ',', self::$categories_to_display )
				)
			);
		}

		$products         = array();
		$product_posts    = get_posts( $args );

		foreach( $product_posts as $product_post ) {

			$product = get_product( $product_post->ID );

			if ( ! is_object( $product ) ) {
				continue;
			}

			if ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) {
				foreach( $product->get_children( true ) as $product_id ) {
					$product  = get_product( $product_id );
					$products = self::build_products_array( $product, $products );
				}
			} else {
				$products = self::build_products_array( $product, $products );
			}
		}
		$products = apply_filters( 'wcopc_products_for_selection_fields', $products, self::$template, self::$raw_shortcode_atts );
		?>
<div id="opc-product-selection" class="hide-if-js wcopc">
	<?php if ( ! empty( $products ) ) : ?>
		<?php wc_get_template( self::$template, array( 'products' => $products ), '', self::$template_path ); ?>
	<?php else : ?>
		<p><?php _e( 'No products found.', 'wcopc' ); ?></p>
	<?php endif; ?>
</div><?php

		$cart_needs_shipping = false;

		// Make sure shipping address fields are displayed if any of the available products require shipping
		if ( ! WC()->cart->needs_shipping_address() && ! empty( $products ) ) {
			foreach ( $products as $product ) {
				if ( $product->needs_shipping() ) {
					add_filter( 'woocommerce_cart_needs_shipping_address', '__return_true' );
					break;
				}
			}
		}

		do_action( 'wcopc_product_selection_fields_after', self::$template, self::$raw_shortcode_atts );
	}

	private static function build_products_array( $product, $products = array() ) {

		if ( ! is_object( $product ) || ! $product->exists() ) {
			return $products;
		}

		$product->add_to_cart_id = ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id;
		$products_in_cart        = self::get_products_in_cart();

		if ( array_key_exists( $product->add_to_cart_id, $products_in_cart ) ) {
			$product->in_cart   = true;
			$product->cart_item = $products_in_cart[ $product->add_to_cart_id ];
		} else {
			$product->in_cart   = false;
			$product->cart_item = array();
		}

		$products[ $product->add_to_cart_id ] = $product;

		return $products;
	}

	/**
	 * Returns the attribute term name given the slug or sanitized value
	 *
	 * @since 1.0
	 * @author Matt Allan <matt.d.a@live.com>
	 */
	public static function get_variation_slug_title( $variation_name, $attributes ) {

		foreach ( $attributes as $attribute_name => $attribute_options ) {
			if ( taxonomy_exists( sanitize_title( $attribute_name ) ) ) {
				$terms = get_terms( sanitize_title( $attribute_name ), array( 'slug' => $variation_name ) );
				if ( count( $terms ) == 1 ) {
					return apply_filters( 'woocommerce_variation_option_name', $terms[0]->name );
				}
			} else {
				foreach ( $attribute_options as $option ) {
					if ( sanitize_title( $option ) == $variation_name ) {
						return apply_filters( 'woocommerce_variation_option_name', $option );
					}
				}
			}
		}

		return apply_filters( 'woocommerce_variation_option_name', $variation_name );
	}

	/**
	 * Get a products variation data formatted in the same form that is used in
	 * the WooCommerce cart
	 *
	 * Based on the WC_Cart::get_item_data() method
	 *
	 * @since 1.0
	 * @author Brent Shepherd
	 */
	public static function get_formatted_variation_data( $variation, $variation_attributes, $flat = false ) {
		$item_data = array();

		// Variation data
		if ( ! empty( $variation->variation_id ) && is_array( $variation_attributes ) ) {

			$variation_list = array();

			foreach ( $variation_attributes as $name => $value ) {

				if ( ! $value )
					continue;

				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $taxonomy ) ) {
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );

				// If this is a custom option slug, get the options name
				} else {
					$value              = apply_filters( 'woocommerce_variation_option_name', $value );
					$product_attributes = $variation->get_attributes();
					$label              = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', urldecode( $name ) ) ]['name'] );
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value
				);
			}
		}

		// Output flat or in list format
		if ( sizeof( $item_data ) > 0 ) {

			ob_start();

			if ( $flat ) {
				foreach ( $item_data as $data ) {
					echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
				}

			} else {

				woocommerce_get_template( 'cart/cart-item-data.php', array( 'item_data' => $item_data ) );

			}

			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Returns the ID of a product variation which has a given attribute.
	 *
	 * Only reliable works on products with 1 attribute for now.
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function get_variation_id_from_attribute( $variations, $attribute_to_find ) {

		foreach ( $variations as $variation ) {
			foreach ( $variation['attributes'] as $attribute ) {
				if ( $attribute == $attribute_to_find ) {
					return $variation['variation_id'];
				}
			}
		}

		return false;
	}

	/**
	 * A custom ajax remove from cart function.
	 *
	 * @since 1.0
	 * @author Matt Allan
	 */
	public static function ajax_remove_from_cart() {

		do_action( 'wcopc_ajax_remove_from_cart_response_before' );

		check_ajax_referer( __FILE__, 'nonce' );
		$response_data = array();
		$item_removed = false;

		// Get cart item id from cart
		$cart = WC()->cart->get_cart();
		foreach ( $cart as $cart_item_id => $value ) {
			if ( $value['product_id'] == $_POST['add_to_cart'] || $value['variation_id'] == $_POST['add_to_cart'] ) {
				WC()->cart->set_quantity( $cart_item_id, 0 );
				wc_add_notice( sprintf( __( '&quot;%s&quot; was successfully removed from your order.', 'wcopc' ), get_the_title( $value['product_id'] ) ), 'success' );
				$response_data['result'] = 'success';
				$item_removed = true;
				break;
			}
		}

		if ( ! $item_removed ) {
			wc_add_notice( sprintf( __( '&quot;%s&quot; could not be removed from your order.', 'wcopc' ), get_the_title( $value['product_id'] ) ), 'error' );
			$response_data['result'] = 'failure';
		}

		$response_data['products_in_cart'] = self::get_products_in_cart();

		ob_start();
		wc_print_notices();
		$response_data['messages'] = ob_get_clean();

		$response_data = apply_filters( 'wcopc_ajax_remove_from_cart_response_data', $response_data );

		WC()->cart->maybe_set_cart_cookies();

		echo json_encode( $response_data );

		do_action( 'wcopc_ajax_remove_from_cart_response_after' );

		die();
	}

	/**
	 * A custom ajax add to cart function.
	 *
	 * The @see woocommerce_ajax_add_to_cart() function does not work for variable
	 * products, and the @see woocommerce_add_to_cart_action() function is too agressive
	 * in it's attribute_x field validation, so we need to use our own function.
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function ajax_add_to_cart() {

		$response_data = array();

		check_ajax_referer( __FILE__, 'nonce' );

		$add_to_cart = (int)apply_filters( 'woocommerce_add_to_cart_product_id', $_POST['add_to_cart'] );
		$quantity   = ( isset( $_REQUEST['quantity'] ) ) ? $_REQUEST['quantity'] : 1;

		$product = get_product( $add_to_cart );

		// Clear cart each time a new radio button is pressed
		if ( isset( $_REQUEST['empty_cart'] ) ) {
			WC()->cart->empty_cart();
		}

		$was_added_to_cart = false;

		// Variable product handling
		if ( $product->is_type( 'variation' ) ) {

			$variable_product = get_product( $product->id );

			$variation_data   = array();

			$attributes           = $variable_product->get_attributes();
			$variation_attributes = $product->get_variation_attributes();

			// Verify all attributes for the variable product were set
			foreach ( $attributes as $attribute ) {

				if ( ! $attribute['is_variation'] ) {
					continue;
				}

				$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

				if ( isset( $variation_attributes[$taxonomy] ) ) {
					$variation_data[ esc_attr( $attribute['name'] ) ] = esc_attr( stripslashes( $variation_attributes[ $taxonomy ] ) );
				}

			}

			// Add to cart validation
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product->id, $quantity );

			if ( $passed_validation ) {

				$variation_details = '';

				foreach ( $variation_attributes as $variation_slug ) {
					if ( '' == $variation_details ) {
						$variation_details .= PP_One_Page_Checkout::get_variation_slug_title( $variation_slug, $attributes );
					} else {
						$variation_details .= ' - ' . PP_One_Page_Checkout::get_variation_slug_title( $variation_slug, $attributes );
					}
				}

				$variation_details = ' (' . $variation_details. ')';

				if ( WC()->cart->add_to_cart( $product->id, $quantity, $product->variation_id, $variation_data ) ) {
					wc_add_notice( sprintf( __( '%s&quot; was successfully added to your order. Complete your order below.', 'wcopc' ), $quantity . ' x &quot;' . get_the_title( $product->id ) . $variation_details ), 'success' );
					$was_added_to_cart = true;
				}
			}

		// Simple Products
		} else {

			// Add to cart validation
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product->id, $quantity );

			if ( $passed_validation ) {
				// Add the product to the cart
				if ( WC()->cart->add_to_cart( $product->id, $quantity ) ) {
					wc_add_notice( sprintf( __( '%s&quot; was successfully added to your order. Complete your order below.', 'wcopc' ), $quantity . ' x &quot;' . get_the_title( $product->id ) ) , 'success' );
					$was_added_to_cart = true;
				}
			}
		}

		do_action( 'wcopc_ajax_add_to_cart_response_before' );

		WC()->cart->maybe_set_cart_cookies();

		ob_start();

		wc_print_notices();

		$response_data['messages'] = ob_get_clean();

		$response_data['products_in_cart'] = self::get_products_in_cart();

		if ( $passed_validation && $was_added_to_cart ) {
			$response_data += apply_filters( 'add_to_cart_fragments', array() );
			$response_data['result'] = 'success';
			do_action( 'woocommerce_ajax_added_to_cart', $product->id );
		} else {
			$response_data['result'] = 'failure';
		}

		$response_data = apply_filters( 'wcopc_ajax_add_to_cart_response_data', $response_data );

		echo json_encode( $response_data );

		do_action( 'wcopc_ajax_add_to_cart_response_after' );

		die();
	}

	/**
	 * Checks if the product already exists in the cart. If it does, set the quantity to 0 (remove it) then call
	 * ajax_add_to_cart() function to add it back into the cart with the correct quantity amount.
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function ajax_update_add_cart() {

		check_ajax_referer( __FILE__, 'nonce' );
		$cart_contents = WC()->cart->get_cart();

		foreach ( $cart_contents as $cart_item_id => $value ) {
			if ( $value['product_id'] == $_POST['add_to_cart'] || $value['variation_id'] == $_POST['add_to_cart'] ) {
				//remove the item if it exists and just add it again with the new quantity
				WC()->cart->set_quantity( $cart_item_id, 0 );
				break;
			}
		}

		self::ajax_add_to_cart();
	}

	/**
	 * Registers our JavaScript for one page checkout with WordPress.
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function enqueue_scripts() {

		if ( self::$add_scripts ) {

			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			wp_enqueue_script( 'woocommerce-one-page-checkout', self::get_url( '/js/one-page-checkout.js' ), array( 'jquery' ), '1.0', true );

			$params = array(
				'wcopc_nonce' => wp_create_nonce( __FILE__ ),
			);

			wp_localize_script( 'woocommerce-one-page-checkout', 'wcopc', $params );

			wp_enqueue_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			wp_enqueue_script( 'prettyPhoto-init', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );

			wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );

			if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) {
				wp_enqueue_script( 'wc-chosen', $assets_path . 'js/frontend/chosen-frontend' . $suffix . '.js', array( 'chosen' ), WC_VERSION, true );
				wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'css/chosen.css' );
			}

			wp_enqueue_script( 'wc-checkout', $assets_path . 'js/frontend/checkout' . $suffix . '.js', array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n' ), WC_VERSION, true );

			wp_enqueue_style( 'woocommerce-one-page-checkout', self::get_url( '/css/one-page-checkout.css' ) );

		}
	}

	/**
	 * Because there is no reliable way to overload is_checkout(), we need to operate on a filter
	 * further up the line, and that is the 'woocommerce_get_checkout_page_id' filter.
	 *
	 * This function checks if we found a page containing the one page checkout shortcode earlier,
	 * and if we did, we let that act as the checkout page.
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function is_checkout_hack( $page_id ) {
		global $wp;
		if ( 0 != self::$shortcode_page_id ) {
			$backtrace = debug_backtrace( false ); // Warned you it was a hack
			if ( ! in_array( $backtrace[4]['function'], array( 'wc_template_redirect', 'get_checkout_url' ) ) ) {
				$page_id = self::$shortcode_page_id;
			}
		}
		return $page_id;
	}

	/**
	 * Make sure the wc_checkout_params.is_checkout JS value is true on custom OPC pages
	 *
	 * @access public
	 * @param array $params
	 * @return array
	 */
	public static function checkout_params( $params ) {
		global $post;

		if ( $post->ID == self::$shortcode_page_id ) {
			$params['is_checkout'] = true;
		}

		return $params;
	}

	/**
	 * Checks if any post about to be displayed contains the one page checkout shortcode.
	 *
	 * We need to set @see self::$add_scripts here rather than in the shortcode so we can conditionally
	 * add the locale to the WooCommerce core script done in @see self::localize_script() hooked to
	 * 'woocommerce_params' which is run on 'wp_enqueue_script' (i.e. before the shortcode is evaluated).
	 *
	 * @since 1.0
	 * @author Brent Shepherd <brent@prospress.com>
	 */
	public static function check_for_shortcode( $posts ) {

		if ( empty( $posts ) )
			return $posts;

		foreach ( $posts as $post ) {

			if ( false !== stripos( $post->post_content, '[woocommerce_one_page_checkout' ) ) {
				self::$add_scripts = true;
				self::$shortcode_page_id = $post->ID;
				break;
			}
		}

		return $posts;
	}

	/**
	 * Helper function to get the URL of a given file.
	 *
	 * As this plugin may be used as both a stand-alone plugin and as a submodule of
	 * a theme, the standard WP API functions, like plugins_url() can not be used.
	 *
	 * @since 1.0
	 * @return string URL to this file
	 */
	public static function get_url( $file ) {

		// Get the path of this file after the WP content directory
		$post_content_path = substr( dirname( __FILE__ ), strpos( __FILE__, basename( WP_CONTENT_DIR ) ) + strlen( basename( WP_CONTENT_DIR ) ) );

		// Return a content URL for this path & the specified file
		return content_url( $post_content_path . $file );
	}

	/**
	 * Helper function to get the URL of a given file.
	 *
	 * @since 1.0
	 */
	public static function get_one_page_checkout( $atts ) {

		// don't evaluate shortcode more than once on the same page
		if ( true === self::$evaluated_shortcode ) {
			return '';
		}

		self::$evaluated_shortcode = true;

		return WC_Shortcodes::shortcode_wrapper( __CLASS__ . '::one_page_checkout_shortcode', $atts );
	}

	/**
	 * Similar to the @see woocommerce_checkout() function except this function does not require
	 * any items to already be in the cart before displaying the checkout.
	 *
	 * @since 1.0
	 */
	public static function one_page_checkout_shortcode( $atts ){

		self::$raw_shortcode_atts = $atts;

		if ( isset( $atts['product_ids'] ) ) {
			self::$products_to_display = $atts['product_ids'];
		} else if ( isset( $atts['category_ids'] ) ) {
			self::$categories_to_display = $atts['category_ids'];
		}

		if ( isset( $atts['template'] ) ) {

			// Template param can accept either a full file name and path or just the file name without path/extension
			if ( file_exists( wc_locate_template( $atts['template'], '', self::$template_path ) ) ) {

				self::$template = $atts['template'];

			} elseif ( file_exists( wc_locate_template( 'checkout/' . $atts['template'] . '.php', '', self::$template_path ) ) ) {

				// But if the template doens't exist, check
				self::$template = 'checkout/' . $atts['template'] . '.php';

			}

			// Allow plugins to override the template
			self::$template = apply_filters( 'wcopc_template', self::$template, $atts );
		}

		do_action( 'wcopc_before_display_checkout' );

		// Show non-cart errors
		wc_print_notices();

		WC()->cart->calculate_totals();

		// Get checkout object for WC 2.0+
		$checkout = WC()->checkout();

		wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout )  );

	}

	/**
	 * Runs just before @see woocommerce_ajax_update_order_review() and terminates the current request if
	 * the cart is empty to prevent WooCommerce printing an error that doesn't not apply on one page checkout purchases.
	 *
	 * @since 1.0
	 */
	public static function short_circuit_ajax_update_order_review() {

		if ( sizeof( WC()->cart->get_cart() ) == 0 ) {
			do_action( 'woocommerce_checkout_order_review' ); // Display review order table
			die();
		}
	}

	/**
	 * Runs just before @see woocommerce_ajax_update_order_review() and terminates the current request if
	 * the cart is empty to prevent WooCommerce printing an error that doesn't not apply on one page checkout purchases.
	 *
	 * @since 1.0
	 */
	public static function improve_empty_cart_error( $error ) {

		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) && $error == sprintf( __( 'Sorry, your session has expired. <a href="%s">Return to homepage &rarr;</a>', 'woocommerce' ), home_url() ) ) {
			$error = __( 'You must select a product.', 'wcopc' );
		}

		return $error;
	}

	/**
	 * Returns the product or variation ID of all products in the cart.
	 *
	 * @return array Associated array of with product or variation IDs as the keys and quantity as the values.
	 * @since 1.0
	 */
	private static function get_products_in_cart() {

		$products_in_cart = array();

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			$products_in_cart[ $product_id ] = $cart_item;
		}

		return $products_in_cart;
	}

	/*
	 * Plugin House Keeping
	 */

	/**
	 * Called when WooCommerce is inactive to display an inactive notice.
	 *
	 * @since 1.0
	 */
	public static function woocommerce_inactive_notice() {
		if ( current_user_can( 'activate_plugins' ) ) :
			if ( ! is_woocommerce_active() ) : ?>
<div id="message" class="error">
	<p><?php printf( __( '%sWooCommerce One Page Checkout is inactive.%s The %sWooCommerce plugin%s must be active for WooCommerce One Page Checkout to work. Please %sinstall & activate WooCommerce%s', 'wcopc' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
</div>
		<?php elseif ( version_compare( get_option( 'woocommerce_db_version' ), '2.1', '<' ) ) : ?>
<div id="message" class="error">
	<p><?php printf( __( '%sWooCommerce One Page Checkout is inactive.%s This plugin requires WooCommerce 2.1 or newer. Please %supdate WooCommerce to version 2.1 or newer%s', 'wcopc' ), '<strong>', '</strong>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
</div>
		<?php endif; ?>
	<?php endif;
	}

	/**
	 * Adds admin styles for setting the tinymce button icon
	 */
	public static function set_tinymce_button_icon() {
		?>
<style>
i.mce-i-wcopc {
	font: 400 20px/1 dashicons;
	padding: 0;
	vertical-align: top;
	speak: none;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
	margin-left: -2px;
	padding-right: 2px
}
</style>
<?php
	}

	/**
	 * Add 'wooommerce' body class. Helps with consistency of WooCommerce styles
	 */
	public static function opc_woocommerce_body_class($classes) {

		global $post;

		if ( empty( $post ) ) {
			return $classes;
		}

		if ( $post->ID == self::$shortcode_page_id ) {
			$classes[] = 'woocommerce-page';
		}

		return $classes;

	}

}
endif;

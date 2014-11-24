<?php
/**
 * WCOPC_Admin_Editor class.
 *
 * @since 2.0
 */
class WCOPC_Admin_Editor {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_head', array( $this, 'add_shortcode_button' ), 20 );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ), 20 );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 20, 1 );

		add_action( 'wp_ajax_one_page_checkout_shortcode_iframe', array( $this, 'one_page_checkout_shortcode_iframe' ), 9 );
	}

	/**
	 * Add a button for the OPC shortcode to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ), 20 );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ), 20 );
		}
	}

	/**
	 * woocommerce_add_tinymce_lang function.
	 *
	 * @param array $arr
	 * @return array
	 */
	public function add_tinymce_lang( $arr ) {
		$arr['wcopc_shortcode_button'] = PP_One_Page_Checkout::$plugin_path . '/js/admin/editor_plugin_lang.php';
		return $arr;
	}

	/**
	 * Register the shortcode button.
	 *
	 * @param array $buttons
	 * @return array
	 */
	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, '|', 'wcopc_shortcode_button' );
		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin( $plugin_array ) {
		$wp_version = get_bloginfo( 'version' );
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$plugin_array['wcopc_shortcode_button'] = PP_One_Page_Checkout::$plugin_url . '/js/admin/editor_plugin.js';

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}


	/**
	 * Display the contents of the iframe used when the One Page Checkout
	 * TinyMCE button is clicked.
	 *
	 * @param int $ver
	 * @return int
	 */
	public static function one_page_checkout_shortcode_iframe() {
		global $wp_scripts;

		set_current_screen( 'wcopc' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), WC_VERSION );

		wp_enqueue_script( 'chosen', WC()->plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), WC_VERSION );
		wp_enqueue_script( 'ajax-chosen', WC()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array( 'jquery', 'chosen' ), WC_VERSION );
		wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		wp_enqueue_script( 'wcopc_iframeresizer_contentwindow', PP_One_Page_Checkout::$plugin_url . '/js/admin/iframe_resizer.content_window.min.js' );
		wp_enqueue_script( 'wcopc_tinymce_dialog', PP_One_Page_Checkout::$plugin_url . '/js/admin/one-page-checkout-iframe.js', array( 'ajax-chosen', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'wcopc_iframeresizer_contentwindow', 'jquery-tiptip' ), WC_VERSION );

		$params = array(
			'search_products_nonce' => wp_create_nonce( 'search-products' ),
		);

		wp_localize_script( 'wcopc_tinymce_dialog', 'wcopc', $params );

		iframe_header(); ?>
<style>
@media screen and (max-width: 782px) {
	/* Fix engorged radio buttons */
	#wcopc_settings input[type="radio"], input[type="checkbox"] {
		width: 16px;
		height: 16px;
	}
	#wcopc_settings input[type="radio"]:checked:before {
		width: 6px;
		height: 6px;
		margin: 4px;
	}
}
/* Enlarge Woo's tiny tooltips */
#tiptip_content {
	min-width: 260px;
}
</style>
<div class="wrap" style="margin: 1em;">
<form id="wcopc_settings" style="float: left; width: 100%;">
	<?php do_action( 'wcopc_shortcode_iframe_before' ); ?>
	<fieldset id="wcopc_product_ids_fields" style="margin: 1em 0;">
		<label for="wcopc_product_ids" style="width: 70px; display: inline-block;"><strong><?php _e( 'Products:', 'wcopc' ); ?></strong></label>
		<select id="wcopc_product_ids" name="wcopc_product_ids[]" class="ajax_chosen_select_products" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wcopc' ); ?>" style="width: 75%;"></select>
	</fieldset>
	<fieldset id="wcopc_template_fields" style="margin: 1em 0;">
		<div style="font-weight: bold;"><?php _e( 'Template:', 'wcopc' ); ?></div>
		<?php $first = true; ?>
		<?php foreach( PP_One_Page_Checkout::$templates as $id => $template_details ) : ?>
		<label for="<?php echo esc_html( $id ); ?>" style="width: 75%; display: inline-block;">
			<input id="<?php echo esc_html( $id ); ?>" name="wcopc_template" type="radio" value="<?php echo $id; ?>" style="width: 16px; height: 16px;" <?php checked( $first ); $first = false; ?>>
			<?php echo esc_html( $template_details['label'] ); ?>
			<?php if ( ! empty( $template_details['description'] ) ) : ?>
			<img data-tip="<?php echo esc_attr( $template_details['description'] ); ?>" class="help_tip" src="<?php echo WC()->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16">
			<?php endif; ?>
		</label>
		<?php endforeach; ?>
	</fieldset>
	<?php do_action( 'wcopc_shortcode_iframe_after' ); ?>
	<fieldset style="margin: 1em 0;">
		<input id="wcopc_submit" type="submit" class="button-primary" value="<?php _e( 'Create Shortcode', 'wcopc' ); ?>" />
		<input id="wcopc_cancel" type="button" class="button" value="<?php _e( 'Cancel', 'wcopc' ); ?>" />
	</fieldset>
</form>
</div>
<?php
		iframe_footer();
		exit();
	}
}

new WCOPC_Admin_Editor();

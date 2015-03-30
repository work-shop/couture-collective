jQuery(document).ready(function($){ 

	/* Add/remove products with number input type */
	$('#opc-product-selection input[type="number"][data-add_to_cart]').change(function(e){

		var data = {
			quantity:    $(this).val(),
			add_to_cart: parseInt( $(this).data('add_to_cart') ),
			nonce:       wcopc.wcopc_nonce,
		}

		if( data['quantity'] == 0 ) {
			data['action'] = 'pp_remove_from_cart';
		} else {
			data['action'] = 'pp_update_add_in_cart';
		}

		$(this).ajax_add_remove_product( data, e );

		e.preventDefault();

	});

	/* Add/remove products with radio or checkobox inputs */
	$('#opc-product-selection input[type="radio"][data-add_to_cart], #opc-product-selection input[type="checkbox"][data-add_to_cart]').on('change', function(e){

		var data = {
			add_to_cart: parseInt( $(this).data('add_to_cart') ),
			nonce:       wcopc.wcopc_nonce
		}

		if ( $(this).is(':checked') ) {
			if( $(this).prop('type') == 'radio' ) {
				data.empty_cart = 'true';
				$('input[data-add_to_cart]').prop('checked', false);
				$(this).prop('checked', true);
				$('.selected').removeClass('selected');
			}
			data.action = 'pp_add_to_cart';
			$(this).parents('.product-item').addClass('selected');
		} else {
			data.action = 'pp_remove_from_cart';
			$(this).parents('.product-item').removeClass('selected');
		}

		$(this).ajax_add_remove_product( data, e );
	});

	/* Add/remove products with button element or a tags */
	$('#opc-product-selection a[data-add_to_cart], #opc-product-selection button[data-add_to_cart]').on('click', function(e){

		var data = {
			add_to_cart: parseInt( $(this).data('add_to_cart') ),
			nonce:       wcopc.wcopc_nonce
		}

		// Toggle button on or off
		if ( ! $(this).parents('.product-item').hasClass('selected') ) {
			data.action = 'pp_add_to_cart';
			$(this).parents('.product-item').addClass('selected');
		} else {
			data.action = 'pp_remove_from_cart';
			$(this).parents('.product-item').removeClass('selected');
		}

		$(this).ajax_add_remove_product( data, e );
	});

	/* Add products from any Easy Pricing Table template */
	$('#opc-product-selection a.ptp-button, #opc-product-selection a.ptp-fancy-button, #opc-product-selection a.btn.sign-up, #opc-product-selection .ptp-stylish-pricing_button a, #opc-product-selection .ptp-design4-col > a').on('click',function(e){

		var productParams = getUrlsParams($(this)[0].search.substring(1));

		if( typeof productParams['variation_id'] == "undefined" ){
			productParams['variation_id'] = null;
		}

		var data = {
			action:      'pp_add_to_cart',
			add_to_cart: productParams['add-to-cart'],
			empty_cart:  'true',
			nonce:       wcopc.wcopc_nonce
		}

		$(this).ajax_add_remove_product( data, e );

	});

	/* Function to add or remove product from cart via an ajax call */
	$.fn.ajax_add_remove_product = function( data, e ) {
		$.post(woocommerce_params.ajax_url, data, function(response) {
				try{
					response = $.parseJSON(response);

					$.each($('#opc-product-selection [data-add_to_cart]'), function(index,value){
						var product_id = $(this).data('add_to_cart')
							in_cart = false;

						// Make products in the cart are checked/set on product selection fields and products no longer is cart are not set
						$.each(response.products_in_cart, function(cart_item_id, quantity) {
							if (product_id == cart_item_id) {
								in_cart = true;
								return false;
							}
						});

						if( $(this).prop('type') == 'number' ){
							if( in_cart ){
								$(this).val(response.products_in_cart[product_id].quantity);
							} else {
								$(this).val(0);
							}
						} else if( $(this).is('a, button') ){
							if( in_cart ){
								$(this).parents('.product-item').addClass('selected');
							} else {
								$(this).parents('.product-item').removeClass('selected');
							}
						} else {
							if( in_cart ){
								$(this).prop('checked',true);
							} else {
								$(this).prop('checked',false);
							}
						}
					});

					if (response.result=='failure') {
						$('.woocommerce-error, .woocommerce-message').remove();
						$('form.checkout').prepend(response.messages);
						$('html, body').animate({
							scrollTop: ($('form.checkout').offset().top - 100)
						}, 500);
					} else if (response.result == 'success') {
						$('.woocommerce-error, .woocommerce-message').remove();
						$('form.checkout').prepend(response.messages);
						$('html, body').animate({
							scrollTop: ($('form.checkout').offset().top - 100)
						}, 500);
					}
				} catch(err) {
					$('.woocommerce-error, .woocommerce-message').remove();
					$('form.checkout').prepend(response.messages);
					$('html, body').animate({
						scrollTop: ($('form.checkout').offset().top - 100)
					}, 500);
				}
			// Tell WooCommerce to update totals
			$('body').trigger('update_checkout');
		});
		e.preventDefault();
	};

	/* Only display the place order button when a product has been selected */
	showHidePlaceOrder();
	$('body').on('updated_checkout',function(){
		showHidePlaceOrder();
	});

	function showHidePlaceOrder(){
		if($('#order_review tbody').children().length>0) {
			$('#place_order').show();
		} else {
			$('#place_order').hide();
		}
	}

	function getUrlsParams(queryString){
		var match,
			pl     = /\+/g,  // Regex for replacing addition symbol with a space
			search = /([^&=]+)=?([^&]*)/g,
			decode = function (s) { return decodeURIComponent(s.replace(pl, ' ')); };

		urlParams = {};
		while (match = search.exec(queryString)){
			urlParams[decode(match[1])] = decode(match[2]);
		}

		return urlParams;
	}
});
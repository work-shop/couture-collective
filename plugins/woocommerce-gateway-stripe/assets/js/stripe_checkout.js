jQuery( function() {

	var stripe_submit = false;

	jQuery( 'form.checkout' ).on( 'checkout_place_order_stripe', function() {
		if ( stripe_submit ) {
			stripe_submit = false;
			return true;
		}
		if ( ! jQuery('#payment_method_stripe').is(':checked') ) {
			return true;
		}
		if ( jQuery('input[name=stripe_card_id]').length > 0 && jQuery('input[name=stripe_card_id]:checked').val() != 'new' ) {
			return true;
		}
		if ( jQuery('input#terms').size() === 1 && jQuery('input#terms:checked').size() === 0 ) {
			alert( wc_stripe_params.i18n_terms );
			return false;
		}
		$required_inputs = jQuery( '.woocommerce-billing-fields .address-field.validate-required' );

		if ( $required_inputs.size() ) {
			var required_error = false;
			$required_inputs.each( function() {
				if ( jQuery( this ).find( 'input.input-text' ).val() === '' ) {
					required_error = true;
				}
			});
			if ( required_error ) {
				alert( wc_stripe_params.i18n_required_fields );
				return false;
			}
		}
		var $form            = jQuery("form.checkout, form#order_review");
		var $stripe_new_card = jQuery( '.stripe_new_card' );
		var token            = $form.find('input.stripe_token');

		token.val('');

		var token_action = function( res ) {
			$form.find('input.stripe_token').remove();
			$form.append("<input type='hidden' class='stripe_token' name='stripe_token' value='" + res.id + "'/>");
			stripe_submit = true;
			$form.submit();
		};

		StripeCheckout.open({
			key:         wc_stripe_params.key,
			address:     false,
			amount:      $stripe_new_card.data( 'amount' ),
			name:        $stripe_new_card.data( 'name' ),
			description: $stripe_new_card.data( 'description' ),
			panelLabel:  $stripe_new_card.data( 'label' ),
			currency:    $stripe_new_card.data( 'currency' ),
			image:       $stripe_new_card.data( 'image' ),
			email: 		 jQuery('#billing_email').val(),
			token:       token_action
		});

		return false;
    });
} );
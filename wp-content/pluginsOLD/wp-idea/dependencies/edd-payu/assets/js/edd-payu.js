+ function ( $ ) {
	'use strict';
	var loading_text = '<img src="' + bpmj_eddpayu.admin_url + 'images/spinner.gif" style="vertical-align: middle;"> '
	                   + bpmj_eddpayu.purchase_loading;
	function setup_payu_fields_and_submit( card_token, pay_for_recurring_only ) {
		var $button = $( '.edd-payu-submit-button' );
		$button.html( loading_text );
		$( '#edd-payu-card-token' ).val( card_token );
		$( '#edd-payu-pay-for-recurring-items-only' ).val( pay_for_recurring_only ? '1' : '0' );
		$( '#edd-purchase-button' ).click();
	}

	function edd_payu_process_token_response( response, pay_for_recurring_only ) {
		if ( 'CARD_TOKEN' === response.type && response.value ) {
			setup_payu_fields_and_submit( response.value, pay_for_recurring_only );
		}
	}

	function init() {
		$( document.body ).on( 'edd_discount_applied edd_discount_removed edd_quantity_updated', function () {
			var payment_mode = $( 'input[name="edd-gateway"]' ).val();
			// When something related to order total changes, we need to reload PayU form
			if ( 'payu' === payment_mode ) {
				var data = {
					action: 'bpmj_eddpayu_get_purchase_submit_html',
					'payment-mode': payment_mode
				};
				$.post( bpmj_eddpayu.ajax_url, data, function ( data ) {
					$( '#edd_purchase_submit' ).replaceWith( data );
				} );
			}
		} );

		$( document ).on( 'submit', '#bpmj-eddpayu-cancel-subscription-form', function ( e ) {
			if ( ! confirm( bpmj_eddpayu.confirm_cancel_subscription ) ) {
				return false;
			}
		} );

		$( document ).on( 'click', '#edd-payu-button-pay-for-all-standard', function ( e ) {
			var $button = $( this );

			$button.html( loading_text );
		} );

		$( document ).on( 'click', '.edd-payu-submit-button:not(#edd-payu-button-pay-for-all-standard)', function ( e ) {
			var edd_purchase_form = document.getElementById( 'edd_purchase_form' );
			var $submit_div = $( '#edd_purchase_submit' );
			var $button = $( this );

			e.preventDefault();
			e.stopImmediatePropagation();

			if ( 'function' === typeof edd_purchase_form.checkValidity && false === edd_purchase_form.checkValidity() ) {
				return;
			}
			var was_clicked = $submit_div.data( 'payu-button-was-clicked' );
			if ( was_clicked ) {
				return;
			}
			$submit_div.data( 'payu-button-was-clicked', true );

			$button.data( 'payu-purchase-text', $button.html() );

			$button.html( loading_text );

			var post_params = $( '#edd_purchase_form' ).serialize().replace( /edd_action=[^&]+&?/g, '' );
			post_params += '&action=bpmj_eddpayu_validate_checkout_form&edd_ajax=true';
			$.post( bpmj_eddpayu.ajax_url, post_params, function ( data ) {
				if ( 'success' === $.trim( data ) ) {
					$( '.edd_errors' ).remove();
					$( '.edd-error' ).hide();
					var payu_script = $button.data( 'payu-script' );
					var button = $button.get( 0 );
					$( document.head ).append( payu_script );
					var interval = setInterval( function () {
						// Wait for PayU to load and setup listener on $button
						// Warning: details below might change if PayU changes its API
						if ( $('#payuFrame').length > 0 ) {
							clearInterval( interval );

							$button.html( $button.data( 'payu-purchase-text' ) );
							$submit_div.data( 'payu-button-was-clicked', false );

							// This removes PayU's click handler so we can safely click on the button again
							$button.replaceWith( $button.clone() );
						}
						else {
							$('#' + button.id + '-hidden').click();
						}
					}, 50 );
				} else {
					$button.html( $button.data( 'payu-purchase-text' ) );
					$( '.edd-cart-ajax' ).remove();
					$( '.edd_errors' ).remove();
					$( '.edd-error' ).hide();
					$submit_div.before( data );
					$submit_div.data( 'payu-button-was-clicked', false );
				}
			} );
		} );
	}

	window.edd_payu_process_token_response_for_recurring_items = function ( response ) {
		edd_payu_process_token_response( response, true );
	};
	window.edd_payu_process_token_response_for_all_items = function ( response ) {
		edd_payu_process_token_response( response, false );
	};

	$( document ).ready( init );
}( jQuery );
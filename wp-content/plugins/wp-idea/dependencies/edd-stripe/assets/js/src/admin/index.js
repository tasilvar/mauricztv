/* global $, edd_stripe_admin */

let testModeCheckbox;
let testModeToggleNotice;

$( document ).ready( function() {
	testModeCheckbox = document.getElementById( 'edd_settings[test_mode]' );
	if ( testModeCheckbox ) {
		testModeToggleNotice = document.getElementById( 'edd_settings[stripe_connect_test_mode_toggle_notice]' );
		EDD_Stripe_Connect_Scripts.init();
	}

	// Show the hidden API key fields
	$( '#edds-api-keys-row-reveal a' ).on( 'click', function( event ) {
		event.preventDefault();
		$( '.edds-api-key-row' ).removeClass( 'edd-hidden' );
		$( this ).parent().addClass( 'edd-hidden' );
		$( '#edds-api-keys-row-hide' ).removeClass( 'edd-hidden' );
	} );

	// Hide API key fields
	$( '#edds-api-keys-row-hide a' ).on( 'click', function( event ) {
		event.preventDefault();
		$( '.edds-api-key-row' ).addClass( 'edd-hidden' );
		$( this ).parent().addClass( 'edd-hidden' );
		$( '#edds-api-keys-row-reveal' ).removeClass( 'edd-hidden' );
	} );
} );

const EDD_Stripe_Connect_Scripts = {

	init() {
		this.listeners();
	},

	listeners() {
		const self = this;

		testModeCheckbox.addEventListener( 'change', function() {
			// Don't run these events if Stripe is not enabled.
			if ( ! edd_stripe_admin.stripe_enabled ) {
				return;
			}

			if ( this.checked ) {
				if ( 'false' === edd_stripe_admin.test_key_exists ) {
					self.showNotice( testModeToggleNotice, 'error' );
					self.addHiddenMarker();
				} else {
					self.hideNotice( testModeToggleNotice );
					const hiddenMarker = document.getElementById( 'edd-test-mode-toggled' );
					if ( hiddenMarker ) {
						hiddenMarker.parentNode.removeChild( hiddenMarker );
					}
				}
			}

			if ( ! this.checked ) {
				if ( 'false' === edd_stripe_admin.live_key_exists ) {
					self.showNotice( testModeToggleNotice, 'error' );
					self.addHiddenMarker();
				} else {
					self.hideNotice( testModeToggleNotice );
					const hiddenMarker = document.getElementById( 'edd-test-mode-toggled' );
					if ( hiddenMarker ) {
						hiddenMarker.parentNode.removeChild( hiddenMarker );
					}
				}
			}
		} );
	},

	addHiddenMarker() {
		const submit = document.getElementById( 'submit' );

		if ( ! submit ) {
			return;
		}

		submit.parentNode.insertAdjacentHTML( 'beforeend', '<input type="hidden" class="edd-hidden" id="edd-test-mode-toggled" name="edd-test-mode-toggled" />' );
	},

	showNotice( element = false, type = 'error' ) {
		if ( ! element ) {
			return;
		}

		if ( typeof element !== 'object' ) {
			return;
		}

		element.className = 'notice notice-' + type;
	},

	hideNotice( element = false ) {
		if ( ! element ) {
			return;
		}

		if ( typeof element !== 'object' ) {
			return;
		}

		element.className = 'edd-hidden';
	},
};

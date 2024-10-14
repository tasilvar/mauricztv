/* global Stripe, edd_stripe_vars */

/**
 * Internal dependencies
 */
/**
 * External dependencies
 */
import { domReady, apiRequest, generateNotice } from 'utils';

import {
	setup as setupCheckout,
	paymentMethods,
} from './checkout';

import { setup as setupProfile } from './profile-editor';

import { setup as setupPaymentHistory } from './payment-receipt';

import {
	mountCardElement,
	getBillingDetails,
	getPaymentMethod,
	confirm as confirmIntent,
	handle as handleIntent,
	retrieve as retrieveIntent,
} from 'frontend/elements';

( () => {
	try {
		window.eddStripe = new Stripe( edd_stripe_vars.publishable_key );

		// Alias some functionality for external plugins.
		window.eddStripe._plugin = {
			domReady,
			apiRequest,
			generateNotice,
			mountCardElement,
			getBillingDetails,
			getPaymentMethod,
			confirmIntent,
			handleIntent,
			retrieveIntent,
			paymentMethods,
		};

		// Setup frontend components when DOM is ready.
		domReady(
			setupCheckout,
			setupProfile,
			setupPaymentHistory
		);
	} catch ( error ) {
		alert( error.message );
	}
} )();

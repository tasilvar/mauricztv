/**
 * Internal dependencies
 */
import { updatePaymentMethodForm } from './update-payment-method';

export function setup() {
	if ( ! document.getElementById( 'edds-update-payment-method' ) ) {
		return;
	}

	updatePaymentMethodForm();
}

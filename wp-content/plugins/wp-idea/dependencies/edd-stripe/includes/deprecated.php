<?php
/**
 * Manage deprecations.
 *
 * @package EDD_Stripe
 * @since   2.7.0
 */

/**
 * Process stripe checkout submission
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function edds_process_stripe_payment( $purchase_data ) {
	_edd_deprecated_function( 'edds_process_stripe_payment', '2.7.0', 'edds_process_purchase_form', debug_backtrace() );

	return edds_process_purchase_form( $purchase_data );
}

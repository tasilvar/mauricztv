<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;

/**
 * Add an errors div
 *
 * @since       1.0
 * @return      void
 */
function edds_add_stripe_errors() {
	echo '<div id="edd-stripe-payment-errors"></div>';
}
add_action( 'edd_after_cc_fields', 'edds_add_stripe_errors', 999 );

/**
 * Stripe uses it's own credit card form because the card details are tokenized.
 *
 * We don't want the name attributes to be present on the fields in order to prevent them from getting posted to the server
 *
 * @since       1.7.5
 * @return      void
 */
function edds_credit_card_form( $echo = true ) {

	global $edd_options;

	if ( edd_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		edd_set_error( 'edd_stripe_error_limit', __( 'We are unable to process your payment at this time, please try again later or contact support.', 'edds' ) );
		return;
	}

	ob_start(); ?>

	<?php if ( ! wp_script_is ( 'edd-stripe-js' ) ) : ?>
		<?php edd_stripe_js( true ); ?>
	<?php endif; ?>

	<?php do_action( 'edd_before_cc_fields' ); ?>

	<fieldset id="edd_cc_fields" class="edd-do-validate">
		<legend><?= Translator_Static_Helper::translate('orders.actions.payment.credit_card_info'); ?></legend>
		<?php if( is_ssl() ) : ?>
			<div id="edd_secure_site_wrapper">
				<span class="padlock">
					<svg class="edd-icon edd-icon-lock" xmlns="http://www.w3.org/2000/svg" width="18" height="28" viewBox="0 0 18 28" aria-hidden="true">
						<path d="M5 12h8V9c0-2.203-1.797-4-4-4S5 6.797 5 9v3zm13 1.5v9c0 .828-.672 1.5-1.5 1.5h-15C.672 24 0 23.328 0 22.5v-9c0-.828.672-1.5 1.5-1.5H2V9c0-3.844 3.156-7 7-7s7 3.156 7 7v3h.5c.828 0 1.5.672 1.5 1.5z"/>
					</svg>
				</span>
				<span><?= Translator_Static_Helper::translate('orders.actions.payment.secure_ssl'); ?></span>
			</div>
		<?php endif; ?>

		<?php
		$existing_cards = edd_stripe_get_existing_cards( get_current_user_id() );
		?>
		<?php if ( ! empty( $existing_cards ) ) { edd_stripe_existing_card_field_radio( get_current_user_id() ); } ?>

		<div class="edd-stripe-new-card" <?php if ( ! empty( $existing_cards ) ) { echo 'style="display: none;"'; } ?>>
			<?php do_action( 'edd_stripe_new_card_form' ); ?>
			<?php do_action( 'edd_after_cc_expiration' ); ?>
		</div>

	</fieldset>
	<?php

	do_action( 'edd_after_cc_fields' );

	$form = ob_get_clean();

	if ( false !== $echo ) {
		echo $form;
	}

	return $form;
}
add_action( 'edd_stripe_cc_form', 'edds_credit_card_form' );

/**
 * Display the markup for the Stripe new card form
 *
 * @since 2.6
 * @return void
 */
function edd_stripe_new_card_form() {
	if ( edd_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		edd_set_error( 'edd_stripe_error_limit', __( 'Adding new payment methods is currently unavailable.', 'edds' ) );
		edd_print_errors();
		return;
	}
?>

<p id="edd-card-name-wrap">
	<label for="card_name" class="edd-label">
		<?= Translator_Static_Helper::translate('orders.actions.payment.name_on_the_card'); ?>
		<span class="edd-required-indicator">*</span>
	</label>
	<span class="edd-description"><?php _e( 'The name printed on the front of your credit card.', 'edds' ); ?></span>
	<input type="text" name="card_name" id="card_name" class="card-name edd-input required" placeholder="<?= Translator_Static_Helper::translate('orders.actions.payment.full_name')?>" autocomplete="cc-name" />
</p>

<div id="edd-card-wrap">
	<label for="edd-card-element" class="edd-label">
		<?= Translator_Static_Helper::translate('orders.actions.payment.credit_card'); ?>
		<span class="edd-required-indicator">*</span>
	</label>

	<div id="edd-stripe-card-element"></div>
	<div id="edd-stripe-card-errors" role="alert"></div>

	<p></p><!-- Extra spacing -->
</div>

<?php
	/**
	 * Allow output of extra content before the credit card expiration field.
	 *
	 * This content no longer appears before the credit card expiration field
	 * with the introduction of Stripe Elements.
	 *
	 * @deprecated 2.7
	 * @since unknown
	 */
	do_action( 'edd_before_cc_expiration' );
}
add_action( 'edd_stripe_new_card_form', 'edd_stripe_new_card_form' );

/**
 * Show the checkbox for updating the billing information on an existing Stripe card
 *
 * @since 2.6
 * @return void
 */
function edd_stripe_update_billing_address_field() {
	$payment_mode   = strtolower( edd_get_chosen_gateway() );
	if ( edd_is_checkout() && 'stripe' !== $payment_mode ) {
		return;
	}

	$existing_cards = edd_stripe_get_existing_cards( get_current_user_id() );
	if ( empty( $existing_cards ) ) {
		return;
	}

	if ( ! did_action( 'edd_stripe_cc_form' ) ) {
		return;
	}

	$default_card = false;

	foreach ( $existing_cards as $existing_card ) {
		if ( $existing_card['default'] ) {
			$default_card = $existing_card['source'];
			break;
		}
	}
	?>
	<p class="edd-stripe-update-billing-address-current">
		<?php
		if ( $default_card ) :
			$address_fields = array( 
				'line1'   => isset( $default_card->address_line1 ) ? $default_card->address_line1 : null,
				'line2'   => isset( $default_card->address_line2 ) ? $default_card->address_line2 : null,
				'city'    => isset( $default_card->address_city ) ? $default_card->address_city : null,
				'state'   => isset( $default_card->address_state ) ? $default_card->address_state : null,
				'zip'     => isset( $default_card->address_zip ) ? $default_card->address_zip : null,
				'country' => isset( $default_card->address_country ) ? $default_card->address_country : null,
			);

			$address_fields = array_filter( $address_fields );

			echo esc_html( implode( ', ', $address_fields ) );
		endif;
		?>
	</p>

	<p class="edd-stripe-update-billing-address-wrapper">
		<input type="checkbox" name="edd_stripe_update_billing_address" id="edd-stripe-update-billing-address" value="1" />
		<label for="edd-stripe-update-billing-address"><?php _e( 'Enter new billing address', 'edds' ); ?></label>
	</p>
	<?php
}
add_action( 'edd_cc_billing_top', 'edd_stripe_update_billing_address_field', 10 );

/**
 * Display a radio list of existing cards on file for a user ID
 *
 * @since 2.6
 * @param int $user_id
 *
 * @return void
 */
function edd_stripe_existing_card_field_radio( $user_id = 0 ) {
	if ( edd_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		edd_set_error( 'edd_stripe_error_limit', __( 'We are unable to process your payment at this time, please try again later or contacts support.', 'edds' ) );
		return;
	}

	// Can't use just edd_is_checkout() because this could happen in an AJAX request.
	$is_checkout = edd_is_checkout() || ( isset( $_REQUEST['action'] ) && 'edd_load_gateway' === $_REQUEST['action'] );

	edd_stripe_css( true );
	$existing_cards = edd_stripe_get_existing_cards( $user_id );
	if ( ! empty( $existing_cards ) ) : ?>
	<div class="edd-stripe-card-selector edd-card-selector-radio">
		<?php foreach ( $existing_cards as $card ) : ?>
			<?php $source = $card['source']; ?>
			<div class="edd-stripe-card-radio-item existing-card-wrapper <?php if ( $card['default'] ) { echo ' selected'; } ?>">
				<input type="hidden" id="<?php echo $source->id; ?>-billing-details"
					   data-address_city="<?php echo $source->address_city; ?>"
					   data-address_country="<?php echo $source->address_country; ?>"
					   data-address_line1="<?php echo $source->address_line1; ?>"
					   data-address_line2="<?php echo $source->address_line2; ?>"
					   data-address_state="<?php echo $source->address_state; ?>"
					   data-address_zip="<?php echo $source->address_zip; ?>"
				/>
				<label for="<?php echo $source->id; ?>">
					<input <?php checked( true, $card['default'], true ); ?> type="radio" id="<?php echo $source->id; ?>" name="edd_stripe_existing_card" value="<?php echo $source->id; ?>" class="edd-stripe-existing-card">
					<span class="card-label">
						<span class="card-data">
							<span class="card-name-number">
								<span class="card-brand"><?php echo $source->brand; ?></span>
								<span class="card-ending-label"><?php _e( 'ending in', 'edds' ); ?></span>
								<span class="card-last-4"><?php echo $source->last4; ?></span>
							</span>
							<span class="card-expires-on">
								<span class="default-card-sep"><?php echo '&mdash; '; ?></span>
								<span class="card-expiration-label"><?php _e( 'expires', 'edds' ); ?></span>
								<span class="card-expiration">
									<?php echo $source->exp_month . '/' . $source->exp_year; ?>
								</span>
							</span>
						</span>
						<?php
							$current  = strtotime( date( 'm/Y' ) );
							$exp_date = strtotime( $source->exp_month . '/' . $source->exp_year );
							if ( $exp_date < $current ) :
							?>
							<span class="card-expired">
									<?php _e( 'Expired', 'edds' ); ?>
								</span>
							<?php
							endif;
						?>
					</span>
					<?php if ( $card['default'] && $is_checkout ) { ?>
						<span class="card-status">
							<span class="default-card-sep"><?php echo '&mdash; '; ?></span>
							<span class="card-is-default"><?php _e( 'Default', 'edds'); ?></span>
						</span>
					<?php } ?>
				</label>
			</div>
		<?php endforeach; ?>
		<div class="edd-stripe-card-radio-item new-card-wrapper">
			<input type="radio" id="edd-stripe-add-new" class="edd-stripe-existing-card" name="edd_stripe_existing_card" value="new" />
			<label for="edd-stripe-add-new"><span class="add-new-card"><?php _e( 'Add New Card', 'edds' ); ?></span></label>
		</div>
	</div>
	<?php endif;
}

/**
 * Output the management interface for a user's Stripe card
 *
 * @since 2.6
 * @return void
 */
function edd_stripe_manage_cards() {
	$enabled = edd_stripe_existing_cards_enabled();
	if ( ! $enabled ) {
		return;
	}

	if ( edd_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		edd_set_error( 'edd_stripe_error_limit', __( 'Payment method management is currently unavailable.', 'edds' ) );
		edd_print_errors();
		return;
	}

	$existing_cards = edd_stripe_get_existing_cards( get_current_user_id() );

	edd_stripe_css( true );
	edd_stripe_js( true );
	$display = edd_get_option( 'stripe_billing_fields', 'full' );
?>
	<div id="edd-stripe-manage-cards">
		<fieldset>
			<legend><?php _e( 'Manage Payment Methods', 'edds' ); ?></legend>
			<input type="hidden" id="stripe-update-card-user_id" name="stripe-update-user-id" value="<?php echo get_current_user_id(); ?>" />
			<?php if ( ! empty( $existing_cards ) ) : ?>
				<?php foreach( $existing_cards as $card ) : ?>
				<?php $source = $card['source']; ?>
				<div id="<?php echo esc_attr( $source->id ); ?>_card_item" class="edd-stripe-card-item">

					<span class="card-details">
						<span class="card-brand"><?php echo $source->brand; ?></span>
						<span class="card-ending-label"><?php _e( 'ending in', 'edds' ); ?></span>
						<span class="card-last-4"><?php echo $source->last4; ?></span>
						<?php if ( $card['default'] ) { ?>
							<span class="default-card-sep"><?php echo '&mdash; '; ?></span>
							<span class="card-is-default"><?php _e( 'Default', 'edds'); ?></span>
						<?php } ?>
					</span>

					<span class="card-meta">
						<span class="card-expiration"><span class="card-expiration-label"><?php _e( 'Expires', 'edds' ); ?>: </span><span class="card-expiration-date"><?php echo $source->exp_month; ?>/<?php echo $source->exp_year; ?></span></span>
						<span class="card-address">
							<?php
							$address_fields = array( 
								'line1'   => isset( $source->address_line1 ) ? $source->address_line1 : '',
								'zip'     => isset( $source->address_zip ) ? $source->address_zip : '',
								'country' => isset( $source->address_country ) ? $source->address_country : '',
							);

							echo esc_html( implode( ' ', $address_fields ) );
							?>
						</span>
					</span>

					<span id="<?php echo esc_attr( $source->id ); ?>-card-actions" class="card-actions">
						<span class="card-update">
							<a href="#" class="edd-stripe-update-card" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Update', 'edds' ); ?></a>
						</span>

						<?php if ( ! $card['default'] ) : ?>
						 |
						<span class="card-set-as-default">
							<a href="#" class="edd-stripe-default-card" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Set as Default', 'edds' ); ?></a>
						</span>
						<?php
						endif;

						$can_delete = apply_filters( 'edd_stripe_can_delete_card', true, $card, $existing_cards );
						if ( $can_delete ) :
						?>
						|
						<span class="card-delete">
							<a href="#" class="edd-stripe-delete-card delete" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Delete', 'edds' ); ?></a>
						</span>
						<?php endif; ?>
						
						<span style="display: none;" class="edd-loading-ajax edd-loading"></span>
					</span>

					<form id="<?php echo esc_attr( $source->id ); ?>-update-form" class="card-update-form" data-source="<?php echo esc_attr( $source->id ); ?>">
						<label><?php _e( 'Billing Details', 'edds' ); ?></label>

						<div class="card-address-fields">
							<p class="edds-card-address-field edds-card-address-field--address1">
							<?php
							echo EDD()->html->text( array(
								'id'    => sprintf( 'edds_address_line1_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_line1 ) ? $source->address_line1 : '' ),
								'label' => esc_html__( 'Address Line 1', 'edds' ),
								'name'  => 'address_line1',
								'class' => 'card-update-field address_line1 text edd-input',
								'data'  => array(
									'key' => 'address_line1',
								)
							) );
							?>
							</p>
							<p class="edds-card-address-field edds-card-address-field--address2">
							<?php
							echo EDD()->html->text( array(
								'id'    => sprintf( 'edds_address_line2_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_line2 ) ? $source->address_line2 : '' ),
								'label' => esc_html__( 'Address Line 2', 'edds' ),
								'name'  => 'address_line2',
								'class' => 'card-update-field address_line2 text edd-input',
								'data'  => array(
									'key' => 'address_line2',
								)
							) );
							?>
							</p>
							<p class="edds-card-address-field edds-card-address-field--city">
							<?php
							echo EDD()->html->text( array(
								'id'    => sprintf( 'edds_address_city_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_city ) ? $source->address_city : '' ),
								'label' => esc_html__( 'City', 'edds' ),
								'name'  => 'address_city',
								'class' => 'card-update-field address_city text edd-input',
								'data'  => array(
									'key' => 'address_city',
								)
							) );
							?>
							</p>
							<p class="edds-card-address-field edds-card-address-field--zip">
							<?php
							echo EDD()->html->text( array(
								'id'    => sprintf( 'edds_address_zip_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_zip ) ? $source->address_zip : '' ),
								'label' => esc_html__( 'ZIP Code', 'edds' ),
								'name'  => 'address_zip',
								'class' => 'card-update-field address_zip text edd-input',
								'data'  => array(
									'key' => 'address_zip',
								)
							) );
							?>
							</p>
							<p class="edds-card-address-field edds-card-address-field--country">
								<label for="<?php echo esc_attr( sprintf( 'edds_address_country_%1$s', $source->id ) ); ?>">
									<?php esc_html_e( 'Country', 'edds' ); ?>
								</label>

								<?php
								$countries = array_filter( edd_get_country_list() );
								$country   = isset( $source->address_country ) ? $source->address_country : edd_get_shop_country();
								echo EDD()->html->select( array(
									'id'               => sprintf( 'edds_address_country_%1$s', $source->id ),
									'name'             => 'address_country',
									'label'            => esc_html__( 'Country', 'edds' ),
									'options'          => $countries,
									'selected'         => $country,
									'class'            => 'card-update-field address_country',
									'data'             => array( 'key' => 'address_country' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
								?>
							</p>

							<p class="edds-card-address-field edds-card-address-field--state">
								<label for="<?php echo esc_attr( sprintf( 'edds_address_state_%1$s', $source->id ) ); ?>">
									<?php esc_html_e( 'State', 'edds' ); ?>
								</label>

								<?php
								$selected_state = isset( $source->address_state ) ? $source->address_state : edd_get_shop_state();
								$states         = edd_get_shop_states( $country );
								echo EDD()->html->select( array(
									'id'               => sprintf( 'edds_address_state_%1$s', $source->id ),
									'name'             => 'address_state',
									'options'          => $states,
									'selected'         => $selected_state,
									'class'            => 'card-update-field address_state card_state',
									'data'             => array( 'key' => 'address_state' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
								?>
							</p>
						</div>

						<p class="card-expiration-fields">
							<label for="<?php echo esc_attr( sprintf( 'edds_card_exp_month_%1$s', $source->id ) ); ?>" class="edd-label">
								<?php _e( 'Expiration (MM/YY)', 'edds' ); ?>
							</label>

							<?php
								$months = array_combine( $r = range( 1, 12 ), $r );
								echo EDD()->html->select( array(
									'id'               => sprintf( 'edds_card_exp_month_%1$s', $source->id ),
									'name'             => 'exp_month',
									'options'          => $months,
									'selected'         => $source->exp_month,
									'class'            => 'card-expiry-month edd-select edd-select-small card-update-field exp_month',
									'data'             => array( 'key' => 'exp_month' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
							?>

							<span class="exp-divider"> / </span>

							<?php
								$years = array_combine( $r = range( date( 'Y' ), date( 'Y' ) + 30 ), $r );
								echo EDD()->html->select( array(
									'id'               => sprintf( 'edds_card_exp_year_%1$s', $source->id ),
									'name'             => 'exp_year',
									'options'          => $years,
									'selected'         => $source->exp_year,
									'class'            => 'card-expiry-year edd-select edd-select-small card-update-field exp_year',
									'data'             => array( 'key' => 'exp_year' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
							?>
						</p>

						<p>
							<input
								type="submit"
								class="edd-stripe-submit-update"
								data-loading="<?php echo esc_attr( 'Please Wait…', 'edds' ); ?>"
								data-submit="<?php echo esc_attr( 'Update Card', 'edds' ); ?>"
								value="<?php echo esc_attr( 'Update Card', 'edds' ); ?>"
							/>

							<a href="#" class="edd-stripe-cancel-update" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Cancel', 'edds' ); ?></a>

							<input type="hidden" name="card_id" data-key="id" value="<?php echo $source->id; ?>" />
							<?php wp_nonce_field( $source->id . '_update', 'card_update_nonce_' . $source->id, true ); ?>
						</p>
					</form>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
			<form id="edd-stripe-add-new-card">
				<div class="edd-stripe-add-new-card" style="display: none;">
					<label><?php _e( 'Add New Card', 'edds' ); ?></label>
					<fieldset id="edd_cc_card_info" class="cc-card-info">
						<legend><?php _e( 'Credit Card Details', 'easy-digital-downloads' ); ?></legend>
						<?php do_action( 'edd_stripe_new_card_form' ); ?>
					</fieldset>
					<?php
					switch( $display ) {
					case 'full' :
						edd_default_cc_address_fields();
						break;

					case 'zip_country' :
						edd_stripe_zip_and_country();
						add_filter( 'edd_purchase_form_required_fields', 'edd_stripe_require_zip_and_country' );

						break;
					}
					?>
				</div>
				<div class="edd-stripe-add-card-errors"></div>
				<div class="edd-stripe-add-card-actions">

					<input
						type="submit"
						class="edd-button edd-stripe-add-new"
						data-loading="<?php echo esc_attr( 'Please Wait…', 'edds' ); ?>"
						data-submit="<?php echo esc_attr( 'Add new card', 'edds' ); ?>"
						value="<?php echo esc_attr( 'Add new card', 'edds' ); ?>"
					/>
					<a href="#" id="edd-stripe-add-new-cancel" style="display: none;"><?php _e( 'Cancel', 'edds' ); ?></a>
					<?php wp_nonce_field( 'edd-stripe-add-card', 'edd-stripe-add-card-nonce', false, true ); ?>
				</div>
			</form>
		</fieldset>
	</div>
	<?php
}
add_action( 'edd_profile_editor_after', 'edd_stripe_manage_cards' );

/**
 * Zip / Postal Code field for when full billing address is disabled
 *
 * @since       2.5
 * @return      void
 */
function edd_stripe_zip_and_country() {

	$logged_in = is_user_logged_in();
	$customer  = EDD()->session->get( 'customer' );
	$customer  = wp_parse_args( $customer, array( 'address' => array(
		'line1'   => '',
		'line2'   => '',
		'city'    => '',
		'zip'     => '',
		'state'   => '',
		'country' => ''
	) ) );

	$customer['address'] = array_map( 'sanitize_text_field', $customer['address'] );

	if( $logged_in ) {
		$existing_cards = edd_stripe_get_existing_cards( get_current_user_id() );
		if ( empty( $existing_cards ) ) {

			$user_address = edd_get_customer_address( get_current_user() );

			foreach( $customer['address'] as $key => $field ) {

				if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
					$customer['address'][ $key ] = $user_address[ $key ];
				} else {
					$customer['address'][ $key ] = '';
				}

			}
		} else {
			foreach ( $existing_cards as $card ) {
				if ( false === $card['default'] ) {
					continue;
				}

				$source = $card['source'];
				$customer['address'] = array(
					'line1'   => $source->address_line1,
					'line2'   => $source->address_line2,
					'city'    => $source->address_city,
					'zip'     => $source->address_zip,
					'state'   => $source->address_state,
					'country' => $source->address_country,
				);
			}
		}

	}
?>
	<fieldset id="edd_cc_address" class="cc-address">
		<legend><?= Translator_Static_Helper::translate('orders.actions.payment.billing_details'); ?></legend>
		<p id="edd-card-country-wrap">
			<label for="billing_country" class="edd-label">
				<?= Translator_Static_Helper::translate('orders.actions.payment.billing_country'); ?>
				<?php if( edd_field_is_required( 'billing_country' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The country for your billing address.', 'edds' ); ?></span>
			<select name="billing_country" id="billing_country" class="billing_country edd-select<?php if( edd_field_is_required( 'billing_country' ) ) { echo ' required'; } ?>"<?php if( edd_field_is_required( 'billing_country' ) ) {  echo ' required '; } ?> autocomplete="billing country">
				<?php

				$selected_country = edd_get_shop_country();

				if( ! empty( $customer['address']['country'] ) && '*' !== $customer['address']['country'] ) {
					$selected_country = $customer['address']['country'];
				}

				$countries = edd_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>
		<p id="edd-card-zip-wrap">
			<label for="card_zip" class="edd-label">
				<?= Translator_Static_Helper::translate('orders.actions.payment.billing_zip'); ?>
				<?php if( edd_field_is_required( 'card_zip' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'edds' ); ?></span>
			<input type="text" size="4" name="card_zip" id="card_zip" class="card-zip edd-input<?php if( edd_field_is_required( 'card_zip' ) ) { echo ' required'; } ?>" placeholder="<?= Translator_Static_Helper::translate('orders.actions.payment.postal_code')?>" value="<?php echo $customer['address']['zip']; ?>"<?php if( edd_field_is_required( 'card_zip' ) ) {  echo ' required '; } ?> autocomplete="billing postal-code" />
		</p>
	</fieldset>
<?php
}

/**
 * Determine how the billing address fields should be displayed
 *
 * @access      public
 * @since       2.5
 * @return      void
 */
function edd_stripe_setup_billing_address_fields() {

	if( ! function_exists( 'edd_use_taxes' ) ) {
		return;
	}

	if( edd_use_taxes() || 'stripe' !== edd_get_chosen_gateway() || ! edd_get_cart_total() > 0 ) {
		return;
	}

	$display = edd_get_option( 'stripe_billing_fields', 'full' );

	switch( $display ) {

		case 'full' :

			// Make address fields required
			add_filter( 'edd_require_billing_address', '__return_true' );

			break;

		case 'zip_country' :

			remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields', 10 );
			add_action( 'edd_after_cc_fields', 'edd_stripe_zip_and_country', 9 );

			// Make Zip required
			add_filter( 'edd_purchase_form_required_fields', 'edd_stripe_require_zip_and_country' );

			break;

		case 'none' :

			remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields', 10 );

			break;

	}

}
add_action( 'init', 'edd_stripe_setup_billing_address_fields', 9 );

/**
 * Force zip code and country to be required when billing address display is zip only
 *
 * @access      public
 * @since       2.5
 * @return      array $fields The required fields
 */
function edd_stripe_require_zip_and_country( $fields ) {

	$fields['card_zip'] = array(
		'error_id' => 'invalid_zip_code',
		'error_message' => __( 'Please enter your zip / postal code', 'edds' )
	);

	$fields['billing_country'] = array(
		'error_id' => 'invalid_country',
		'error_message' => __( 'Please select your billing country', 'edds' )
	);

	return $fields;
}

<?php

use bpmj\wpidea\admin\settings\core\configuration\Cart_Settings_Group;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\cart\api\Cart_API_Static_Helper;
use bpmj\wpidea\events\actions\Action_Name;

/**
 *
 */
function bpmj_edd_invoice_data_enable_forms() {

	global $edd_options;

	$options = edd_get_payment_gateways();

	foreach ( $options as $key => $option ) :
		if ( isset( $edd_options[ 'edd_id_gateways' ][ $key ] ) ) {
			add_action( 'edd_' . $key . '_cc_form', 'bpmj_edd_invoice_data_cc_form', 1 );
		}
	endforeach;
}

add_action( 'plugins_loaded', 'bpmj_edd_invoice_data_enable_forms' );

/**
 *
 */
function bpmj_edd_invoice_data_cc_form() {

	//Definiujemy nazwy

	$invoice_checkbox_label          = __( 'I want to receive an invoice', 'bpmj-edd-invoice-data' );
	$invoice_data_label              = __( 'Invoice data', 'bpmj-edd-invoice-data' );
	$invoice_receiver_checkbox_label = __( 'Same buyer and receiver', 'bpmj-edd-invoice-data' );

	$force    = bpmj_edd_invoice_data_get_cb_setting( 'edd_id_force' );
	$person   = bpmj_edd_invoice_data_get_cb_setting( 'edd_id_person' );
	$receiver = bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_receiver' );

	$user_id = get_current_user_id();

	$user_invoice_data = array(
		'check'             => false,
		'type'              => 'person',
		'person_name'       => '',
		'company_name'      => '',
		'nip'               => '',
		'street'            => '',
        'building_number'   => '',
        'apartment_number'  => '',
		'postcode'          => '',
		'city'              => '',
		'country'   	    => '',
		'receiver_set'      => 0,
		'receiver_name'     => '',
		'receiver_street'   => '',
		'receiver_postcode' => '',
		'receiver_city'     => '',
	);

	if ( $user_id ) {
		$user_invoice_data_meta = get_user_meta( $user_id, 'bpmj_edd_invoice_data', true );
		if ( ! empty( $user_invoice_data_meta ) ) {
			$user_invoice_data = array_merge( $user_invoice_data, $user_invoice_data_meta );
		} else if ( isset( $_COOKIE['edd_purchase_form_data'] ) ) {
		    $cookie_invoice_data = json_decode( str_replace('\"', '"', $_COOKIE['edd_purchase_form_data'] ), true );
		    if( is_array($cookie_invoice_data) ) {
		        $user_invoice_data = array_merge( $user_invoice_data, $cookie_invoice_data );
		    }
        }
	} else if ( isset( $_COOKIE['edd_purchase_form_data'] ) ) {
	    $cookie_invoice_data = json_decode( str_replace('\"', '"', $_COOKIE['edd_purchase_form_data'] ), true );
	    if( is_array($cookie_invoice_data) ) {
	        $user_invoice_data = array_merge( $user_invoice_data, $cookie_invoice_data );
	    }
    }

	ob_start();
	if ( ! $force ) {
		?>
        <fieldset class="bpmj_edd_invoice_data_invoice_check">
            <input type="checkbox" value="1"
                   name="bpmj_edd_invoice_data_invoice_check" <?php checked( $user_invoice_data[ 'check' ] ); ?>
                   id="bpmj_edd_invoice_data_invoice_check"/>
            <label for="bpmj_edd_invoice_data_invoice_check"  class="edd-label <?php echo $user_invoice_data['check'] == true ? 'checked' : '' ?>">
				<?php echo $invoice_checkbox_label; ?>
            </label>
            
        </fieldset>
		<?php
	} else {
		?>
        <input type="hidden" value="1" name="bpmj_edd_invoice_data_invoice_check"
               id="bpmj_edd_invoice_data_invoice_check"/>
		<?php
	}
	?>

    <div class="bpmj_edd_invoice_data_invoice<?php
	if ( $force ) {
		echo '_force';
	}
	?>">

        <fieldset>
            <span><legend><?php echo $invoice_data_label; ?></legend></span>

			<?php
			if ( $person ) {
				?>
                <p>
                    <label for="bpmj_edd_invoice_data_invoice_type"
                           class="edd-label"><?php _e( 'I order as', 'bpmj-edd-invoice-data' ); ?>
                        <span class="edd-required-indicator">*</span>
                    </label>
                    <span class="bpmj_edd_invoice_data_invoice_type_wrapper">
						<select id="bpmj_edd_invoice_data_invoice_type" name="bpmj_edd_invoice_data_invoice_type">
							<option
                                    value="person" <?php selected( $user_invoice_data[ 'type' ], 'person' ); ?>><?php _e( 'Individual', 'bpmj-edd-invoice-data' ); ?></option>
							<option
                                    value="company" <?php selected( $user_invoice_data[ 'type' ], 'company' ); ?>><?php _e( 'Company / Organization', 'bpmj-edd-invoice-data' ); ?></option>
						</select>
					</span>
                </p>

                <p id="bpmj_edd_invoice_data_person_name_p">
                    <label for="bpmj_edd_invoice_data_invoice_person_name"
                           class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.full_name') ?>
                        <span class="edd-required-indicator">*</span>
                    </label>
                    <span
                            class="edd-description"><?php _e( 'Enter a name for the invoice', 'bpmj-edd-invoice-data' ); ?></span>
                    <input id="bpmj_edd_invoice_data_invoice_person_name" type="text"
                           value="<?php echo esc_attr( $user_invoice_data[ 'person_name' ] ); ?>"
                           name="bpmj_edd_invoice_data_invoice_person_name" class="edd-input">
                </p>
				<?php
			} else {
				?>
                <input type="hidden" value="company" name="bpmj_edd_invoice_data_invoice_type"
                       id="bpmj_edd_invoice_data_invoice_type"/>
				<?php
			}
			?>
            <p id="bpmj_edd_invoice_data_nip_p<?php
            if ( ! $person ) {
                echo '_show';
            }
            ?>">
                <label for="bpmj_edd_invoice_data_invoice_nip"
                       class="edd-label"><?php _e( 'Tax ID', 'bpmj-edd-invoice-data' ); ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input id="bpmj_edd_invoice_data_invoice_nip" type="text"
                       value="<?php echo esc_attr( $user_invoice_data[ 'nip' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_nip" class="edd-input" maxlength="15">

                <?php
                  do_action(Action_Name::DISPLAY_BUTTON_GET_DATA_FROM_GUS)
                ?>

            </p>

            <p id="bpmj_edd_invoice_data_company_name_p<?php
			if ( ! $person ) {
				echo '_show';
			}
			?>">
                <label for="bpmj_edd_invoice_data_invoice_company_name"
                       class="edd-label"><?php _e( 'Company', 'bpmj-edd-invoice-data' ); ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <span
                        class="edd-description"><?php _e( 'Enter a company name for the invoice', 'bpmj-edd-invoice-data' ); ?></span>
                <input id="bpmj_edd_invoice_data_invoice_company_name" type="text"
                       value="<?php echo esc_attr( $user_invoice_data[ 'company_name' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_company_name" class="edd-input">
            </p>

            <p>
                <label for="bpmj_edd_invoice_data_invoice_street"
                       class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.street') ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input id="bpmj_edd_invoice_data_invoice_street" type="text"
                       value="<?php echo esc_attr( $user_invoice_data[ 'street' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_street" class="edd-input">
            </p>

            <p>
                <label for="bpmj_edd_invoice_data_invoice_building_number"
                       class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.building_number') ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input id="bpmj_edd_invoice_data_invoice_building_number" type="text" pattern="[0-9a-zA-Z]+"
                       value="<?php echo esc_attr( $user_invoice_data[ 'building_number' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_building_number" class="edd-input">
            </p>

            <p>
                <label for="bpmj_edd_invoice_data_invoice_apartment_number"
                       class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.apartment_number') ?>
                </label>
                <input id="bpmj_edd_invoice_data_invoice_apartment_number" type="text" pattern="[0-9a-zA-Z]+"
                       value="<?php echo esc_attr( $user_invoice_data[ 'apartment_number' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_apartment_number" class="edd-input">
            </p>

            <p>
                <label for="bpmj_edd_invoice_data_invoice_postcode"
                       class="edd-label"><?php _e( 'Postal Code', 'bpmj-edd-invoice-data' ); ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input id="bpmj_edd_invoice_data_invoice_postcode" type="text"
                       value="<?php echo esc_attr( $user_invoice_data[ 'postcode' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_postcode" class="edd-input">
            </p>

            <p>
                <label for="bpmj_edd_invoice_data_invoice_city"
                       class="edd-label"><?php _e( 'City', 'bpmj-edd-invoice-data' ); ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input id="bpmj_edd_invoice_data_invoice_city" type="text"
                       value="<?php echo esc_attr( $user_invoice_data[ 'city' ] ); ?>"
                       name="bpmj_edd_invoice_data_invoice_city" class="edd-input">
            </p>

			<?php
			$country_enabled = apply_filters('eddid_form_show_country', false);

			if($country_enabled) {
				$def_country = !empty($user_invoice_data[ 'country' ]) ? $user_invoice_data[ 'country' ] : 'PL';
			?>
			<p>
				<label for="billing_country"
					   class="edd-label"><?php _e( 'Country', 'bpmj-edd-invoice-data' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<select name="billing_country" class="billing-country edd-select">
					<?php foreach ( edd_get_country_list() as $country_key => $country ) : ?>
						<?php $selected = $country_key == $def_country ? 'selected' : ''; ?>
						<option value="<?php echo $country_key; ?>" <?php echo $selected; ?>><?php echo $country; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php } ?>

			<?php
			if ( $receiver ):
				?>
                <p id="bpmj_edd_invoice_data_receiver_info_set_checkbox"
                   style="<?php echo 'company' !== $user_invoice_data[ 'type' ] ? 'display: none;' : ''; ?>">
                    <input type="hidden" name="bpmj_edd_invoice_data_receiver_info_set" value="1"/>
                    <input type="checkbox" value="0"
                           name="bpmj_edd_invoice_data_receiver_info_set" <?php checked( ! $user_invoice_data[ 'receiver_set' ] ); ?>
                           id="bpmj_edd_invoice_data_receiver_info_set"/>
                    <label for="bpmj_edd_invoice_data_receiver_info_set" class="edd-label">
						<?php echo $invoice_receiver_checkbox_label; ?>
                    </label>
                </p>

                <div id="bpmj_edd_invoice_data_receiver"
                     style="<?php echo empty( $user_invoice_data[ 'receiver_set' ] ) ? 'display:none;' : ''; ?>">

                    <p>
                        <label for="bpmj_edd_invoice_data_invoice_receiver_name"
                               class="edd-label"><?php _e( 'Individual or company name', 'bpmj-edd-invoice-data' ); ?>
                            <span class="edd-required-indicator">*</span>
                        </label>
                        <span
                                class="edd-description"><?php _e( 'Enter the name of the receiver', 'bpmj-edd-invoice-data' ); ?>
				</span>
                        <input id="bpmj_edd_invoice_data_invoice_receiver_name" type="text"
                               value="<?php echo esc_attr( $user_invoice_data[ 'receiver_name' ] ); ?>"
                               name="bpmj_edd_invoice_data_invoice_receiver_name" class="edd-input">
                    </p>

                    <p>
                        <label for="bpmj_edd_invoice_data_invoice_receiver_street"
                               class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.street') ?>
                            <span class="edd-required-indicator">*</span>
                        </label>
                        <input id="bpmj_edd_invoice_data_invoice_receiver_street" type="text"
                               value="<?php echo esc_attr( $user_invoice_data[ 'receiver_street' ] ); ?>"
                               name="bpmj_edd_invoice_data_invoice_receiver_street" class="edd-input">
                    </p>

                    <p>
                        <label for="bpmj_edd_invoice_data_invoice_receiver_building_number"
                               class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.building_number') ?>
                            <span class="edd-required-indicator">*</span>
                        </label>
                        <input id="bpmj_edd_invoice_data_invoice_receiver_building_number" type="text" pattern="[0-9a-zA-Z]+"
                               value="<?php echo esc_attr( $user_invoice_data[ 'receiver_building_number' ] ); ?>"
                               name="bpmj_edd_invoice_data_invoice_receiver_building_number" class="edd-input">
                    </p>

                    <p>
                        <label for="bpmj_edd_invoice_data_invoice_receiver_apartment_number"
                               class="edd-label"><?= Translator_Static_Helper::translate('orders.invoice_data.apartment_number') ?>
                            <span class="edd-required-indicator">*</span>
                        </label>
                        <input id="bpmj_edd_invoice_data_invoice_receiver_apartment_number" type="text" pattern="[0-9a-zA-Z]+"
                               value="<?php echo esc_attr( $user_invoice_data[ 'receiver_apartment_number' ] ); ?>"
                               name="bpmj_edd_invoice_data_invoice_receiver_apartment_number" class="edd-input">
                    </p>

                    <p>
                        <label for="bpmj_edd_invoice_data_invoice_receiver_postcode"
                               class="edd-label"><?php _e( 'Receiver postal code', 'bpmj-edd-invoice-data' ); ?>
                            <span class="edd-required-indicator">*</span>
                        </label>
                        <input id="bpmj_edd_invoice_data_invoice_receiver_postcode" type="text"
                               value="<?php echo esc_attr( $user_invoice_data[ 'receiver_postcode' ] ); ?>"
                               name="bpmj_edd_invoice_data_invoice_receiver_postcode" class="edd-input">
                    </p>

                    <p>
                        <label for="bpmj_edd_invoice_data_invoice_receiver_city"
                               class="edd-label"><?php _e( 'Receiver city', 'bpmj-edd-invoice-data' ); ?>
                            <span class="edd-required-indicator">*</span>
                        </label>
                        <input id="bpmj_edd_invoice_data_invoice_receiver_city" type="text"
                               value="<?php echo esc_attr( $user_invoice_data[ 'receiver_city' ] ); ?>"
                               name="bpmj_edd_invoice_data_invoice_receiver_city" class="edd-input">
                    </p>

                </div>
			<?php
			endif;
			?>
        </fieldset>

    </div>


    <script>

		// Ukrywanie i pokazywanie danych do faktury/rachunku

		jQuery( "input[name=bpmj_edd_invoice_data_invoice_check]" ).on( "click", function () {

			if ( jQuery( 'input[name=bpmj_edd_invoice_data_invoice_check]:checked' ).val() === '1' ) {
				jQuery( '.bpmj_edd_invoice_data_invoice' ).slideDown();

			} else {
				jQuery( '.bpmj_edd_invoice_data_invoice' ).slideUp();
			}

		} );

		jQuery( "input#bpmj_edd_invoice_data_receiver_info_set" ).on( "click", function () {

			if ( jQuery( this ).is( ':checked' ) ) {
				jQuery( '#bpmj_edd_invoice_data_receiver' ).slideUp();
			} else {
				jQuery( '#bpmj_edd_invoice_data_receiver' ).slideDown();
			}

		} );

		// Ukrywanie i pokazywanie danych w zależności od wyboru: osoba fiz. / firma

		jQuery( "select[name=bpmj_edd_invoice_data_invoice_type]" ).on( "change", function () {

			if ( jQuery( 'select[name=bpmj_edd_invoice_data_invoice_type]' ).val() === 'person' ) {
				jQuery( '#bpmj_edd_invoice_data_person_name_p' ).slideDown();
				jQuery( '#bpmj_edd_invoice_data_company_name_p' ).slideUp();
				jQuery( '#bpmj_edd_invoice_data_nip_p' ).slideUp();
				jQuery( '#bpmj_edd_invoice_data_receiver' ).slideUp();
				jQuery( '#bpmj_edd_invoice_data_receiver_info_set_checkbox' ).hide();
			} else {
				jQuery( '#bpmj_edd_invoice_data_person_name_p' ).slideUp();
				jQuery( '#bpmj_edd_invoice_data_company_name_p' ).slideDown();
				jQuery( '#bpmj_edd_invoice_data_nip_p' ).slideDown();
				jQuery( '#bpmj_edd_invoice_data_receiver_info_set_checkbox' ).show();
				if ( jQuery( '#bpmj_edd_invoice_data_receiver_info_set' ).is( ":checked" ) ) {
					jQuery( '#bpmj_edd_invoice_data_receiver' ).slideUp();
				} else {
					jQuery( '#bpmj_edd_invoice_data_receiver' ).slideDown();
				}
			}

		} );

		jQuery( function ( $ ) {
			$( "select[name=bpmj_edd_invoice_data_invoice_type]" ).change();
			if ( $( "input[name=bpmj_edd_invoice_data_invoice_check][checked]" ).length > 0 ) {
				$( '.bpmj_edd_invoice_data_invoice' ).slideDown();
			}
		} );
    </script>

	<?php
	echo ob_get_clean();
}

/**
 * Zapisuje do bazy dane potrzebne do faktury lub rachunku
 */
function bpmj_edd_invoice_data_save_invoice( $payment_meta ) {
    if ( isset( $_POST['bpmj_edd_invoice_data_receipt_nip'] ) ) {
        $payment_meta[ 'bpmj_edd_invoice_data_receipt_nip' ] = sanitize_text_field( $_POST['bpmj_edd_invoice_data_receipt_nip'] );
    }

	if ( isset( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) && $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] == 1 ) {

		$payment_meta[ 'bpmj_edd_invoice_check' ]             = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_type' ]              = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_type' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_type' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_person_name' ]       = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_person_name' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_person_name' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_company_name' ]      = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_company_name' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_company_name' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_nip' ]               = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_nip' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_nip' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_street' ]            = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_street' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_street' ] ) : '';
        $payment_meta[ 'bpmj_edd_invoice_building_number' ]    = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_building_number' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_building_number' ] ) : '';
        $payment_meta[ 'bpmj_edd_invoice_apartment_number' ]   = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_apartment_number' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_apartment_number' ] ) : '';
        $payment_meta[ 'bpmj_edd_invoice_postcode' ]          = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_postcode' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_postcode' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_city' ]              = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_city' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_city' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_country' ]			  = isset( $_POST[ 'billing_country' ] ) ? sanitize_text_field( $_POST[ 'billing_country' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] = isset( $_POST[ 'bpmj_edd_invoice_data_receiver_info_set' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_receiver_info_set' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_receiver_name' ]     = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_name' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_name' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_receiver_street' ]   = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_street' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_street' ] ) : '';
        $payment_meta[ 'bpmj_edd_invoice_receiver_building_number' ]   = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_building_number' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_building_number' ] ) : '';
        $payment_meta[ 'bpmj_edd_invoice_receiver_apartment_number' ]   = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_apartment_number' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_apartment_number' ] ) : '';
        $payment_meta[ 'bpmj_edd_invoice_receiver_postcode' ] = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_postcode' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_postcode' ] ) : '';
		$payment_meta[ 'bpmj_edd_invoice_receiver_city' ]     = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_city' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_receiver_city' ] ) : '';

		if ( empty( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) ) {
			$payment_meta[ 'bpmj_edd_invoice_receiver_name' ]     = '';
			$payment_meta[ 'bpmj_edd_invoice_receiver_street' ]   = '';
            $payment_meta[ 'bpmj_edd_invoice_receiver_building_number' ]   = '';
            $payment_meta[ 'bpmj_edd_invoice_receiver_apartment_number' ]   = '';
			$payment_meta[ 'bpmj_edd_invoice_receiver_postcode' ] = '';
			$payment_meta[ 'bpmj_edd_invoice_receiver_city' ]     = '';
		}

		$user_id = get_current_user_id();
		if ( $user_id ) {
			$user_invoice_data = array(
				'check'             => $payment_meta[ 'bpmj_edd_invoice_check' ],
				'type'              => $payment_meta[ 'bpmj_edd_invoice_type' ],
				'person_name'       => $payment_meta[ 'bpmj_edd_invoice_person_name' ],
				'company_name'      => $payment_meta[ 'bpmj_edd_invoice_company_name' ],
				'nip'               => $payment_meta[ 'bpmj_edd_invoice_nip' ],
				'street'            => $payment_meta[ 'bpmj_edd_invoice_street' ],
                'building_number'   => $payment_meta[ 'bpmj_edd_invoice_building_number' ],
                'apartment_number'  => $payment_meta[ 'bpmj_edd_invoice_apartment_number' ],
				'postcode'          => $payment_meta[ 'bpmj_edd_invoice_postcode' ],
				'city'              => $payment_meta[ 'bpmj_edd_invoice_city' ],
				'country'		    => $payment_meta[ 'bpmj_edd_invoice_country' ],
				'receiver_set'      => $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ],
				'receiver_name'     => $payment_meta[ 'bpmj_edd_invoice_receiver_name' ],
				'receiver_street'   => $payment_meta[ 'bpmj_edd_invoice_receiver_street' ],
				'receiver_postcode' => $payment_meta[ 'bpmj_edd_invoice_receiver_postcode' ],
				'receiver_city'     => $payment_meta[ 'bpmj_edd_invoice_receiver_city' ],
			);
			update_user_meta( $user_id, 'bpmj_edd_invoice_data', $user_invoice_data );
		}
	}

	return $payment_meta;
}

add_filter( 'edd_payment_meta', 'bpmj_edd_invoice_data_save_invoice' );

/**
 * Sprawdza dane potrzebne do faktury lub rachunku
 *
 * @param array $valid_data
 * @param array $data
 */
function bpmj_edd_invoice_data_validate_invoice( $valid_data, $data ) {

	// Sprawdzaj dane tylko wtedy, gdy zaznaczony jest checkbox z chęcią otrzymania faktury lub rachunku (ewentulnie, gdy włączona jest opcja wymuszenia)
	if ( isset( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) && $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] == 1 ) {

		$is_company = ( 'person' !== $_POST[ 'bpmj_edd_invoice_data_invoice_type' ] ) ? true : false;
		$country = !empty( $data[ 'billing_country' ] ) ? $data[ 'billing_country' ] : null;

		// Sprawdzamy imię i nazwisko
		if ( ! $is_company && (
				empty( $data[ 'bpmj_edd_invoice_data_invoice_person_name' ] ) ||
				strlen( $data[ 'bpmj_edd_invoice_data_invoice_person_name' ] ) < 3 ||
				false === strpos( trim( $data[ 'bpmj_edd_invoice_data_invoice_person_name' ] ), ' ' ) // check if there are at least two words in the name
			) ) {
			edd_set_error( 'edd_invoice_data_invalid_person', __( 'Please enter a name for the invoice', 'bpmj-edd-invoice-data' ) );
		}

		// Sprawdzamy nazwę firmy
		if ( $is_company && empty( $data[ 'bpmj_edd_invoice_data_invoice_company_name' ] ) ) {
			edd_set_error( 'bpmj_edd_invoice_data_invalid_company', __( 'Please enter a company name', 'bpmj-edd-invoice-data' ) );
		}

		$taxid_verification_disabled = bpmj_edd_invoice_data_get_cb_setting( 'edd_id_disable_taxid_verification' );
		// Sprawdzamy NIP
		if ( $is_company && empty( $data[ 'bpmj_edd_invoice_data_invoice_nip' ] ) ) {
			edd_set_error( 'bpmj_edd_invoice_data_invalid_nip', __( 'Please enter a Tax ID', 'bpmj-edd-invoice-data' ) );
		} else if ( $is_company && ! $taxid_verification_disabled && ('PL' === $country || empty($country)) && ! bpmj_edd_invoice_data_check_nip( $data[ 'bpmj_edd_invoice_data_invoice_nip' ] ) ) { // TODO: VAT ID
			edd_set_error( 'bpmj_edd_invoice_data_invalid_nip_format', __( 'Tax ID number is invalid', 'bpmj-edd-invoice-data' ) );
		}
		// Sprawdzamy nazwę ulicy
		if ( empty( $data[ 'bpmj_edd_invoice_data_invoice_street' ] ) || strlen( $data[ 'bpmj_edd_invoice_data_invoice_street' ] ) < 3 ) {
            Cart_API_Static_Helper::set_error('bpmj_edd_invoice_data_invalid_street', 'orders.invoice_data.validate.street');
		}

        if (!preg_match('/[0-9a-z]+/', $data['bpmj_edd_invoice_data_invoice_building_number'])) {
            Cart_API_Static_Helper::set_error('bpmj_edd_invoice_data_invalid_building_number', 'orders.invoice_data.validate.building_number');
        }

        if (!empty($data['bpmj_edd_invoice_data_invoice_apartment_number'])) {
            if (!preg_match('/[0-9a-z]+/', $data['bpmj_edd_invoice_data_invoice_apartment_number'])) {
                Cart_API_Static_Helper::set_error(
                    'bpmj_edd_invoice_data_invalid_apartment_number',
                    'orders.invoice_data.validate.apartment_number'
                );
            }
        }

		// Sprawdzamy kod pocztowy
        $is_country_pl = !isset($data[ 'billing_country' ]) || $data[ 'billing_country' ]=='PL';

		if ( empty( $data[ 'bpmj_edd_invoice_data_invoice_postcode' ] ) ||
            strlen( $data[ 'bpmj_edd_invoice_data_invoice_postcode' ] ) < 5 &&  $is_country_pl
            ) {
			edd_set_error( 'bpmj_edd_invoice_data_postcode', __( 'Please enter a postal code', 'bpmj-edd-invoice-data' ) );
		}

		// Sprawdzamy miejscowość
		if ( empty( $data[ 'bpmj_edd_invoice_data_invoice_city' ] ) || strlen( $data[ 'bpmj_edd_invoice_data_invoice_city' ] ) < 3 ) {
			edd_set_error( 'bpmj_edd_invoice_data_city', __( 'Please enter a city name', 'bpmj-edd-invoice-data' ) );
		}

		// srawdzamy kraj
		$country_enabled = apply_filters('eddid_form_show_country', false);
		if ( $country_enabled && empty( $data[ 'billing_country' ] ) ) {
			edd_set_error( 'billing_country', __( 'Please select your country', 'bpmj-edd-invoice-data' ) );
		}
	}
}

add_action( 'edd_checkout_error_checks', 'bpmj_edd_invoice_data_validate_invoice', 10, 2 );

/**
 * Wyswietla dane potrzebne do wystawienia faktury lub  rachunku
 *
 * Dane widoczne są w metaboxie dane kupującego w historii płatności
 *
 * @param array $payment_meta
 * @param array $user_info
 */
function bpmj_edd_invoice_data_show_invoice_data( $payment_meta, $user_info ) {

	// Wyświetla dane do faktury lub rachunku tylko wtedy, gdy klient wybrał je podczas składania zamówienia

	if ( ! isset( $payment_meta[ 'bpmj_edd_invoice_type' ] ) ) {
		$payment_meta[ 'bpmj_edd_invoice_type' ] = 'person';
	}
	$is_company = ( 'person' !== $payment_meta[ 'bpmj_edd_invoice_type' ] ) ? true : false;
	$is_person_with_nip = false;
	if ( ! $is_company && isset( $payment_meta['bpmj_edd_invoice_data_receipt_nip'] ) && $payment_meta['bpmj_edd_invoice_data_receipt_nip'] )
	    $is_person_with_nip = true;

	$person_name             = isset( $payment_meta[ 'bpmj_edd_invoice_person_name' ] ) ? $payment_meta[ 'bpmj_edd_invoice_person_name' ] : '';
	$company_name            = isset( $payment_meta[ 'bpmj_edd_invoice_company_name' ] ) ? $payment_meta[ 'bpmj_edd_invoice_company_name' ] : '';
	$nip                     = isset( $payment_meta[ 'bpmj_edd_invoice_nip' ] ) ? $payment_meta[ 'bpmj_edd_invoice_nip' ] : '';
	$street                  = isset( $payment_meta[ 'bpmj_edd_invoice_street' ] ) ? $payment_meta[ 'bpmj_edd_invoice_street' ] : '';
    $building_number                  = isset( $payment_meta[ 'bpmj_edd_invoice_building_number' ] ) ? $payment_meta[ 'bpmj_edd_invoice_building_number' ] : '';
    $apartment_number                  = isset( $payment_meta[ 'bpmj_edd_invoice_apartment_number' ] ) ? $payment_meta[ 'bpmj_edd_invoice_apartment_number' ] : '';
	$postcode                = isset( $payment_meta[ 'bpmj_edd_invoice_postcode' ] ) ? $payment_meta[ 'bpmj_edd_invoice_postcode' ] : '';
	$city                    = isset( $payment_meta[ 'bpmj_edd_invoice_city' ] ) ? $payment_meta[ 'bpmj_edd_invoice_city' ] : '';
	$different_receiver_data = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) ? ! empty( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) : false;

	$receiver_name     = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_name' ] ) ? $payment_meta[ 'bpmj_edd_invoice_receiver_name' ] : '';
	$receiver_street   = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_street' ] ) ? $payment_meta[ 'bpmj_edd_invoice_receiver_street' ] : '';
    $receiver_building_number   = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_building_number' ] ) ? $payment_meta[ 'bpmj_edd_invoice_receiver_building_number' ] : '';
    $receiver_apartment_number   = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_apartment_number' ] ) ? $payment_meta[ 'bpmj_edd_invoice_receiver_apartment_number' ] : '';
	$receiver_postcode = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_postcode' ] ) ? $payment_meta[ 'bpmj_edd_invoice_receiver_postcode' ] : '';
	$receiver_city     = isset( $payment_meta[ 'bpmj_edd_invoice_receiver_city' ] ) ? $payment_meta[ 'bpmj_edd_invoice_receiver_city' ] : '';

	echo '<div id="bpmj-eddid-view"><h4>' . __( 'Invoice data', 'bpmj-edd-invoice-data' ) . ':</h4><ul>';
	if ( isset( $payment_meta[ 'bpmj_edd_invoice_company_name' ] ) || isset( $payment_meta[ 'bpmj_edd_invoice_person_name' ] ) ) {
		if ( $is_company ) {
			echo '<li><b>' . __( 'Type:', 'bpmj-edd-invoice-data' ) . '</b> ' . __( 'Company / Organization', 'bpmj-edd-invoice-data' ) . '</li>';
			echo '<li><b>' . __( 'Company name:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $company_name ? $company_name : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
			echo '<li><b>' . __( 'Tax ID:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $nip ? $nip : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
		} else {
			echo '<li><b>' . __( 'Type:', 'bpmj-edd-invoice-data' ) . '</b> ' . __( 'Individual', 'bpmj-edd-invoice-data' ) . '</li>';
			echo '<li><b>' . __( 'Name:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $person_name ? $person_name : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
		}
		echo '<li><b>' . __( 'Street:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $street ? $street : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
        echo '<li><b>' . Translator_Static_Helper::translate('orders.invoice_data.building_number') . '</b> ' . ( $building_number ? $building_number : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
        echo '<li><b>' . Translator_Static_Helper::translate('orders.invoice_data.apartment_number') . '</b> ' . ( $apartment_number ? $apartment_number : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';

        echo '<li><b>' . __( 'Postal Code:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $postcode ? $postcode : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
		echo '<li><b>' . __( 'City:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $city ? $city : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
		echo '</ul>';
		if ( $different_receiver_data || bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_receiver' ) ) {
			echo '<h4>' . __( 'Receiver\'s data', 'bpmj-edd-invoice-data' ) . ':</h4><ul>';
			if ( $different_receiver_data ) {
				echo '<li><b>' . __( 'Company or individual name:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $receiver_name ? $receiver_name : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
				echo '<li><b>' . __( 'Street:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $receiver_street ? $receiver_street : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
                echo '<li><b>' . Translator_Static_Helper::translate('orders.invoice_data.building_number') . '</b> ' . ( $receiver_building_number ? $receiver_building_number : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
                echo '<li><b>' . Translator_Static_Helper::translate('orders.invoice_data.apartment_number') . '</b> ' . ( $receiver_apartment_number ? $receiver_apartment_number : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';

                echo '<li><b>' . __( 'Postal Code:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $receiver_postcode ? $receiver_postcode : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
				echo '<li><b>' . __( 'City:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $receiver_city ? $receiver_city : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
			} else {
				echo '<li>' . __( 'Not specified', 'bpmj-edd-invoice-data' ) . '</li>';
			}
			echo '</ul>';
		}
		echo '<a href="" id="bpmj-eddid-edit-switch" class="button button-secondary">' . __( 'Edit data', 'bpmj-edd-invoice-data' ) . '</a>';
	} else if ( $is_person_with_nip ) {
        $nip = isset( $payment_meta[ 'bpmj_edd_invoice_data_receipt_nip' ] ) ? $payment_meta[ 'bpmj_edd_invoice_data_receipt_nip' ] : '';

        echo '<li><b>' . __( 'Type:', 'bpmj-edd-invoice-data' ) . '</b> ' . __( 'Individual (with NIP - for receipts only)', 'bpmj-edd-invoice-data' ) . '</li>';
        echo '<li><b>' . __( 'NIP:', 'bpmj-edd-invoice-data' ) . '</b> ' . ( $nip ? $nip : __( 'none', 'bpmj-edd-invoice-data' ) ) . '</li>';
        echo '</ul><a href="" id="bpmj-eddid-edit-switch" class="button button-secondary">' . __( 'Add invoice data', 'bpmj-edd-invoice-data' ) . '</a>';
    } else {
		echo '</ul><a href="" id="bpmj-eddid-edit-switch" class="button button-secondary">' . __( 'Add invoice data', 'bpmj-edd-invoice-data' ) . '</a>';
	}
	echo '</div>';
	?>
    <div id="bpmj-eddid-edit" style="display: none;">
        <input type="hidden" name="bpmj_eddid_invoice_save" id="bpmj_eddid_invoice_save" value=""/>
        <p id="bpmj-eddid-edit-type">
            <strong><?php _e( 'Type:', 'bpmj-edd-invoice-data' ); ?></strong><br/>
            <label><input type="radio" name="bpmj_eddid_invoice_type"
                          value="company" <?php checked( $payment_meta[ 'bpmj_edd_invoice_type' ], 'company' ); ?>
                          data-initial="<?php echo 'company' === $payment_meta[ 'bpmj_edd_invoice_type' ] ? 'checked' : ''; ?>"/> <?php _e( 'Company / Organization', 'bpmj-edd-invoice-data' ); ?>
            </label>
            <label><input type="radio" name="bpmj_eddid_invoice_type"
                          value="person" <?php echo ( ! $is_company && ! $is_person_with_nip ) ? 'checked="checked"' : ''; ?>
                          data-initial="<?php echo ( ! $is_company && ! $is_person_with_nip ) ? 'checked' : ''; ?>"/> <?php _e( 'Individual', 'bpmj-edd-invoice-data' ); ?>
            </label>
            <label><input type="radio" name="bpmj_eddid_invoice_type"
                          value="person" <?php echo ( ! $is_company && $is_person_with_nip ) ? 'checked="checked"' : ''; ?>
                          data-type="nip_for_receipts"
                          data-initial="<?php echo ( ! $is_company && $is_person_with_nip ) ? 'checked' : ''; ?>"/> <?php _e( 'Individual (with NIP - for receipts only)', 'bpmj-edd-invoice-data' ); ?>
            </label>
        </p>
        <div id="bpmj-eddid-edit-company" style="<?php echo $is_company ? '' : 'display: none;'; ?>">
            <p>
                <label
                        for="bpmj_edd_invoice_company_name"><strong><?php _e( 'Company name:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                <input type="text" name="bpmj_edd_invoice_company_name" id="bpmj_edd_invoice_company_name"
                       value="<?php echo esc_attr( $company_name ); ?>"
                       data-initial="<?php echo esc_attr( $company_name ); ?>"/>
            </p>
            <p>
                <label
                        for="bpmj_edd_invoice_nip"><strong><?php _e( 'Tax ID:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                <input type="text" name="bpmj_edd_invoice_nip" id="bpmj_edd_invoice_nip"
                       value="<?php echo esc_attr( $nip ); ?>"
                       data-initial="<?php echo esc_attr( $nip ); ?>"/>
            </p>
        </div>
        <div id="bpmj-eddid-edit-person" style="<?php echo $is_company ? 'display: none;' : ''; ?>">
            <p>
                <label
                        for="bpmj_edd_invoice_person_name"><strong><?php _e( 'Name:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                <input type="text" name="bpmj_edd_invoice_person_name" id="bpmj_edd_invoice_person_name"
                       value="<?php echo esc_attr( $person_name ); ?>"
                       data-initial="<?php echo esc_attr( $person_name ); ?>"/>
            </p>
        </div>
        <p>
            <label
                    for="bpmj_edd_invoice_street"><strong><?php _e( 'Street:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
            <input type="text" name="bpmj_edd_invoice_street" id="bpmj_edd_invoice_street"
                   value="<?php echo esc_attr( $street ); ?>"
                   data-initial="<?php echo esc_attr( $street ); ?>"/>
        </p>

        <p>
            <label
                    for="bpmj_edd_invoice_building_number"><strong><?= Translator_Static_Helper::translate('orders.invoice_data.building_number') ?></strong><br/></label>
            <input type="text" name="bpmj_edd_invoice_building_number" id="bpmj_edd_invoice_building_number"
                   value="<?php echo esc_attr( $building_number ); ?>"
                   data-initial="<?php echo esc_attr( $building_number ); ?>"/>
        </p>

        <p>
            <label
                    for="bpmj_edd_invoice_apartment_number"><strong><?= Translator_Static_Helper::translate('orders.invoice_data.apartment_number') ?></strong><br/></label>
            <input type="text" name="bpmj_edd_invoice_apartment_number" id="bpmj_edd_invoice_apartment_number"
                   value="<?php echo esc_attr( $apartment_number ); ?>"
                   data-initial="<?php echo esc_attr( $apartment_number ); ?>"/>
        </p>

        <p>
            <label
                    for="bpmj_edd_invoice_postcode"><strong><?php _e( 'Postal Code:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
            <input type="text" name="bpmj_edd_invoice_postcode" id="bpmj_edd_invoice_postcode"
                   value="<?php echo esc_attr( $postcode ); ?>"
                   data-initial="<?php echo esc_attr( $postcode ); ?>"/>
        </p>
        <p>
            <label
                    for="bpmj_edd_invoice_city"><strong><?php _e( 'City:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
            <input type="text" name="bpmj_edd_invoice_city" id="bpmj_edd_invoice_city"
                   value="<?php echo esc_attr( $city ); ?>"
                   data-initial="<?php echo esc_attr( $city ); ?>"/>
        </p>
        <div id="bpmj-eddid-edit-person-for-receipts">
            <p>
                <label
                        for="bpmj_edd_invoice_nip"><strong><?php _e( 'Tax ID:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                <input type="text" name="bpmj_edd_invoice_nip" id="bpmj_edd_invoice_nip"
                       value="<?php echo esc_attr( $nip ); ?>"
                       data-initial="<?php echo esc_attr( $nip ); ?>"/>
            </p>
        </div>
        <div id="bpmj-eddid-edit-receiver">
            <p style="<?php echo empty( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) && ! bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_receiver' ) ? 'display:none;' : ''; ?>">
                <input type="hidden" name="bpmj_edd_invoice_receiver_info_set" value="1"/>
                <label>
                    <input type="checkbox" id="bpmj-edd-invoice-receiver-info-set"
                           name="bpmj_edd_invoice_receiver_info_set"
                           value="0"
						<?php checked( empty( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) ); ?>
                           data-initial="<?php echo empty( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) ? 'checked' : ''; ?>"/>
					<?php _e( 'The same buyer and receiver', 'bpmj-edd-invoice-data' ); ?>
                </label>
            </p>
            <div id="bpmj-eddid-receiver-data"
                 style="<?php echo ! empty( $payment_meta[ 'bpmj_edd_invoice_receiver_info_set' ] ) ? '' : 'display:none;'; ?>">
                <p>
                    <label
                            for="bpmj_edd_invoice_receiver_name"><strong><?php _e( 'Company or individual name:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                    <input type="text" name="bpmj_edd_invoice_receiver_name"
                           id="bpmj_edd_invoice_receiver_name"
                           value="<?php echo esc_attr( $receiver_name ); ?>"
                           data-initial="<?php echo esc_attr( $receiver_name ); ?>"/>
                </p>
                <p>
                    <label
                            for="bpmj_edd_invoice_receiver_street"><strong><?php _e( 'Street:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                    <input type="text" name="bpmj_edd_invoice_receiver_street" id="bpmj_edd_invoice_receiver_street"
                           value="<?php echo esc_attr( $receiver_street ); ?>"
                           data-initial="<?php echo esc_attr( $receiver_street ); ?>"/>
                </p>

                <p>
                    <label
                            for="bpmj_edd_invoice_receiver_building_number"><strong><?= Translator_Static_Helper::translate('orders.invoice_data.building_number') ?></strong><br/></label>
                    <input type="text" name="bpmj_edd_invoice_receiver_building_number" id="bpmj_edd_invoice_receiver_building_number"
                           value="<?php echo esc_attr( $receiver_building_number ); ?>"
                           data-initial="<?php echo esc_attr( $receiver_building_number ); ?>"/>
                </p>

                <p>
                    <label
                            for="bpmj_edd_invoice_receiver_apartment_number"><strong><?= Translator_Static_Helper::translate('orders.invoice_data.apartment_number') ?></strong><br/></label>
                    <input type="text" name="bpmj_edd_invoice_receiver_apartment_number" id="bpmj_edd_invoice_receiver_apartment_number"
                           value="<?php echo esc_attr( $receiver_apartment_number ); ?>"
                           data-initial="<?php echo esc_attr( $receiver_apartment_number ); ?>"/>
                </p>

                <p>
                    <label
                            for="bpmj_edd_invoice_receiver_postcode"><strong><?php _e( 'Postal Code:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                    <input type="text" name="bpmj_edd_invoice_receiver_postcode" id="bpmj_edd_invoice_receiver_postcode"
                           value="<?php echo esc_attr( $receiver_postcode ); ?>"
                           data-initial="<?php echo esc_attr( $receiver_postcode ); ?>"/>
                </p>
                <p>
                    <label
                            for="bpmj_edd_invoice_receiver_city"><strong><?php _e( 'City:', 'bpmj-edd-invoice-data' ); ?></strong><br/></label>
                    <input type="text" name="bpmj_edd_invoice_receiver_city" id="bpmj_edd_invoice_receiver_city"
                           value="<?php echo esc_attr( $receiver_city ); ?>"
                           data-initial="<?php echo esc_attr( $receiver_city ); ?>"/>
                </p>
            </div>
        </div>
        <a href="" id="bpmj-eddid-edit-cancel"><?php _e( 'Cancel', 'bpmj-edd-invoice-data' ); ?></a>
    </div>
    <script type="text/javascript">
		jQuery( function ( $ ) {
			$( '#bpmj-eddid-edit-type' ).find( 'input' ).click( function () {
				var is_company = 'company' === $( this ).val();
				if ( is_company ) {
					$( '#bpmj-eddid-edit-company' ).show();
					$( '#bpmj-eddid-edit-receiver' ).show();
					$( '#bpmj-eddid-edit-person' ).hide();
                    $('#bpmj-eddid-edit-person-for-receipts').hide();
                    $( '#bpmj-eddid-edit > p' ).show();
				} else {
				    if ( $(this).data('type') === 'nip_for_receipts' ) {
                        $('#bpmj-eddid-edit-company').hide();
                        $('#bpmj-eddid-edit-receiver').hide();
                        $('#bpmj-eddid-edit-person').hide();
                        $('#bpmj-eddid-edit > p:not(#bpmj-eddid-edit-type').hide();
                        $('#bpmj-eddid-edit-person-for-receipts').show();
                    } else {
                        $('#bpmj-eddid-edit-company').hide();
                        $('#bpmj-eddid-edit-receiver').hide();
                        $('#bpmj-eddid-edit-person').show();
                        $('#bpmj-eddid-edit-person-for-receipts').hide();
                        $('#bpmj-eddid-edit > p').show();
                    }
				}
			} );

			$('#bpmj-eddid-edit-type input:checked').trigger('click');

			$( '#bpmj-eddid-edit-switch' ).click( function ( e ) {
				var $bpmj_eddid_edit = $( '#bpmj-eddid-edit' );
				e.preventDefault();
				$( '#bpmj_eddid_invoice_save' ).val( '1' );
				$bpmj_eddid_edit.show();
				$bpmj_eddid_edit.find( 'input[type="text"]:visible' )[ 0 ].focus();
				$( '#bpmj-eddid-view' ).hide();
			} );
			$( '#bpmj-edd-invoice-receiver-info-set' ).click( function ( e ) {
				if ( $( this ).is( ':checked' ) ) {
					$( '#bpmj-eddid-receiver-data' ).hide();
				} else {
					$( '#bpmj-eddid-receiver-data' ).show();
				}
			} );
			$( '#bpmj-eddid-edit-cancel' ).click( function ( e ) {
				var $bpmj_eddid_edit = $( '#bpmj-eddid-edit' );
				e.preventDefault();
				$bpmj_eddid_edit.find( ':input[data-initial]' ).each( function () {
					if ( 'radio' === $( this ).attr( 'type' ) || 'checkbox' === $( this ).attr( 'type' ) ) {
						if ( 'checked' === $( this ).data( 'initial' ) ) {
							$( this ).click();
						}
					} else {
						$( this ).val( $( this ).data( 'initial' ) );
					}
				} );
				$( '#bpmj_eddid_invoice_save' ).val( '' );
				$bpmj_eddid_edit.hide();
				$( '#bpmj-eddid-view' ).show();
			} );
		} );
    </script>
	<?php
}

add_action( 'edd_payment_personal_details_list', 'bpmj_edd_invoice_data_show_invoice_data', 10, 2 );

/**
 * Filtr, który dodaje skrypt ukrywający formularz z fakturą gdy wartość produktów wynosi 0zł
 *
 */
function bpmj_edd_invoice_data_hide_invoice_after_total_dis() {
	?>
    <script type="text/javascript">

		var bpmj_edd_invoice_data_total_amount = jQuery( '.edd_cart_total .edd_cart_amount' ).text();
		var bpmj_edd_invoice_data_action1 = jQuery( 'body' );

		var bpmj_edd_invoice_data_to_hide = jQuery( '.bpmj_edd_invoice_data_invoice_check, .bpmj_edd_invoice_data_invoice' );

		bpmj_edd_invoice_data_action1.mousemove( function () {

			bpmj_edd_invoice_data_total_amount = jQuery( '.edd_cart_total .edd_cart_amount' ).text();

			if ( bpmj_edd_invoice_data_total_amount.indexOf( '0.00' ) === 0 || bpmj_edd_invoice_data_total_amount.indexOf( '0,00' ) === 0 ||
			     bpmj_edd_invoice_data_total_amount.indexOf( ' 0.00' ) !== - 1 || bpmj_edd_invoice_data_total_amount.indexOf( ' 0,00' ) !== - 1 ) {
				bpmj_edd_invoice_data_to_hide.hide();
			} else {
				jQuery( '.bpmj_edd_invoice_data_invoice_check' ).show();
			}

		} );
    </script>

	<?php
}

add_action( 'edd_after_purchase_form', 'bpmj_edd_invoice_data_hide_invoice_after_total_dis' );

/**
 * @param array $required_fields
 *
 * @return array
 */
function bpmj_edd_invoice_data_unrequire_fields($required_fields) {

    $fields_to_unrequire = [
        Cart_Settings_Group::EDD_ID_HIDE_FNAME => 'edd_first',
        Cart_Settings_Group::EDD_ID_HIDE_LNAME => 'edd_last',
    ];

    foreach ($fields_to_unrequire as $option_key => $field_key) {
        if (!bpmj_edd_invoice_data_get_cb_setting($option_key)) {
            continue;
        }

        unset($required_fields[$field_key]);
    }

	return $required_fields;
}

add_filter( 'edd_purchase_form_required_fields', 'bpmj_edd_invoice_data_unrequire_fields');

/**
 * @param int $post_id
 */
function bpmj_edd_invoice_data_save_data( $post_id ) {
	if ( 'edd_payment' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST[ 'bpmj_eddid_invoice_type' ] ) || empty( $_POST[ 'bpmj_eddid_invoice_save' ] ) ) {
		return;
	}

	$invoice_data_fields = array(
		'bpmj_edd_invoice_person_name',
		'bpmj_edd_invoice_company_name',
		'bpmj_edd_invoice_nip',
		'bpmj_edd_invoice_street',
        'bpmj_edd_invoice_building_number',
        'bpmj_edd_invoice_apartment_number',
		'bpmj_edd_invoice_postcode',
		'bpmj_edd_invoice_city',
	);

	$payment_meta                            = edd_get_payment_meta( $post_id );
	$payment_meta[ 'bpmj_edd_invoice_type' ] = isset( $_POST[ 'bpmj_eddid_invoice_type' ] ) ? sanitize_text_field( $_POST[ 'bpmj_eddid_invoice_type' ] ) : '';
	$all_empty                               = true;
	foreach ( $invoice_data_fields as $field ) {
		$payment_meta[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( $_POST[ $field ] ) : '';
		if ( ! empty( $payment_meta[ $field ] ) ) {
			$all_empty = false;
		}
	}

	$receiver_data_fields = array(
		'bpmj_edd_invoice_receiver_name',
		'bpmj_edd_invoice_receiver_street',
        'bpmj_edd_invoice_receiver_building_number',
        'bpmj_edd_invoice_receiver_apartment_number',
		'bpmj_edd_invoice_receiver_postcode',
		'bpmj_edd_invoice_receiver_city',
		'bpmj_edd_invoice_receiver_info_set',
	);

	if ( empty( $_POST[ 'bpmj_edd_invoice_receiver_info_set' ] ) ) {
		foreach ( $receiver_data_fields as $field ) {
			unset( $payment_meta[ $field ] );
		}
	} else {
		foreach ( $receiver_data_fields as $field ) {
			$payment_meta[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( $_POST[ $field ] ) : '';
		}
	}

	if ( $payment_meta[ 'bpmj_edd_invoice_type' ] && ( $payment_meta[ 'bpmj_edd_invoice_person_name' ] || $payment_meta[ 'bpmj_edd_invoice_company_name' ] ) ) {
		$payment_meta[ 'bpmj_edd_invoice_check' ] = '1';
	} else {
		unset( $payment_meta[ 'bpmj_edd_invoice_check' ] );
		if ( $all_empty ) {
			foreach ( array_merge( $invoice_data_fields, $receiver_data_fields ) as $field ) {
				unset( $payment_meta[ $field ] );
			}
		}
	}

	edd_update_payment_meta( $post_id, '_edd_payment_meta', $payment_meta );
}

add_action( 'save_post', 'bpmj_edd_invoice_data_save_data' );

function bpmj_eddcm_add_invoice_data_to_cookie() {
    if ( isset( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) && $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] == 1 ) {
        $payment_meta[ 'check' ]		 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_check' ] ) : '';
        $payment_meta[ 'type' ]		 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_type' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_type' ] ) : '';
        $payment_meta[ 'person_name' ]	 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_person_name' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_person_name' ] ) : '';
        $payment_meta[ 'company_name' ] = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_company_name' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_company_name' ] ) : '';
        $payment_meta[ 'nip' ]			 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_nip' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_nip' ] ) : '';
        $payment_meta[ 'street' ]		 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_street' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_street' ] ) : '';
        $payment_meta[ 'building_number' ]		 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_building_number' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_building_number' ] ) : '';
        $payment_meta[ 'apartment_number' ]		 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_apartment_number' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_apartment_number' ] ) : '';
        $payment_meta[ 'postcode' ]	 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_postcode' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_postcode' ] ) : '';
        $payment_meta[ 'city' ]		 = isset( $_POST[ 'bpmj_edd_invoice_data_invoice_city' ] ) ? sanitize_text_field( $_POST[ 'bpmj_edd_invoice_data_invoice_city' ] ) : '';
		$payment_meta[ 'country' ]		 = isset( $_POST[ 'billing_country' ] ) ? sanitize_text_field( $_POST[ 'billing_country' ] ) : '';

		setcookie('edd_purchase_form_data', json_encode( $payment_meta, JSON_UNESCAPED_UNICODE ), time() + (60 * 60 * 24 * 365));
    }
}

add_action( 'init', 'bpmj_eddcm_add_invoice_data_to_cookie', 1 );

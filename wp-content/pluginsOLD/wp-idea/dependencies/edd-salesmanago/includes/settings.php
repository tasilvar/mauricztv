<?php
/**
 * Generowanie widoku ustawień EDD->Dodatki
 */


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;


/**
 * Dodanie opcji wysyłki do ustawień
 *
 * @return array of settings.
 */
function bpmj_edd_sm_add_settings( $settings ) {
	$admin_settings = array(
		array(
			'id'  => 'salesmanago',
			'name'  => '<strong id="homepay_area">' . __( 'Ustawienia<br>EDD SALESmanago', BPMJ_EDD_SM_DOMAIN ) . '</strong>',
			'desc'  => __( 'Zarządzaj ustawieniami dodatku EDD SALESmanago', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'header'
		),
		array(
			'id'  => 'salesmanago_owner',
			'name'  => __( 'Adres email konta SALESmanago', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Adres email na który zarejestrowane jest Twoje konto SAELESmanago.', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_endpoint',
			'name'  => __( 'Endpoint', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Indentyfikator Twojego serwera (endpoint) z panelu SALESmanago (Ustawienia->Integracja).', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_client_id',
			'name'  => __( 'ID Klienta', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Twoje ID Klienta z panelu SALESmanago (Ustawienia->Integracja).', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_api_secret',
			'name'  => __( 'API Secret', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Kod API Secret z panelu SALESmanago (Ustawienia->Integracja).', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_tracking_code',
			'name'  => __( 'Kod śledzący', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Zaznacz aby umieścić kod śledzący.', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'checkbox',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_checkout_mode',
			'name'  => __( 'Pole zapisu', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Zaznacz aby pole zapisu zostało pokazane.', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'checkbox',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_checkout_label',
			'name'  => __( 'Opis pola zapisu', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Ten tekst wyświetli się obok opcji zapisu w podsumowaniu koszyka.', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'  => 'salesmanago_tags',
			'name'  => __( 'Tagi dopisywane do użytkownika', BPMJ_EDD_SM_DOMAIN ),
			'desc'  => __( 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po każdym zakupie.<br>Tagi te będą dodane tylko jeżeli będzie wyświetlone i zaznaczone pole zapisu w podsumowaniu koszyka.<br>Tagi produktów będą dodane niezależnie.', BPMJ_EDD_SM_DOMAIN ),
			'type'  => 'salesmanago_tags',
			'size'  => 'regular'
		),
	);

	return array_merge( $settings, $admin_settings );
}
add_filter( 'edd_settings_extensions', 'bpmj_edd_sm_add_settings' );




function edd_salesmanago_tags_callback( $args ) {
?>
	<input name="edd_settings[bpmj_eddsm_salesmanago_tags]" id="salesmanago-tags" value="<?php echo edd_get_option( 'bpmj_eddsm_salesmanago_tags', '' ); ?>" />
	<label for="edd_settings[bpmj_eddsm_salesmanago_tags]"><?php echo $args[ 'desc' ]; ?></label>
<?php
}

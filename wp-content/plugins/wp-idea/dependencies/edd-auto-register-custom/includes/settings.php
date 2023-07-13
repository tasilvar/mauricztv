<?php

/**
 * Dodaje formularz z ustawieniami
 * EDD -> ustawienia -> Extensions
 */
function bpmj_edd_auto_register_custom_add_settings( $settings ) {

	global $edd_options;

	$default_email_body = "Dzień dobry {firstname},\n\n";
	$default_email_body .= "Twoje zamówienie zostało przyjęte.\n\n";
	$default_email_body .= "Utworzyliśmy dla Ciebie konto na platformie " . get_option( 'blogname' ) . ". Oto Twoje dane logowania:\n\n";
	$default_email_body .= "Login: {login}\n";
	$default_email_body .= "Hasło: {password}\n\n";
	$default_email_body .= "-- \n";
	$default_email_body .= "Wiadomość wygenerowana automatycznie.\n";
	$default_email_body .= get_option( 'siteurl' ) . "\r\n";

	$one_item_settings = array(
		array(
			'id'	 => 'bplmj_edd_arc_settings',
			'name'	 => '<strong>' . __( 'Auto Register Custom', 'edd-auto-register-custom' ) . '</strong>',
			'type'	 => 'header'
		),
		array(
			'id'	 => 'bpmj_edd_arc_subject',
			'name'	 => __( 'Tytuł wiadomości wysyłanej do użytkownika', 'edd-auto-register-custom' ),
			'type'	 => 'text',
			'size'	 => 'regular',
			'std'	 => 'Twoje dane logowania do ' . get_option( 'blogname' )
		),
		array(
			'id'	 => 'bpmj_edd_arc_content',
			'name'	 => __( 'Treść wiadomości wysyłanej do użytkownika', 'edd-auto-register-custom' ),
			'type'	 => 'rich_editor',
			'std'	 => $default_email_body
		)
	);


	return array_merge( $settings, $one_item_settings );
}

add_filter( 'edd_settings_extensions', 'bpmj_edd_auto_register_custom_add_settings' );

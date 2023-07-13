<?php

function edd_payu_return_url_callback( $args ) {
	$html = '<input type="text" class="payu-return-url regular-text" id="edd_settings[' . edd_sanitize_key( $args[ 'id' ] ) . ']" value="' . esc_attr( $args[ 'value' ] ) . '" readonly="readonly"/>';
	$html .= '<label for="edd_settings[' . edd_sanitize_key( $args[ 'id' ] ) . ']"> ' . wp_kses_post( $args[ 'desc' ] ) . '</label>';

	echo $html;
}
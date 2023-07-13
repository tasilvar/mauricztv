<?php

/*
 * Funkcje związane z kursami
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

// usunięcie "krzaków"
function bpmj_wpwf_remove_ent( $in, $replace ) {
	$entArr = array( '&#8222;', '&#8221;', '&quot;' );
	return html_entity_decode( str_replace( $entArr, $replace, $in ), ENT_COMPAT, get_bloginfo( 'charset' ) );
}

// kodowanie html
function bpmj_wpwf_htmlentities( $in ) {
	return htmlentities( $in, ENT_COMPAT, get_bloginfo( 'charset' ) );
}

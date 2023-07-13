<?php

/*
 * Rejestracja menu administratora
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if (!defined('ABSPATH'))
    exit;


/*
 * Tworzy menu
 */

function bpmj_wpifirma_add_options_link() {

    add_submenu_page('edit.php?post_type=bpmj_wp_ifirma', __('Ustawienia', 'bpmj_wpifirma'), __('Ustawienia', 'bpmj_wpifirma'), 'manage_options', 'bpmj_wpifirma_options', 'bpmj_wpifirma_options_page');
}

add_action('admin_menu', 'bpmj_wpifirma_add_options_link', 10);

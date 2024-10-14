<?php

/*
 * Wszystkie akcje użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if (!defined('ABSPATH'))
    exit;


// Wykonuje Cron Hook
add_action('bpmj_wpifirma_cron', 'bpmj_wpifirma_call_cron');

function bpmj_wpifirma_call_cron() {

    require_once BPMJ_WPIFIRMA_DIR . 'includes/cron.php';
}

/*
 * Ukrywa z typu posta "bpmj_wp_ifirma" przycisk dodaj nowy
 */

function bpmj_wpifirma_custom_post_ui() {

    if (isset($_GET['post_type']) && $_GET['post_type'] === 'bpmj_wp_ifirma' ) {

        echo '<style type="text/css">
    .add-new-h2, .view-switch {display:none;}
    </style>';
    }
}

add_action('admin_head', 'bpmj_wpifirma_custom_post_ui');
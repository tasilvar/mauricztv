<?php


/**
 * Dodaje formularz z opcjami dotpay w ustawieniach EDD
 * Dodaje następujące opcje widoczne w: Karta ustawień |  EDD -> ustawienia -> Bramki płatności
  /**********************************
  1. - ID konta dotpay
  2. - PIN konta dotpay
  3. - Ustawienie Online Transfer
 * ******************************** */
function bpmj_dot_edd_add_settings($settings) {

    $sample_gateway_settings = array(
        array(
            'id' => 'dotpay_gateway_settings',
            'name' => '<strong id="dotpay_area" style="margin-top:40px;display:inline-block;font-size:17px;">' . __('Ustawienia Dotpay', 'bpmj_dot_edd') . '<hr /></strong>',
            'desc' => __('Zarządzaj ustawieniami bramki Dotpay', 'bpmj_dot_edd'),
            'type' => 'header'
        ),
        array(
            'id' => 'dotpay_id',
            'name' => __('Identyfikator Dotpay', 'bpmj_dot_edd'),
            'desc' => __('Wprowadź Twój identyfikator z serwisu Dotpay', 'bpmj_dot_edd'),
            'type' => 'text',
            'size' => 'regular'
        ),
        array(
            'id' => 'dotpay_pin',
            'name' => __('PIN Dotpay', 'bpmj_dot_edd'),
            'desc' => __('Wprowadź Twój PIN z serwisu Dotpay', 'bpmj_dot_edd'),
            'type' => 'text',
            'size' => 'regular'
        ),
        array(
            'id' => 'dotpay_onlinetransfer',
            'name' => __('Płatności tylko w czasie rzeczywistym', 'bpmj_dot_edd'),
            'desc' => __('Jeżeli nie będzie można zaksięgować płatności w czasie rzeczywisty, kanał płatności Dotpay nie będzie aktywny.', 'bpmj_dot_edd'),
            'type' => 'checkbox',
            'size' => 'regular'
        )
    );

    return array_merge($settings, $sample_gateway_settings);
}

add_filter('edd_settings_gateways', 'bpmj_dot_edd_add_settings');


/**  
 * Funckja ładuje skrypt odpowiedzialny za ukrywanie i pokazywanie ustawień Dotpay
 * 
 */
function bpmj_dot_edd_load_admin_scripts() {
        if(isset($_GET['tab']) && $_GET['tab'] == 'gateways')
       wp_enqueue_script( 'bpmj_dot_edd_setting_scripts', BPMJ_DOT_EDD_URL . 'assets/js/admin-settings.js' );
}
add_action( 'admin_enqueue_scripts', 'bpmj_dot_edd_load_admin_scripts' );

?>
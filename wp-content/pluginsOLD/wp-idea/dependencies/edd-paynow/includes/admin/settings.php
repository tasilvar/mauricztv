<?php

function bpmj_paynow_edd_add_settings($settings) {
    $sample_gateway_settings = [
        [
            'id' => 'paynow_gateway_settings',
            'name' => '<strong id="paynow_area" style="margin-top:40px;display:inline-block;font-size:17px;">' . __('Ustawienia Paynow', 'bpmj_dot_edd') . '<hr /></strong>',
            'desc' => __( 'Manage Paynow settings', 'bpmj_paynow_edd' ),
            'type' => 'header',
        ],
        [
            'id' => 'paynow_access_key',
            'name' => __('Access key to API', 'bpmj_dot_edd'),
            'type' => 'text',
            'size' => 'regular',
        ],
        [
            'id' => 'paynow_signature_key',
            'name' => __('Signature key to API', 'bpmj_dot_edd'),
            'type' => 'text',
            'size' => 'regular',
        ],
        [
            'id' => 'paynow_environment',
            'name' => __('Paynow environment', 'bpmj_dot_edd'),
            'desc'    => __( 'Select Paynow environment', 'bpmj_dot_edd' ),
            'type' => 'text',
            'size' => 'regular',
        ],
    ];

    return array_merge($settings, $sample_gateway_settings);
}

add_filter('edd_settings_gateways', 'bpmj_paynow_edd_add_settings');

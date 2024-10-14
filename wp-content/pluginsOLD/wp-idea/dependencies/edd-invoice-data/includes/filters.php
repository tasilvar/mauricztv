<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bpmj_edd_invoice_data_email_tags( $tags ) {
	$tags = array_merge( $tags, array(
		array(
			'tag'         => 'invoice_type',
			'description' => __( 'Invoice type', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_type',
		),
		array(
			'tag'         => 'invoice_person_name',
			'description' => __( 'Invoice person name', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_person_name',
		),
		array(
			'tag'         => 'invoice_company_name',
			'description' => __( 'Invoice company name', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_company_name',
		),
		array(
			'tag'         => 'invoice_buyer_name',
			'description' => __( 'Invoice person or company name', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_buyer_name',
		),
		array(
			'tag'         => 'invoice_nip',
			'description' => __( 'Invoice NIP', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_nip',
		),
		array(
			'tag'         => 'invoice_street',
			'description' => __( 'Invoice street', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_street',
		),
        array(
            'tag'         => 'invoice_building_number',
            'description' => Translator_Static_Helper::translate('orders.invoice_data.building_number'),
            'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_building_number',
        ),
        array(
            'tag'         => 'invoice_apartment_number',
            'description' => Translator_Static_Helper::translate('orders.invoice_data.apartment_number'),
            'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_apartment_number',
        ),
		array(
			'tag'         => 'invoice_postcode',
			'description' => __( 'Invoice postal code', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_postcode',
		),
		array(
			'tag'         => 'invoice_city',
			'description' => __( 'Invoice city', 'bpmj-edd-invoice-data' ),
			'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_city',
		),
	) );

	if ( bpmj_edd_invoice_data_get_cb_setting( 'edd_id_enable_receiver' ) ) {
		$tags = array_merge( $tags, array(
			array(
				'tag'         => 'invoice_receiver_name',
				'description' => __( 'Invoice receiver company or individual name', 'bpmj-edd-invoice-data' ),
				'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_receiver_name',
			),
			array(
				'tag'         => 'invoice_receiver_street',
				'description' => __( 'Invoice receiver street', 'bpmj-edd-invoice-data' ),
				'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_receiver_street',
			),
            array(
                'tag'         => 'invoice_receiver_building_number',
                'description' => Translator_Static_Helper::translate('orders.invoice_data.building_number'),
                'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_receiver_building_number',
            ),
            array(
                'tag'         => 'invoice_receiver_apartment_number',
                'description' => Translator_Static_Helper::translate('orders.invoice_data.apartment_number'),
                'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_receiver_apartment_number',
            ),
			array(
				'tag'         => 'invoice_receiver_postcode',
				'description' => __( 'Invoice receiver postal code', 'bpmj-edd-invoice-data' ),
				'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_receiver_postcode',
			),
			array(
				'tag'         => 'invoice_receiver_city',
				'description' => __( 'Invoice receiver city', 'bpmj-edd-invoice-data' ),
				'function'    => 'bpmj_edd_invoice_data_email_tag_invoice_receiver_city',
			),
		) );
	}

	return $tags;
}

add_filter( 'edd_email_tags', 'bpmj_edd_invoice_data_email_tags' );

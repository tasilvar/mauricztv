<?php

namespace bpmj\wpidea\physical_product\filters;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Delivery_Address_Saver implements Interface_Initiable
{
    private const PHONE_FIELD_NAME = 'edd_delivery_address_phone';
    private const STREET_FIELD_NAME = 'edd_delivery_address_street';
    private const BUILDING_NUMBER_FIELD_NAME = 'edd_delivery_address_building_number';
    private const APARTMENT_NUMBER_FIELD_NAME = 'edd_delivery_address_apartment_number';
    private const POSTAL_CODE_FIELD_NAME = 'edd_delivery_address_postal_code';
    private const CITY_FIELD_NAME = 'edd_delivery_address_city';
    private const FIRST_NAME_FIELD_NAME = 'edd_delivery_address_first_name';
    private const LAST_NAME_FIELD_NAME = 'edd_delivery_address_last_name';
    private const COMPANY_NAME_FIELD_NAME = 'edd_delivery_address_company';

    private Current_Request $current_request;
    private Interface_Filters $filters;

    public function __construct(
        Interface_Filters $filters,
        Current_Request $current_request
    ) {
        $this->filters = $filters;
        $this->current_request = $current_request;
    }

    public function init(): void
    {
        $this->filters->add(Filter_Name::PAYMENT_META, [$this, 'save_delivery_address']);
    }

    public function save_delivery_address(array $payment_meta): array
    {
        $phone = $this->current_request->get_body_arg(self::PHONE_FIELD_NAME);
        $street = $this->current_request->get_body_arg(self::STREET_FIELD_NAME);
        $building_number = $this->current_request->get_body_arg(self::BUILDING_NUMBER_FIELD_NAME);
        $apartment_number = $this->current_request->get_body_arg(self::APARTMENT_NUMBER_FIELD_NAME);
        $postal_code = $this->current_request->get_body_arg(self::POSTAL_CODE_FIELD_NAME);
        $city = $this->current_request->get_body_arg(self::CITY_FIELD_NAME);
        $first_name = $this->current_request->get_body_arg(self::FIRST_NAME_FIELD_NAME);
        $last_name = $this->current_request->get_body_arg(self::LAST_NAME_FIELD_NAME);
        $company_name = $this->current_request->get_body_arg(self::COMPANY_NAME_FIELD_NAME);

        $payment_meta[self::PHONE_FIELD_NAME] = isset($phone) ? $this->sanitize_text_field($phone) : '';
        $payment_meta[self::STREET_FIELD_NAME] = isset($street) ? $this->sanitize_text_field($street) : '';
        $payment_meta[self::BUILDING_NUMBER_FIELD_NAME] = isset($building_number) ? $this->sanitize_text_field($building_number) : '';
        $payment_meta[self::APARTMENT_NUMBER_FIELD_NAME] = isset($apartment_number) ? $this->sanitize_text_field($apartment_number) : '';
        $payment_meta[self::POSTAL_CODE_FIELD_NAME] = isset($postal_code) ? $this->sanitize_text_field($postal_code) : '';
        $payment_meta[self::CITY_FIELD_NAME] = isset($city) ? $this->sanitize_text_field($city) : '';
        $payment_meta[self::FIRST_NAME_FIELD_NAME] = isset($first_name) ? $this->sanitize_text_field($first_name) : '';
        $payment_meta[self::LAST_NAME_FIELD_NAME] = isset($last_name) ? $this->sanitize_text_field($last_name) : '';
        $payment_meta[self::COMPANY_NAME_FIELD_NAME] = isset($company_name) ? $this->sanitize_text_field($company_name) : '';

        return $payment_meta;
    }

    private function sanitize_text_field(string $text): string
    {
        return sanitize_text_field($text);
    }
}
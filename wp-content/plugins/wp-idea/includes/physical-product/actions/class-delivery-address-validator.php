<?php

namespace bpmj\wpidea\physical_product\actions;

use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\cart\api\Cart_API;

class Delivery_Address_Validator implements Interface_Initiable
{
    private const PHONE_FIELD_NAME = 'edd_delivery_address_phone';
    private const PHONE_PATTERN = '/^\+?[0-9]{9,14}/';

    private const STREET_FIELD_NAME = 'edd_delivery_address_street';

    private const BUILDING_NUMBER_FIELD_NAME = 'edd_delivery_address_building_number';
    private const BUILDING_NUMBER_PATTERN = '/[0-9a-zA-Z]+/';

    private const APARTMENT_NUMBER_FIELD_NAME = 'edd_delivery_address_apartment_number';
    private const APARTMENT_NUMBER_PATTERN = '/[0-9a-zA-Z]+/';

    private const POSTAL_CODE_FIELD_NAME = 'edd_delivery_address_postal_code';
    private const POSTAL_CODE_PATTERN = '/^[0-9]{2}-[0-9]{3}$/';

    private const CITY_FIELD_NAME = 'edd_delivery_address_city';
    private const FIRST_NAME_FIELD_NAME = 'edd_delivery_address_first_name';
    private const LAST_NAME_FIELD_NAME = 'edd_delivery_address_last_name';

    private Physical_Products_App_Service $physical_products_app_service;
    private Interface_Actions $actions;
    private Cart_API $cart_api;

    public function __construct(
        Interface_Actions $actions,
        Cart_API $cart_api,
        Physical_Products_App_Service $physical_products_app_service
    ) {
        $this->actions = $actions;
        $this->cart_api = $cart_api;
        $this->physical_products_app_service = $physical_products_app_service;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::CHECKOUT_ERROR_CHECKS, [$this, 'validate_delivery_address_fields'], 10, 2);
    }

    public function validate_delivery_address_fields(array $valid_data, array $data): void
    {
        if (!$this->physical_products_app_service->is_physical_product_in_the_cart()) {
            return;
        }

        if (!preg_match(self::PHONE_PATTERN, $data[self::PHONE_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_phone', 'physical_product_editor.cart.delivery_address.validate.phone');
        }

        if (empty($data[self::STREET_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_street', 'physical_product_editor.cart.delivery_address.validate.street');
        }

        if (!preg_match(self::BUILDING_NUMBER_PATTERN, $data[self::BUILDING_NUMBER_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_building_number', 'physical_product_editor.cart.delivery_address.validate.building_number');
        }

        if (!empty($data[self::APARTMENT_NUMBER_FIELD_NAME])) {
            if (!preg_match(self::APARTMENT_NUMBER_PATTERN, $data['edd_delivery_address_apartment_number'])) {
                $this->cart_api->set_error(
                    'invalid_delivery_address_apartment_number',
                    'physical_product_editor.cart.delivery_address.validate.apartment_number'
                );
            }
        }

        if (!preg_match(self::POSTAL_CODE_PATTERN, $data[self::POSTAL_CODE_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_postal_code', 'physical_product_editor.cart.delivery_address.validate.postal_code');
        }

        if (empty($data[self::CITY_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_city', 'physical_product_editor.cart.delivery_address.validate.city');
        }

        if (empty($data[self::FIRST_NAME_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_first_name', 'physical_product_editor.cart.delivery_address.validate.first_name');
        }

        if (empty($data[self::LAST_NAME_FIELD_NAME])) {
            $this->cart_api->set_error('invalid_delivery_address_last_name', 'physical_product_editor.cart.delivery_address.validate.last_name');
        }
    }
}
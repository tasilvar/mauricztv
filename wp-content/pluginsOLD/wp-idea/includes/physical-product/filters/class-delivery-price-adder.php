<?php

namespace bpmj\wpidea\physical_product\filters;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\cart\api\Fees_API;
use bpmj\wpidea\modules\cart\core\entities\Fee;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\service\Net_Price_Calculator;
use bpmj\wpidea\sales\product\service\Product_Vat_Rate_Getter;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;

class Delivery_Price_Adder implements Interface_Initiable
{
    private const DELIVERY_FEE_ID = 'delivery';
    private const FEE_TAX_RATE_INVOICE_FILTER = 'bpmj_edd_fee_tax_rate';

    private Interface_Translator $translator;
    private Interface_Events $events;
    private Interface_Settings $settings;
    private Physical_Products_App_Service $physical_products_app_service;
    private Fees_API $fees_api;
    private Product_Vat_Rate_Getter $product_vat_rate_getter;
    private Net_Price_Calculator $net_price_calculator;
    private Interface_Filters $filters;

    public function __construct(
        Interface_Events $events,
        Interface_Settings $settings,
        Interface_Translator $translator,
        Physical_Products_App_Service $physical_products_app_service,
        Fees_API $fees_api,
        Product_Vat_Rate_Getter $product_vat_rate_getter,
        Net_Price_Calculator $net_price_calculator
    ) {
        $this->events = $events;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->physical_products_app_service = $physical_products_app_service;
        $this->fees_api = $fees_api;
        $this->product_vat_rate_getter = $product_vat_rate_getter;
        $this->net_price_calculator = $net_price_calculator;
    }

    public function init(): void
    {
        if ($this->settings->get(Settings_Const::PHYSICAL_PRODUCTS_ENABLED)) {
            $this->events->on(Event_Name::PRODUCT_ADDED_TO_CART, [$this, 'add_or_recalculate_delivery']);
            $this->events->on(Event_Name::PRODUCT_REMOVED_FROM_CART, [$this, 'add_or_recalculate_delivery']);
        }
    }

    public function add_or_recalculate_delivery(): void
    {
        $delivery_price = $this->settings->get(Settings_Const::DELIVERY_PRICE);

        $this->fees_api->remove_fee(self::DELIVERY_FEE_ID);

        if (!$this->is_physical_product_in_the_cart() || empty($delivery_price)) {
            return;
        }

        $this->fees_api->add_fee(
            Fee::create(
                self::DELIVERY_FEE_ID,
                $this->get_delivery_label(),
                $delivery_price,
                $this->calculate_net_delivery_price($delivery_price),
                $this->get_tax_rate()
            )
        );
    }

    private function get_delivery_label(): string
    {
        $label = $this->translator->translate('templates.checkout_cart.delivery');

        $delivery_provider = $this->settings->get(Settings_Const::DELIVERY_PROVIDER);
        if (!empty($delivery_provider)) {
            $label .= ': ' . $delivery_provider;
        }

        return $label;
    }

    private function is_physical_product_in_the_cart(): bool
    {
        return $this->physical_products_app_service->is_physical_product_in_the_cart();
    }

    private function calculate_net_delivery_price($delivery_price): float
    {
        $highest_vat_rate = $this->get_tax_rate();

        return $this->net_price_calculator->calculate_net_price($delivery_price, $highest_vat_rate);
    }

    private function get_tax_rate(): int
    {
        $highest_vat_rate = Product_Vat_Rate_Getter::VAT_RATE_ZEO;

        foreach ($this->physical_products_app_service->get_physical_products_in_the_cart() as $product) {
            $vat_rate = $this->product_vat_rate_getter->get_vat_rate_for_product(
                new Product_ID($product->get_id()->to_int())
            );

            if ($vat_rate <= $highest_vat_rate) {
                continue;
            }

            $highest_vat_rate = $vat_rate;
        }

        return $highest_vat_rate;
    }
}
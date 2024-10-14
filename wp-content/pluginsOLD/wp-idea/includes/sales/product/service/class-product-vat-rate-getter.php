<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\service;

use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;

class Product_Vat_Rate_Getter
{
    private const INVOICES_VAT_RATE_META_NAME = 'invoices_vat_rate';
    private const DEFAULT_VAT_RATE_OPTION_NAME = 'invoices_default_vat_rate';
    private const FREE_OF_TAX = 'zw';
    private const DEFAULT_VAT_RATE = '23';
    public const VAT_RATE_ZEO = 0;

    private Interface_Product_Repository $product_repository;
    private Interface_Settings $settings;

    public function __construct(
        Interface_Product_Repository $product_repository,
        Interface_Settings $settings
    )
    {
        $this->product_repository = $product_repository;
        $this->settings = $settings;
    }

    public function get_vat_rate_for_product(Product_ID $product_id): int
    {
        if(!$this->invoice_tax_payer_option_enabled()) {
            return self::VAT_RATE_ZEO;
        }

        $vat_rate = $this->product_repository->get_meta($product_id, self::INVOICES_VAT_RATE_META_NAME);

        if (!preg_match('/^([0-9]{1,2})$/', $vat_rate) && $vat_rate !== self::FREE_OF_TAX) {
            $vat_rate = $this->get_default_vat_rate();
        }

        if ($vat_rate === self::FREE_OF_TAX) {
            $vat_rate = self::VAT_RATE_ZEO;
        }

        return (int)$vat_rate;
    }

    private function get_default_vat_rate(): string
    {
        $vat_rate = $this->settings->get(self::DEFAULT_VAT_RATE_OPTION_NAME);

        return empty($vat_rate) ? self::DEFAULT_VAT_RATE : $vat_rate;
    }

    private function invoice_tax_payer_option_enabled(): bool
    {
        return Invoice_Tax_Payer_Helper::is_enabled();
    }
}
<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\service;

use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\admin\settings\Settings_Const;

class Information_About_Available_Quantities
{
    public const AVAILABLE_QUANTITIES_FORMAT_X_OF_Y = 'format_x_of_y';
    public const AVAILABLE_QUANTITIES_FORMAT_X = 'format_x';
    private static ?Information_About_Available_Quantities $instance = null;
    private Interface_Translator $translator;
    private Interface_Product_Repository $product_repository;
    private Interface_Settings $settings;

    public function __construct(
        Interface_Translator $translator,
        Interface_Product_Repository $product_repository,
        Interface_Settings $settings
    ) {
        $this->translator = $translator;
        $this->product_repository = $product_repository;
        $this->settings = $settings;

        self::$instance = $this;
    }

    public static function get_instance(): self
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }
        return self::$instance;
    }

    public function get_available_quantities_information(Product_ID $product_id): array
    {
        if (!$this->enabled_show_available_quantities()) {
            return [];
        }

        $product = $this->product_repository->find($product_id);

        if (is_null($product)) {
            return [];
        }

        if ($product->get_variable_pricing_enabled()) {
            return $this->get_available_quantities_information_by_variants($product);
        }

        $purchase_limit_items_left = $product->get_purchase_limit_items_left();
        $purchase_limit = $product->get_purchase_limit();

        if (!$purchase_limit) {
            return [];
        }

        return [0 => $this->prepare_available_quantities_information($purchase_limit_items_left, $purchase_limit)];
    }

    private function get_available_quantities_information_by_variants(Product $product): array
    {
        $result = [];
        $variants = $product->get_product_variants();

        if (!$variants) {
            return $result;
        }

        foreach ($variants as $product_variant) {
            $int_variant_id = $product_variant->get_id()->to_int();
            $purchase_limit_items_left = $product_variant->get_purchase_limit_items_left();
            $purchase_limit = $product_variant->get_purchase_limit();

            if(!$purchase_limit){
                continue;
            }

            $result[$int_variant_id] = $this->prepare_available_quantities_information($purchase_limit_items_left, $purchase_limit);
        }

        return $result;
    }

    private function prepare_available_quantities_information(int $purchase_limit_items_left, int $purchase_limit): string
    {
        return sprintf(
            $this->translator->translate('product.available_quantities.' . $this->get_available_quantities_format()),
            $purchase_limit_items_left,
            $purchase_limit
        );
    }

    private function enabled_show_available_quantities(): bool
    {
        return $this->settings->get(Settings_Const::SHOW_AVAILABLE_QUANTITIES) ?? false;
    }

    private function get_available_quantities_format(): string
    {
        $available_quantities_format = $this->settings->get(Settings_Const::AVAILABLE_QUANTITIES_FORMAT);

        return !empty($available_quantities_format) ? $available_quantities_format : self::AVAILABLE_QUANTITIES_FORMAT_X_OF_Y;
    }
}
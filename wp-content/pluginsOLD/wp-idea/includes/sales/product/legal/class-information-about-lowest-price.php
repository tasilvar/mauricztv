<?php
declare(strict_types=1);

namespace bpmj\wpidea\sales\product\legal;

use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\sales\price_history\core\model\Lowest_Price;
use bpmj\wpidea\sales\price_history\core\model\Lowest_Price_Collection;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Price_History_Provider;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Information_About_Lowest_Price
{
    public const MAX_DATE_DIFFERENCE = 30;
    private static ?Information_About_Lowest_Price $instance = null;
    private Interface_Translator $translator;
    private System $system;
    private Interface_Product_Repository $product_repository;
    private Interface_Price_History_Provider $price_history_provider;

    public function __construct(
        Interface_Translator $translator,
        System $system,
        Interface_Product_Repository $product_repository,
        Interface_Price_History_Provider $price_history_provider
    )
    {
        $this->translator = $translator;
        $this->system = $system;
        $this->product_repository = $product_repository;
        $this->price_history_provider = $price_history_provider;

        self::$instance = $this;
    }

    public static function get_instance(): self
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }
        return self::$instance;
    }

    private function get_product_with_active_promotion_lowest_price(Product $product): Lowest_Price_Collection
    {
        return $this->price_history_provider->get_product_with_active_promotion_lowest_price($product->get_id()->to_int(), $product->get_variable_pricing_enabled());
    }

    public function get_lowest_price_information(Product_ID $product_id): array
    {
        $product = $this->product_repository->find($product_id);

        if (is_null($product)) {
            return [];
        }

        if ($product->get_variable_pricing_enabled()) {
            return $this->get_lowest_price_information_by_variants($product);
        }

        if (!$product->has_active_promotion()) {
            return [];
        }

        $lowest_price_information = $this->get_product_with_active_promotion_lowest_price($product);

        $lowest_price_value = $lowest_price_information->is_empty()
            ? Price_Formatting::round_and_format_to_int($product->get_price()->get_value(), Price_Formatting::MULTIPLY_BY_100)
            : (int)$lowest_price_information->current()->get_amount()->get_value();

        return [0 => $this->prepare_lowest_price_information($lowest_price_value)];
    }

    private function get_lowest_price_information_by_variants(Product $product): array
    {
        $result = [];
        $variants_with_promotion = $product->get_variants_with_promotion();

        if ($variants_with_promotion->count() === 0) {
            return $result;
        }

        $lowest_price_information = $this->get_product_with_active_promotion_lowest_price($product)->to_array();

        foreach ($variants_with_promotion as $product_variant) {
            $lowest_price_value = Price_Formatting::round_and_format_to_int($product_variant->get_amount(), Price_Formatting::MULTIPLY_BY_100);
            $int_variant_id = $product_variant->get_id()->to_int();

            foreach ($lowest_price_information as $lowest_price) {
                /** @var Lowest_Price $lowest_price */
                if ($lowest_price->get_product_variant_id()->to_int() === $int_variant_id) {
                    $lowest_price_value = (int)$lowest_price->get_amount()->get_value();
                    break;
                }
            }

            $result[$int_variant_id] = $this->prepare_lowest_price_information($lowest_price_value);
        }

        return $result;
    }

    private function prepare_lowest_price_information(int $lowest_price): string
    {
        return sprintf(
            $this->translator->translate('product.lowest_price_information'),
            self::MAX_DATE_DIFFERENCE,
            Price_Formatting::format_to_float($lowest_price, Price_Formatting::DIVIDE_BY_100),
            $this->system->get_system_currency()
        );
    }

    public function get_product_from_offer_lowest_price_information(Offer $offer): string
    {
        $product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;
        $offered_product_id_int = $offer->get_offered_product_id()->to_int();
        $product_lowest_price = $this->price_history_provider->get_product_lowest_price($offered_product_id_int, $product_variant_id);

        if (!is_null($product_lowest_price)) {
            return $this->prepare_lowest_price_information((int)$product_lowest_price->get_amount()->get_value());
        }

        $product = $this->product_repository->find(new Product_ID($offered_product_id_int));
        return $this->prepare_lowest_price_information(Price_Formatting::round_and_format_to_int($product->get_price()->get_value(), Price_Formatting::MULTIPLY_BY_100));
    }
}
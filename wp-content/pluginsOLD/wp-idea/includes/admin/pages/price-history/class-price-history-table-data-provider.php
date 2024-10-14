<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\price_history;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\sales\price_history\core\model\Historic_Price;
use bpmj\wpidea\sales\price_history\core\provider\Interface_Price_History_Provider;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Interface_Price_History_Persistence;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Price_History_Query_Criteria;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Price_History_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Interface_Price_History_Provider $price_history_provider;
    private Interface_Translator $translator;
    private Interface_Product_Repository $product_repository;
    private System $system;
    private Interface_Price_History_Persistence $price_history_persistence;

    public function __construct(
        Interface_Price_History_Provider $price_history_provider,
        Interface_Translator $translator,
        Interface_Product_Repository $product_repository,
        System $system,
        Interface_Price_History_Persistence $price_history_persistence
    )
    {
        $this->price_history_provider = $price_history_provider;
        $this->translator = $translator;
        $this->product_repository = $product_repository;
        $this->system = $system;
        $this->price_history_persistence = $price_history_persistence;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $rows = [];

        $per_page = ($per_page <= 0) ? 0 : $per_page;

        $price_history = $this->price_history_provider->find_by_criteria(
            $this->get_criteria_from_filters($filters),
            $per_page,
            $page,
	        (new Sort_By_Clause())->sort_by('id', true)
        );

        foreach ($price_history as $historic_price) {
            /** @var Historic_Price $historic_price */
            $product = $this->product_repository->find($historic_price->get_product_id());

            if (is_null($product)) {
                continue;
            }

            $rows[] = [
                'id' => $historic_price->get_id()->to_int(),
                'product_name' => $this->get_product_name($historic_price, $product),
                'price' => $this->get_price($historic_price),
                'type_of_price' => $this->get_type_of_price($historic_price),
                'changed_at' => $historic_price->get_date()->format('Y-m-d H:i:s'),
            ];
        }

        return $rows;
    }

    public function get_total(array $filters): int
    {
        return $this->price_history_persistence->count_by_criteria($this->get_criteria_from_filters($filters));
    }

    private function get_type_of_price(Historic_Price $historic_price): string
    {
        return $historic_price->change_occurred_in_regular_price()
            ? $this->translator->translate('price_history.type_of_price.regular')
            : $this->translator->translate('price_history.type_of_price.promo');
    }

    private function get_product_name(Historic_Price $historic_price, Product $product): string
    {
        $product_variant_id = $historic_price->get_product_variant_id();

        return $this->get_name_from_product($product, is_null($product_variant_id) ? null : $product_variant_id->to_int());
    }

    private function get_price(Historic_Price $historic_price): string
    {
        if ($historic_price->change_occurred_in_regular_price()) {
            return $this->prepare_price_format_for_table_view($historic_price->get_regular_price()->get_value());
        }

        $price = $historic_price->get_promo_price();
        return !is_null($price) ? $this->prepare_price_format_for_table_view($price->get_value()) : $this->translator->translate('price_history.column.price.promotion_disabled');
    }

    private function prepare_price_format_for_table_view(float $price): string
    {
        return number_format($price, 2, '.', '') . ' ' . $this->system->get_system_currency();
    }

    private function get_name_from_product(Product $product, ?int $product_variant_id): string
    {
        $product_variants = $product->get_product_variants();
        $product_variant_name = '';

        if ($product_variants) {
            foreach ($product_variants as $variant) {
                if ($variant->get_id()->to_int() === $product_variant_id) {
                    $product_variant_name = ' - ' . $variant->get_name();
                }
            }
        }

        return $product->get_name()->get_value() . $product_variant_name;
    }

    private function get_criteria_from_filters(array $filters): Price_History_Query_Criteria
    {
        $products = $this->get_filter_value_if_present($filters, 'product_name');
        $price = $this->get_filter_value_if_present($filters, 'price');
        $type_of_price = $this->get_filter_value_if_present($filters, 'type_of_price');
        $date_of_change = $this->get_filter_value_if_present($filters, 'changed_at');

        return new Price_History_Query_Criteria(
            $products,
            $price,
            $type_of_price,
            $date_of_change,
        );
    }

    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
            array_filter($filters, static function ($filter, $key) use ($filter_name) {
                return $filter['id'] === $filter_name;
            }, ARRAY_FILTER_USE_BOTH)
        )[0]['value'] ?? null;
    }
}
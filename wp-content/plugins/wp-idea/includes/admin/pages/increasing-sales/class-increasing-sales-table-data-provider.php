<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\increasing_sales;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\increasing_sales\api\controllers\Admin_Increasing_Sales_Ajax_Controller;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Offer_Query_Criteria;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\collection\Product_Variant_Collection;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;

class Increasing_Sales_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const EMPTY = '-';
    private Interface_Url_Generator $url_generator;
    private Interface_Product_Repository $product_repository;
    private Interface_Translator $translator;
    private System $system;
    private string $system_currency;
    private Interface_Offers_Persistence $offers_persistence;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Product_Repository $product_repository,
        System $system,
        Interface_Translator $translator,
        Interface_Offers_Persistence $offers_persistence
    ) {
        $this->url_generator = $url_generator;
        $this->product_repository = $product_repository;
        $this->system = $system;
        $this->translator = $translator;
        $this->offers_persistence = $offers_persistence;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $models = $this->offers_persistence->find_by_criteria(
            $this->get_criteria_from_filters($filters),
            $per_page,
            $page,
            $sort_by
        );

        return $this->models_to_rows($models->to_array());
    }

    public function get_total(array $filters): int
    {
        return $this->offers_persistence->count_by_criteria($this->get_criteria_from_filters($filters));
    }

    private function get_make_offer_label(string $label): string
    {
        return $this->translator->translate('increasing_sales.event.' . $label);
    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }

    private function models_to_rows(array $models): array
    {
        $rows = [];

        foreach ($models as $model) {
            $product_variant_id = $model->get_product_variant_id() ? $model->get_product_variant_id()->to_int() : null;
            $offered_product_variant_id = $model->get_offered_product_variant_id() ? $model->get_offered_product_variant_id()->to_int() : null;

            /* @var Offer $model */
            $rows[] = [
                'id' => $model->get_id()->to_int(),
                'product' => $this->get_product_name_by_id($model->get_product_id()->to_int(), $product_variant_id),
                'offer_type' => $model->get_offer_type()->get_value(),
                'offer_type_label' => $this->get_make_offer_label($model->get_offer_type()->get_value()),
                'offered_product' => $this->get_product_name_by_id($model->get_offered_product_id()->to_int(), $offered_product_variant_id),
                'discount' => $this->amount_in_fractions_to_float($model->get_discount_in_fractions()),
                'currency' => $this->get_currency(),
                'edit_offer' => $this->get_edit_offer_url($model->get_id()->to_int()),
                'delete_offer' => $this->get_delete_offer_url($model->get_id()->to_int())
            ];
        }

        return $rows;
    }

    private function get_criteria_from_filters(array $filters): Offer_Query_Criteria
    {
        $product = $this->get_filter_value_if_present($filters, 'product');
        $offer_type = $this->get_filter_value_if_present($filters, 'offer_type');
        $offered_product = $this->get_filter_value_if_present($filters, 'offered_product');
        $discount = $this->get_filter_value_if_present($filters, 'discount');

        return new Offer_Query_Criteria(
            null,
            $product,
            null,
            $offer_type,
            $offered_product,
            $discount
        );
    }

    private function get_edit_offer_url(int $offer_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::INCREASING_SALES,
            'view' => 'edit',
            'id' => $offer_id
        ]);
    }

    private function get_delete_offer_url(int $offer_id): string
    {
        return $this->url_generator->generate(Admin_Increasing_Sales_Ajax_Controller::class, 'delete_offer', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $offer_id
        ]);
    }

    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
                array_filter($filters, static function ($filter, $key) use ($filter_name) {
                    return $filter['id'] === $filter_name;
                }, ARRAY_FILTER_USE_BOTH)
            )[0]['value'] ?? null;
    }

    private function get_product_name_by_id(int $product_id, ?int $product_variant_id): string
    {
        $product = $this->product_repository->find(new Product_ID($product_id));

        if (!$product) {
            return self::EMPTY;
        }

        $product_name = $product->get_name()->get_value();

        if ($product_variant_id) {
            $variant_name = $this->get_product_variant_name($product->get_product_variants(), $product_variant_id);
            if (!$variant_name || !$product->get_variable_pricing_enabled()) {
                return self::EMPTY;
            }

            return $product_name . $variant_name;
        }

        if ($product->get_variable_pricing_enabled()) {
            return self::EMPTY;
        }

        return $product_name;
    }

    private function get_product_variant_name(?Product_Variant_Collection $product_variants, ?int $product_variant_id): ?string
    {
        $variant_name = null;

        foreach ($product_variants as $variant) {
            if ($variant->get_id()->to_int() === $product_variant_id) {
                $variant_name = ' - ' . $variant->get_name();
            }
        }

        return $variant_name;
    }

    private function amount_in_fractions_to_float(?int $amount): ?float
    {
        if (!$amount) {
            return null;
        }
        return Price_Formatting::format_to_float($amount, Price_Formatting::DIVIDE_BY_100);
    }
}
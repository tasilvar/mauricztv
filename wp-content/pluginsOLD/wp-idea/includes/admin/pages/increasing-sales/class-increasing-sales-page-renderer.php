<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\increasing_sales;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\increasing_sales\api\controllers\Admin_Increasing_Sales_Controller;
use bpmj\wpidea\modules\increasing_sales\core\dto\Offer_Data_DTO;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Offer_Query_Criteria;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Variant_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\service\Variant_IDs_Parser;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Increasing_Sales_Page_Renderer
{
    private System $system;
    private Interface_Offers_Persistence $offers_persistence;
    private Interface_Product_Repository $product_repository;
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Interface_Url_Generator $url_generator;
    private Increasing_Sales_Presenter $increasing_sales_presenter;
    private Current_Request $current_request;
    private Increasing_Sales_Table_Config_Provider $config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Variant_IDs_Parser $variant_ids_parser;

    public function __construct(
        System $system,
        Interface_Offers_Persistence $offers_persistence,
        Interface_Product_Repository $product_repository,
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Interface_Url_Generator $url_generator,
        Increasing_Sales_Presenter $increasing_sales_presenter,
        Current_Request $current_request,
        Increasing_Sales_Table_Config_Provider $config_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        Variant_IDs_Parser $variant_ids_parser
    ) {
        $this->system = $system;
        $this->offers_persistence = $offers_persistence;
        $this->product_repository = $product_repository;
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->url_generator = $url_generator;
        $this->increasing_sales_presenter = $increasing_sales_presenter;
        $this->current_request = $current_request;
        $this->config_provider = $config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->variant_ids_parser = $variant_ids_parser;
    }

    public function render_page(): void
    {
        $request_view = $this->current_request->get_request_arg('view');
        if (!isset($request_view)) {
            $this->get_increasing_sales_page_html();
        }

        if (('add' === $request_view) || ('edit' === $request_view)) {
            $this->render_action_page();
        }
    }

    private function render_action_page(): void
    {
        $fields_form = null;
        $id_offer = null;

        if ($this->current_request->query_arg_exists('id')) {
            $id_offer = (int)$this->current_request->get_request_arg('id');
            $fields_form = $this->increasing_sales_presenter->get_offer_data_by_id($id_offer);
        }

        echo $this->view_provider->get_admin('/pages/increasing-sales/form', [
            'page_title' => $this->increasing_sales_presenter->get_page_offer_header($id_offer),
            'action' => $this->get_increasing_sales_action_url($fields_form),
            'fields' => $fields_form,
            'products' => $this->get_products_in_array(),
            'products_not_assigned_to_the_offer' => $this->get_products_not_assigned_to_the_offer($fields_form),
            'url_increasing_sales_page' => $this->get_increasing_sales_page_url(),
            'currency' => $this->get_currency(),
            'translator' => $this->translator,
        ]);
    }

    private function get_increasing_sales_page_html(): void
    {
        echo $this->view_provider->get_admin('/pages/increasing-sales/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('increasing_sales.title')
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }

    private function get_increasing_sales_action_url(?Offer_Data_DTO $fields_form): string
    {
        if ($fields_form) {
            return $this->url_generator->generate(Admin_Increasing_Sales_Controller::class, 'edit_offer', [
                Nonce_Handler::DEFAULT_ACTION_NAME => Nonce_Handler::create()
            ]);
        }

        return $this->url_generator->generate(Admin_Increasing_Sales_Controller::class, 'add_offer', [
            Nonce_Handler::DEFAULT_ACTION_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_increasing_sales_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::INCREASING_SALES
        ]);
    }

    private function get_products_in_array(): array
    {
        $array = [];

        $products = $this->product_repository->find_all();

        foreach ($products as $product) {
            $variants = $product->get_product_variants();
            $product_id = $product->get_id()->to_int();

            if (!$variants->count() || !$product->get_variable_pricing_enabled()) {
                $array[] = [
                    $product_id => $product->get_name()->get_value()
                ];
                continue;
            }

            foreach ($variants as $variant) {
                $array[] = [
                    $this->variant_ids_parser->parse_product_id_to_string_id(new Product_ID($product_id), new Variant_ID($variant->get_id()->to_int()))
                    => $product->get_name()->get_value() . ' - ' . $variant->get_name()
                ];
            }
        }

        return $array;
    }

    private function get_products_not_assigned_to_the_offer(?Offer_Data_DTO $fields_form): array
    {
        $array = [];

        foreach ($this->get_products_in_array() as $product) {
            foreach ($product as $key => $value) {
                $id = (string)$key;

                $product_and_variant_id = $this->variant_ids_parser->parse_string_id_to_product_and_variant_id($id);

                $product_id = $product_and_variant_id->get_product_id()->to_int();
                $variant_id = $product_and_variant_id->get_variant_id() ? $product_and_variant_id->get_variant_id()->to_int() : null;

                $offer = $this->offers_persistence->find_by_criteria(
                    new Offer_Query_Criteria(
                        null,
                        [$product_id],
                        $variant_id
                    )
                );

                if (!$offer->is_empty() && (!$this->is_edited_product_in_array($fields_form, $id))) {
                    continue;
                }

                $array[] = [
                    $key => $value
                ];
            }
        }

        return $array;
    }

    private function is_edited_product_in_array(?Offer_Data_DTO $fields_form, string $id): bool
    {
        if (!$fields_form) {
            return false;
        }

        if ($fields_form->product_id !== $id) {
            return false;
        }

        return true;
    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }
}
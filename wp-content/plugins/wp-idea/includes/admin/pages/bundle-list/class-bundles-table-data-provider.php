<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\bundle_list;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\controllers\admin\Admin_Bundles_Controller;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\vo\Product_Sales_Status;
use bpmj\wpidea\translator\Interface_Translator;

class Bundles_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Bundles_App_Service $bundles_app_service;
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;

    public function __construct(
        Bundles_App_Service $bundles_app_service,
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator
    ) {
        $this->bundles_app_service = $bundles_app_service;
        $this->url_generator = $url_generator;
        $this->translator = $translator;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $entities = $this->bundles_app_service->find_all_offers();
        $rows = [];

        foreach ($entities as $entity) {
            $bundle_sales_status = $this->get_bundle_toggled_sales_status($entity);

            $rows[] = [
                'id' => $entity->get_id()->to_int(),
                'name' => $entity->get_name()->get_value(),
                'products' => $this->get_products_name_in_bundle($entity),
                'purchase_links' => $this->get_purchase_links_url($entity),
                'sales' => $bundle_sales_status,
                'sales_label' => $this->get_name_bundle_sales_status_label($bundle_sales_status),
                'edit_bundle' => $this->get_edit_bundle_url($entity),
                'delete_bundle' => $this->get_delete_bundle_url($entity),
                'change_bundle_sales' => $this->get_change_bundle_sales_status_url($entity)
            ];
        }

        return $rows;
    }

    private function get_products_name_in_bundle(Product $entity): string
    {
        $bundle_items = $this->bundles_app_service->get_bundle_items($entity->get_id());

        $array = [];

        foreach ($bundle_items as $item) {
            $array[] = $item->get_name()->get_value();
        }

        return $array ? implode(', ', $array) : '-';
    }

    public function get_bundle_toggled_sales_status(Product $entity): string
    {
        $bundle_status = new Product_Sales_Status(Product_Sales_Status::ENABLED);

        if ($entity->sales_disabled()) {
            $bundle_status = new Product_Sales_Status(Product_Sales_Status::DISABLED);
        }

        return $bundle_status->get_value();
    }

    private function get_purchase_links_url(Product $entity): string
    {
        return $this->url_generator->generate(Admin_Bundles_Controller::class, 'purchase_links', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    public function get_name_bundle_sales_status_label(string $status): string
    {
        $bundle_status = new Product_Sales_Status($status);

        return $this->translator->translate(
            'bundles_list.sales.status.' . $bundle_status->get_name()
        );
    }

    private function get_edit_bundle_url(Product $entity): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_PACKAGES,
            Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME => $entity->get_id()->to_int()
        ]);
    }

    private function get_delete_bundle_url(Product $entity): string
    {
        return $this->url_generator->generate(Admin_Bundles_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_change_bundle_sales_status_url(Product $entity): string
    {
        return $this->url_generator->generate(Admin_Bundles_Controller::class, 'disable_sales', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    public function get_total(array $filters): int
    {
        return 0;
    }
}
<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\physical_products;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\app\services\Services_App_Service;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\vo\Product_Sales_Status;
use bpmj\wpidea\controllers\admin\Admin_Physical_Products_Controller;
use bpmj\wpidea\sales\product\service\Interface_Url_Resolver;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;
use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer;

class Physical_Products_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;
    private Physical_Products_App_Service $app_service;
    private Interface_Url_Resolver $url_resolver;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        Physical_Products_App_Service $physical_products_app_service,
        Interface_Url_Resolver $url_resolver
    ) {
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->app_service = $physical_products_app_service;
        $this->url_resolver = $url_resolver;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $rows = [];

        $product_collection = $this->app_service->find_all_offers();

        foreach ($product_collection as $entity) {
            $service_sales_status = $this->get_physical_product_toggled_sales_status($entity);

            $rows[] = [
                'id' => $entity->get_id()->to_int(),
                'name' => $entity->get_name()->get_value(),
                'purchase_links' => $this->get_purchase_links_url($entity),
                'sales' => $service_sales_status,
                'sales_label' => $this->get_physical_product_sales_status_label($service_sales_status),
                'physical_product_panel' => $this->get_physical_product_panel_url($entity),
                'edit_physical_product' => $this->get_edit_physical_product_url($entity),
                'delete_physical_product' => $this->get_physical_product_product_url($entity),
                'change_physical_product_sales' => $this->get_change_physical_product_sales_status_url($entity)
            ];
        }

        return $rows;
    }

    private function get_physical_product_toggled_sales_status(Product $entity): string
    {
        $digital_product_status = new Product_Sales_Status(Product_Sales_Status::ENABLED);

        if ($entity->sales_disabled()) {
            $digital_product_status = new Product_Sales_Status(Product_Sales_Status::DISABLED);
        }

        return $digital_product_status->get_value();
    }

    private function get_purchase_links_url(Product $entity): string
    {
        return $this->url_generator->generate(Admin_Physical_Products_Controller::class, 'purchase_links', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    public function get_physical_product_sales_status_label(string $status): string
    {
        $digital_product_status = new Product_Sales_Status($status);

        return $this->translator->translate('physical_products.sales.status.' . $digital_product_status->get_name());
    }

    private function get_physical_product_panel_url(Product $entity): string
    {
        $url = $this->url_resolver->get_by_product_id(new Product_ID($entity->get_id()->to_int()));
        return $url->get_value() ?? '';
    }

    private function get_edit_physical_product_url(Product $entity): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_PHYSICAL_PRODUCT,
            Physical_Product_Editor_Page_Renderer::PHYSICAL_PRODUCT_ID_QUERY_ARG_NAME => $entity->get_id()->to_int()
        ]);
    }

    private function get_physical_product_product_url(Product $entity): string
    {
        return $this->url_generator->generate(Admin_Physical_Products_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    private function get_change_physical_product_sales_status_url(Product $entity): string
    {
        return $this->url_generator->generate(Admin_Physical_Products_Controller::class, 'disable_sales', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }

    public function get_total(array $filters): int
    {
        return 0;
    }
}
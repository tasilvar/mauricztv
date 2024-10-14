<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;
use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\app\digital_products\Digital_Products_App_Service;
use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\app\services\Services_App_Service;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\dto\Course_DTO;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\digital_products\dto\Digital_Product_DTO;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\service\dto\Service_DTO;
use bpmj\wpidea\translator\Interface_Translator;
use Exception;

class Admin_Creator_Controller extends Ajax_Controller
{
    private Courses_App_Service $courses_app_service;
    private Digital_Products_App_Service $digital_products_app_service;
    private Services_App_Service $services_app_service;
    private Bundles_App_Service $bundles_app_service;
    private Interface_Url_Generator $url_generator;
    private Courses $courses;
    private Physical_Products_App_Service $physical_products_app_service;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Digital_Products_App_Service $digital_products_app_service,
        Services_App_Service $services_app_service,
        Interface_Url_Generator $url_generator,
        Courses_App_Service $courses_app_service,
        Bundles_App_Service $bundles_app_service,
        Courses $courses,
        Physical_Products_App_Service $physical_products_app_service
    ) {
        $this->digital_products_app_service = $digital_products_app_service;
        $this->services_app_service = $services_app_service;
        $this->url_generator = $url_generator;
        $this->courses_app_service = $courses_app_service;
        $this->bundles_app_service = $bundles_app_service;
        $this->courses = $courses;
        $this->physical_products_app_service = $physical_products_app_service;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_PRODUCTS],
            'allowed_methods' => [Request_Method::POST],
        ];
    }

    public function create_physical_product_action(Current_Request $current_request): string
    {
        $name = $current_request->get_body_arg('name');
        $price = $current_request->get_body_arg('price');

        $validate = $this->validate_name_price_field_and_return_error_messages($name, $price);

        if (count($validate)) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $validate
            ]);
        }

        try {
            $product_dto = $this->get_product_dto($name, $price);
            $physical_product_dto = $this->get_physical_product_dto($name);

            $product_id = $this->physical_products_app_service->save_physical_product($product_dto, $physical_product_dto);
        } catch (Exception $e) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'error' => $this->translator->translate('product_editor.popup.save.error')
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->get_physical_product_editor_page_url($product_id)
        ]);
    }

    public function create_bundle_action(Current_Request $current_request): string
    {
        $name = $current_request->get_body_arg('name');
        $price = $current_request->get_body_arg('price');

        $validate = $this->validate_name_price_field_and_return_error_messages($name, $price);

        if (count($validate)) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $validate
            ]);
        }

        try {
            $product_dto = $this->get_product_dto_for_bundle($name, $price);

            $product_id = $this->bundles_app_service->save_bundle($product_dto);
        } catch (Exception $e) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'error' => $this->translator->translate('product_editor.popup.save.error')
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->get_bundle_editor_page_url($product_id)
        ]);
    }

    public function create_course_action(Current_Request $current_request): string
    {
        $name = $current_request->get_body_arg('name');
        $price = $current_request->get_body_arg('price');

        $validate = $this->validate_name_price_field_and_return_error_messages($name, $price);

        if (count($validate)) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $validate
            ]);
        }

        try {
            $product_dto = $this->get_product_dto($name, $price);
            $course_dto = new Course_DTO();

            $product_id = $this->courses_app_service->save_course($product_dto, $course_dto);
        } catch (Exception $e) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'error' => $this->translator->translate('product_editor.popup.save.error')
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->get_course_editor_page_url($product_id)
        ]);
    }


    public function create_digital_product_action(Current_Request $current_request): string
    {
        $name = $current_request->get_body_arg('name');
        $price = $current_request->get_body_arg('price');

        $validate = $this->validate_name_price_field_and_return_error_messages($name, $price);

        if (count($validate)) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $validate
            ]);
        }

        try {
            $product_dto = $this->get_product_dto($name, $price);
            $digital_product_dto = $this->get_digital_product_dto($name);

            $product_id = $this->digital_products_app_service->save_digital_product($product_dto, $digital_product_dto);
        } catch (Exception $e) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'error' => $this->translator->translate('product_editor.popup.save.error')
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->get_digital_product_editor_page_url($product_id)
        ]);
    }


    public function create_service_action(Current_Request $current_request): string
    {
        $name = $current_request->get_body_arg('name');
        $price = $current_request->get_body_arg('price');

        $validate = $this->validate_name_price_field_and_return_error_messages($name, $price);

        if (count($validate)) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $validate
            ]);
        }

        try {
            $product_dto = $this->get_product_dto($name, $price);
            $service_dto = $this->get_service_dto($name);

            $product_id = $this->services_app_service->save_service($product_dto, $service_dto);
        } catch (Exception $e) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'error' => $this->translator->translate('product_editor.popup.save.error')
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->get_service_editor_page_url($product_id)
        ]);
    }

    private function get_service_editor_page_url(Product_ID $id): string
    {
        if (!$id->to_int()) {
            return '';
        }

        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_SERVICE,
            Service_Editor_Page_Renderer::SERVICE_ID_QUERY_ARG_NAME => $id->to_int()
        ]);
    }

    private function get_physical_product_editor_page_url(Product_ID $id): string
    {
        if (!$id->to_int()) {
            return '';
        }

        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_PHYSICAL_PRODUCT,
            Physical_Product_Editor_Page_Renderer::PHYSICAL_PRODUCT_ID_QUERY_ARG_NAME => $id->to_int()
        ]);
    }

    private function get_digital_product_editor_page_url(Product_ID $id): string
    {
        if (!$id->to_int()) {
            return '';
        }

        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_DIGITAL_PRODUCT,
            Digital_Product_Editor_Page_Renderer::DIGITAL_PRODUCT_ID_QUERY_ARG_NAME => $id->to_int()
        ]);
    }

    private function get_bundle_editor_page_url(Product_ID $id): string
    {
        if (!$id->to_int()) {
            return '';
        }

        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_PACKAGES,
            Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME => $id->to_int(),
        ]);
    }

    private function get_course_editor_page_url(Product_ID $id): string
    {
        if (!$id->to_int()) {
            return $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::COURSES
            ]);
        }

        $courses = $this->courses->get_course_by_product($id->to_int());

        if (empty($courses)) {
            return $this->url_generator->generate_admin_page_url('admin.php', [
                'page' => Admin_Menu_Item_Slug::COURSES
        ]);
        }
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
            Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $courses->ID
        ]);
    }

    private function get_product_dto_for_bundle(string $name, string $price): Product_DTO
    {
        $is_bundle = true;
        return $this->get_product_dto($name, $price, $is_bundle);
    }

    private function get_product_dto(string $name, string $price, bool $is_bundle = false): Product_DTO
    {
        $dto = new Product_DTO();

        $dto->name = $name;
        $dto->price = (float)$price;
        $dto->description = '';
        $dto->sales_disabled = true;
        $dto->hide_from_list = true;
        $dto->is_bundle = $is_bundle;

        return $dto;
    }

    private function get_service_dto(string $name): Service_DTO
    {
        $dto = new Service_DTO();

        $dto->name = $name ?? '';

        return $dto;
    }

    private function get_physical_product_dto(string $name): Physical_Product_DTO
    {
        $dto = new Physical_Product_DTO();

        $dto->name = $name ?? '';

        return $dto;
    }

    private function get_digital_product_dto(string $name): Digital_Product_DTO
    {
        $dto = new Digital_Product_DTO();

        $dto->name = $name ?? '';
        $dto->included_files = [];

        return $dto;
    }

    private function validate_name_price_field_and_return_error_messages(string $name, string $price): array
    {
        $validation_errors = [];

        if (!$name) {
            $validation_errors['name'] = $this->translator->translate('product_editor.popup.field.error.empty');
        }

        if (!is_numeric($price) || (float)$price < 0) {
            $validation_errors['price'] = $this->translator->translate('product_editor.popup.field.error.price');
        }

        return $validation_errors;
    }
}

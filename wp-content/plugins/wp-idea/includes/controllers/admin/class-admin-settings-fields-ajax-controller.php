<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;
use bpmj\wpidea\admin\settings\core\entities\fields\License_Setting_Field;
use bpmj\wpidea\admin\settings\core\factories\Settings_Api_Factory;
use bpmj\wpidea\admin\settings\core\services\Settings_Api;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Admin_Settings_Fields_Ajax_Controller extends Ajax_Controller
{
    private Settings_Api_Factory $settings_api_factory;
    private Interface_View_Provider $view_provider;
    private Current_Request $current_request;
    private Settings_Api $settings_api;

    private ?int $service_id;
    private ?int $digital_product_id;
    private ?int $course_id;
    private ?int $bundle_id;
    private ?int $physical_product_id;
    private ?int $quiz_id;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_View_Provider $view_provider,
        Settings_Api_Factory $settings_api_factory,
        Current_Request $current_request
    ) {
        $this->view_provider = $view_provider;
        $this->settings_api_factory = $settings_api_factory;
        $this->current_request = $current_request;

        $this->load_query_param();

        parent::__construct($access_control, $translator, $redirector);
    }

    private function load_query_param(): void
    {
        $this->service_id = $this->current_request->get_query_arg(Service_Editor_Page_Renderer::SERVICE_ID_QUERY_ARG_NAME);
        $this->digital_product_id = $this->current_request->get_query_arg(Digital_Product_Editor_Page_Renderer::DIGITAL_PRODUCT_ID_QUERY_ARG_NAME);
        $this->course_id = $this->current_request->get_query_arg(Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME);
        $this->bundle_id = $this->current_request->get_query_arg(Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME);
        $this->physical_product_id = $this->current_request->get_query_arg(Physical_Product_Editor_Page_Renderer::PHYSICAL_PRODUCT_ID_QUERY_ARG_NAME);
        $this->quiz_id = $this->current_request->get_query_arg(Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME);
    }

    public function before_action(): void
    {
        if (!empty($this->service_id)) {
            $this->settings_api = $this->settings_api_factory->create_service_settings_api(new Product_ID($this->service_id));
        } elseif (!empty($this->digital_product_id)) {
            $this->settings_api = $this->settings_api_factory->create_digital_product_settings_api(new Product_ID($this->digital_product_id));
        } elseif (!empty($this->course_id)) {
            $this->settings_api = $this->settings_api_factory->create_course_settings_api(new Course_ID($this->course_id));
        } elseif (!empty($this->bundle_id)) {
            $this->settings_api = $this->settings_api_factory->create_bundle_settings_api(new Product_ID($this->bundle_id));
        }  elseif (!empty($this->physical_product_id)) {
            $this->settings_api = $this->settings_api_factory->create_physical_product_settings_api(new Product_ID($this->physical_product_id));
        } elseif (!empty($this->quiz_id)) {
            $this->settings_api = $this->settings_api_factory->create_quiz_settings_api(new Quiz_ID($this->quiz_id));
        } else {
            $this->settings_api = $this->settings_api_factory->create_app_settings_api();
        }

        $this->settings_api->init();

        parent::before_action();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_SETTINGS],
            'allowed_methods' => [Request_Method::POST]
        ];

        if (!empty($this->service_id) || !empty($this->digital_product_id) || !empty($this->course_id) || !empty($this->bundle_id) || !empty($this->physical_product_id) || !empty($this->quiz_id)) {
            $behaviors['caps'][] = Caps::CAP_MANAGE_PRODUCTS;
        }

        return $behaviors;
    }

    public function get_tab_content_action(Current_Request $current_request): string
    {
        $tab = $current_request->get_body_arg('tab');

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'content' => $this->view_provider->get_admin('/settings/tab', [
                'tab' => $tab,
                'settings_api' => $this->settings_api
            ])
        ]);
    }

    public function save_field_value_action(Current_Request $current_request): string
    {
        $name = $current_request->get_body_arg('name');
        $value = $current_request->get_body_arg('value', [
            Current_Request::ALLOW_HTML,
            Current_Request::ALLOW_STYLE_ATTRIBUTES
        ]);

        $field = $this->settings_api->get_setting_by_name($name);

        $field->change_value($value);

        if (!$field->validate()->is_valid()) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $this->translate_error_messages($field->validate()->error_messages())
            ]);
        }

        $this->settings_api->update_field($field);

        return $this->return_as_json(self::STATUS_SUCCESS, [
            $name,
            $this->settings_api->get_field_value($field)
        ]);
    }

    public function save_configuration_group_fields_value_action(Current_Request $current_request): string
    {
        $fields_value = $current_request->get_body_arg('fields_value', [
            Current_Request::ALLOW_HTML,
            Current_Request::ALLOW_STYLE_ATTRIBUTES
        ]) ?? [];

        $validation_errors = [];

        $count_fields = count($fields_value);

        $updated_fields = 0;

        foreach ($fields_value as $value) {
            $name = $value['name'];
            $value = $value['value'];

            $field = $this->settings_api->get_setting_by_name($name);

            if (!$field) {
                $count_fields--;
                continue;
            }

            if ($field->get_use_raw_value()) {
                $value = $this->get_raw_value_for_field($current_request, $name);
            }

            $field->change_value($value);

            if ($field->validate()->is_valid()) {
                $this->settings_api->update_field($field);
                $updated_fields++;
            } else {
                $validation_errors[$name] = $this->translate_error_messages($field->validate()->error_messages());
            }
        }

        if ($updated_fields !== $count_fields) {
            return $this->return_as_json(self::STATUS_SUCCESS, [
                'validation_errors' => $validation_errors
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->translator->translate('settings.popup.saved')
        ]);
    }

    private function get_raw_value_for_field(Current_Request $current_request, string $name)
    {
        $raw_fields_value = $current_request->get_raw_body_arg('fields_value');

        foreach ($raw_fields_value as $raw_value) {
            if ($raw_value['name'] == $name) {
                return $raw_value['value'];
            }
        }
    }

    public function get_license_field_info_html_action()
    {
        /** @var License_Setting_Field $field */
        $field = $this->settings_api->get_setting_by_name('license_key');

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'html' => $field->get_license_field_additional_html()
        ]);
    }

    private function translate_error_messages(array $error_messages): array
    {
        foreach ($error_messages as $key => $message) {
            $error_messages[$key] = $this->translator->translate($message);
        }
        return $error_messages;
    }
}
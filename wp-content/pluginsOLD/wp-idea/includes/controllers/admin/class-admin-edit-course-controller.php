<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\support\diagnostics\items\Max_Input_Vars;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\courses\core\value_objects\{Drip, Drip_Unit, Drip_Value};
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\Packages;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use Exception;


class Admin_Edit_Course_Controller extends Ajax_Controller
{
    private Max_Input_Vars $max_input_vars;
    private Courses_App_Service $courses_app_service;
    private Packages $packages;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Courses_App_Service $courses_app_service,
        Packages $packages,
        Max_Input_Vars $max_input_vars
    ) {
        $this->courses_app_service = $courses_app_service;
        $this->packages = $packages;
        $this->max_input_vars = $max_input_vars;

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

    public function get_variable_prices_action(Current_Request $current_request): string
    {
        $post_id = $current_request->get_request_arg('post_id');

        if (!$post_id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $data = $this->courses_app_service->get_variable_prices($post_id);

        return $this->return_as_json(self::STATUS_SUCCESS, ['html' => $data]);
    }


    public function save_variable_prices_action(Current_Request $current_request): string
    {
        $product_id = $current_request->get_request_arg('product_id');
        if (!$product_id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $fields = [
            '_edd_price_options_mode' => $current_request->get_body_arg('_edd_price_options_mode'),
            'edd_variable_prices' => $current_request->get_body_arg('edd_variable_prices'),
            '_edd_default_price_id' => $current_request->get_body_arg('_edd_default_price_id')
        ];

        $data = $this->courses_app_service->save_variable_prices((int)$product_id, $fields);

        return $this->return_as_json(self::STATUS_SUCCESS, $data);
    }

    public function save_course_structure_action(Current_Request $current_request): string
    {
        $course_id = $current_request->get_request_arg('course_id');
        $save_module = $current_request->get_body_arg('bpmj_eddcm_save_modules');
        $drip_value = $current_request->get_body_arg('drip_value');
        $drip_unit = $current_request->get_body_arg('drip_unit');
        $module = $current_request->get_body_arg('bpmj_eddcm_module');

        if (!$course_id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        if (!$save_module || !$module) {
            return $this->success_with_warning_message([
                'errorMessage' => $this->translator->translate('course_editor.sections.structure.validation.empty_structure')
            ]);
        }

         if(count($module, COUNT_RECURSIVE) >= $this->max_input_vars->get_current_value()) {
             return $this->success_with_warning_message([
                 'errorMessage' => $this->translator->translate('edit_courses.max_input_vars.error')
             ]);
         }

        $drip = new Drip(
            new Drip_Value((int)$drip_value),
            new Drip_Unit(!empty($drip_unit) ? $drip_unit : Drip_Unit::MINUTES)
        );

        $fields = [
            'course_id' => new Course_ID((int)$course_id),
            'access_to_dripping' => $this->has_access_to_dripping(),
            'drip' => $drip,
            'module' => $module
        ];

        try {
            $should_reload_structure = $this->courses_app_service->save_course_structure($fields);
        } catch (Exception $e) {
            return $this->success_with_warning_message([
                'errorMessage' => $this->translator->translate('course_editor.sections.structure.validation.error_message')
            ]);
        }

        if (!$should_reload_structure) {
            return $this->success_with_warning_message([
                'errorMessage' => $this->translator->translate('course_editor.sections.structure.error_message')
            ]);
        }

        return $this->success([
            'successMessage' => $this->translator->translate('course_editor.sections.structure.success_message'),
            'courseId' => $course_id
        ]);
    }

    private function has_access_to_dripping(): bool
    {
        return $this->packages->has_access_to_feature(Packages::FEAT_DELAYED_ACCESS);
    }
}

<?php

namespace bpmj\wpidea\admin\settings\core\factories;

use bpmj\wpidea\admin\Edit_Course;
use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Active_Groups_Provider;
use bpmj\wpidea\admin\pages\bundle_editor\infrastructure\services\Bundles_Settings_Fields_Service;
use bpmj\wpidea\admin\pages\course_editor\core\services\Checkboxes_Value_Changer as Course_Checkboxes_Value_Changer;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Active_Groups_Provider;
use bpmj\wpidea\admin\pages\course_editor\infrastructure\services\Courses_Settings_Fields_Service;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Active_Groups_Provider;
use bpmj\wpidea\admin\pages\digital_product_editor\infrastructure\services\Digital_Products_Settings_Fields_Service;
use bpmj\wpidea\admin\pages\physical_product_editor\infrastructure\services\Physical_Product_Settings_Fields_Service;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Active_Groups_Provider;
use bpmj\wpidea\admin\pages\product_editor\core\services\Checkboxes_Value_Changer;
use bpmj\wpidea\admin\pages\quiz_editor\infrastructure\services\Checkboxes_Value_Changer as Quiz_Settings_Checkboxes_Value_Changer;
use bpmj\wpidea\admin\pages\quiz_editor\infrastructure\services\Quizzes_Settings_Fields_Service;
use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Active_Groups_Provider;
use bpmj\wpidea\admin\pages\service_editor\infrastructure\services\Services_Settings_Fields_Service;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Active_Groups_Provider;
use bpmj\wpidea\admin\pages\settings\App_Settings_Active_Groups_Provider;
use bpmj\wpidea\admin\settings\core\services\Settings_Api;
use bpmj\wpidea\admin\settings\core\services\Settings_Events;
use bpmj\wpidea\admin\settings\infrastructure\services\App_Settings_Fields_Service;
use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\app\digital_products\Digital_Products_App_Service;
use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\app\quizzes\Quizzes_App_Service;
use bpmj\wpidea\app\services\Services_App_Service;
use bpmj\wpidea\courses\core\dto\Course_To_Dto_Mapper;
use bpmj\wpidea\digital_products\dto\Digital_Product_To_Dto_Mapper;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\quiz\dto\Quiz_To_Dto_Mapper;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;

class Settings_Api_Factory
{
    private Settings_Group_Factory $group_factory;
    private Service_Editor_Active_Groups_Provider $service_editor_active_groups_provider;
    private Product_To_DTO_Mapper $product_to_DTO_mapper;
    private Interface_Product_Repository $product_repository;
    private Services_App_Service $services_app_service;
    private Checkboxes_Value_Changer $product_checkboxes_value_changer;
    private App_Settings_Fields_Service $app_settings_fields_service;
    private App_Settings_Active_Groups_Provider $app_settings_active_groups_provider;
    private Digital_Product_Editor_Active_Groups_Provider $digital_product_editor_active_groups_provider;
    private Digital_Products_App_Service $digital_products_app_service;
    private Digital_Product_To_Dto_Mapper $digital_product_to_dto_mapper;
    private Course_Editor_Active_Groups_Provider $course_editor_active_groups_provider;
    private Courses_App_Service $courses_app_service;
    private Course_To_Dto_Mapper $course_to_dto_mapper;
    private Edit_Course $edit_course;
    private Course_Checkboxes_Value_Changer $course_checkboxes_value_changer;
    private Bundle_Editor_Active_Groups_Provider $bundle_editor_active_groups_provider;
    private Bundles_App_Service $bundles_app_service;
    private Interface_Events $events;
    private Physical_Products_App_Service $physical_products_app_service;
    private Physical_Product_Editor_Active_Groups_Provider $physical_product_editor_active_groups_provider;
    private Settings_Events $settings_events;
    private Product_Events $product_events;
    private Quiz_Editor_Active_Groups_Provider $quiz_editor_active_groups_provider;
    private Quizzes_App_Service $quizzes_app_service;
    private Quiz_To_Dto_Mapper $quiz_to_dto_mapper;
	private Quiz_Settings_Checkboxes_Value_Changer $quiz_settings_checkboxes_value_changer;


	public function __construct(
        Settings_Group_Factory $group_factory,
        Service_Editor_Active_Groups_Provider $service_editor_active_groups_provider,
        Product_To_DTO_Mapper $product_to_DTO_mapper,
        Interface_Product_Repository $product_repository,
        Services_App_Service $services_app_service,
        Checkboxes_Value_Changer $product_checkboxes_value_changer,
        App_Settings_Fields_Service $app_settings_fields_service,
        App_Settings_Active_Groups_Provider $app_settings_active_groups_provider,
        Digital_Product_Editor_Active_Groups_Provider $digital_product_editor_active_groups_provider,
        Digital_Products_App_Service $digital_products_app_service,
        Digital_Product_To_Dto_Mapper $digital_product_to_dto_mapper,
        Course_Editor_Active_Groups_Provider $course_editor_active_groups_provider,
        Courses_App_Service $courses_app_service,
        Course_To_Dto_Mapper $course_to_dto_mapper,
        Edit_Course $edit_course,
        Course_Checkboxes_Value_Changer $course_checkboxes_value_changer,
        Bundle_Editor_Active_Groups_Provider $bundle_editor_active_groups_provider,
        Bundles_App_Service $bundles_app_service,
        Interface_Events $events,
        Physical_Products_App_Service $physical_products_app_service,
        Physical_Product_Editor_Active_Groups_Provider $physical_product_editor_active_groups_provider,
        Settings_Events $settings_events,
        Product_Events $product_events,
        Quiz_Editor_Active_Groups_Provider $quiz_editor_active_groups_provider,
        Quizzes_App_Service $quizzes_app_service,
        Quiz_To_Dto_Mapper $quiz_to_dto_mapper,
	    Quiz_Settings_Checkboxes_Value_Changer $quiz_settings_checkboxes_value_changer
    ) {
        $this->group_factory = $group_factory;
        $this->service_editor_active_groups_provider = $service_editor_active_groups_provider;
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
        $this->product_repository = $product_repository;
        $this->services_app_service = $services_app_service;
        $this->product_checkboxes_value_changer = $product_checkboxes_value_changer;
        $this->app_settings_fields_service = $app_settings_fields_service;
        $this->app_settings_active_groups_provider = $app_settings_active_groups_provider;
        $this->digital_product_editor_active_groups_provider = $digital_product_editor_active_groups_provider;
        $this->digital_products_app_service = $digital_products_app_service;
        $this->digital_product_to_dto_mapper = $digital_product_to_dto_mapper;
        $this->course_editor_active_groups_provider = $course_editor_active_groups_provider;
        $this->courses_app_service = $courses_app_service;
        $this->course_to_dto_mapper = $course_to_dto_mapper;
        $this->edit_course = $edit_course;
        $this->course_checkboxes_value_changer = $course_checkboxes_value_changer;
        $this->bundle_editor_active_groups_provider = $bundle_editor_active_groups_provider;
        $this->bundles_app_service = $bundles_app_service;
        $this->events = $events;
        $this->physical_products_app_service = $physical_products_app_service;
        $this->physical_product_editor_active_groups_provider = $physical_product_editor_active_groups_provider;
        $this->settings_events = $settings_events;
        $this->product_events = $product_events;
        $this->quiz_editor_active_groups_provider = $quiz_editor_active_groups_provider;
        $this->quizzes_app_service = $quizzes_app_service;
        $this->quiz_to_dto_mapper = $quiz_to_dto_mapper;
		$this->quiz_settings_checkboxes_value_changer = $quiz_settings_checkboxes_value_changer;
	}

    public function create_service_settings_api(Product_ID $product_id): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->service_editor_active_groups_provider,
            new Services_Settings_Fields_Service(
                $product_id,
                $this->product_to_DTO_mapper,
                $this->product_repository,
                $this->services_app_service,
                $this->product_checkboxes_value_changer,
                $this->product_events
            )
        );
    }

    public function create_physical_product_settings_api(Product_ID $product_id): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->physical_product_editor_active_groups_provider,
            new Physical_Product_Settings_Fields_Service(
                $product_id,
                $this->product_to_DTO_mapper,
                $this->product_repository,
                $this->physical_products_app_service,
                $this->product_checkboxes_value_changer,
                $this->product_events
            )
        );
    }

    public function create_app_settings_api(): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->app_settings_active_groups_provider,
            $this->app_settings_fields_service
        );
    }

    public function create_bundle_settings_api(Product_ID $product_id): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->bundle_editor_active_groups_provider,
            new Bundles_Settings_Fields_Service(
                $product_id,
                $this->product_to_DTO_mapper,
                $this->product_repository,
                $this->product_checkboxes_value_changer,
                $this->bundles_app_service,
                $this->events,
                $this->product_events
            )
        );
    }

    public function create_digital_product_settings_api(Product_ID $product_id): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->digital_product_editor_active_groups_provider,
            new Digital_Products_Settings_Fields_Service(
                $product_id,
                $this->product_to_DTO_mapper,
                $this->product_repository,
                $this->product_checkboxes_value_changer,
                $this->digital_products_app_service,
                $this->digital_product_to_dto_mapper,
                $this->product_events
            )
        );
    }

    public function create_course_settings_api(Course_ID $edited_course_id): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->course_editor_active_groups_provider,
            new Courses_Settings_Fields_Service(
                $edited_course_id,
                $this->product_to_DTO_mapper,
                $this->product_repository,
                $this->course_checkboxes_value_changer,
                $this->courses_app_service,
                $this->course_to_dto_mapper,
                $this->edit_course,
                $this->events,
                $this->product_events
            )
        );
    }

    public function create_quiz_settings_api(Quiz_ID $edited_quiz_id): Settings_Api
    {
        return new Settings_Api(
            $this->group_factory,
            $this->quiz_editor_active_groups_provider,
            new Quizzes_Settings_Fields_Service(
                $edited_quiz_id,
                $this->quizzes_app_service,
                $this->quiz_to_dto_mapper,
	            $this->quiz_settings_checkboxes_value_changer
            )
        );
    }
}
<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\configuration;

use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_Link_Generator_Group;
use bpmj\wpidea\admin\pages\product_editor\core\fields\Link_Generator_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\learning\course\Course_ID;

class Link_Generator_Group extends Abstract_Link_Generator_Group
{
    private const LINK_GENERATOR = 'link_generator_field';

    private Courses_App_Service $courses_app_service;

    public function __construct(
        Courses_App_Service $courses_app_service
    )
    {
        $this->courses_app_service = $courses_app_service;
    }

    protected function get_id_query_arg_name(): string
    {
        return Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME;
    }

    protected function get_link_generator_field(): Abstract_Setting_Field
    {
        $product_id = $this->get_product_id_by_course_id();

	    $field = new Link_Generator_Field(
		    self::LINK_GENERATOR,
		    $this->translator->translate( 'service_editor.sections.link_generator.link_generator' )
	    );

	    $field->set_product_id(
            $product_id
        )->set_variable_prices_links_html(
           $this->get_variable_prices_add_to_cart_links_html($product_id)
        );

		if(!$this->app_settings->get(Settings_Const::ENABLE_BUY_AS_GIFT)){
			$field->disable();
		}

		return $field;
    }

    private function get_variable_prices_add_to_cart_links_html(?int $product_id): ?string
    {
        if(!$product_id){
            return null;
        }

        return $this->courses_app_service->get_variable_prices_add_to_cart_links_html($product_id);
    }

    private function get_product_id_by_course_id(): ?int
    {
        $course_id = $this->current_request->get_query_arg($this->get_id_query_arg_name());

        if(!$course_id){
            return null;
        }

        $course_with_product = $this->courses_app_service->find_course(new Course_ID($course_id));

        if(!$course_with_product){
            return null;
        }

        return $course_with_product->get_product_id() ? $course_with_product->get_product_id()->to_int() : null;
    }


}
<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\configuration;

use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_Invoices_Group;
use bpmj\wpidea\admin\pages\product_editor\core\fields\Flat_Rate_Tax_Symbol_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\admin\pages\product_editor\core\fields\Code_Gtu_Field;
use bpmj\wpidea\sales\product\model\Product_ID;

class Invoices_Group extends Abstract_Invoices_Group
{
    private const GTU_VARIABLE_PRICES = 'gtu_variable_prices';
    private const FLAT_RATE_TAX_SYMBOL_VARIABLE_PRICES = 'flat_rate_tax_symbol_variable_prices';

    protected function get_id_query_arg_name(): string
    {
        return Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME;
    }

    protected function get_flat_rate_tax_symbol_field(): Abstract_Setting_Field
    {
        $field_name = !$this->get_variable_prices() ? self::FLAT_RATE_TAX_SYMBOL : self::FLAT_RATE_TAX_SYMBOL_VARIABLE_PRICES;

        return (new Flat_Rate_Tax_Symbol_Field(
            $field_name,
            $this->translator->translate('service_editor.sections.invoices.flat_rate_tax_symbol'),
            null,
            null,
            null,
            $this->get_flat_rate_tax_symbol_field_options()
        ))->set_variable_prices(
            $this->get_variable_prices()
        );
    }

    protected function get_gtu_field(): Abstract_Setting_Field
    {
        $field_name = !$this->get_variable_prices() ? self::GTU : self::GTU_VARIABLE_PRICES;

        return (new Code_Gtu_Field(
            $field_name,
            $this->translator->translate('service_editor.sections.invoices.gtu'),
            null,
            null,
            null,
            $this->get_gtu_field_options()
        ))->set_variable_prices(
            $this->get_variable_prices()
        );
    }

    private function get_variable_prices(): ?array
    {
        $product_id = $this->get_product_id_by_course_id();

        $price_variants = $this->product_api->get_price_variants($product_id);

        if(!$price_variants->has_pricing_variants){
            return null;
        }

        return $price_variants->variable_prices;
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
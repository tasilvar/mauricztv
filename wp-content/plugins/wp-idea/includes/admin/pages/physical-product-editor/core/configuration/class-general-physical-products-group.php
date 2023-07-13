<?php

namespace bpmj\wpidea\admin\pages\physical_product_editor\core\configuration;

use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_General_Products_Group;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\settings\web\Settings_Info_Box;

class General_Physical_Products_Group extends Abstract_General_Products_Group
{
    protected function get_translate_prefix(): string
    {
        return 'physical_product_editor';
    }

    protected function get_id_query_arg_name(): string
    {
        return Physical_Product_Editor_Page_Renderer::PHYSICAL_PRODUCT_ID_QUERY_ARG_NAME;
    }

    protected function add_price_fieldset(): void
    {
        $fields_collection = new Fields_Collection();

        if (empty($this->app_settings->get(Settings_Const::DELIVERY_PRICE))) {
            $fields_collection->add($this->get_delivery_info_field());
        }

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.price'),
            $fields_collection
                ->add($this->get_price_field())
                ->add($this->get_special_offer_field())
        );
    }

    protected function get_delivery_info_field(): Abstract_Setting_Field
    {
        return new Message($this->translate_with_prefix('sections.general.delivery_price_info'), Settings_Info_Box::INFO_BOX_TYPE_WARNING);
    }
}
<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\fields\Link_Generator_Field;
use bpmj\wpidea\admin\settings\core\collections\{Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field, Message};
use bpmj\wpidea\admin\settings\Settings_Const;

abstract class Abstract_Link_Generator_Group extends Abstract_Settings_Group
{
    private const LINK_GENERATOR = 'link_generator_field';

    public function get_name(): string
    {
        return 'link_generator';
    }

    abstract protected function get_id_query_arg_name();

    public function register_fields(): void
    {
        $this->add_field((
            new Message($this->translator->translate('service_editor.sections.link_generator.message_1'))
        ));

        $this->add_fieldset(
            $this->translator->translate('service_editor.sections.link_generator.fieldset.general'),
            (new Fields_Collection())
                ->add($this->get_link_generator_field())
        );
    }

    protected function get_link_generator_field(): Abstract_Setting_Field
    {
        $product_id = $this->current_request->get_query_arg($this->get_id_query_arg_name());

	    $field = new Link_Generator_Field(
		    self::LINK_GENERATOR,
		    $this->translator->translate( 'service_editor.sections.link_generator.link_generator' )
	    );

	    $field->set_product_id(
            (int)$product_id
        );

		if(!$this->app_settings->get(Settings_Const::ENABLE_BUY_AS_GIFT)) {
			$field->disable();
		}

		return $field;
    }
}
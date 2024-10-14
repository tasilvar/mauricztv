<?php

namespace bpmj\wpidea\admin\pages\digital_product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_Mailings_Group;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\pages\digital_product_editor\core\fields\Files_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Renderer;

class Files_Group extends Abstract_Settings_Group
{
    private const FILES = 'included_files';

    public function get_name(): string
    {
        return 'files';
    }

    public function register_fields(): void
    {
        $this->add_field($this->get_info_field());
        $this->add_field($this->get_files_field());
    }

    private function get_files_field(): Abstract_Setting_Field
    {
        return new Files_Field(self::FILES, $this->get_save_files_endpoint());
    }

    private function get_info_field(): Abstract_Setting_Field
    {
        return new Message($this->translator->translate('digital_product_editor.sections.files.info'));
    }

    private function get_save_files_endpoint(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            Digital_Product_Editor_Page_Renderer::DIGITAL_PRODUCT_ID_QUERY_ARG_NAME => $this->get_product_id()
        ]);
    }

    private function get_product_id(): int
    {
        $id = $this->current_request->get_query_arg(Digital_Product_Editor_Page_Renderer::DIGITAL_PRODUCT_ID_QUERY_ARG_NAME);

        return (int)$id;
    }
}
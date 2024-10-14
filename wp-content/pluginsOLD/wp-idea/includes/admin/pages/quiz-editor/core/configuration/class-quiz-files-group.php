<?php

namespace bpmj\wpidea\admin\pages\quiz_editor\core\configuration;

use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\pages\quiz_editor\core\fields\Files_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;

class Quiz_Files_Group extends Abstract_Settings_Group
{
    private const GROUP_NAME = 'files';
    private const FILES = 'files';

    public function get_name(): string
    {
        return self::GROUP_NAME;
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

    private function get_save_files_endpoint(): string
    {
        return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME => $this->get_product_id()
        ]);
    }

    private function get_product_id(): int
    {
        $id = $this->current_request->get_query_arg(Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME);

        return (int)$id;
    }

    private function get_info_field(): Abstract_Setting_Field
    {
        return new Message($this->translator->translate('quiz_editor.sections.files.info'));
    }
}
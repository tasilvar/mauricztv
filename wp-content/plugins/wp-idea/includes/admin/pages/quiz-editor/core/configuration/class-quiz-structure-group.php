<?php

namespace bpmj\wpidea\admin\pages\quiz_editor\core\configuration;

use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\quiz_editor\core\fields\Questions_Structure_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\learning\quiz\Quiz_ID;

class Quiz_Structure_Group extends Abstract_Settings_Group
{
    private const QUESTIONS_STRUCTURE = 'questions_structure';
    public const GROUP_NAME = 'structure';

    public function get_name(): string
    {
        return self::GROUP_NAME;
    }

    public function register_fields(): void
    {
        $this->add_field($this->get_info_field());
        $this->add_field($this->get_questions_structure_field());
    }

    private function get_questions_structure_field(): Abstract_Setting_Field
    {
        $quiz_id = $this->current_request->get_query_arg(Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME);

        return new Questions_Structure_Field(
            self::QUESTIONS_STRUCTURE,
            new Quiz_ID((int)$quiz_id),
            $this->translator
        );
    }

    private function get_info_field(): Abstract_Setting_Field
    {
        return new Message($this->translator->translate('quiz_editor.sections.structure.info'));
    }
}
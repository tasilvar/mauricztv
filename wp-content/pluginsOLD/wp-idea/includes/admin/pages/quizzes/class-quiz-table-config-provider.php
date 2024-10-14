<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\learning\quiz\Resolved_Quiz;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Role_Factory;

class Quiz_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const DEFAULT_HIDDEN_COLUMNS = ['user_email'];

    private const TABLE_ID = 'wpi_quizzes_table';

    private Interface_Translator $translator;
    private Quiz_Table_Data_Provider $data_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Capability_Factory $user_capability_factory;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Interface_Translator $translator,
        Quiz_Table_Data_Provider $data_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Capability_Factory $user_capability_factory,
        User_Role_Factory $user_role_factory
    ) {
        $this->translator = $translator;
        $this->data_provider = $data_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_capability_factory = $user_capability_factory;
        $this->user_role_factory = $user_role_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_table_columns_config()
        )
            ->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT))
            ->set_required_caps($this->get_required_caps())
            ->set_default_hidden_columns(self::DEFAULT_HIDDEN_COLUMNS)
            ->disable_multi_sort()
            ->set_top_panel_buttons($this->get_table_actions())
            ->set_row_actions($this->get_row_actions());
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_table_actions(): array
    {
        return [];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('quizzes.actions.show_answers'),
                'class' => 'show-quiz-answers',
                'use_json_property_as_url' => 'details_url',
                'target' => '_blank'
            ],
            [
                'type' => 'link',
                'label' => $this->translator->translate('quizzes.actions.show_student_profile'),
                'class' => 'show-student-profile',
                'use_json_property_as_url' => 'student_profile_url',
                'target' => '_blank'
            ],
            [
                'type' => 'link',
                'label' => $this->translator->translate('quizzes.actions.edit_quiz'),
                'class' => 'edit-quiz',
                'use_json_property_as_url' => 'quiz_edit_url',
                'target' => '_blank'
            ],
        ];
    }

    private function get_status_filter_values(): array
    {
        return [
            [
                'value' => Resolved_Quiz::RESULT_NOT_RATED_YET,
                'label' => $this->translator->translate('quiz.result.' . Resolved_Quiz::RESULT_NOT_RATED_YET)
            ],
            [
                'value' => Resolved_Quiz::RESULT_PASSED,
                'label' => $this->translator->translate('quiz.result.' . Resolved_Quiz::RESULT_PASSED)
            ],
            [
                'value' => Resolved_Quiz::RESULT_FAILED,
                'label' => $this->translator->translate('quiz.result.' . Resolved_Quiz::RESULT_FAILED)
            ]
        ];
    }

    public function get_table_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('quizzes.column.id'),
                'always_visible' => true,
                'use_json_property_as_link' => 'details_url',
                'type' => 'id',
                'prefix' => '#'
            ],
            [
                'property' => 'course',
                'label' => $this->translator->translate('quizzes.column.course'),
                'use_json_property_as_link' => 'course_edit_url',
                'sortable' => false,
                'filter' => 'text'
            ],
            [
                'property' => 'title',
                'label' => $this->translator->translate('quizzes.column.title'),
                'use_json_property_as_link' => 'quiz_edit_url',
                'filter' => 'text'
            ],
            [
                'property' => 'user_email',
                'label' => $this->translator->translate('quizzes.column.email'),
                'filter' => 'text'
            ],
            [
                'property' => 'user_full_name',
                'label' => $this->translator->translate('quizzes.column.full_name'),
                'sortable' => false,
                'filter' => 'text'
            ],
            [
                'property' => 'points',
                'label' => $this->translator->translate('quizzes.column.points'),
            ],
            [
                'property' => 'result',
                'label' => $this->translator->translate('quizzes.column.result'),
                'type' => 'status',
                'use_json_property_as_label' => 'result_label',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values(),
            ],
            [
                'property' => 'date',
                'label' => $this->translator->translate('quizzes.column.date'),
                'type' => 'date',
                'filter' => 'date_range',
            ],
        ];
    }

    private function get_required_caps(): User_Capability_Collection
    {
        return (new User_Capability_Collection())
            ->add($this->user_capability_factory->create_from_name(Caps::CAP_MANAGE_QUIZZES));
    }
}
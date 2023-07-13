<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\students;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Role_Factory;

class Student_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_students_table';

    private Interface_Translator $translator;
    private Interface_Readable_Course_Repository $courses_repository;
    private Student_Table_Data_Provider $student_table_data_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Role_Factory $user_role_factory;
    private User_Capability_Factory $user_caps_factory;

    public function __construct(
        Interface_Translator $translator,
        Interface_Readable_Course_Repository $courses_repository,
        Student_Table_Data_Provider $student_table_data_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Role_Factory $user_role_factory,
        User_Capability_Factory $user_caps_factory
    ) {
        $this->translator = $translator;
        $this->courses_repository = $courses_repository;
        $this->student_table_data_provider = $student_table_data_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_role_factory = $user_role_factory;
        $this->user_caps_factory = $user_caps_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        $caps = new User_Capability_Collection();
        $students_cap = $this->user_caps_factory->create_from_name(Caps::CAP_MANAGE_STUDENTS);
        $caps->add($students_cap);

        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->student_table_data_provider,
            $this->get_table_columns_config()
        )
            ->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT))
            ->set_required_caps($caps)
            ->disable_multi_sort()
            ->set_row_actions($this->get_row_actions());
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    public function get_table_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('students.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'always_visible' => true,
                'use_json_property_as_link' => 'edit_url',
                'target' => '_blank'
            ],
            [
                'property' => 'login',
                'label' => $this->translator->translate('students.column.user_login'),
                'use_json_property_as_link' => 'edit_url',
                'filter' => 'text'
            ],
            [
                'property' => 'email',
                'label' => $this->translator->translate('students.column.email'),
                'filter' => 'text',
            ],
            [
                'property' => 'full_name',
                'label' => $this->translator->translate('students.column.name'),
                'filter' => 'text'
            ],
            [
                'property' => 'courses',
                'label' => $this->translator->translate('students.column.courses'),
                'filter' => 'multiselect',
                'filter_options' => $this->get_filter_options(),
                'sortable' => false
            ]
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('students.edit'),
                'class' => 'edit',
                'type' => 'link',
                'use_json_property_as_url' => 'edit_url',
                'target' => '_blank'
            ]
        ];
    }

    private function get_filter_options(): array
    {
        $options = $this->courses_repository->find_all()->to_array();
        foreach ($options as $key => $option) {
            $encoded_title = htmlspecialchars($option['title'], ENT_QUOTES);

            unset($options[$key]['title']);

            $options[$key]['label'] = $encoded_title;
            $options[$key]['value'] = $option['id']->to_int();
        }

        return $options;
    }
}
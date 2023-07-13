<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\certificates;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Permissions_Wp_Service;
use bpmj\wpidea\user\User_Role_Factory;

class Certificates_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{

    private const TABLE_ID = 'wpi-certificates-dynamic-table';

    private Certificates_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Readable_Course_Repository $course_repository;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Capability_Factory $user_capability_factory;
    private User_Role_Factory $user_role_factory;
    private Interface_Current_User_Getter $current_user_getter;
    private Interface_User_Permissions_Service $permissions_service;

    public function __construct(
        Certificates_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Readable_Course_Repository $course_repository,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Capability_Factory $user_capability_factory,
        User_Role_Factory $user_role_factory,
        Interface_Current_User_Getter $current_user_getter,
        Interface_User_Permissions_Service $permissions_service
    )
    {
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->course_repository = $course_repository;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_capability_factory = $user_capability_factory;
        $this->user_role_factory = $user_role_factory;
        $this->permissions_service = $permissions_service;
        $this->current_user_getter = $current_user_getter;
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
            ->disable_multi_sort()
            ->set_row_actions($this->get_row_actions());
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_table_columns_config(): array
    {
        return [
            [
                'property'                  => 'id',
                'label'                     => $this->translator->translate('certificates.column.id'),
                'type'                      => 'id',
                'prefix'                    => '#',
                'always_visible'            => true,
                'use_json_property_as_link' => 'edit_url',
                'target'                    => '_blank'
            ],
            [
                'property'       => 'course',
                'label'          => $this->translator->translate('certificates.column.course'),
                'filter'         => 'select',
                'filter_options' => $this->get_course_column_options()
            ],
            [
                'property' => 'full_name',
                'label'    => $this->translator->translate('certificates.column.full_name'),
                'filter'   => 'text',
                'sortable' => false
            ],
            [
                'property' => 'email',
                'label'    => $this->translator->translate('certificates.column.email'),
                'filter'   => 'text',
                'sortable' => false
            ],
            [
                'property' => 'certificate_number',
                'label'    => $this->translator->translate('certificates.column.certificate_number'),
                'filter'   => 'text',
                'sortable' => false
            ],
            [
                'property' => 'created',
                'label'    => $this->translator->translate('certificates.column.created'),
                'filter'   => 'date_range',
            ]
        ];
    }

    private function get_course_column_options(): array
    {
        return array_map(fn($course) => [
            'value' => $this->course_repository->get_course_panel_id($course['id']),
            'label' => htmlspecialchars($course['title'], ENT_QUOTES)
        ], $this->course_repository->find_all()->to_array());
    }

    private function get_row_actions(): array
    {
        if (!$this->permissions_service->has_capability(
            $this->current_user_getter->get(),
            $this->user_capability_factory->create_from_name(Caps::CAP_VIEW_SENSITIVE_DATA)
        )) {
            return [];
        }

        return [
            [
                'label'                    => $this->translator->translate('certificates.regenerate'),
                'class'                    => 'regenerate',
                'type'                     => 'link',
                'use_json_property_as_url' => 'regenerate_url',
            ],
            [
                'label'                    => $this->translator->translate('certificates.download'),
                'class'                    => 'download',
                'type'                     => 'link',
                'use_json_property_as_url' => 'download_url',
                'target'                   => '_blank'
            ]
        ];
    }

    private function get_required_caps(): User_Capability_Collection
    {
        return (new User_Capability_Collection())
            ->add($this->user_capability_factory->create_from_name(Caps::CAP_MANAGE_CERTIFICATES));
    }
}
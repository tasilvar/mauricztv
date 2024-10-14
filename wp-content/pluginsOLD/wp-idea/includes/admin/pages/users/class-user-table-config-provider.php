<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\users;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Users_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Role_Factory;

class User_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_users_table';

    private Interface_Translator $translator;
    private Interface_User_Permissions_Service $user_permissions_service;
    private User_Table_Data_Provider $student_table_data_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Role_Factory $user_role_factory;
    private User_Capability_Factory $user_caps_factory;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Translator $translator,
        Interface_User_Permissions_Service $user_permissions_service,
        User_Table_Data_Provider $student_table_data_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Role_Factory $user_role_factory,
        User_Capability_Factory $user_caps_factory,
        Interface_Url_Generator $url_generator
    ) {
        $this->translator = $translator;
        $this->user_permissions_service = $user_permissions_service;
        $this->student_table_data_provider = $student_table_data_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_role_factory = $user_role_factory;
        $this->user_caps_factory = $user_caps_factory;
        $this->url_generator = $url_generator;
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
            ->set_row_actions($this->get_row_actions())
            ->set_bulk_actions($this->get_bulk_actions())
            ->set_top_panel_buttons($this->get_top_panel_buttons());
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
                'label' => $this->translator->translate('users.column.id'),
                'use_json_property_as_link' => 'edit_url',
                'always_visible' => true,
                'type' => 'id',
                'prefix' => '#'
            ],
            [
                'property' => 'login',
                'label' => $this->translator->translate('users.column.name'),
                'filter' => 'text',
                'use_json_property_as_link' => 'edit_url',
            ],
            [
                'property' => 'full_name',
                'label' => $this->translator->translate('users.column.full_name'),
                'filter' => 'text',
            ],
            [
                'property' => 'email',
                'label' => $this->translator->translate('users.column.email'),
                'filter' => 'text'
            ],
            [
                'property' => 'roles',
                'label' => $this->translator->translate('users.column.roles'),
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
                'label' => $this->translator->translate('users.actions.edit'),
                'class' => 'edit',
                'type' => 'link',
                'use_json_property_as_url' => 'edit_url'
            ],
            [
                'label' => $this->translator->translate('users.actions.delete'),
                'class' => 'delete',
                'type' => 'link',
                'use_json_property_as_url' => 'delete_url',
                'disabled' => 'disabled_button_for_logged_user'
            ],
            [
                'label' => $this->translator->translate('users.actions.send_link'),
                'class' => 'send_link',
                'type' => 'link',
                'use_json_property_as_url' => 'send_link_url',
                'disabled' => 'disabled_button_for_logged_user'
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'target' => $this->get_added_user_url(),
                'label' => $this->translator->translate('users.actions.added_user'),
                'class' => 'added-user'
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('users.actions.delete'),
                'class' => 'delete-users',
                'action' => 'redirect',
                'url' => $this->url_generator->generate(Admin_Users_Controller::class, 'delete_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('users.actions.loading')
            ],
            [
                'label' => $this->translator->translate('users.actions.send_link'),
                'class' => 'send-links',
                'action' => 'redirect',
                'url' => $this->url_generator->generate(Admin_Users_Controller::class, 'send_links_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('users.actions.loading')
            ]
        ];
    }

    private function get_added_user_url(): string
    {
        return $this->url_generator->generate_admin_page_url('user-new.php');
    }

    private function get_filter_options(): array
    {
        $options = [];

        $all_roles = $this->user_permissions_service->get_all_roles();
        foreach ($all_roles as $role) {
            $options[] = [
                'label' => $this->translator->translate('users.column.role.' . $role->get_name()),
                'value' => $role->get_name(),
            ];
        }
        return array_reverse($options);
    }
}
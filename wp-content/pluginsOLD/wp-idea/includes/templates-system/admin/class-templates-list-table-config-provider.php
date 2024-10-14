<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\templates_system\admin\ajax\Group_Settings_Ajax_Handler;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Role_Factory;
use bpmj\wpidea\nonce\Nonce_Handler;

class Templates_List_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const AJAX_NONCE_NAME = 'bpmj_template_groups_security_token';

    private const TABLE_ID = 'wpi_templates_list';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Interface_Translator $translator;
    private Templates_List_Table_Data_Provider $data_provider;
    private User_Role_Factory $user_role_factory;
    private User_Capability_Factory $user_capability_factory;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Interface_Translator $translator,
        Templates_List_Table_Data_Provider $data_provider,
        User_Role_Factory $user_role_factory,
        User_Capability_Factory $user_capability_factory,
        Interface_Url_Generator $url_generator
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->translator = $translator;
        $this->data_provider = $data_provider;
        $this->user_role_factory = $user_role_factory;
        $this->user_capability_factory = $user_capability_factory;
        $this->url_generator = $url_generator;
    }

    public function get_config(): Dynamic_Table_Config
    {
        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_table_columns_config()
        )
            ->disable_multi_sort()
            ->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT))
            ->set_required_caps($this->get_required_caps())
            ->disable_allow_columns_hiding()
            ->disable_pagination()
            ->disable_export()
            ->set_row_actions($this->get_row_actions())
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
                'property' => 'layout_type',
                'label' => $this->translator->translate('templates_list.column.layout_type'),
            ],
            [
                'property' => 'layout_description',
                'label' => $this->translator->translate('templates_list.column.layout_description'),
            ],
        ];
    }

    private function get_required_caps(): User_Capability_Collection
    {
        return (new User_Capability_Collection())
            ->add($this->user_capability_factory->create_from_name(Caps::CAP_MANAGE_SETTINGS));
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'class' => 'edit-template',
                'label' => $this->translator->translate('templates_list.actions.edit'),
                'use_json_property_as_url' => 'edit_url',
            ],
            [
                'type' => 'async-action',
                'class' => 'restore-template',
                'status_active_value' => 'on',
                'label_active' => $this->translator->translate('templates_list.actions.restore.active'),
                'confirm_message' => $this->translator->translate('templates_list.actions.restore.confirm_message'),
                'use_json_property_as_url' => 'restore_url',
            ],
        ];
    }

    private function get_top_panel_buttons(): array
    {
        $group = Template_Group::get_active_group();

        return [
            [
                'target' => $group->get_color_settings_url(),
                'label' => $this->translator->translate('templates_list.actions.colors_settings'),
                'class' => 'create-course'
            ],
            [
                'type' => 'button-wpi-popup',
                'text' => $this->translator->translate('templates_list.actions.settings'),
                'classes' => 'dynamic-table__header__buttons__button create-course',
                'ajax' => [
                    'url' => $this->url_generator->generate_admin_page_url('admin-ajax.php', [
                        'action' => Group_Settings_Ajax_Handler::AJAX_GET_SETTINGS_ACTION_NAME,
                        'group_id' => $group->get_id()->stringify(),
                        'nonce' => Nonce_Handler::create(self::AJAX_NONCE_NAME)
                    ]),
                    'method' => 'GET',
                    'response_property_with_content' => 'data',
                ],
                'call_js_function_after_load_content' => 'bind_template_group_setting',
            ],
        ];
    }
}
<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\webhooks;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Role_Factory;

class Webhook_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_webhooks_table';

    private Webhook_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Interface_Webhooks_Form $webhooks_form;
    private Webhooks_Table_Data_Parser $data_parser;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Webhook_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Interface_Webhooks_Form $webhooks_form,
        Webhooks_Table_Data_Parser $data_parser,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Role_Factory $user_role_factory
    ) {
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->webhooks_form = $webhooks_form;
        $this->data_parser = $data_parser;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_role_factory = $user_role_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        )
            ->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT))
            ->disable_allow_columns_hiding()
            ->disable_export()
            ->set_top_panel_buttons($this->get_table_actions())
            ->set_row_actions($this->get_row_actions());
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_event_filter_values(): array
    {
        $filter_values = [];

        $webhook_event_types = $this->webhooks_form->get_webhook_event_types();

        foreach ($webhook_event_types as $event) {
            foreach ($event as $key => $value) {
                $filter_values[] = [
                    'value' => $key,
                    'label' => $value
                ];
            }
        }

        return $filter_values;
    }

    private function get_status_filter_values(): array
    {
        $filter_values = [];

        foreach (Webhook_Status::VALID_STATUS as $status) {
            $filter_values[] = [
                'value' => $status,
                'label' => $this->data_parser->get_name_status_label($status)
            ];
        }

        return $filter_values;
    }

    private function get_table_actions(): array
    {
        return [
            [
                'target' => $this->get_add_webhook_url(),
                'label' => $this->translator->translate('webhooks.actions.add_webhook'),
                'class' => 'add-webhook'
            ]
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('webhooks.actions.edit'),
                'class' => 'edit-webhook',
                'use_json_property_as_url' => 'edit_webhook'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('webhooks.actions.delete'),
                'class' => 'delete-webhook',
                'use_json_property_as_url' => 'delete_webhook',
                'loading_message' => $this->translator->translate('webhooks.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('webhooks.actions.delete.confirm')
            ],
            [
                'type' => 'link',
                'label' => $this->translator->translate('webhooks.actions.documentation'),
                'class' => 'doc-webhook',
                'use_json_property_as_url' => 'doc_webhook'
            ],
            [
                'type' => 'async-action',
                'status_name' => 'status',
                'status_active_value' => Webhook_Status::ACTIVE,
                'label_active' => $this->translator->translate('webhooks.actions.status.active'),
                'label_inactive' => $this->translator->translate('webhooks.actions.status.inactive'),
                'class' => 'doc-status',
                'use_json_property_as_url' => 'change_status_webhook',
                'loading_message' => $this->translator->translate('webhooks.actions.status.loading')
            ]
        ];
    }

    private function get_add_webhook_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::WEBHOOKS,
            'view' => 'add'
        ]);
    }

    private function get_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('webhooks.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'use_json_property_as_link' => 'edit_webhook',
            ],
            [
                'property' => 'type_of_event',
                'label' => $this->translator->translate('webhooks.column.type_of_event'),
                'use_json_property_as_label' => 'type_of_event_label',
                'filter' => 'select',
                'filter_options' => $this->get_event_filter_values()
            ],
            [
                'property' => 'url',
                'label' => $this->translator->translate('webhooks.column.url'),
                'filter' => 'text'
            ],
            [
                'property' => 'status',
                'label' => $this->translator->translate('webhooks.column.status'),
                'type' => 'status',
                'use_json_property_as_label' => 'status_label',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values()
            ],
        ];
    }
}
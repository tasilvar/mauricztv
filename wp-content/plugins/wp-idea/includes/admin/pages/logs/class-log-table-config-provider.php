<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\logs;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Logs_Controller;
use bpmj\wpidea\helpers\Interface_Debug_Helper;
use bpmj\wpidea\infrastructure\logs\model\Log_Level;
use bpmj\wpidea\infrastructure\logs\model\Log_Source;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Role_Factory;

class Log_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_logs_table';

    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Interface_Debug_Helper $debug_helper;
    private Log_Table_Data_Provider $data_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Interface_Debug_Helper $debug_helper,
        Log_Table_Data_Provider $data_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Role_Factory $user_role_factory
    ) {
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->debug_helper = $debug_helper;
        $this->data_provider = $data_provider;
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
            ->show_refresh_button()
            ->disable_allow_columns_hiding()
            ->set_row_actions($this->get_row_actions())
            ->set_top_panel_buttons($this->get_table_actions());
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_status_filter_values(): array
    {
        $filter_values = [];

        foreach (Log_Level::VALID_LEVELS as $level) {
            $filter_values[] = [
                'value' => $level,
                'label' => $this->translator->translate('logs.level.' . $level)
            ];
        }

        return $filter_values;
    }

    private function get_source_filter_values(): array
    {
        return [
            [
                'value' => Log_Source::DEFAULT,
                'label' => $this->translator->translate('logs.source.' . Log_Source::DEFAULT)
            ],
            [
                'value' => Log_Source::ORDERS,
                'label' => $this->translator->translate('logs.source.' . Log_Source::ORDERS)
            ],
            [
                'value' => Log_Source::COMMUNICATION,
                'label' => $this->translator->translate('logs.source.' . Log_Source::COMMUNICATION)
            ],
            [
                'value' => Log_Source::INVOICE_FAKTUROWNIA,
                'label' => $this->translator->translate('logs.source.' . Log_Source::INVOICE_FAKTUROWNIA)
            ],
            [
                'value' => Log_Source::INVOICE_IFIRMA,
                'label' => $this->translator->translate('logs.source.' . Log_Source::INVOICE_IFIRMA)
            ],
            [
                'value' => Log_Source::INVOICE_WFIRMA,
                'label' => $this->translator->translate('logs.source.' . Log_Source::INVOICE_WFIRMA)
            ],
            [
                'value' => Log_Source::INVOICE_INFAKT,
                'label' => $this->translator->translate('logs.source.' . Log_Source::INVOICE_INFAKT)
            ],
            [
                'value' => Log_Source::INVOICE_TAXE,
                'label' => $this->translator->translate('logs.source.' . Log_Source::INVOICE_TAXE)
            ],
        ];
    }

    private function get_table_actions(): array
    {
        if(!$this->debug_helper->is_dev_mode_enabled()) {
            return [];
        }

        return [
            [
                'target' => $this->url_generator->generate(Admin_Logs_Controller::class, 'delete_all_logs', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'label' => $this->translator->translate('logs.delete_all'),
                'class' => 'delete-all-logs-button'
            ]
        ];
    }

    private function get_row_actions(): array
    {
        if(!$this->debug_helper->is_dev_mode_enabled()) {
            return [];
        }

        return [
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('logs.delete'),
                'class' => 'delete-log',
                'use_json_property_as_url' => 'delete_log_url',
                'loading_message' => $this->translator->translate('logs.deleting')
            ]
        ];
    }

    private function get_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('logs.column.id'),
                'type' => 'id',
                'prefix' => '#'
            ],
            [
                'property' => 'created_at',
                'label' => $this->translator->translate('logs.column.created_at'),
                'type' => 'date',
                'filter' => 'date_range',
            ],
            [
                'property' => 'level',
                'label' => $this->translator->translate('logs.column.level'),
                'type' => 'status',
                'use_json_property_as_label' => 'level_label',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values()
            ],
            [
                'property' => 'source',
                'label' => $this->translator->translate('logs.column.source'),
                'use_json_property_as_label' => 'source_label',
                'filter' => 'select',
                'filter_options' => $this->get_source_filter_values()
            ],
            [
                'property' => 'message',
                'type' => 'longtext',
                'label' => $this->translator->translate('logs.column.message'),
                'filter' => 'text',
                'max_length' => 70
            ],
        ];
    }
}
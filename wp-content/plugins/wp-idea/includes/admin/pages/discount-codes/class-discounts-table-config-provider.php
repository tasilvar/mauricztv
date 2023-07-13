<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\discount_codes;

use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Status;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\Caps;

class Discounts_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_discount_codes_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Discounts_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private User_Capability_Factory $user_capability_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Discounts_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        User_Capability_Factory $user_capability_factory
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->user_capability_factory = $user_capability_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        $config = $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        );

        $config->disable_multi_sort();
        $config->set_row_actions($this->get_row_actions());
        $config->set_top_panel_buttons($this->get_top_panel_buttons());
        $config->set_required_caps(
            $this->user_capability_factory->create_many_from_names([Caps::CAP_MANAGE_DISCOUNTS])
        );

        return $config;
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('discount_codes.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'use_json_property_as_link' => 'edit_url',
            ],
            [
                'property' => 'name',
                'filter' => 'text',
                'label' => $this->translator->translate('discount_codes.column.name'),
                'use_json_property_as_link' => 'edit_url',
            ],
            [
                'property' => 'code',
                'filter' => 'text',
                'label' => $this->translator->translate('discount_codes.column.code')
            ],
            [
                'property' => 'amount',
                'label' => $this->translator->translate('discount_codes.column.amount'),
                'sortable' => false
            ],
            [
                'property' => 'uses',
                'label' => $this->translator->translate('discount_codes.column.uses')
            ],
            [
                'property' => 'start',
                'type' => 'date',
                'label' => $this->translator->translate('discount_codes.column.start_date')
            ],
            [
                'property' => 'expiration',
                'type' => 'date',
                'label' => $this->translator->translate('discount_codes.column.end_date')
            ],
            [
                'property' => 'status',
                'type' => 'status',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values(),
                'label' => $this->translator->translate('discount_codes.column.status'),
                'use_json_property_as_label' => 'status_label',
            ],
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('discount_codes.actions.edit'),
                'class' => 'edit-code',
                'use_json_property_as_url' => 'edit_url',
                'target' => '_blank'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('discount_codes.actions.delete'),
                'class' => 'delete-code',
                'use_json_property_as_url' => 'delete_url',
                'loading_message' => $this->translator->translate('discount_codes.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('discount_codes.actions.delete.confirm')
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'target' => $this->url_generator->generate_admin_page_url('admin.php', [
                    'page' => 'wp-idea-discounts',
                    'wp-idea-action' => 'add_discount'
                ]),
                'label' => $this->translator->translate('discount_codes.actions.add'),
                'class' => 'add-code-button'
            ],
            [
                'target' => $this->url_generator->generate_admin_page_url('admin.php', [
                    'page' => 'wp-idea-discounts',
                    'wp-idea-action' => 'edd-dc-generator'
                ]),
                'label' => $this->translator->translate('discount_codes.actions.generate'),
                'class' => 'add-code-button'
            ]
        ];
    }

    private function get_status_filter_values(): array
    {
        return array_map(fn(string $status) => [
            'value' => $status,
            'label' => $this->translator->translate('discount_codes.status.' . $status)
        ], $this->get_filterable_statuses());
    }

    private function get_filterable_statuses(): array
    {
        return [
            Status::STATUS_ACTIVE,
            Status::STATUS_INACTIVE
        ];
    }
}
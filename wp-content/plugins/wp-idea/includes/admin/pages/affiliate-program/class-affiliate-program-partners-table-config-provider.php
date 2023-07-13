<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program;

use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\Caps;
use bpmj\wpidea\user\User_Role_Factory;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;

class Affiliate_Program_Partners_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_affiliate_program_partners_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Affiliate_Program_Partners_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Affiliate_Program_Partners_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        User_Role_Factory $user_role_factory
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->user_role_factory = $user_role_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        $config = $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        );

        $config->set_top_panel_buttons($this->get_top_panel_buttons());
        $config->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT));

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
                'label' => $this->translator->translate('affiliate_program.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'use_json_property_as_link' => 'edit_url',
            ],
            [
                'property' => 'name',
                'filter' => 'text',
                'label' => $this->translator->translate('affiliate_program.column.name'),
                'use_json_property_as_link' => 'edit_url',
            ],
            [
                'property' => 'partner_email',
                'filter' => 'text',
                'label' => $this->translator->translate('affiliate_program.column.partner_email'),
                'use_json_property_as_link' => 'edit_url',
            ],
            [
                'property' => 'partner_link',
                'filter' => 'text',
                'type' => 'longtext',
                'label' => $this->translator->translate('affiliate_program.column.partner_link')
            ],
            [
                'property' => 'sale_amount_sum',
                'type' => 'price',
                'filter' => 'number_range',
                'label' => $this->translator->translate('affiliate_program.column.total_sales'),
                'use_json_property_as_currency' => 'currency',
            ],
            [
                'property' => 'amount_sum',
                'type' => 'price',
                'filter' => 'number_range',
                'label' => $this->translator->translate('affiliate_program.column.total_commissions'),
                'use_json_property_as_currency' => 'currency',
            ],
            [
                'property' => 'unsettled_amount_sum',
                'type' => 'price',
                'filter' => 'number_range',
                'label' => $this->translator->translate('affiliate_program.column.unsettled_commissions'),
                'use_json_property_as_currency' => 'currency',
            ],
            [
                'property' => 'status',
                'type' => 'status',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values(),
                'label' => $this->translator->translate('affiliate_program.participants.status'),
                'use_json_property_as_label' => 'status_label'
            ],
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'target' => $this->url_generator->generate_admin_page_url('user-new.php?partner=1'),
                'label' => $this->translator->translate('affiliate_program.actions.add_partner'),
                'class' => 'add-partner-button'
            ]
        ];
    }

    private function get_status_filter_values(): array
    {
        $filter_values = [];

        foreach (Partner::VALID_STATUSES as $status) {
            $filter_values[] = [
                'value' => $status,
                'label' => $this->translator->translate('affiliate_program.participants.status.' . $status)
            ];
        }

        return $filter_values;
    }

}

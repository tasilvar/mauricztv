<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\customers;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Role_Factory;

class Customer_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_customers_table';

    private Interface_Translator $translator;
    private Customer_Table_Data_Provider $customer_table_data_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Interface_Translator $translator,
        Customer_Table_Data_Provider $customer_table_data_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Role_Factory $user_role_factory
    ) {
        $this->translator             = $translator;
        $this->customer_table_data_provider = $customer_table_data_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_role_factory = $user_role_factory;
    }

    public function get_config(): Dynamic_Table_Config
    {
        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->customer_table_data_provider,
            $this->get_table_columns_config()
        )
            ->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT))
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
                'label'                     => $this->translator->translate('customers.column.id'),
                'type'                      => 'id',
                'prefix'                    => '#',
                'always_visible'            => true,
                'use_json_property_as_link' => 'customer_data_url',
            ],
            [
                'property' => 'name',
                'label'    => $this->translator->translate('customers.column.name'),
                'filter'   => 'text'
            ],
            [
                'property' => 'email',
                'label'    => $this->translator->translate('customers.column.email'),
                'filter'   => 'text',
            ],
            [
                'property'       => 'purchase_count',
                'label'          => $this->translator->translate('customers.column.purchases'),
            ],
            [
                'property'       => 'purchase_value',
                'label'          => $this->translator->translate('customers.column.total_spent'),
                'type' => 'price',
                'use_json_property_as_currency' => 'currency',
                'filter' => 'number_range'
            ],
            [
                'property'       => 'date_created',
                'label'          => $this->translator->translate('customers.column.date_created'),
                 'type' => 'date',
                 'filter' => 'date_range',

            ]
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'label'                    => $this->translator->translate('customers.actions.data'),
                'class'                    => 'see-customer-details',
                'type'                     => 'link',
                'use_json_property_as_url' => 'customer_data_url'
            ],
            [
                'label'                    => $this->translator->translate('customers.actions.delete'),
                'class'                    => 'delete-customer',
                'type'                     => 'link',
                'use_json_property_as_url' => 'delete_customer_url'
            ]
        ];
    }
}
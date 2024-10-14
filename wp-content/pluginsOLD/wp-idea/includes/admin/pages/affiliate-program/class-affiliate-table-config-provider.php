<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program;

use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_Affiliate_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\Caps;
use bpmj\wpidea\user\User_Role_Factory;

class Affiliate_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_affiliate_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Affiliate_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Interface_Product_Repository $products_repository;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Affiliate_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Interface_Product_Repository $products_repository,
        User_Role_Factory $user_role_factory
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->products_repository = $products_repository;
        $this->user_role_factory = $user_role_factory;
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
        $config->set_bulk_actions($this->get_bulk_actions());
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
                'property' => 'partner_affiliate_id',
                'filter' => 'text',
                'label' => $this->translator->translate('affiliate_program.column.partner_id'),
                'use_json_property_as_link' => 'partner_profile_url',
            ],
            [
                'property' => 'partner_email',
                'filter' => 'text',
                'label' => $this->translator->translate('affiliate_program.column.partner_email')
            ],
            [
                'property' => 'name',
                'filter' => 'text',
                'label' => $this->translator->translate('affiliate_program.column.name')
            ],
            [
                'property' => 'email',
                'filter' => 'text',
                'label' => $this->translator->translate('affiliate_program.column.email')
            ],
            [
                'property' => 'created_at',
                'label' => $this->translator->translate('affiliate_program.column.sale_date'),
                'type' => 'date',
                'filter' => 'date_range'
            ],
            [
                'property' => 'products',
                'label' => $this->translator->translate('affiliate_program.column.purchased_products'),
                'filter'         => 'multiselect',
                'filter_options' => $this->get_product_filter_values()
            ],
            [
                'property' => 'sale_amount',
                'label' => $this->translator->translate('affiliate_program.column.sales_amount'),
                'type' => 'price',
                'use_json_property_as_currency' => 'currency',
                'filter' => 'number_range'
            ],
            [
                'property' => 'percentage',
                'label' => $this->translator->translate('affiliate_program.column.commission_percentage'),
                'type' => 'text',
                'use_json_property_as_currency' => 'percent',
                'filter' => 'number_range'
            ],
            [
                'property' => 'amount',
                'label' => $this->translator->translate('affiliate_program.column.commission_amount'),
                'type' => 'price',
                'use_json_property_as_currency' => 'currency',
                'filter' => 'number_range'
            ],
            [
                'property' => 'status',
                'type' => 'status',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values(),
                'label' => $this->translator->translate('affiliate_program.column.status'),
                'use_json_property_as_label' => 'status_label',
            ],
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('affiliate_program.actions.change_status'),
                'class' => 'status-partner',
                'use_json_property_as_url' => 'status_url',
                'loading_message' => $this->translator->translate('affiliate_program.actions.change_status.loading'),
                'confirm_message' => $this->translator->translate('affiliate_program.actions.change_status.confirm')
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('affiliate_program.actions.delete'),
                'class' => 'delete-partner',
                'use_json_property_as_url' => 'delete_url',
                'loading_message' => $this->translator->translate('affiliate_program.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('affiliate_program.actions.delete.confirm')
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('affiliate_program.actions.delete.bulk'),
                'class' => 'delete-partner',
                'url' => $this->url_generator->generate(Admin_Affiliate_Ajax_Controller::class, 'delete_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('affiliate_program.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('affiliate_program.actions.delete.bulk.confirm')
            ],
            [
                'label' => $this->translator->translate('affiliate_program.actions.change_status.bulk'),
                'class' => 'status-partner',
                'url' => $this->url_generator->generate(Admin_Affiliate_Ajax_Controller::class, 'change_status_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('affiliate_program.actions.change_status.loading'),
                'confirm_message' => $this->translator->translate('affiliate_program.actions.change_status.bulk.confirm')
            ]
        ];
    }

    private function get_product_filter_values(): array
    {
        $options = $this->products_repository->find_all()->to_array();
        foreach ($options as $key => $option) {
            $encoded_name = htmlspecialchars($option['name'], ENT_QUOTES);

            unset($options[$key]['name']);

            $options[$key]['label'] = $encoded_name;
            $options[$key]['value'] = $option['id'];
        }

        return $options;
    }

    private function get_status_filter_values(): array
    {
        $filter_values = [];

        foreach (Status::VALID_STATUSES as $status) {
            $filter_values[] = [
                'value' => $status,
                'label' => $this->translator->translate('affiliate_program.status.' . $status)
            ];
        }

        return $filter_values;
    }

}
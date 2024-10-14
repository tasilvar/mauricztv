<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program_redirections;

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
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Affiliate_Redirections_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_affiliate_redirections_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Affiliate_Redirections_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Interface_Product_Repository $products_repository;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Affiliate_Redirections_Table_Data_Provider $data_provider,
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
        $config->disable_pagination();
        $config->set_row_actions($this->get_row_actions());
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
                'label' => $this->translator->translate('affiliate_program_redirections.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'sortable' => false
            ],
            [
                'property' => 'product',
                'label' => $this->translator->translate('affiliate_program_redirections.column.product'),
                'sortable' => false
            ],
            [
                'property' => 'url',
                'label' => $this->translator->translate('affiliate_program_redirections.column.url'),
                'sortable' => false
            ],
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('affiliate_program_redirections.actions.delete'),
                'class' => 'delete',
                'use_json_property_as_url' => 'delete_url',
                'loading_message' => $this->translator->translate('affiliate_program_redirections.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('affiliate_program_redirections.actions.delete.confirm')
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'target' => $this->url_generator->generate_admin_page_url('admin.php', [
                    'page' => Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_REDIRECTIONS,
                    'action' => 'add'
                ]),
                'label' => $this->translator->translate('affiliate_program_redirections.actions.add'),
                'class' => 'add-redirection-button'
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
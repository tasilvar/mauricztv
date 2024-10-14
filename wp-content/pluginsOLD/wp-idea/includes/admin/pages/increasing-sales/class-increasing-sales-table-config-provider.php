<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\increasing_sales;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\modules\increasing_sales\api\controllers\Admin_Increasing_Sales_Ajax_Controller;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Role_Factory;

class Increasing_Sales_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_increasing_sales_table';

    private Interface_Product_Repository $products_repository;
    private Increasing_Sales_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Role_Factory $user_role_factory;

    public function __construct(
        Interface_Product_Repository $products_repository,
        Increasing_Sales_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Role_Factory $user_role_factory
    ) {
        $this->products_repository = $products_repository;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
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
            ->set_bulk_actions($this->get_bulk_actions())
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

        foreach (Increasing_Sales_Offer_Type::VALID_OFFER_TYPE as $offer) {
            $filter_values[] = [
                'value' => $offer,
                'label' => $this->translator->translate('increasing_sales.event.' . $offer)
            ];
        }
        
        return $filter_values;
    }

    private function get_table_actions(): array
    {
        return [
            [
                'target' => $this->get_add_increasing_sales_url(),
                'label' => $this->translator->translate('increasing_sales.actions.add_offer'),
                'class' => 'add-increasing-sales'
            ]
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('increasing_sales.actions.edit'),
                'class' => 'edit-offer',
                'use_json_property_as_url' => 'edit_offer'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('increasing_sales.actions.delete'),
                'class' => 'delete-offer',
                'use_json_property_as_url' => 'delete_offer',
                'loading_message' => $this->translator->translate('increasing_sales.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('increasing_sales.actions.delete.confirm')
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('increasing_sales.actions.delete.bulk'),
                'class' => 'delete-payment',
                'url' => $this->url_generator->generate(Admin_Increasing_Sales_Ajax_Controller::class, 'delete_offer_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('orders.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('increasing_sales.actions.delete.bulk.confirm')
            ]
        ];
    }

    private function get_add_increasing_sales_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::INCREASING_SALES,
            'view' => 'add'
        ]);
    }

    private function get_columns_config(): array
    {
        return [
            [
                'property' => 'id',
                'label' => $this->translator->translate('increasing_sales.column.id'),
                'type' => 'id',
                'always_visible' => true,
                'use_json_property_as_link' => 'edit_offer',
                'prefix' => '#'
            ],
            [
                'property' => 'product',
                'label' => $this->translator->translate('increasing_sales.column.product'),
                'filter' => 'multiselect',
                'filter_options' => $this->get_product_filter_values(),
                'use_json_property_as_link' => 'edit_offer',
                'max_length' => 100,
                'sortable' => false
            ],
            [
                'property' => 'offer_type',
                'label' => $this->translator->translate('increasing_sales.column.offer_type'),
                'use_json_property_as_label' => 'offer_type_label',
                'filter' => 'select',
                'filter_options' => $this->get_event_filter_values()
            ],
            [
                'property' => 'offered_product',
                'label' => $this->translator->translate('increasing_sales.column.offered_product'),
                'filter' => 'multiselect',
                'filter_options' => $this->get_product_filter_values(),
                'max_length' => 100,
                'sortable' => false
            ],
            [
                'property' => 'discount',
                'label' => $this->translator->translate('increasing_sales.column.discount'),
                'type' => 'price',
                'use_json_property_as_currency' => 'currency',
                'filter' => 'number_range'
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
}
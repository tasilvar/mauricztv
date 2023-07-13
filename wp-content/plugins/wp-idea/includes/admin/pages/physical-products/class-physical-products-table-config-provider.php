<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\physical_products;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Physical_Products_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\vo\Product_Sales_Status;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Factory;

class Physical_Products_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_physical_products_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Physical_Products_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private User_Capability_Factory $user_capability_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Physical_Products_Table_Data_Provider $data_provider,
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
        $config->disable_export();
        $config->set_bulk_actions($this->get_bulk_actions());
        $config->set_required_caps(
            $this->user_capability_factory->create_many_from_names([Caps::CAP_MANAGE_PRODUCTS])
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
                'label' => $this->translator->translate('physical_products.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'use_json_property_as_link' => 'edit_physical_product',
                'sortable' => false
            ],
            [
                'property' => 'name',
                'filter' => 'text',
                'label' => $this->translator->translate('physical_products.column.name'),
                'sortable' => false,
                'use_json_property_as_link' => 'edit_physical_product',
            ],
            [
                'property' => null,
                'label' => $this->translator->translate('physical_products.column.show'),
                'sortable' => false,
                'buttons' => [
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-visibility\\"></span>',
                        'tooltip' => $this->translator->translate('physical_products.buttons.product_panel.tooltip'),
                        'use_json_property_as_url_redirect' => 'physical_product_panel'
                    ],
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-admin-links\\"></span>',
                        'tooltip' => $this->translator->translate('physical_products.buttons.purchase_links.tooltip'),
                        'ajax' => [
                            'use_json_property_as_url' => 'purchase_links',
                            'method' => 'POST',
                            'response_property_with_content' => 'content',
                        ],
                        'popup_buttons' => [
                            [
                                'text' => $this->translator->translate('physical_products.popup.close'),
                            ],
                        ]
                    ]
                ],
            ],
            [
                'property' => 'sales',
                'label' => $this->translator->translate('physical_products.column.sales'),
                'type' => 'status',
                'sortable' => false,
                'use_json_property_as_label' => 'sales_label'
            ],
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('physical_products.actions.edit'),
                'class' => 'edit-physical-product',
                'use_json_property_as_url' => 'edit_physical_product'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('physical_products.actions.delete'),
                'class' => 'delete-physical-product',
                'use_json_property_as_url' => 'delete_physical_product',
                'loading_message' => $this->translator->translate('physical_products.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('physical_products.actions.delete.confirm')
            ],
            [
                'type' => 'async-action',
                'status_name' => 'sales',
                'status_active_value' => Product_Sales_Status::ENABLED,
                'label_active' => $this->translator->translate('physical_products.actions.sales.active'),
                'label_inactive' => $this->translator->translate('physical_products.actions.sales.inactive'),
                'class' => 'sales-physical-product',
                'use_json_property_as_url' => 'change_physical_product_sales',
                'loading_message' => $this->translator->translate('physical_products.actions.sales.loading')
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('physical_products.actions.sales.bulk'),
                'class' => 'status-physical-product',
                'url' => $this->url_generator->generate(Admin_Physical_Products_Controller::class, 'disable_sales_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('physical_products.actions.sales.loading')
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'type' => 'button-wpi-popup',
                'text' => $this->translator->translate('physical_products.actions.create_product'),
                'classes' => 'dynamic-table__header__buttons__button create-physical-products',
                'ajax' => [
                    'url' => $this->url_generator->generate(Admin_Physical_Products_Controller::class, 'get_popup_create_physical_products', [
                        Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                    ]),
                    'method' => 'POST',
                    'response_property_with_content' => 'content',
                ]
            ]
        ];
    }
}
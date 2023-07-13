<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\digital_products_list;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Digital_Products_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\vo\Product_Sales_Status;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Factory;

class Digital_Products_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'wpi_digital_products_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Digital_Products_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private User_Capability_Factory $user_capability_factory;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Digital_Products_Table_Data_Provider $data_provider,
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
        $config->set_bulk_actions($this->get_bulk_actions());
        $config->set_top_panel_buttons($this->get_top_panel_buttons());
        $config->disable_export();
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
                'label' => $this->translator->translate('digital_products_list.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'sortable' => false,
                'use_json_property_as_link' => 'edit_digital_product',
            ],
            [
                'property' => 'name',
                'filter' => 'text',
                'sortable' => false,
                'label' => $this->translator->translate('digital_products_list.column.name'),
                'use_json_property_as_link' => 'edit_digital_product',
            ],
            [
                'property' => null,
                'label' => $this->translator->translate('digital_products_list.column.show'),
                'sortable' => false,
                'buttons' => [
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-visibility\\"></span>',
                        'tooltip' => $this->translator->translate('digital_products_list.buttons.digital_product_panel.tooltip'),
                        'use_json_property_as_url_redirect' => 'digital_product_panel'
                    ],
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-admin-links\\"></span>',
                        'tooltip' => $this->translator->translate('digital_products_list.buttons.purchase_links.tooltip'),
                        'ajax' => [
                            'use_json_property_as_url' => 'purchase_links',
                            'method' => 'POST',
                            'response_property_with_content' => 'content',
                        ],
                        'popup_buttons' => [
                            [
                                'text' => $this->translator->translate('digital_products_list.popup.close'),
                            ],
                        ]
                    ]
                ],
            ],
            [
                'property' => 'sales',
                'label' => $this->translator->translate('digital_products_list.column.sales'),
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
                'label' => $this->translator->translate('digital_products_list.actions.edit'),
                'class' => 'edit-digital-product',
                'use_json_property_as_url' => 'edit_digital_product'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('digital_products_list.actions.delete'),
                'class' => 'delete-digital-product',
                'use_json_property_as_url' => 'delete_digital_product',
                'loading_message' => $this->translator->translate('digital_products_list.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('digital_products_list.actions.delete.confirm')
            ],
            [
                'type' => 'async-action',
                'status_name' => 'sales',
                'status_active_value' => Product_Sales_Status::ENABLED,
                'label_active' => $this->translator->translate('digital_products_list.actions.sales.active'),
                'label_inactive' => $this->translator->translate('digital_products_list.actions.sales.inactive'),
                'class' => 'sales-digital-product',
                'use_json_property_as_url' => 'change_digital_product_sales',
                'loading_message' => $this->translator->translate('digital_products_list.actions.sales.loading')
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('digital_products_list.actions.sales.bulk'),
                'class' => 'status-partner',
                'url' => $this->url_generator->generate(Admin_Digital_Products_Controller::class, 'disable_sales_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('digital_products_list.actions.sales.loading')
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'type' => 'button-wpi-popup',
                'text' => $this->translator->translate('digital_products.actions.create_digital_product'),
                'classes' => 'dynamic-table__header__buttons__button create-digital-product',
                'ajax' => [
                    'url' => $this->url_generator->generate(Admin_Digital_Products_Controller::class, 'get_popup_create_digital_product', [
                        Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                    ]),
                    'method' => 'POST',
                    'response_property_with_content' => 'content',
                ]
            ]
        ];
    }
}
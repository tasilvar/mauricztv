<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Payment_History_Ajax_Controller;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\order\value_objects\Recurring_Payment_Type;
use bpmj\wpidea\sales\payments\Interface_Payment_Gates;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Capability_Factory;
use bpmj\wpidea\user\User_Role_Factory;

class Order_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const DEFAULT_HIDDEN_COLUMNS = [
        'discount_code',
        'invoice_country',
        'invoice_nip',
        'invoice_company_name',
        'phone_no',
    ];

    private const TABLE_ID = 'wpi_payments_history';
    private const MANUAL_PURCHASES = 'manual_purchases';

    private Order_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private Interface_Product_Repository $products_repository;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private User_Capability_Factory $user_capability_factory;
    private User_Role_Factory $user_role_factory;
    private Interface_Payment_Gates $payment_gates;

    public function __construct(
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Interface_Product_Repository $products_repository,
        Order_Table_Data_Provider $data_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        User_Capability_Factory $user_capability_factory,
        User_Role_Factory $user_role_factory,
        Interface_Payment_Gates $payment_gates
    ) {
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->products_repository = $products_repository;
        $this->data_provider = $data_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->user_capability_factory = $user_capability_factory;
        $this->user_role_factory = $user_role_factory;
        $this->payment_gates = $payment_gates;
    }


    public function get_config(): Dynamic_Table_Config
    {
        return $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_table_columns_config()
        )
            ->set_required_roles($this->user_role_factory->create_many_from_name(Caps::ROLES_ADMINS_SUPPORT))
            ->set_required_caps($this->get_required_caps())
            ->disable_multi_sort()
            ->set_default_hidden_columns(self::DEFAULT_HIDDEN_COLUMNS)
            ->set_bulk_actions($this->get_bulk_actions())
            ->set_top_panel_buttons($this->get_table_actions())
            ->set_row_actions($this->get_row_actions());
    }

    public function get_table_id(): string
    {
        return self::TABLE_ID;
    }

    private function get_status_filter_values(): array
    {
        $filter_values = [];
        $status_array = edd_get_payment_status_keys();

        foreach ($status_array as $status) {
            $label = $status === 'publish' ? 'Complete' : ucwords($status);
            $filter_values[] = [
                'value' => $status,
                'label' => __($label, 'easy-digital-downloads')
            ];
        }

        return $filter_values;
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('orders.actions.see_details'),
                'class' => 'see-payment-details',
                'use_json_property_as_url' => 'details_url',
                'target' => '_blank'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('orders.actions.resend'),
                'class' => 'resend-payment-notification',
                'use_json_property_as_url' => 'resend_payment_email_url',
                'loading_message' => $this->translator->translate('orders.actions.resend.loading')
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('orders.actions.delete'),
                'class' => 'delete-payment',
                'use_json_property_as_url' => 'delete_payment_url',
                'loading_message' => $this->translator->translate('orders.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('orders.actions.delete.confirm')
            ]
        ];
    }

    public function get_table_columns_config(): array
    {
        return [
            [
                'property' => 'ID',
                'label' => $this->translator->translate('orders.column.id'),
                'always_visible' => true,
                'use_json_property_as_link' => 'details_url',
                'type' => 'id',
                'prefix' => '#'
            ],
            [
                'property' => 'full_name',
                'label' => $this->translator->translate('orders.column.full_name'),
                'use_json_property_as_link' => 'client_profile_url',
                'filter' => 'text',
                'sortable' => false
            ],
            [
                'property' => 'user_email',
                'label' => $this->translator->translate('orders.column.email'),
                'filter' => 'text',
                'sortable' => false
            ],
            [
                'property' => 'phone_no',
                'label' => $this->translator->translate('orders.column.phone_no'),
                'filter' => 'text',
                'sortable' => false,
            ],
            [
                'property' => 'delivery_address',
                'label' => $this->translator->translate('orders.column.delivery_address'),
                'sortable' => false,
                'type' => 'html'
            ],
            [
                'property' => 'date',
                'label' => $this->translator->translate('orders.column.date'),
                'type' => 'date',
                'filter' => 'date_range',
            ],
            [
                'property' => 'total',
                'label' => $this->translator->translate('orders.column.amount'),
                'type' => 'price',
                'use_json_property_as_currency' => 'currency',
                'filter' => 'number_range'
            ],
            [
                'property' => 'discount_code',
                'label' => $this->translator->translate('orders.column.discount_code'),
                'filter' => 'text',
                'sortable' => false
            ],
            [
                'property' => 'products',
                'label' => $this->translator->translate('orders.column.products'),
                'filter' => 'multiselect',
                'filter_options' => $this->get_product_filter_values(),
                'max_length' => 100,
                'custom_column_class' => 'col-order-products',
                'sortable' => false
            ],
            [
                'property' => 'increasing_sales_offer_type',
                'label' => $this->translator->translate('orders.column.increasing_sales_offer_type'),
                'filter' => 'select',
                'filter_options' => $this->get_increasing_sales_offer_type_filter_values(),
                'sortable' => false
            ],
            [
                'property' => 'status',
                'label' => $this->translator->translate('orders.column.status'),
                'type' => 'status',
                'filter' => 'select',
                'filter_options' => $this->get_status_filter_values(),
                'use_json_property_as_label' => 'status_label',
                'sortable' => false
            ],
            [
                'property' => 'payment_method',
                'label' => $this->translator->translate('orders.column.payment_method'),
                'filter' => 'multiselect',
                'filter_options' => $this->get_payment_method_filter_values(),
            ],
            [
                'property' => 'recurring_payment',
                'label' => $this->translator->translate('orders.column.recurring_payments'),
                'filter' => 'multiselect',
                'filter_options' => $this->get_recurring_payment_filter_values(),
            ],
            [
                'property' => 'invoice_country',
                'label' => $this->translator->translate('orders.column.country'),
                'filter' => 'text',
                'sortable' => false
            ],
            [
                'property' => 'invoice_nip',
                'label' => $this->translator->translate('orders.column.nip'),
                'filter' => 'text',
                'sortable' => false
            ],
            [
                'property' => 'invoice_company_name',
                'label' => $this->translator->translate('orders.column.company_name'),
                'filter' => 'text',
                'sortable' => false
            ],
            [
                'property' => 'first_checkbox',
                'label' => $this->translator->translate('orders.column.first_checkbox'),
                'filter' => 'select',
                'filter_options' => $this->get_checkboxes_filter_values(),
                'sortable' => false
            ],
            [
                'property' => 'second_checkbox',
                'label' => $this->translator->translate('orders.column.second_checkbox'),
                'filter' => 'select',
                'filter_options' => $this->get_checkboxes_filter_values(),
                'sortable' => false
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('orders.actions.delete.bulk'),
                'class' => 'delete-payment',
                'url' => $this->url_generator->generate(Admin_Payment_History_Ajax_Controller::class, 'delete_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('orders.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('orders.actions.delete.bulk.confirm')
            ],
            [
                'label' => $this->translator->translate('orders.actions.resend.bulk'),
                'class' => 'resend-payment-notification',
                'url' => $this->url_generator->generate(Admin_Payment_History_Ajax_Controller::class, 'resend_email_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('orders.actions.resend.bulk.loading')
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

    private function get_checkboxes_filter_values(): array
    {
        return [
            [
                'value' => true,
                'label' => $this->translator->translate('orders.column.additional_checkbox.yes')
            ],
            [
                'value' => false,
                'label' => $this->translator->translate('orders.column.additional_checkbox.no')
            ]
        ];
    }

    private function get_increasing_sales_offer_type_filter_values(): array
    {
        $options = [];

        foreach (Increasing_Sales_Offer_Type::VALID_OFFER_TYPE as $offer) {
            $options[] = [
                'value' => $offer,
                'label' => $offer
            ];
        }

        return $options;
    }

    private function get_table_actions(): array
    {
        return [
            [
                'target' => $this->url_generator->generate_admin_page_url('options.php', [
                    'page' => 'edd-manual-purchase'
                ]),
                'label' => $this->translator->translate('orders.actions.add_payment'),
                'class' => 'add-payment-button'
            ]
        ];
    }

    private function get_required_caps(): User_Capability_Collection
    {
        return (new User_Capability_Collection())
            ->add($this->user_capability_factory->create_from_name(Caps::CAP_MANAGE_ORDERS));
    }

    private function get_payment_method_filter_values(): array
    {
        $options = [];
        foreach ($this->payment_gates->get_registered_gates() as $payment_gateway_key => $payment_gateway_data) {
            $options[] = [
                'label' => $payment_gateway_data['checkout_label'],
                'value' => $payment_gateway_key,
            ];
        }

        $options[] = [
            'label' => $this->translator->translate('gateways.manual_purchases'),
            'value' => self::MANUAL_PURCHASES,
        ];

        return $options;
    }

    private function get_recurring_payment_filter_values(): array
    {
        $options = [];
        foreach (Recurring_Payment_Type::ALL_TYPES as $recurring_payment) {
            $options[] = [
                'label' => $this->translator->translate('orders.recurring_payment.' . $recurring_payment),
                'value' => $recurring_payment,
            ];
        }

        return $options;
    }
}
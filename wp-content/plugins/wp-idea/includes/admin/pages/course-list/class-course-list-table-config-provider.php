<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\course_list;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\admin\Admin_Courses_Controller;
use bpmj\wpidea\data_types\course\Course_Sales_Status;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Factory;

class Course_List_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'course_list_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Course_List_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;
    private User_Capability_Factory $user_capability_factory;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Course_List_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        User_Capability_Factory $user_capability_factory,
        Interface_Packages_API $packages_api
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->user_capability_factory = $user_capability_factory;
        $this->packages_api = $packages_api;
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
        $config->set_bulk_actions($this->get_bulk_actions());
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
                'label' => $this->translator->translate('course_list.column.id'),
                'type' => 'id',
                'prefix' => '#',
                'use_json_property_as_link' => 'edit_course',
                'sortable' => false
            ],
            [
                'property' => 'name',
                'label' => $this->translator->translate('course_list.column.name'),
                'use_json_property_as_link' => 'edit_course',
                'sortable' => false
            ],
            [
                'property' => null,
                'label' => $this->translator->translate('course_list.column.show'),
                'sortable' => false,
                'buttons' => [
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-welcome-learn-more\\"></span>',
                        'tooltip' => $this->translator->translate('course_list.buttons.course_panel.tooltip'),
                        'use_json_property_as_url_redirect' => 'course_panel',
                        'url_redirect_target' => '_blank'
                    ],
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-chart-bar\\"></span>',
                        'tooltip' => $this->translator->translate('course_list.buttons.course_stats.tooltip'),
                        'ajax' => [
                            'use_json_property_as_url' => 'course_stats',
                            'method' => 'POST',
                            'response_property_with_content' => 'content',
                        ],
                        'popup_buttons' => [
                            [
                                'text' => $this->translator->translate('course_list.popup.close'),
                            ],
                        ]
                    ],
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-admin-users\\"></span>',
                        'tooltip' => $this->translator->translate('course_list.buttons.course_students.tooltip'),
                        'ajax' => [
                            'use_json_property_as_url' => 'course_students',
                            'method' => 'POST',
                            'response_property_with_content' => 'content',
                        ],
                        'popup_buttons' => [
                            [
                                'text' => $this->translator->translate('course_list.popup.close'),
                            ],
                        ]
                    ],
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-admin-links\\"></span>',
                        'tooltip' => $this->translator->translate('course_list.buttons.purchase_links.tooltip'),
                        'ajax' => [
                            'use_json_property_as_url' => 'purchase_links',
                            'method' => 'POST',
                            'response_property_with_content' => 'content',
                        ],
                        'popup_buttons' => [
                            [
                                'text' => $this->translator->translate('course_list.popup.close'),
                            ],
                        ]
                    ],
                    [
                        'type' => 'button-wpi-popup',
                        'text' => '<span class=\\"dashicons dashicons-clock\\"></span>',
                        'tooltip' => $this->translator->translate('course_list.buttons.expiring_customers.tooltip'),
                        'use_json_property_as_url_redirect' => 'expiring_customers',
                        'disabled' => 'disabled_expiring_customers'
                    ],
                ],
            ],
            [
                'property' => 'sales',
                'label' => $this->translator->translate('course_list.column.sales'),
                'type' => 'status',
                'use_json_property_as_label' => 'sales_label',
                'sortable' => false
            ],
            [
                'property' => 'sales_limit_status',
                'label' => $this->translator->translate('course_list.column.sales_limit_status'),
                'sortable' => false
            ],
        ];
    }

    private function get_row_actions(): array
    {
        return [
            [
                'type' => 'link',
                'label' => $this->translator->translate('course_list.actions.edit'),
                'class' => 'edit-course',
                'use_json_property_as_url' => 'edit_course'
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('course_list.actions.duplicate'),
                'loading_message' => $this->translator->translate('course_list.actions.duplicate.loading'),
                'class' => 'duplicate-course ' . ($this->packages_api->has_access_to_feature(Packages::FEAT_COURSE_CLONING) ? '' : 'duplicate-course--disabled'),
                'use_json_property_as_url' => 'duplicate_course',
            ],
            [
                'type' => 'async-action',
                'label' => $this->translator->translate('course_list.column.delete'),
                'class' => 'delete-course',
                'use_json_property_as_url' => 'delete_course',
                'loading_message' => $this->translator->translate('course_list.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('course_list.actions.delete.confirm')
            ],
            [
                'type' => 'async-action',
                'status_name' => 'sales',
                'status_active_value' => Course_Sales_Status::ENABLED,
                'label_active' => $this->translator->translate('course_list.actions.sales.active'),
                'label_inactive' => $this->translator->translate('course_list.actions.sales.inactive'),
                'class' => 'sales-course',
                'use_json_property_as_url' => 'change_course_sales',
                'loading_message' => $this->translator->translate('course_list.actions.sales.loading')
            ]
        ];
    }

    private function get_bulk_actions(): array
    {
        return [
            [
                'label' => $this->translator->translate('course_list.actions.sales.bulk'),
                'class' => 'sales-course',
                'url' => $this->url_generator->generate(Admin_Courses_Controller::class, 'disable_sales_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('course_list.actions.sales.loading')
            ],
            [
                'label' => $this->translator->translate('course_list.actions.delete.bulk'),
                'class' => 'delete-course',
                'url' => $this->url_generator->generate(Admin_Courses_Controller::class, 'delete_course_bulk', [
                    Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                ]),
                'loading_message' => $this->translator->translate('course_list.actions.delete.loading'),
                'confirm_message' => $this->translator->translate('course_list.actions.delete.bulk.confirm')
            ]
        ];
    }

    private function get_top_panel_buttons(): array
    {
        return [
            [
                'type' => 'button-wpi-popup',
                'text' => $this->translator->translate('course_list.actions.create_course'),
                'classes' => 'dynamic-table__header__buttons__button create-course',
                'ajax' => [
                    'url' => $this->url_generator->generate(Admin_Courses_Controller::class, 'get_popup_create_course', [
                        Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
                    ]),
                    'method' => 'POST',
                    'response_property_with_content' => 'content',
                ]
            ]
        ];
    }
}
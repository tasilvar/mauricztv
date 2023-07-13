<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\expiring_customers;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Interface_Dynamic_Table_Config_Provider;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Caps;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\User_Capability_Factory;

class Expiring_Customers_Table_Config_Provider implements Interface_Dynamic_Table_Config_Provider
{
    private const TABLE_ID = 'expiring_customers_table';

    private Dynamic_Tables_Module $dynamic_tables_module;
    private Expiring_Customers_Table_Data_Provider $data_provider;
    private Interface_Translator $translator;
    private User_Capability_Factory $user_capability_factory;
    private Interface_Readable_Course_Repository $course_repository;

    public function __construct(
        Dynamic_Tables_Module $dynamic_tables_module,
        Expiring_Customers_Table_Data_Provider $data_provider,
        Interface_Translator $translator,
        User_Capability_Factory $user_capability_factory,
        Interface_Readable_Course_Repository $course_repository
    ) {
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->data_provider = $data_provider;
        $this->translator = $translator;
        $this->user_capability_factory = $user_capability_factory;
        $this->course_repository = $course_repository;
    }

    public function get_config(): Dynamic_Table_Config
    {
        $config = $this->dynamic_tables_module->create_table_config(
            $this->get_table_id(),
            $this->data_provider,
            $this->get_columns_config()
        );

        $config->disable_multi_sort();
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
                'property' => 'name',
                'label' => $this->translator->translate('expiring_customers.column.name'),
                'sortable' => false
            ],
            [
                'property' => 'email',
                'label' => $this->translator->translate('expiring_customers.column.email'),
                'sortable' => false
            ],
            [
                'property' => 'access_to',
                'label' => $this->translator->translate('expiring_customers.column.access_to'),
                'type' => 'date'
            ],
            [
                'property' => 'course',
                'label' => $this->translator->translate('expiring_customers.column.course'),
                'sortable' => false,
                'filter' => 'select',
                'filter_options' => $this->get_course_filter_values()
            ],
        ];
    }

    private function get_course_filter_values(): array
    {
        $course_collection = $this->course_repository->find_all();
        $options = [];
        foreach ($course_collection as $index => $course) {
            $encoded_name = htmlspecialchars($course->get_title(), ENT_QUOTES);

            $options[] = [
                'label' => $encoded_name,
                'value' => $course->get_id()->to_int()
            ];
        }

        return $options;
    }
}
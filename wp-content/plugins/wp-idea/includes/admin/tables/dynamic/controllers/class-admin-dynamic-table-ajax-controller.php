<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\controllers;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config_Provider_Registry;
use bpmj\wpidea\admin\tables\dynamic\controllers\access\Dynamic_Table_Data_Permissions_Validator;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\export\Dynamic_Table_Data_Exporter;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Dynamic_Table_Ajax_Controller extends Ajax_Controller
{
    private Dynamic_Table_Config_Provider_Registry $config_provider_registry;
    private Dynamic_Table_Data_Exporter $dynamic_table_data_exporter;
    private Dynamic_Table_Data_Permissions_Validator $permissions_validator;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Dynamic_Table_Config_Provider_Registry $config_provider_registry,
        Dynamic_Table_Data_Exporter $dynamic_table_data_exporter,
        Dynamic_Table_Data_Permissions_Validator $permissions_validator
    ) {
        $this->config_provider_registry = $config_provider_registry;
        $this->dynamic_table_data_exporter = $dynamic_table_data_exporter;
        $this->permissions_validator = $permissions_validator;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'caps' => [Caps::CAP_MANAGE_POSTS, Caps::CAP_USE_WP_IDEA_MODE],
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function get_data_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $table_id = $request_body['tableId'] ?? null;
        $filters = $request_body['filters'] ?? [];
        $page = $request_body['page'] ?? 1;
        $per_page = $request_body['perPage'] ?? -1;
        $sort_by = $request_body['sortBy'] ?? [];

        $table_config = $this->get_table_config_by_table_id($table_id);

        $this->permissions_validator->verify_permissions($table_config->get_required_caps(), $table_config->get_required_roles());

        $data_provider = $table_config->get_data_provider();

        $total_items = $data_provider->get_total($filters);
        $total_pages = ceil($total_items / $per_page);
        $rows = $data_provider->get_rows(
            $filters,
            $this->process_sort_by($sort_by),
            $per_page,
            $page,
            Dynamic_Table_Data_Usage_Context::from_value(Dynamic_Table_Data_Usage_Context::DISPLAY_DATA)
        );

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'rows'        => $rows,
            'total_items' => $total_items,
            'total_pages' => $total_pages
        ]);
    }

    public function export_action(Current_Request $current_request): void
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $table_id = $request_body['tableId'] ?? null;
        $filters = $request_body['filters'] ?? [];
        $hidden_columns = $request_body['hiddenColumns'] ?? [];

        $table_config = $this->get_table_config_by_table_id($table_id);

        $this->permissions_validator->verify_permissions($table_config->get_required_caps(), $table_config->get_required_roles());

        $data_provider = $table_config->get_data_provider();
        $context = Dynamic_Table_Data_Usage_Context::from_value(Dynamic_Table_Data_Usage_Context::EXPORT_DATA);
        $rows = $data_provider->get_rows(
            $filters,
            new Sort_By_Clause(),
            -1,
            1,
            $context
        );

        $this->dynamic_table_data_exporter->export_rows(
            $rows,
            $table_config->get_columns(),
            $hidden_columns
        );
    }

    private function process_sort_by(array $sort_by): Sort_By_Clause
    {
        $default_sort_by = (new Sort_By_Clause())
            ->sort_by('created_at', true)
            ->sort_by('id', true);

        if(empty($sort_by)) {
            return $default_sort_by;
        }

        $parsed_sort_by = new Sort_By_Clause();

        foreach ($sort_by as $sort_by_condition) {
            $parsed_sort_by->sort_by($sort_by_condition['id'], $sort_by_condition['desc']);
        }

        return $parsed_sort_by;
    }

    private function get_table_config_by_table_id(?string $table_id): Dynamic_Table_Config
    {
        if (is_null($table_id)) {
            throw new \Exception('No tableId param provided!');
        }

        $config_provider = $this->config_provider_registry->get_provider_by_table_id($table_id);

        if (is_null($config_provider)) {
            throw new \Exception('Invalid table ID provided: ' . var_export($table_id, true) . '. Make sure you have registered config provider for this table.');
        }

        return $config_provider->get_config();
    }
}
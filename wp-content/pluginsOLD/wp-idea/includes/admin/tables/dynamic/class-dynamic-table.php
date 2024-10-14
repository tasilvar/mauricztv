<?php

namespace bpmj\wpidea\admin\tables\dynamic;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\controllers\Admin_Dynamic_Table_Ajax_Controller;
use bpmj\wpidea\admin\tables\dynamic\user_settings\Interface_User_Table_Settings_Service;
use bpmj\wpidea\admin\tables\dynamic\controllers\Admin_User_Table_Settings_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Dynamic_Table implements Interface_Dynamic_Table
{
    private Dynamic_Table_Config $config;

    private Interface_View_Provider $view_provider;

    private Interface_Translator $translator;

    private Interface_Url_Generator $url_generator;

    private Interface_User_Table_Settings_Service $user_table_settings_service;

    private Url_Filters_Parser $url_filters_parser;

    public function __construct(
        Dynamic_Table_Config $config,
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator,
        Url_Filters_Parser $url_filters_parser,
        Interface_User_Table_Settings_Service $user_table_settings_service
    ) {
        $this->config = $config;
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
        $this->url_filters_parser = $url_filters_parser;
        $this->user_table_settings_service = $user_table_settings_service;
    }

    public function get_html(string $html_classes = ''): string
    {
        $per_page = $this->user_table_settings_service->get_results_per_page_option($this->config->get_table_id());
        $hidden_columns = $this->user_table_settings_service->get_hidden_columns_option($this->config->get_table_id());

        $this->config->set_user_settings_values($per_page, $hidden_columns);
        $this->config->set_i18n_strings($this->get_i18n());
        $this->config->set_save_table_settings_url($this->get_save_table_settings_url());
        $this->config->set_filters($this->url_filters_parser->get_filters());
        $this->config->set_export_url($this->get_export_url());
        $this->config->set_data_source_url($this->get_data_source_url());

        return $this->view_provider->get_admin('/utils/tables/dynamic/table', [
            'config' => $this->config,
            'classes' => $html_classes
        ]);
    }

    private function get_i18n(): array
    {
        return $this->translator->translate_many([
            'results_per_page',
            'data_types',
            'data_types.hint',
            'results.showing',
            'results.to',
            'results.of',
            'results.results',
            'pagination.item_x_of',
            'pagination.prev',
            'pagination.next',
            'loading',
            'refresh',
            'filters.select',
            'filters.show',
            'filters.hide',
            'filters.clear',
            'filters.clear_one',
            'filters.active_count',
            'filters.type',
            'filters.select_date',
            'filters.select_date.today',
            'filters.select_date.yesterday',
            'filters.select_date.this_week',
            'filters.select_date.last_week',
            'filters.select_date.this_month',
            'filters.select_date.last_month',
            'filters.select_date.from_the_start',
            'filters.select_date.to_the_end',
            'filters.select_date.apply',
            'filters.select_date.custom_range',
            'filters.select_date.custom_range.days',
            'filters.select_date.cancel',
            'filters.number_range.to',
            'export',
            'export.loading',
            'cell_content.read_more',
            'cell_content.read_less',
            'bulk_actions'
        ], 'dynamic_table');
    }

    private function get_save_table_settings_url(): string
    {
        return $this->url_generator->generate(Admin_User_Table_Settings_Ajax_Controller::class, 'save_user_settings', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_export_url(): string
    {
        return $this->url_generator->generate(Admin_Dynamic_Table_Ajax_Controller::class, 'export', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_data_source_url(): string
    {
        return $this->url_generator->generate(Admin_Dynamic_Table_Ajax_Controller::class, 'get_data', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }
}
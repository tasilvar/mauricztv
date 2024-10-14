<?php

namespace bpmj\wpidea\admin\tables\dynamic;

use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\user_settings\Interface_User_Table_Settings_Service;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Dynamic_Table_Factory implements Interface_Dynamic_Table_Factory
{
    private $view_provider;

    private $i18n_handler;

    private $url_generator;

    private $user_table_settings_service;

    private $url_filters_parser;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $i18n_handler,
        Interface_Url_Generator $url_generator,
        Interface_User_Table_Settings_Service $user_table_settings_service,
        Url_Filters_Parser $url_filters_parser
    )
    {
        $this->view_provider = $view_provider;
        $this->i18n_handler = $i18n_handler;
        $this->url_generator = $url_generator;
        $this->user_table_settings_service = $user_table_settings_service;
        $this->url_filters_parser = $url_filters_parser;
    }

    public function create(Dynamic_Table_Config $config): Interface_Dynamic_Table
    {
        return new Dynamic_Table(
            $config,
            $this->view_provider,
            $this->i18n_handler,
            $this->url_generator,
            $this->url_filters_parser,
            $this->user_table_settings_service
        );
    }
}

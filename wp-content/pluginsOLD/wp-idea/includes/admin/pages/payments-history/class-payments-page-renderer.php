<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table_Factory;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\controllers\admin\Admin_Payment_History_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;

class Payments_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Order_Table_Config_Provider $config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Order_Table_Config_Provider $config_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        Interface_Url_Generator $url_generator
    ) {
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->config_provider = $config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->url_generator = $url_generator;
    }

    public function get_rendered_page(): string
    {
        if ( isset( $_GET['view'] ) && 'order-details' === $_GET['view'] ) {
            return $this->view_provider->get_admin('/pages/payment-details/details',[
                'url_generator' => $this->url_generator
            ]);
        }

        return $this->view_provider->get_admin('/pages/payments/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('orders.page_title')
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }
}
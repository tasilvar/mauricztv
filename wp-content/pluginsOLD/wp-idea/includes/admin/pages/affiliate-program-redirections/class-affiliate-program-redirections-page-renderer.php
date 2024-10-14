<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program_redirections;

use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_External_Landing_Link_Controller;
use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Admin_View_Names;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\admin\tables\dynamic\Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\api\Product_API_Search_Criteria;

class Affiliate_Program_Redirections_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_View_Provider $view_provider;
    private Interface_Translator $translator;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Affiliate_Redirections_Table_Config_Provider $config_provider;
    private Current_Request $current_request;
    private Interface_Url_Generator $url_generator;
    private Interface_Product_API $product_API;

    public function __construct(
        Interface_View_Provider $view_provider,
        Interface_Translator $translator,
        Dynamic_Tables_Module $dynamic_tables_module,
        Affiliate_Redirections_Table_Config_Provider $config_provider,
        Current_Request $current_request,
        Interface_Url_Generator $url_generator,
        Interface_Product_API $product_API
    ) {
        $this->view_provider = $view_provider;
        $this->translator = $translator;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->config_provider = $config_provider;
        $this->current_request = $current_request;
        $this->url_generator = $url_generator;
        $this->product_API = $product_API;
    }

    public function get_rendered_page(): string
    {
        $action = $this->current_request->get_request_arg('action');

        if ('add' === $action) {
            return $this->view_provider->get_admin(Admin_View_Names::AFFILIATE_PROGRAM_REDIRECTIONS_ADD_LINK, [
                'page_title' => $this->translator->translate('affiliate_program_redirections.actions.add.page_title'),
                'action_url' => $this->get_add_action_url(),
                'go_back_url' => $this->get_go_back_url(),
                'translator' => $this->translator,
                'products' => $this->get_products_for_select_input_options()
            ]);
        }

        return $this->view_provider->get_admin(Admin_View_Names::AFFILIATE_PROGRAM_REDIRECTIONS, [
            'page_title' => $this->translator->translate('affiliate_program_redirections.page_title'),
            'table' => $this->prepare_table()
        ]);
    }

    private function get_add_action_url(): string
    {
        return $this->url_generator->generate(Admin_External_Landing_Link_Controller::class, 'add', [
            Nonce_Handler::DEFAULT_ACTION_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_go_back_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::AFFILIATE_PROGRAM_REDIRECTIONS
        ]);
    }

    private function prepare_table(): Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }

    private function get_products_for_select_input_options(): array
    {
        $products = $this->product_API->find_by_criteria(
            Product_API_Search_Criteria::create()
        );

        $products_for_input = [];
        foreach ($products as $product) {
            $products_for_input[$product->get_id()] = $product->get_name();
        }

        return $products_for_input;
    }
}
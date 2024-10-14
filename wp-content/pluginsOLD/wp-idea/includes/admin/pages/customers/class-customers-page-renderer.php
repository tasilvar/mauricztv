<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\customers;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\admin\pages\customers\Customers;

class Customers_Page_Renderer
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Customer_Table_Config_Provider $table_config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Current_Request $current_request;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Customer_Table_Config_Provider $table_config_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        Current_Request $current_request
    ) {
        $this->translator             = $translator;
        $this->view_provider          = $view_provider;
        $this->table_config_provider = $table_config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->current_request = $current_request;
    }

    public function render_page(): void
    {
        $request_view = $this->current_request->get_request_arg('view');

        switch ($request_view) {
            case Customers::DETAILS_VIEW_OVERVIEW:
                $this->get_customer_overview_html();
                return;
            case Customers::DETAILS_VIEW_NOTES:
                $this->get_customer_notes_html();
                return;
            case Customers::DETAILS_VIEW_TOOLS:
                $this->get_customer_tools_html();
                return;
            case Customers::DETAILS_VIEW_DELETE:
                $this->get_customer_delete_html();
                return;
            default:
                $this->get_customer_table_html();
        }
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->table_config_provider->get_config()
        );
    }

    private function get_customer_table_html(): void
    {
        echo $this->view_provider->get_admin('/pages/customers/customers', [
            'table'      => $this->prepare_table(),
            'page_title' => $this->translator->translate('customers.page_title')
        ]);
    }

    private function get_customer_overview_html(): void
    {
        if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
            _e( 'Invalid Customer ID Provided.', BPMJ_EDDCM_DOMAIN );
        }

        $customer_id = (int)$_GET['id'];
        $customer    = new \EDD_Customer( $customer_id );

        if ( empty( $customer->id ) ) {
            _e( 'Invalid Customer ID Provided.', BPMJ_EDDCM_DOMAIN );
            return;
        }

        $customer_tabs = edd_customer_tabs();

        $customer_edit_role = apply_filters( 'edd_edit_customers_role', 'edit_shop_payments' );

        $customer_name = apply_filters( 'lms_filter_sensitive__customer_name', $customer->name, $customer->user_id, $customer->email );
        $customer_email = apply_filters( 'lms_filter_sensitive__customer_email', $customer->email, $customer->user_id, $customer->email );
        $no_edit = apply_filters( 'lms_filter_sensitive__disallow_edit', false );

        echo $this->view_provider->get_admin('/pages/customers/overview', [
            'customer'      => $customer,
            'customer_tabs' => $customer_tabs,
            'customer_edit_role' => $customer_edit_role,
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'no_edit' => $no_edit,
        ]);
    }

    private function get_customer_notes_html(): void
    {
        $customer_id = (int)$_GET['id'];
        $customer    = new \EDD_Customer( $customer_id );

        if ( empty( $customer->id ) ) {
            _e( 'Invalid Customer ID Provided.', BPMJ_EDDCM_DOMAIN );
            return;
        }

        $paged       = isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ? $_GET['paged'] : 1;
        $paged       = absint( $paged );
        $note_count  = $customer->get_notes_count();
        $per_page    = apply_filters( 'edd_customer_notes_per_page', 20 );
        $total_pages = ceil( $note_count / $per_page );

        $customer_notes = $customer->get_notes( $per_page, $paged );

        $customer_name = apply_filters( 'lms_filter_sensitive__customer_name', $customer->name, $customer->user_id, $customer->email );
        $customer_email = apply_filters( 'lms_filter_sensitive__customer_email', $customer->email, $customer->user_id, $customer->email );

        $customer_tabs = edd_customer_tabs();

        echo $this->view_provider->get_admin('/pages/customers/notes', [
            'customer'      => $customer,
            'customer_tabs' => $customer_tabs,
            'customer_notes'=> $customer_notes,
            'customer_name' => $customer_name,
            'customer_email'=> $customer_email,
            'paged'         => $paged,
            'total_pages'   => $total_pages,
        ]);
    }

    private function get_customer_tools_html(): void
    {
        $customer_id = (int)$_GET['id'];
        $customer    = new \EDD_Customer( $customer_id );

        if ( empty( $customer->id ) ) {
            _e( 'Invalid Customer ID Provided.', BPMJ_EDDCM_DOMAIN );
            return;
        }

        $customer_name = apply_filters( 'lms_filter_sensitive__customer_name', $customer->name, $customer->user_id, $customer->email );
        $customer_email = apply_filters( 'lms_filter_sensitive__customer_email', $customer->email, $customer->user_id, $customer->email );

        $customer_tabs = edd_customer_tabs();

        echo $this->view_provider->get_admin('/pages/customers/tools', [
            'customer'      => $customer,
            'customer_tabs' => $customer_tabs,
            'customer_name' => $customer_name,
            'customer_email'=> $customer_email,
        ]);
    }

    private function get_customer_delete_html(): void
    {
        $customer_id = (int)$_GET['id'];
        $customer    = new \EDD_Customer( $customer_id );

        if ( empty( $customer->id ) ) {
            _e( 'Invalid Customer ID Provided.', BPMJ_EDDCM_DOMAIN );
            return;
        }

        $customer_name = apply_filters( 'lms_filter_sensitive__customer_name', $customer->name, $customer->user_id, $customer->email );
        $customer_email = apply_filters( 'lms_filter_sensitive__customer_email', $customer->email, $customer->user_id, $customer->email );

        $customer_tabs = edd_customer_tabs();

        echo $this->view_provider->get_admin('/pages/customers/delete', [
            'customer'      => $customer,
            'customer_tabs' => $customer_tabs,
            'customer_name' => $customer_name,
            'customer_email'=> $customer_email,
        ]);
    }

}

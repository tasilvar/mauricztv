<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Users_Controller extends Base_Controller
{
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Url_Generator $url_generator
    ) {
        $this->url_generator = $url_generator;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'allowed_methods' => [Request_Method::GET]
        ];
    }

    public function delete_bulk_action(Current_Request $current_request): void
    {
        $params = $current_request->get_request_arg('params');
        $params = json_decode($params, true) ?? [];

        $ids = $params['ids'] ?? [];

        if (!$ids) {
            $this->redirector->redirect($this->get_user_lists_url());
        }

        $this->redirector->redirect($this->get_delete_user_bulk_url($ids));
    }

    public function send_links_bulk_action(Current_Request $current_request): void
    {
        $params = $current_request->get_request_arg('params');
        $params = json_decode($params, true) ?? [];

        $ids = $params['ids'] ?? [];

        if (!$ids) {
            $this->redirector->redirect($this->get_user_lists_url());
        }

        $this->redirector->redirect($this->get_send_link_bulk_url($ids));
    }

    private function get_delete_user_bulk_url(array $ids): string
    {
        return $this->url_generator->generate_admin_page_url('users.php', [
            'action' => 'delete',
            'users[]' => implode('&users[]=', $ids),
            'wp_http_referer' => $this->get_user_proxy_url(),
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create('bulk-users')
        ]);
    }

    private function get_send_link_bulk_url(array $ids): string
    {
        return $this->url_generator->generate_admin_page_url('users.php', [
            'action' => 'resetpassword',
            'users[]' => implode('&users[]=', $ids),
            'wp_http_referer' => $this->get_user_lists_url(),
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create('bulk-users')
        ]);
    }

    private function get_user_proxy_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::USERS_PROXY,
        ]);
    }

    private function get_user_lists_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::USERS,
        ]);
    }
}

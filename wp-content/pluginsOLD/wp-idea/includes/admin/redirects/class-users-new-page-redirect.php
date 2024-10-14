<?php

namespace bpmj\wpidea\admin\redirects;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\menu\Admin_Menu_Reorderer;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\user\Interface_User_Repository;

class Users_New_Page_Redirect implements Interface_Initiable
{
    private Interface_Actions $actions;
    private Interface_Filters $filters;
    private Interface_Url_Generator $url_generator;
    private Current_Request $current_request;
    private Interface_User_Repository $user_repository;
    private Admin_Menu_Reorderer $admin_menu_reorderer;
    private Interface_Redirector $redirector;

    public function __construct(
        Interface_Actions $actions,
        Interface_Filters $filters,
        Interface_Url_Generator $url_generator,
        Current_Request $current_request,
        Interface_User_Repository $user_repository,
        Admin_Menu_Reorderer $admin_menu_reorderer,
        Interface_Redirector $redirector
    ) {
        $this->actions = $actions;
        $this->filters = $filters;
        $this->url_generator = $url_generator;
        $this->current_request = $current_request;
        $this->user_repository = $user_repository;
        $this->admin_menu_reorderer = $admin_menu_reorderer;
        $this->redirector = $redirector;
    }

    public function init(): void
    {
        $this->filters->add('wp_redirect', [$this, 'redirect_after_add_new_user'], 10, 1);
        $this->actions->add('admin_init', [$this, 'redirect_after_deleted_user']);
    }

    public function redirect_after_add_new_user(string $location): string
    {
        if (!$this->is_create_new_user_request()) {
            return $location;
        }

        if (!$this->is_default_redirect_to_default_user_list_page($location)) {
            return $location;
        }

        if (!$this->admin_menu_reorderer->should_menu_be_reordered_for_current_user()) {
            return $location;
        }

        return $this->get_users_page_url_with_action_added();
    }

    public function redirect_after_deleted_user(): void
    {
        if (!$this->is_user_proxy_page()) {
            return;
        }

        $this->redirector->redirect($this->get_users_page_url_with_action_deleted());
    }

    private function is_user_proxy_page(): bool
    {
        $page = $this->current_request->get_request_arg('page');

        return $this->is_page('admin.php') && isset($_GET['page']) && Admin_Menu_Item_Slug::USERS_PROXY === $page;
    }

    private function is_create_new_user_request(): bool
    {
        return $this->is_page('user-new.php') && is_admin();
    }

    private function is_page(string $page): bool
    {
        return strpos($_SERVER['REQUEST_URI'], $page);
    }

    private function get_users_page_url_with_action_added(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::USERS,
            'action' => 'added'
        ]);
    }

    private function get_users_page_url_with_action_deleted(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::USERS,
            'action' => 'deleted'
        ]);
    }

    private function is_default_redirect_to_default_user_list_page(string $location): bool
    {
        $email = $this->current_request->get_request_arg('email');

        if(!$email) {
            return false;
        }

        $user = $this->user_repository->find_by_email($email);

        return $location === 'users.php?update=add&id=' . $user->get_id()->to_int();
    }
}
<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\users;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\user\User_Query_Criteria;

class User_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Interface_Current_User_Getter $current_user_getter;
    private Interface_Translator $translator;
    private Interface_User_Repository $user_repository;
    private Interface_User_Permissions_Service $user_permissions_service;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Current_User_Getter $current_user_getter,
        Interface_Translator $translator,
        Interface_User_Repository $user_repository,
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_Url_Generator $url_generator

    ) {
        $this->current_user_getter = $current_user_getter;
        $this->translator = $translator;
        $this->user_repository = $user_repository;
        $this->user_permissions_service = $user_permissions_service;
        $this->url_generator = $url_generator;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $criteria = $this->get_criteria_from_query_filters($filters);
        $entities = $this->user_repository->find_by_criteria($criteria, $page, $per_page, $sort_by);
        $rows = [];

        foreach ($entities as $entity) {
            $rows[] = [
                'id' => $entity->get_id()->to_int(),
                'login' => $entity->get_login(),
                'full_name' => $entity->full_name() ? $entity->full_name()->get_full_name() : $entity->get_first_name(),
                'email' => $entity->get_email(),
                'roles' => $this->get_name_roles($entity),
                'edit_url' => $this->get_edit_url($entity),
                'delete_url' => $this->get_delete_url($entity),
                'send_link_url' => $this->get_send_link_url($entity),
                'disabled_button_for_logged_user' => $this->get_disabled_button_for_the_currently_logged_in_user($entity)
            ];
        }

        return $rows;
    }

    public function get_total(array $filters): int
    {
        $criteria = $this->get_criteria_from_query_filters($filters);

        return $this->user_repository->count_by_criteria($criteria);
    }

    private function get_name_roles(Interface_User $entity): string
    {
        $roles = $this->user_permissions_service->get_roles($entity);

        $array = [];
        foreach ($roles as $role) {
            $array[] = $this->translator->translate('users.column.role.' . $role->get_name());
        }
        return implode(',', $array);
    }

    private function get_edit_url(Interface_User $entity): string
    {
        return $this->url_generator->generate_admin_page_url('user-edit.php', [
            'user_id' => $entity->get_id()->to_int(),
            'wp_http_referer' => $this->get_user_lists_url()
        ]);
    }

    private function get_user_lists_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::USERS,
        ]);
    }

    private function get_user_proxy_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::USERS_PROXY,
        ]);
    }

    private function get_delete_url(Interface_User $entity): string
    {
        return $this->url_generator->generate_admin_page_url('users.php', [
            'action' => 'delete',
            'user' => $entity->get_id()->to_int(),
            'wp_http_referer' => $this->get_user_proxy_url(),
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create('bulk-users')
        ]);
    }

    private function get_send_link_url(Interface_User $entity): string
    {
        return $this->url_generator->generate_admin_page_url('users.php', [
            'action' => 'resetpassword',
            'users' => $entity->get_id()->to_int(),
            'wp_http_referer' => $this->get_user_lists_url(),
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create('bulk-users')
        ]);
    }

    private function get_disabled_button_for_the_currently_logged_in_user(Interface_User $entity): bool
    {
        $current_user = $this->current_user_getter->get();

        if (!$current_user) {
            return false;
        }

        if (!$current_user->get_id()->equals(new ID($entity->get_id()->to_int()))) {
            return false;
        }

        return true;
    }

    private function get_criteria_from_query_filters(array $filters): User_Query_Criteria
    {
        return (new User_Query_Criteria())->get_from_query_filters($filters);
    }
}
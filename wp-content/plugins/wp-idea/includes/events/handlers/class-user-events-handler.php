<?php

declare(strict_types=1);

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\user\User_Role_Collection;
use Psr\Log\LoggerInterface;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\user\Interface_User_Permissions_Service;


class User_Events_Handler implements Interface_Event_Handler
{
    private $translator;
    private $events;
    private $logger;
    private $current_user;
    private $user;

    public function __construct(
        Interface_Translator $translator,
        Interface_Events $events,
        LoggerInterface $logger,
        Interface_Current_User_Getter $current_user_getter,
        Interface_User_Repository $user_repository,
        Interface_User_Permissions_Service $user_permissions_service
    )
    {
        $this->translator = $translator;
        $this->events = $events;
        $this->logger = $logger;
        $this->current_user_getter = $current_user_getter;
        $this->user_repository = $user_repository;
        $this->user_permissions_service = $user_permissions_service;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::USER_REGISTRED,  [$this, 'user_register'], 10, 1);
        $this->events->on(Event_Name::PROFILE_UPDATED ,  [$this, 'changing_user_permissions'], 10, 2);
    }

    public function user_register(int $user_id): void
    {
                $current_user = $this->current_user_getter->get();
                $user_register = $this->user_repository->find_by_id(new User_ID($user_id));
                $registered_user_roles = $this->user_permissions_service->get_roles($user_register);

                $user_roles = $this->get_user_roles_as_array($registered_user_roles);

                if($this->is_account_created_by_other_user($current_user)){
                    $log = 'logs.log_message.user_registred_by_admin';
                    $current_user_login = $current_user->get_login();
                }else{
                    $log = 'logs.log_message.user_registred_during_checkout';
                    $current_user_login = '';
                }  

                $this->logger->info(
                    $this->translator->translate($log), [
                         empty($current_user_login) ? '' : 'current_user_login' => $current_user_login,
                        'user_register_login' =>  $user_register->get_login(),
                        'user_register_role' => implode(',', $user_roles)
                    ]
                );
    }

    public function changing_user_permissions(int $user_id, \WP_User $old_user_data): void
    {
        
            $current_user = $this->current_user_getter->get();
            $edit_user = $this->user_repository->find_by_id(new User_ID($user_id));
            $registred_user_roles = $this->user_permissions_service->get_roles($edit_user);

            $edit_user_roles = $this->get_user_roles_as_array($registred_user_roles);

            $comparison_of_role_tables = array_diff_assoc($edit_user_roles, $old_user_data->roles);

            if(!$this->user_roles_changed($comparison_of_role_tables)){
                return;
            }

            $this->logger->info(
                $this->translator->translate('logs.log_message.user_changed_permissions'), [
                    'current_user_login' => $current_user ? $current_user->get_login() : 'System',
                    'edit_user_login' => $edit_user->get_login(),
                    'edit_user_role' => implode(',', $edit_user_roles)
                ]
            );

         
    }

    private function get_user_roles_as_array(User_Role_Collection $registred_user_roles): array
    {
        $user_roles = [];
        foreach($registred_user_roles as $user_role) {
            $user_roles[] = $user_role->get_name();
        }
        return $user_roles;
    }

    private function user_roles_changed(array $comparison_of_role_tables): bool
    {
        return count($comparison_of_role_tables) > 0;
    }

    private function is_account_created_by_other_user(?Interface_User $current_user): bool
    {
        return !is_null($current_user);
    }

}
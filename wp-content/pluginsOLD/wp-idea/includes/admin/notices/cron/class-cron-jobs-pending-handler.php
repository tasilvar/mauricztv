<?php

namespace bpmj\wpidea\admin\notices\cron;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\admin\notices\Interface_Notice_Handler;
use bpmj\wpidea\Caps;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Role;
use bpmj\wpidea\user\User_Role_Collection;

class Cron_Jobs_Pending_Handler implements Interface_Notice_Handler
{
    private const MAX_DELAY_IN_MINUTES = 15;

    private Notices $notices;

    private Interface_Translator $translator;

    private Interface_User_Permissions_Service $user_permissions_service;

    private Interface_Current_User_Getter $current_user_getter;

    private Interface_Actions $actions;

    public function __construct(
        Notices $notices,
        Interface_Translator $translator,
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_Current_User_Getter $current_user_getter,
        Interface_Actions $actions
    ) {
        $this->notices    = $notices;
        $this->translator = $translator;
        $this->user_permissions_service = $user_permissions_service;
        $this->current_user_getter = $current_user_getter;
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add('init', function () {
            $this->render_cron_jobs_pending_notice();
        });
    }

    public function render_cron_jobs_pending_notice(): void
    {
        if ($this->should_show_notice()) {
            $this->notices->display_notice(
                $this->notice_content(),
                Notices::TYPE_ERROR
            );
        }
    }

    private function should_show_notice(): bool
    {
        $current_user = $this->current_user_getter->get();

        if ( is_null( $current_user) ) {
            return false;
        }

        $admin_user_role = new User_Role(Caps::ROLE_SITE_ADMIN);

        $required_roles = new User_Role_Collection();
        $required_roles->add($admin_user_role);

        return $this->minutes_since_last_cron_execution()
            >= self::MAX_DELAY_IN_MINUTES && $this->user_permissions_service->has_any_of_the_roles($current_user, $required_roles);
    }

    private function notice_content(): string
    {
        return $this->translator->translate('notifications.cron_not_working_correctly');
    }

    private function minutes_since_last_cron_execution(): int
    {
        $latest_cron_execution_timestamp = get_transient(
            Cron_Watchdog_Job::TRANSIENT_NAME
        );
        if ( ! $latest_cron_execution_timestamp) {
            return self::MAX_DELAY_IN_MINUTES;
        }
        $current_timestamp     = time();
        $difference_in_seconds = $current_timestamp - $latest_cron_execution_timestamp;

        return floor($difference_in_seconds / 60);
    }
}

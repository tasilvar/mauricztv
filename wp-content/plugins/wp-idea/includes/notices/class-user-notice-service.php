<?php namespace bpmj\wpidea\notices;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\user\Interface_Current_User_Getter;

class User_Notice_Service
{

    const ALLOW_USER_NOTICE_OPTION_KEY = 'allow_user_notice';

    const USER_NOTICE_CONTENT_OPTION_KEY = 'user_notice_content';

    const USER_NOTICE_CLOSE_BUTTON_OPTION_KEY = 'user_notice_show_close_button';

    private $subscription;

    private $options;

    private $actions;

    private $current_user;

    private $user_model;

    private $notice_metadata_service;

    public function __construct(
        Subscription $subscription,
        LMS_Settings $options,
        Interface_Actions $actions,
        User_Notice_Metadata_Service $notice_metadata_service,
        Interface_Current_User_Getter $current_user
    ) {
        $this->subscription            = $subscription;
        $this->options                 = $options;
        $this->actions                 = $actions;
        $this->notice_metadata_service = $notice_metadata_service;
        $this->current_user            = $current_user;
        $this->hook_actions();
    }

    public function should_show_notice(): bool
    {
        if (
            null === $this->user_model ||
            ! $this->functionality_allowed_in_current_plan() ||
            empty($this->notice_content())
        ) {
            return false;
        }

        return $this->notices_enabled() && ! $this->notice_metadata_service->has_notice_closed($this->user_model);
    }

    public function should_show_close_button(): bool
    {
        return (bool)$this->options->get(self::USER_NOTICE_CLOSE_BUTTON_OPTION_KEY);
    }

    public function close_notice_for_current_user(): void
    {
        $this->notice_metadata_service->close_notice($this->user_model);
    }

    public function get_html(): string
    {
        $content = $this->notice_content();
        $button  = $this->get_close_button_html();

        return "<div>{$content}</div><div>{$button}</div>";
    }

    public function functionality_allowed_in_current_plan(): bool
    {
        return in_array($this->subscription->get_plan(), [
            Subscription_Const::PLAN_PLUS,
            Subscription_Const::PLAN_PRO
        ]);
    }

    private function get_close_button_html(): string
    {
        $img_url = WPI()->templates->get_template_url() . '/assets/img/dismiss.svg';

        if ($this->should_show_close_button()) {
            return "<span onclick='closeNotice()' class='notice-close-button'>" .
                   "<img src='{$img_url}'/>" .
                   "</span>";
        } else {
            return '';
        }
    }

    private function notices_enabled(): bool
    {
        return (bool)$this->options->get(self::ALLOW_USER_NOTICE_OPTION_KEY);
    }

    private function notice_content(): string
    {
        return nl2br($this->options->get(self::USER_NOTICE_CONTENT_OPTION_KEY, ''));
    }

    private function hook_actions(): void
    {
        $update_option_hook = 'update_option_' . BPMJ_EDDCM_SETTINGS_SLUG;
        $this->actions->add($update_option_hook, function ($oldValue, $newValue) {
            if (
                ($oldValue['allow_user_notice'] ?? null) != ($newValue['allow_user_notice'] ?? null) ||
                ($oldValue['user_notice_content'] ?? null) != ($newValue['user_notice_content'] ?? null) ||
                ($oldValue['user_notice_show_close_button'] ?? null) != ($newValue['user_notice_show_close_button'] ?? null)
            ) {
                $this->notice_metadata_service->unmark_notice_is_closed_for_all_users();
            }
        }, 10, 2);

        $this->actions->add('init', function () {
            $this->user_model = $this->current_user->get();
        });
    }

}

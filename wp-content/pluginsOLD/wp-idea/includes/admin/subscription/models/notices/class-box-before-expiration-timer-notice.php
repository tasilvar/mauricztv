<?php

namespace bpmj\wpidea\admin\subscription\models\notices;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\Packages;
use bpmj\wpidea\View;

class Box_Before_Expiration_Timer_Notice
{
    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    private const PATH_TO_BOX_EXPIRATION_TIMER_NOTICE_VIEW = '/subscription/box/expiration_timer_notice';

    public function get_notice(): ?string
    {
        return View::get_admin(self::PATH_TO_BOX_EXPIRATION_TIMER_NOTICE_VIEW, [
            'message' => $this->get_message(),
            'buttons_settings' => $this->get_buttons_settings(),
            'show_timer'=> $this->should_show_timer(),
            'expiration_date' => $this->subscription->get_expiration_date()->get_as_timestamp()
        ]);
    }

    private function should_show_timer(): bool
    {
        return !$this->subscription->get_expiration_date()->is_exceeded();
    }

    private function get_message(): string
    {
        $exceeded_days = $this->subscription->get_expiration_date()->get_days_left();

        $message = sprintf(__('Your WP Idea license will expire in %1$s days (%2$s).', BPMJ_EDDCM_DOMAIN), '<b>' . $exceeded_days . '</b>',  $this->subscription->get_expiration_date()->get_as_datetime()->format('Y-m-d'));

        if($exceeded_days == 1 || $exceeded_days == 0){
            $day_string = 0 === $exceeded_days ? __('today', BPMJ_EDDCM_DOMAIN) : __('tomorrow', BPMJ_EDDCM_DOMAIN);
            $message = sprintf(__('Your WP Idea license will expire %1$s.', BPMJ_EDDCM_DOMAIN), '<b>' . $day_string . '</b>');
        }

        $message .= '<small>';
        $message .= sprintf(__('%1$sClick here%2$s to renew your license with discounted price (valid only when the license is active).', BPMJ_EDDCM_DOMAIN), ' <a href="' . Packages::instance()->get_renewal_url() . '">','</a>');
        $message .= '<br />';
        $message .= __('An active license gives you access to support and new versions of WP Idea (new features and bug fixes).', BPMJ_EDDCM_DOMAIN);
        $message .= '</small>';

        return $message;
    }

    private function get_buttons_settings(): array
    {
        return [
            [
                'text' => __('Renew', BPMJ_EDDCM_DOMAIN),
                'url' => Packages::instance()->get_renewal_url(),
            ],
        ];
    }
}

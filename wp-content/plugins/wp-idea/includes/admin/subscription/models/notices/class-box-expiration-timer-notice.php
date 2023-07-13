<?php

namespace bpmj\wpidea\admin\subscription\models\notices;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\Packages;
use bpmj\wpidea\View;

class Box_Expiration_Timer_Notice
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

        $message = __('Your license has expired.', BPMJ_EDDCM_DOMAIN);

        $message .= '<small>';
        $message .=  __('An active license gives you access to support and new versions of WP Idea (new features and bug fixes).', BPMJ_EDDCM_DOMAIN);
        $message .= '<br />';
        $message .=  sprintf(__('%1$sClick here%2$s to renew your license.', BPMJ_EDDCM_DOMAIN), ' <a href="' . Packages::instance()->get_renewal_url() . '">','</a>');
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

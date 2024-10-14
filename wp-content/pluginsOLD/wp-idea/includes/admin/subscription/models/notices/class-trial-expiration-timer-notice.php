<?php

namespace bpmj\wpidea\admin\subscription\models\notices;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\Software_Purchase;
use bpmj\wpidea\View;

class Trial_Expiration_Timer_Notice
{
    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    private const PATH_TO_TRIAL_EXPIRATION_TIMER_NOTICE_VIEW = '/subscription/go/trial_expiration_timer_notice';

    public function get_notice(): ?string
    {
        return View::get_admin(self::PATH_TO_TRIAL_EXPIRATION_TIMER_NOTICE_VIEW, [
            'message' => $this->get_message(),
            'buttons_settings' => $this->get_buttons_settings(),
            'show_timer'=> $this->should_show_timer(),
            'expiration_date' => $this->subscription->get_expiration_date()->get()
        ]);
    }

    private function should_show_timer(): bool
    {
        return !$this->subscription->get_expiration_date()->is_exceeded();
    }

    private function get_message(): string
    {
        $message = '<small>' . __('You are currently using a trial version of WP Idea.', BPMJ_EDDCM_DOMAIN) . '</small>';

        if($this->subscription->get_expiration_date()->is_exceeded()){
            return __('The WP Idea evaluation period ended!', BPMJ_EDDCM_DOMAIN);
        }

        switch ($this->subscription->get_expiration_date()->get_days_left()) {
            case 0:
                $message .= ' ' . sprintf(__('The evaluation period will end %1$s.', BPMJ_EDDCM_DOMAIN), '<b>' . __('today', BPMJ_EDDCM_DOMAIN) . '</b>');
                break;
            case 1:
                $message .= ' ' . sprintf(__('The evaluation period will end %1$s.', BPMJ_EDDCM_DOMAIN), '<b>' . __('tomorrow', BPMJ_EDDCM_DOMAIN) . '</b>');
                break;
            default:
                $message = __('The WP Idea evaluation period will end soon!', BPMJ_EDDCM_DOMAIN);
                break;
        }
        return $message;
    }

    private function get_buttons_settings(): array
    {
        return [
            [
                'text' => __('Buy GO', BPMJ_EDDCM_DOMAIN),
                'text_separator' => __('or', BPMJ_EDDCM_DOMAIN),
                'classes' => 'go',
                'html-data' => [
                    'modal-title' => __('WP Idea GO Prices', BPMJ_EDDCM_DOMAIN),
                    'buy-url' => Software_Purchase::GO_PRICING_URL,
                    'uid' =>  $this->subscription->get_id(),
                    'domain' => get_home_url(),
                ],
                'url' => Software_Purchase::GO_PRICING_URL,
            ],
            [
                'text' => __('Buy BOX', BPMJ_EDDCM_DOMAIN),
                'classes' => 'box',
                'html-data' => [
                    'modal-title' => __('WP Idea BOX Prices', BPMJ_EDDCM_DOMAIN),
                    'buy-url' => Software_Purchase::BOX_PRICING_URL,
                    'uid' => $this->subscription->get_id(),
                    'domain' => get_home_url(),
                ],
                'url' => Software_Purchase::GO_PRICING_URL,
            ],
        ];
    }
}

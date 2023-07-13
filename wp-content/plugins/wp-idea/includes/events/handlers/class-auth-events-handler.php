<?php

declare(strict_types=1);

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\translator\Interface_Translator;
use Psr\Log\LoggerInterface;

class Auth_Events_Handler implements Interface_Event_Handler
{
    private $translator;

    private $events;

    private $logger;

    private $request;

    public function __construct(
        Interface_Translator $translator,
        Interface_Events $events,
        LoggerInterface $logger,
        Current_Request $request
    )
    {
        $this->translator = $translator;
        $this->events = $events;
        $this->logger = $logger;
        $this->request = $request;
    }

    public function init(): void
    {
        $this->log_successful_user_login();
        $this->log_login_failed();
    }

    private function log_successful_user_login(): void
    {
        $this->events->on(Event_Name::USER_HAS_LOGGED_IN, function(string $user_login, $user) {
            $this->logger->info($this->translator->translate('logs.log_message.user_logged_in'), [
                'login' => $user_login,
                'email' => $user->user_email,
                'ip' => $this->request->get_user_ip()
            ]);
        }, 10, 2);
    }

    private function log_login_failed(): void
    {
        $this->events->on(Event_Name::USER_LOGIN_FAILED, function(string $user_login, $user) {
            $this->logger->info($this->translator->translate('logs.log_message.user_login_failed'), [
                'login' => $user_login,
                'ip' => $this->request->get_user_ip()
            ]);
        }, 10, 2);
    }
}
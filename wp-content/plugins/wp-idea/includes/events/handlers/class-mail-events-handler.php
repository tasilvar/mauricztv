<?php namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\logs\model\Log_Source;
use bpmj\wpidea\translator\Interface_Translator;
use Psr\Log\LoggerInterface;

class Mail_Events_Handler implements Interface_Event_Handler
{
    private $translator;

    private $logger;

    private $events;

    public function __construct(
        Interface_Translator $translator,
        LoggerInterface $logger,
        Interface_Events $events
    ) {
        $this->translator = $translator;
        $this->logger     = $logger;
        $this->events     = $events;
    }

    public function init(): void
    {
        $this->hook_actions();
    }

    private function hook_actions(): void
    {
        $this->events->on(Event_Name::WP_MAIL_SEND_ATTEMPT, [$this, 'log_send_attempt']);
        $this->events->on(Event_Name::WP_MAIL_SEND_ATTEMPT_FAILED, [$this, 'log_send_attempt_failed']);
    }

    public function log_send_attempt($data)
    {
        if(!isset($data['to'])){
            return;
        }

        $this->logger->info(
            $this->translator->translate('logs.emails.send_attempt')
            . PHP_EOL .
            $this->get_message_string($data['to'], $data['subject']),
            [
                'source' => Log_Source::COMMUNICATION
            ]
        );

        return $data;
    }

    public function log_send_attempt_failed(\WP_Error $error)
    {
        $error_data = $error->get_error_data();

        if(empty($error_data['to'][0])){
            return;
        }
        
        $this->logger->critical(
            $this->translator->translate('logs.emails.send_failed')
            . PHP_EOL .
            $error->get_error_message()
            . PHP_EOL .
            $this->get_message_string($error_data['to'][0] ?? '', $error_data['subject'] ?? ''),
            [
                'source' => Log_Source::COMMUNICATION
            ]
        );
    }

    private function get_message_string($to, $subject): string
    {
        return $this->translator->translate('logs.emails.to') . filter_var($to, FILTER_SANITIZE_EMAIL) . PHP_EOL .
               $this->translator->translate('logs.emails.subject') . strip_tags($subject);
    }

}
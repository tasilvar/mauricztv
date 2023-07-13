<?php

declare(strict_types=1);

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\data_types\Error_Status_Code;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;
use bpmj\wpidea\translator\Interface_Translator;
use Psr\Log\LoggerInterface;

class Log_Webhook_Events_Handler implements Interface_Event_Handler
{
    private Interface_Translator $translator;
    private LoggerInterface $logger;
    private Interface_Events $events;

    public function __construct(
        Interface_Translator $translator,
        LoggerInterface $logger,
        Interface_Events $events
    ) {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->events = $events;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::WEBHOOK_HAS_BEEN_CALLED,  [$this, 'saving_the_log_after_triggering_the_webhook_event'], 10, 2);
    }

   public function saving_the_log_after_triggering_the_webhook_event(Webhook $webhook, Error_Status_Code $error): void
   {
       $this->logger->info($this->translator->translate('logs.log_message.triggering_an_webhook_event'), [
           'type_of_webhook' => $this->translator->translate('webhooks.event.'.$webhook->get_type_of_event()->get_value()),
           'url' => $webhook->get_url()->get_value(),
           'request' => $error->get_value()
       ]);
   }

}
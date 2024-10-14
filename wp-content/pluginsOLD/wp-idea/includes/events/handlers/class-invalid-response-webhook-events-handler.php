<?php namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\data_types\{Error_Status_Code, mail\Email_Address, mail\Message, mail\Subject};
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\mail\Interface_Mailer;
use bpmj\wpidea\infrastructure\mail\Mail_Factory;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\options\Options_Const;

class Invalid_Response_Webhook_Events_Handler implements Interface_Event_Handler
{
    private $events;
    private $options;
    private $mailer;
    private $mail_factory;
    private const SUBJECT_ERROR_MAIL = 'ERROR MAIL';

    public function __construct(
        Interface_Events $events,
        Interface_Options $options,
        Interface_Mailer $mailer,
        Mail_Factory $mail_factory
    ) {
        $this->events = $events;
        $this->options = $options;
        $this->mailer = $mailer;
        $this->mail_factory = $mail_factory;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::WEBHOOK_INVALID_RESPONSE_RECEIVED,  [$this, 'send_mail'], 10, 2);
    }

   public function send_mail(Webhook $webhook, Error_Status_Code $error): void
   {
          $message = $webhook->get_url()->get_value().' '.$error->get_value();

          $mail_address = new Email_Address($this->options->get(Options_Const::ADMIN_EMAIL));
          $subject      = new Subject(self::SUBJECT_ERROR_MAIL);
          $message      = new Message($message);
          $mail = $this->mail_factory->create($mail_address, $subject, $message);

          $this->mailer->send($mail);
    }

}
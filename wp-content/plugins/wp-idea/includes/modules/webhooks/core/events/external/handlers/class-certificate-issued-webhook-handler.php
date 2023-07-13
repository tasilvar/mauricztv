<?php

namespace bpmj\wpidea\modules\webhooks\core\events\external\handlers;

use bpmj\wpidea\certificates\Certificate;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Sender;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Prepare_Urls;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\user\Interface_User;

class Certificate_Issued_Webhook_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Webhook_Sender $webhook_sender;
    private Webhook_Prepare_Urls $webhook_prepare_urls;
    private Interface_Webhook_Factory $webhook_factory;

    public function __construct(
        Interface_Events $events,
        Interface_Webhook_Sender $webhook_sender,
        Webhook_Prepare_Urls $webhook_prepare_urls,
        Interface_Webhook_Factory $webhook_factory
    ) {
        $this->events = $events;
        $this->webhook_sender = $webhook_sender;
        $this->webhook_prepare_urls = $webhook_prepare_urls;
        $this->webhook_factory = $webhook_factory;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::CERTIFICATE_ISSUED, [$this, 'send_certificate_details_webhook'], 10, 3);
    }

    public function send_certificate_details_webhook(?Certificate $certificate, Course $course, Interface_User $user): void
    {
        if (!$certificate) {
            return;
        }

        $data = $this->prepare_data($certificate, $course, $user);
        $webhook_urls = $this->webhook_prepare_urls->get_urls(Webhook_Types_Of_Events::CERTIFICATE_ISSUED);

        foreach ($webhook_urls as $url) {
            $webhook = $this->webhook_factory->create(Webhook_Types_Of_Events::CERTIFICATE_ISSUED, $url);

            $this->webhook_sender->send_data($webhook, $data);
        }

    }

    private function prepare_data(Certificate $certificate, Course $course, Interface_User $user): array
    {
        $data = [
            'course' => $course->get_title(),
            'full_name' => $user->full_name() ? $user->full_name()->get_full_name() : '',
            'email' => $user->get_email(),
            'certificate_number' => $certificate->get_certificate_number() ? $certificate->get_certificate_number()->get_value() : '',
            'date_certificate_created' => date_format($certificate->get_created(), 'Y-m-d')
        ];

        return $data;
    }
}

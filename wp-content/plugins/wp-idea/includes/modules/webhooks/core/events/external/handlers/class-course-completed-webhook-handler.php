<?php

namespace bpmj\wpidea\modules\webhooks\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Sender;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Prepare_Urls;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User;

class Course_Completed_Webhook_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Webhook_Sender $webhook_sender;
    private Webhook_Prepare_Urls $webhook_prepare_urls;
    private Interface_Webhook_Factory $webhook_factory;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Events $events,
        Interface_Webhook_Sender $webhook_sender,
        Webhook_Prepare_Urls $webhook_prepare_urls,
        Interface_Webhook_Factory $webhook_factory,
        Interface_Translator $translator
    ) {
        $this->events = $events;
        $this->webhook_sender = $webhook_sender;
        $this->webhook_prepare_urls = $webhook_prepare_urls;
        $this->webhook_factory = $webhook_factory;
        $this->translator = $translator;
    }


    public function init(): void
    {
        $this->events->on(Event_Name::COURSE_COMPLETED, [$this, 'send_course_completed_details_webhook'], 10, 2);
    }

    public function send_course_completed_details_webhook(Course $course, Interface_User $user): void
    {
        if (!$course && !$user) {
            return;
        }

        $data = $this->prepare_data($course, $user);
        $webhook_urls = $this->webhook_prepare_urls->get_urls(Webhook_Types_Of_Events::COURSE_COMPLETED);

        foreach ($webhook_urls as $url) {
            $webhook = $this->webhook_factory->create(Webhook_Types_Of_Events::COURSE_COMPLETED, $url);
            $this->webhook_sender->send_data($webhook, $data);
        }
    }

    private function prepare_data(Course $course, Interface_User $user): array
    {
        $data = [
            'course' => $course->get_title(),
            'full_name' => $user->full_name() ? $user->full_name()->get_full_name() : '',
            'email' => $user->get_email(),
            'date_of_the_event' => date('d.m.Y - H:i:s')
        ];

        return $data;
    }

}
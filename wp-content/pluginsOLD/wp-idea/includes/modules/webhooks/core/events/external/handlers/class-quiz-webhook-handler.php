<?php

namespace bpmj\wpidea\modules\webhooks\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\quiz\Interface_Resolved_Quiz_Repository;
use bpmj\wpidea\learning\quiz\Resolved_Quiz;
use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Sender;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Prepare_Urls;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\user\Interface_User_Repository;

class Quiz_Webhook_Handler implements Interface_Event_Handler
{

    private Interface_Events $events;
    private Interface_Webhook_Sender $webhook_sender;
    private Webhook_Prepare_Urls $webhook_prepare_urls;
    private Interface_Resolved_Quiz_Repository $quiz_repository;
    private Interface_User_Repository $user_repository;
    private Interface_Webhook_Factory $webhook_factory;

    public function __construct(
        Interface_Events $events,
        Interface_Webhook_Sender $webhook_sender,
        Webhook_Prepare_Urls $webhook_prepare_urls,
        Interface_Resolved_Quiz_Repository $quiz_repository,
        Interface_User_Repository $user_repository,
        Interface_Webhook_Factory $webhook_factory
    ) {
        $this->events = $events;
        $this->webhook_sender = $webhook_sender;
        $this->webhook_prepare_urls = $webhook_prepare_urls;
        $this->quiz_repository = $quiz_repository;
        $this->user_repository = $user_repository;
        $this->webhook_factory = $webhook_factory;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::QUIZ_FINISHED, [$this, 'send_quiz_details_webhook'], 10, 1);
    }

    public function send_quiz_details_webhook(int $inserted_quiz_id): void
    {
        $quiz = $this->quiz_repository->find_by_id($inserted_quiz_id);

        if (!$quiz) {
            return;
        }

        $data = $this->prepare_data($quiz);
        $webhook_urls = $this->webhook_prepare_urls->get_urls(Webhook_Types_Of_Events::QUIZ_FINISHED);

        foreach ($webhook_urls as $url) {
            $webhook = $this->webhook_factory->create(Webhook_Types_Of_Events::QUIZ_FINISHED, $url);

            $this->webhook_sender->send_data($webhook, $data);
        }
    }

    private function prepare_data(Resolved_Quiz $quiz): array
    {
        $user_id = $this->user_repository->find_by_email($quiz->get_user_email());

        $data = [
            'id' => $quiz->get_id(),
            'title' => $quiz->get_title(),
            'date' => $quiz->get_completed_at()->format('Y-m-d H:i:s'),
            'status' => $quiz->get_result(),
            'student' => [
                'id' => $user_id->get_id(),
                'first_name' => $quiz->get_user_first_name() ?? '',
                'last_name' => $quiz->get_user_last_name() ?? '',
                'email' => $quiz->get_user_email(),
            ]
        ];

        return $data;
    }
}

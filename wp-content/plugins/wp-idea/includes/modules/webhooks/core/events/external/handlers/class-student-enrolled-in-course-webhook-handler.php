<?php

namespace bpmj\wpidea\modules\webhooks\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\system\date\Interface_System_Datetime_Info;
use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\services\Interface_Webhook_Sender;
use bpmj\wpidea\modules\webhooks\core\services\Webhook_Prepare_Urls;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\user\User;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\translator\Interface_Translator;
use DateTimeImmutable;

class Student_Enrolled_In_Course_Webhook_Handler implements Interface_Event_Handler
{

    private Interface_Events $events;
    private Interface_Webhook_Sender $webhook_sender;
    private Webhook_Prepare_Urls $webhook_prepare_urls;
    private Interface_User_Repository $user_repository;
    private Interface_Webhook_Factory $webhook_factory;
    private Interface_Readable_Course_Repository $course_repository;
    private Interface_Translator $translator;
    private Interface_System_Datetime_Info $datetime_info;

    public function __construct(
        Interface_Events $events,
        Interface_Webhook_Sender $webhook_sender,
        Webhook_Prepare_Urls $webhook_prepare_urls,
        Interface_User_Repository $user_repository,
        Interface_Webhook_Factory $webhook_factory,
        Interface_Readable_Course_Repository $course_repository,
        Interface_Translator $translator,
        Interface_System_Datetime_Info $datetime_info
    ) {
        $this->events = $events;
        $this->webhook_sender = $webhook_sender;
        $this->webhook_prepare_urls = $webhook_prepare_urls;
        $this->user_repository = $user_repository;
        $this->webhook_factory = $webhook_factory;
        $this->course_repository = $course_repository;
        $this->translator = $translator;
        $this->datetime_info = $datetime_info;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::STUDENT_ENROLLED_IN_COURSE, [$this, 'send_student_enrolled_in_course_details_webhook'], 10, 2);
    }

    public function send_student_enrolled_in_course_details_webhook(int $course_id, int $user_id): void
    {
        try {
            $course = $this->course_repository->find_by_id(new Course_ID($course_id));
            $user = $this->user_repository->find_by_id(new User_ID($user_id));
        }
        catch(\Exception $e)
        {
            return;
        }


        if (!$course || !$user) {
            return;
        }

        $data = $this->prepare_data($course, $user);
        if (!$data) {
            return;
        }
        $webhook_urls = $this->webhook_prepare_urls->get_urls(Webhook_Types_Of_Events::STUDENT_ENROLLED_IN_COURSE);

        foreach ($webhook_urls as $url) {
            $webhook = $this->webhook_factory->create(Webhook_Types_Of_Events::STUDENT_ENROLLED_IN_COURSE, $url);

            $this->webhook_sender->send_data($webhook, $data);
        }

    }

    private function prepare_data(Course $course, User $user): array
    {
        $access = get_user_meta( $user->get_id()->to_int(), '_bpmj_eddpc_access', true );
        if (!is_array($access) || !array_key_exists($course->get_product_id()->to_int(), $access)) {
            return [];
        }

        $access_time = $access[$course->get_product_id()->to_int()]['access_time'];

        $data = [
            'course' =>  $course->get_title(),
            'full_name' => $user->full_name() ? $user->full_name()->get_full_name() : '',
            'email' => $user->get_email(),
            'until_when_access' => $access_time ? $this->get_local_date($access_time) : $this->translator->translate('no_limit'),
            'date_of_the_event' => $this->get_local_date()
        ];

        return $data;
    }

    private function get_local_date(?int $timestamp = null): string
    {
        $date = new DateTimeImmutable(
            date('Y-m-d H:i:s', $timestamp)
        );

        $local_date = $date->setTimezone(
            $this->datetime_info->get_current_timezone()
        );

        return $local_date->format('d.m.Y - H:i:s');
    }
}

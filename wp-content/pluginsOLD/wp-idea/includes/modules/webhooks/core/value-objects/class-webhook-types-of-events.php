<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\value_objects;

use Exception;

class Webhook_Types_Of_Events
{
    public const ORDER_PAID = 'order_paid';
    public const QUIZ_FINISHED = 'quiz_finished';
    public const CERTIFICATE_ISSUED = 'certificate_issued';
    public const STUDENT_ENROLLED_IN_COURSE = 'student_enrolled_in_course';
    public const COURSE_COMPLETED = 'course_completed';


    public const VALID_EVENT = [
        self::ORDER_PAID,
        self::QUIZ_FINISHED,
        self::CERTIFICATE_ISSUED,
        self::STUDENT_ENROLLED_IN_COURSE,
        self::COURSE_COMPLETED
    ];

    private string $type_of_event;

    public function __construct(string $type_of_event)
    {
        if(!in_array($type_of_event, self::VALID_EVENT, true)) {
            throw new Exception('Invalid webhook event provided!');
        }

        $this->type_of_event = $type_of_event;
    }

    public function equals(Webhook_Types_Of_Events $other_event): bool
    {
        return $this->get_value() === $other_event->get_value();
    }

    public function get_value(): string
    {
        return $this->type_of_event;
    }

}
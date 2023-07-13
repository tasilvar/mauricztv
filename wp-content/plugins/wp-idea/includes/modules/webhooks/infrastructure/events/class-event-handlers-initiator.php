<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\infrastructure\events;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\modules\webhooks\core\events\external\handlers\Order_Paid_Webhook_Handler;
use bpmj\wpidea\modules\webhooks\core\events\external\handlers\Quiz_Webhook_Handler;
use bpmj\wpidea\modules\webhooks\core\events\external\handlers\Certificate_Issued_Webhook_Handler;
use bpmj\wpidea\modules\webhooks\core\events\external\handlers\Student_Enrolled_In_Course_Webhook_Handler;
use bpmj\wpidea\modules\webhooks\core\events\external\handlers\Course_Completed_Webhook_Handler;


class Event_Handlers_Initiator
{
    public function __construct(
        Order_Paid_Webhook_Handler $order_paid_webhook_handler,
        Quiz_Webhook_Handler $quiz_webhook_events,
        Certificate_Issued_Webhook_Handler $certificate_issued_webhook_handler,
        Student_Enrolled_In_Course_Webhook_Handler $student_enrolled_in_course_webhook_handler,
        Course_Completed_Webhook_Handler $course_completed_webhook_events
    ) {
        $this->init_handlers([
            $order_paid_webhook_handler,
            $quiz_webhook_events,
            $certificate_issued_webhook_handler,
            $student_enrolled_in_course_webhook_handler,
            $course_completed_webhook_events
        ]);
    }

    private function init_handler(Interface_Event_Handler $handler): void
    {
        $handler->init();
    }

    private function init_handlers(array $handlers): void
    {
        foreach ($handlers as $handler) {
            $this->init_handler($handler);
        }
    }
}
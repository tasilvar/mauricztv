<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\events;

use bpmj\wpidea\events\handlers\Auth_Events_Handler;
use bpmj\wpidea\events\handlers\Check_Available_Features_Handler;
use bpmj\wpidea\events\handlers\Check_License_Data_Event_Handler;
use bpmj\wpidea\events\handlers\Debug_Events_Handler;
use bpmj\wpidea\events\handlers\Invalid_Response_Webhook_Events_Handler;
use bpmj\wpidea\events\handlers\Invoice_Events_Handler;
use bpmj\wpidea\events\handlers\Log_Webhook_Events_Handler;
use bpmj\wpidea\events\handlers\Mail_Events_Handler;
use bpmj\wpidea\events\handlers\Order_Events_Handler;
use bpmj\wpidea\events\handlers\Update_Payment_Event_Handler;
use bpmj\wpidea\events\handlers\User_Events_Handler;
use bpmj\wpidea\learning\quiz\events\Cached_Not_Rated_Quizzes_Counter_Events_Handler;
use bpmj\wpidea\sales\price_history\core\event\{Price_Deleted_Events_Handler, Price_Updated_Events_Handler};


class Event_Handlers_Initiator
{
    public function __construct(
        Check_Available_Features_Handler $check_available_features_handler,
        Check_License_Data_Event_Handler $check_license_data_event_handler,
        Auth_Events_Handler $auth_events_handler,
        Debug_Events_Handler $debug_events_handler,
        Order_Events_Handler $order_events_handler,
        Invoice_Events_Handler $invoice_events_handler,
        Mail_Events_Handler $mail_events_handler,
        User_Events_Handler $user_events_handler,
        Invalid_Response_Webhook_Events_Handler $invalid_response_webhook_events,
        Log_Webhook_Events_Handler $log_webhook_events,
        Update_Payment_Event_Handler $update_payment_event_handler,
        Cached_Not_Rated_Quizzes_Counter_Events_Handler $cached_not_rated_quizzes_counter_events_handler,
        Price_Updated_Events_Handler $price_updated_events_handler,
        Price_Deleted_Events_Handler $price_deleted_events_handler
    ) {
        $this->init_handlers([
            $check_available_features_handler,
            $check_license_data_event_handler,
            $auth_events_handler,
            $debug_events_handler,
            $order_events_handler,
            $invoice_events_handler,
            $mail_events_handler,
            $user_events_handler,
            $invalid_response_webhook_events,
            $log_webhook_events,
            $update_payment_event_handler,
            $cached_not_rated_quizzes_counter_events_handler,
            $price_updated_events_handler,
            $price_deleted_events_handler
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
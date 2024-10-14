<?php namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Update_Payment_Event_Handler implements Interface_Event_Handler
{

    private Interface_Events $events;

    private Interface_Settings $settings;

    private Interface_Orders_Repository $orders_repository;

    public function __construct(
        Interface_Events $events,
        Interface_Settings $settings,
        Interface_Orders_Repository $orders_repository
    ) {
        $this->events            = $events;
        $this->settings          = $settings;
        $this->orders_repository = $orders_repository;
    }

    public function init(): void
    {
        $this->events->on(
            Event_Name::PAYMENT_UPDATED,
            [$this, 'handle_payment_updated_event'],
            10,
            3
        );
    }

    public function handle_payment_updated_event($payment_id, $status, $old_status)
    {
        /*
         * Send an order notification to the staff but only if admin notices are disabled. Otherwise there is no need
         * to duplicate the information
         */
        $admin_notices_disabled    = edd_admin_notices_disabled($payment_id);
        $order_notification_policy = $this->settings->get('bpmj_eddcm_admin_notice_policy', 'disabled');
        $is_published              = 'publish' === $status;
        $send_all_messages         = 'all' === $order_notification_policy;
        $send_only_with_comments   = 'comments' === $order_notification_policy;

        if (
            $is_published &&
            $admin_notices_disabled &&
            ($send_all_messages || $send_only_with_comments && $this->is_payment_with_comment($payment_id))
        ) {
            edd_admin_email_notice($payment_id, edd_get_payment_meta($payment_id));
        }
    }

    private function is_payment_with_comment(int $payment_id): bool
    {
        $order = $this->orders_repository->find_by_id($payment_id);

        if ( ! $order) {
            return false;
        }

        return strlen($order->get_additional_fields()->get_order_comment()) > 0;
    }


}
<?php

namespace bpmj\wpidea\modules\payment_reminders\core\jobs;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\scheduler\Interface_Schedulable;
use bpmj\wpidea\modules\payment_reminders\core\services\Options;
use bpmj\wpidea\modules\payment_reminders\core\services\Payment_Reminder_Email_Sender;
use bpmj\wpidea\modules\payment_reminders\core\value_objects\email\Payment_Reminder_Email;
use bpmj\wpidea\modules\payment_reminders\core\value_objects\email\Payment_Reminder_Email_Product_Row;
use bpmj\wpidea\modules\payment_reminders\core\value_objects\email\Payment_Reminder_Email_Product_Row_Collection;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;
use bpmj\wpidea\sales\order\Order;
use bpmj\wpidea\sales\order\Order_Collection;
use bpmj\wpidea\sales\order\Order_Query_Criteria;
use DateInterval;
use DateTime;
use DateTimeInterface;

class Send_Reminders_Job implements Interface_Schedulable
{
    private const DEFAULT_PAYMENT_REMINDERS_NUMBER_DAYS = 3;
    private const PAYMENT_REMINDERS_MARK = 'payment-reminders-mark';
    private const TPAY_PAYMENT_SUBTYPE = '_tpay_payment_subtype';
    private const PAYU_PAYMENT_SUBTYPE = '_payu_payment_subtype';

    private Options $options;
    private Interface_Orders_Repository $payment_repository;
    private Payment_Reminder_Email_Sender $payment_reminder_email_sender;

    public function __construct(
        Options $options,
        Interface_Orders_Repository $payment_repository,
        Payment_Reminder_Email_Sender $payment_reminder_email_sender
    ) {
        $this->options = $options;
        $this->payment_repository = $payment_repository;
        $this->payment_reminder_email_sender = $payment_reminder_email_sender;
    }

    public function get_method_to_run(): callable
    {
        return [$this, 'run'];
    }

    public function get_first_run_time(): DateTime
    {
        return new DateTime();
    }

    public function get_interval(): DateInterval
    {
        return new DateInterval(self::INTERVAL_1HOUR);
    }

    public function get_args(): array
    {
        return [];
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $orders = $this->get_pending_orders_ready_for_reminder();

        foreach ($orders as $order) {
            if($this->is_recurring_payment($order)) {
                continue;
            }
            $is_marked_as_already_sent = $this->check_if_reminder_was_sent_for_the_order($order);
            if (!$is_marked_as_already_sent) {
                $this->send_reminder($order);
                $this->mark_as_reminder_sent_for_the_order($order);
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function send_reminder(Order $order): void
    {
        $client = $order->get_client();
        $cart = $order->get_cart_content();
        $products = new Payment_Reminder_Email_Product_Row_Collection();
        foreach ($cart->get_item_names() as $item_name) {
            $products->add(new Payment_Reminder_Email_Product_Row($item_name));
        }

        $email = new Payment_Reminder_Email(
            new Email_Address($client->get_email()),
            new Full_Name($client->get_first_name(), $client->get_last_name()),
            $products,
            $order->get_total() . ' ' . $order->get_currency(),
            $order->get_id(),
            $order->get_date()
        );

        $this->payment_reminder_email_sender->send($email);
    }

    private function get_pending_orders_ready_for_reminder(): Order_Collection
    {
        $days_back = $this->options->get(
            Settings_Const::PAYMENT_REMINDERS_NUMBER_DAYS 
        ) ?: self::DEFAULT_PAYMENT_REMINDERS_NUMBER_DAYS;
        $start_date = date(DateTimeInterface::ATOM, strtotime('-' . ($days_back+1) . ' day'));
        $end_date = date(DateTimeInterface::ATOM, strtotime('-' . ($days_back) . ' day'));

        $criteria_array = [
            'perPage' => -1,
            'page' => 1,
            'filters' => [
                [
                    'id' => 'status',
                    'value' => 'pending'
                ],
                [
                    'id' => 'date',
                    'value' => [
                        'startDate' => $start_date,
                        'endDate' => $end_date
                    ]
                ]
            ],
            'sortBy' => new Sort_By_Clause()
        ];

        $criteria = new Order_Query_Criteria($criteria_array);

        return $this->payment_repository->find_by_criteria($criteria);
    }

    private function check_if_reminder_was_sent_for_the_order($order): bool
    {
        return '1' === $this->payment_repository->get_meta($order, self::PAYMENT_REMINDERS_MARK);
    }

    private function mark_as_reminder_sent_for_the_order($order): void
    {
        $this->payment_repository->store_meta($order, self::PAYMENT_REMINDERS_MARK, '1');
    }

    private function is_recurring_payment($order): bool
    {
        $tpay = $this->payment_repository->get_meta($order, self::TPAY_PAYMENT_SUBTYPE);
        $payu = $this->payment_repository->get_meta($order, self::PAYU_PAYMENT_SUBTYPE);
        return $tpay === 'recurrent' || $payu === 'recurrent';
    }
}
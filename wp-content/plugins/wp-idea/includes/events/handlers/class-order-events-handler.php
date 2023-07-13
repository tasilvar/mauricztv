<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use Psr\Log\LoggerInterface;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\infrastructure\logs\model\Log_Source;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Order_Events_Handler implements Interface_Event_Handler
{
    private $logger;
    private $translator;
    private $events;

    public function __construct(LoggerInterface $logger, Interface_Translator $translator,  Interface_Events $events, Interface_Orders_Repository $order_repository)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->events = $events;
        $this->order_repository = $order_repository;
    }

    public function init(): void
    {
        $this->events->on( Event_Name::ORDER_CREATED,  [$this, 'on_order_creation'], 10, 1 );
        $this->events->on( Event_Name::ORDER_COMPLETED ,  [$this, 'on_complete_purchase'], 10, 1 );
    }

    public function on_order_creation( int $payment_id): void
    {
        $order_data_array = $this->get_order_data_as_array($payment_id);
        if (!$order_data_array) {
            return;
        }

        $this->logger->info(
            $this->translator->translate('logs.log_message.order_created'), $order_data_array
        );

    }

    public function on_complete_purchase( int $payment_id): void
    {
        $order_data_array = $this->get_order_data_as_array($payment_id);
        if (!$order_data_array) {
            return;
        }

        $this->logger->info(
            $this->translator->translate('logs.log_message.order_completed'), $order_data_array
        );
    }

    private function get_order_data_as_array(int $payment_id): ?array
    {
        $order = $this->order_repository->find_by_id($payment_id);
        if(!$order){
           return null;
        }
        return [
            'order_id' => $payment_id,
            'email' => $order->get_client()->get_email(),
            'amount' => $order->get_subtotal().' '.$order->get_currency(),
            'source' => Log_Source::ORDERS
        ];
    }
}

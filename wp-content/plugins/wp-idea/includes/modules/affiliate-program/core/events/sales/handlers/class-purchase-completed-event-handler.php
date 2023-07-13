<?php

namespace bpmj\wpidea\modules\affiliate_program\core\events\sales\handlers;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\affiliate_program\core\services\Commission_Collector;

class Purchase_Completed_Event_Handler implements Interface_Event_Handler
{
    private const PURCHASE_COMPLETED_HOOK = 'edd_complete_purchase';

    private Interface_Events $events;
    private Commission_Collector $commission_collector;

    public function __construct(
        Interface_Events $events,
        Commission_Collector $commission_collector
    ) {
        $this->events = $events;
        $this->commission_collector = $commission_collector;
    }

    public function init(): void
    {
        $this->events->on(self::PURCHASE_COMPLETED_HOOK, [$this, 'collect_commission']);
    }

    public function collect_commission(int $payment_id): void
    {
        $this->commission_collector->collect_for_order($payment_id);
    }
}

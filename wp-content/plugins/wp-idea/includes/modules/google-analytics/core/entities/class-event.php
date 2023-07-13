<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\google_analytics\core\entities;

use bpmj\wpidea\modules\google_analytics\core\collections\Event_Item_Collection;

class Event
{
    private string $event_name;
    private ?string $transaction_id;
    private string $currency;
    private float $value;
    private Event_Item_Collection $items;

    public function __construct(
        string $event_name,
        ?string $transaction_id,
        string $currency,
        float $value,
        Event_Item_Collection $items
    ) {
        $this->event_name = $event_name;
        $this->transaction_id = $transaction_id;
        $this->currency = $currency;
        $this->value = $value;
        $this->items = $items;
    }

    public function get_event_name(): string
    {
        return $this->event_name;
    }

    public function get_transaction_id(): ?string
    {
        return $this->transaction_id;
    }

    public function get_currency(): string
    {
        return $this->currency;
    }

    public function get_value(): float
    {
        return $this->value;
    }

    public function get_items(): Event_Item_Collection
    {
        return $this->items;
    }
}

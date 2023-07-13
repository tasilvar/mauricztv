<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\entities;

use Iterator;

class Webhook_Collection implements Iterator
{
    private array $webhooks = [];
    private int $current_position = 0;

    public function add($webhook): Webhook_Collection
    {
        $this->webhooks[] = $webhook;

        return $this;
    }

    public function current(): Webhook
    {
        return $this->webhooks[$this->current_position];
    }

    public function next(): void
    {
        ++$this->current_position;
    }

    public function key(): int
    {
        return $this->current_position;
    }

    public function valid(): bool
    {
        return isset($this->webhooks[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function get_first(): ?Webhook
    {
        return $this->webhooks[array_key_first($this->webhooks)] ?? null;
    }
}

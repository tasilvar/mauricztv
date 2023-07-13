<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use Iterator;

class Commission_Collection implements Iterator
{
    private array $commission = [];
    private int $current_position = 0;

    public function add($commission): Commission_Collection
    {
        $this->commission[] = $commission;

        return $this;
    }

    public function current(): Commission
    {
        return $this->commission[$this->current_position];
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
        return isset($this->commission[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function get_first(): ?Commission
    {
        return $this->commission[array_key_first($this->commission)] ?? null;
    }
}

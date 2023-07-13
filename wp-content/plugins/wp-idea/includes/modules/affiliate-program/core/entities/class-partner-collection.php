<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use Iterator;

class Partner_Collection implements Iterator
{
    private array $partners = [];
    private int $current_position = 0;

    public function add(Partner $partner): self
    {
        $this->partners[] = $partner;

        return $this;
    }

    public function current(): Partner
    {
        return $this->partners[$this->current_position];
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
        return isset($this->partners[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function get_first(): ?Partner
    {
        return $this->partners[array_key_first($this->partners)] ?? null;
    }
}

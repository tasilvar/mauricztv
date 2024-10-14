<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\api\dto;

use Iterator;

class Commission_DTO_Collection implements Iterator
{
    private array $commission = [];
    private int $current_position = 0;

    public function add($commission): Commission_DTO_Collection
    {
        $this->commission[] = $commission;

        return $this;
    }

    public function current(): Commission_DTO
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
}

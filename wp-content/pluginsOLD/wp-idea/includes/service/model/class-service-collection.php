<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\model;

use Iterator;

class Service_Collection implements Iterator
{
    private array $services = [];

    private int $current_position = 0;

    public function add(Service $service): self
    {
        $this->services[] = $service;

        return $this;
    }

    public function current(): Service
    {
        return $this->services[$this->current_position];
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
        return isset($this->services[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function first(): ?Service
    {
        return $this->services[0] ?? null;
    }
}

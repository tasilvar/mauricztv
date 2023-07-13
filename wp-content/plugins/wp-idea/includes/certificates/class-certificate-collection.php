<?php namespace bpmj\wpidea\certificates;

use \Iterator;

class Certificate_Collection implements Iterator
{

    private int $current_position = 0;

    private array $certificates = [];

    public function add(Interface_Certificate $certificate): self
    {
        $this->certificates[] = $certificate;

        return $this;
    }

    public function remove(Interface_Certificate $certificate_to_remove): bool
    {
        $result = false;
        foreach ($this->certificates as $key => $user) {
            if ($user->get_id() === $certificate_to_remove->get_id()) {
                unset($this->certificates[$key]);
                $result = true;
            }
        }

        return $result;
    }

    public function size(): int
    {
        return sizeof($this->certificates);
    }

    public function get_first(): Interface_Certificate
    {
        return $this->certificates[array_key_first($this->certificates)];
    }

    public function current(): Interface_Certificate
    {
        return $this->certificates[$this->current_position];
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
        return isset($this->certificates[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}

<?php namespace bpmj\wpidea\certificates;

use \Iterator;

class Certificate_Display_Model_Collection implements Iterator
{

    private int $current_position = 0;

    private array $models = [];

    public function add(Certificate_Display_Model $model): self
    {
        $this->models[] = $model;

        return $this;
    }

    public function remove(Certificate_Display_Model $model_to_remove): bool
    {
        $result = false;
        foreach ($this->models as $key => $model) {
            if ($model->get_id() === $model_to_remove->get_id()) {
                unset($this->models[$key]);
                $result = true;
            }
        }

        return $result;
    }

    public function size(): int
    {
        return sizeof($this->models);
    }

    public function get_first(): Certificate_Display_Model
    {
        return $this->models[array_key_first($this->models)];
    }

    public function current(): Certificate_Display_Model
    {
        return $this->models[$this->current_position];
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
        return isset($this->models[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}

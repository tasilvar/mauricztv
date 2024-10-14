<?php

namespace bpmj\wpidea\modules\videos\core\entities;

use Iterator;
use Countable;

class Video_Collection implements Iterator, Countable
{
    private array $videos = [];
    private int $current_position = 0;

    public function count(): int
    {
        return count($this->videos);
    }

    public function add(Video $video): self
    {
        $this->videos[] = $video;

        return $this;
    }

    public function current(): Video
    {
        return $this->videos[$this->current_position];
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
        return isset($this->videos[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function get_first(): ?Video
    {
        return $this->videos[array_key_first($this->videos)] ?? null;
    }
}

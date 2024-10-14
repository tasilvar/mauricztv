<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\database;

class Sort_By_Clause
{
    private $sort_by = [];

    public function sort_by(string $property, bool $desc = false): self
    {
        $this->sort_by[$property] = new Sort_By($property, $desc);

        return $this;
    }

    public function reset(): void
    {
        $this->sort_by = [];
    }

    public function get_all(): array
    {
        return array_values($this->sort_by);
    }

    public function get(string $property): ?Sort_By
    {
        return $this->sort_by[$property] ?? null;
    }
    
    public function get_first(): ?Sort_By
    {
        return $this->get_all()[0] ?? null;
    }

    public function remove(string $property): void
    {
        unset($this->sort_by[$property]);
    }
}
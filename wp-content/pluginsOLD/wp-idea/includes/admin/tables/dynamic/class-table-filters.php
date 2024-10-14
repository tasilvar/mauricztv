<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic;

class Table_Filters
{
    private $filters = [];

    /**
     * @param mixed $value
     */
    public function add_simple_filter(string $name, $value): void
    {
        $this->filters[$name] = [
            'id' => $name,
            'value' => $value
        ];
    }

    public function get_all(): array
    {
        return $this->filters;
    }
}
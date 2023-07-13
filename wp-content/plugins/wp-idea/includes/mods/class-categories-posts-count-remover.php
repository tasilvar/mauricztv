<?php

namespace bpmj\wpidea\mods;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\filters\Interface_Filters;

class Categories_Posts_Count_Remover implements Interface_Initiable
{
    private $filters;

    public function __construct(Interface_Filters $filters)
    {
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->filters->add('manage_edit-download_category_columns', [$this, 'remove_count_column']);
    }

    public function remove_count_column(array $columns): array
    {
        unset($columns['posts']);
        return $columns;
    }
}
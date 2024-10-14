<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic;

use bpmj\wpidea\Current_Request;

class Url_Filters_Parser
{
    private $current_request;

    public function __construct(
        Current_Request $current_request
    )
    {
        $this->current_request = $current_request;
    }

    public function get_filters(): Table_Filters
    {
        $filters = new Table_Filters();

        foreach ($this->get_simple_filters_from_query() as $filter_name => $filter_value) {
            $filters->add_simple_filter($filter_name, $filter_value);
        }

        return $filters;
    }

    private function get_simple_filters_from_query(): array
    {
        $filters = [];

        $query_param_matches = array_filter($this->current_request->get_query_args(), function ($value, $key) {
            return strpos($key, 'filter_') !== false
                && strpos($key, '_from') === false
                && strpos($key, '_to') === false;
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($query_param_matches as $key => $value) {
            $filter_name = str_replace('filter_', '', $key);

            $filters[$filter_name] = $value;
        }

        return $filters;
    }
}
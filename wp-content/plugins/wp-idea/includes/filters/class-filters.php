<?php

namespace bpmj\wpidea\filters;

class Filters
{
    public function register(Interface_Filter $filter): void
    {
        if(!$filter->is_valid()) {
            return;
        }

        $this->add_filter($filter);
    }

    protected function add_filter(Interface_Filter $filter): void
    {
        add_filter( $filter->get_tag(), $filter->get_function(), $filter->get_priority(), $filter->get_accepted_args() );
    }

    public function is_registred(Interface_Filter  $filter): bool
    {
        return has_filter( $filter->get_tag(), $filter->get_function() );
    }
}
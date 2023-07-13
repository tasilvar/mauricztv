<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\filters;

interface Interface_Filter
{
    public function get_tag(): string;
    public function get_function(): callable;
    public function get_priority(): int;
    public function get_accepted_args(): int;
    public function is_valid(): bool;
}
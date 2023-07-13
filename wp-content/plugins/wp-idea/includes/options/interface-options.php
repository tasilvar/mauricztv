<?php
/**
 * This file is licensed under proprietary license
 */

namespace bpmj\wpidea\options;

interface Interface_Options
{
    public function get(string $option_name);
    public function set(string $option_name, $value): bool;
    public function delete(string $option_name): bool;
}

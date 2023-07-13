<?php
/**
 * This file is licensed under proprietary license
 */

namespace bpmj\wpidea\settings;

interface Interface_Settings
{
    public function get(string $option_name);

    public function set(string $option_name, $value): bool;
}

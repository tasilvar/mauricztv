<?php namespace bpmj\wpidea\helpers;

interface Interface_Debug_Helper
{
    public function is_dev_mode_enabled(): bool;

    public function in_debug_mode(): bool;
}

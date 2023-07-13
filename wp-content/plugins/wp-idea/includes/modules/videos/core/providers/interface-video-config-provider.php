<?php

namespace bpmj\wpidea\modules\videos\core\providers;

interface Interface_Video_Config_Provider
{
    public function get_configuration(): array;

    public function is_set(): bool;
    
}
<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\settings\core\services;

interface Interface_Active_Settings_Groups_Provider
{
    public function get_groups(): array;
}
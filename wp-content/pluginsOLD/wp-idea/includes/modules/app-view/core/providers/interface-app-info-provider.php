<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\app_view\core\providers;

interface Interface_App_Info_Provider
{
    public function get_app_name(): string;
}
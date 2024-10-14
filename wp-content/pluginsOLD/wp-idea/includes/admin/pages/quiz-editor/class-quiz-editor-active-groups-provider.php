<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quiz_editor;

use bpmj\wpidea\admin\pages\quiz_editor\core\configuration\{Quiz_Files_Group, Quiz_Structure_Group, General_Quiz_Group};
use bpmj\wpidea\admin\settings\core\services\Interface_Active_Settings_Groups_Provider;

class Quiz_Editor_Active_Groups_Provider implements Interface_Active_Settings_Groups_Provider
{
    public function get_groups(): array
    {
        return [
            General_Quiz_Group::class,
            Quiz_Structure_Group::class,
            Quiz_Files_Group::class,
        ];
    }
}
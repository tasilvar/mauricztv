<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\api;

use bpmj\wpidea\modules\conflicting_plugins_detector\core\services\Conflicting_Plugins_Detector;

class Conflicting_Plugins_API
{
    private Conflicting_Plugins_Detector $conflicting_plugins_detector;

    public function __construct(
        Conflicting_Plugins_Detector $conflicting_plugins_detector
    )
    {
        $this->conflicting_plugins_detector = $conflicting_plugins_detector;
    }

    public function get_active_conflicting_plugins_names(): array
    {
        return $this->conflicting_plugins_detector->get_active_conflicting_plugins_name_list();
    }
}
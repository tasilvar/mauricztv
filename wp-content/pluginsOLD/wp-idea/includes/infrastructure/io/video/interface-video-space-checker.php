<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\io\video;

interface Interface_Video_Space_Checker
{
    public function get_usage_info(): Video_Space_Usage_Info;
}
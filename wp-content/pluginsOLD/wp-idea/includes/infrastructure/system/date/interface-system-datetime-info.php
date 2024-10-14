<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\system\date;

interface Interface_System_Datetime_Info
{
    public function get_current_timezone(): \DateTimeZone;
}
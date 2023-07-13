<?php
declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\system\date;

class System_Datetime_Info implements Interface_System_Datetime_Info
{
    public function get_current_timezone(): \DateTimeZone
    {
        return wp_timezone();
    }
}
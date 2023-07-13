<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\admin\helpers;

use DateTime;

class Date_Helper
{

    public static function is_date_in_the_past(DateTime $date): bool
    {
        $today = new DateTime();
        $interval = $today->diff($date);

        return ($interval->invert) ? true : false;
    }

}

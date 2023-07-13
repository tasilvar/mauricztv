<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\scheduler;

use DateTime;
use DateInterval;

interface Interface_Schedulable
{
    public const INTERVAL_1DAY = 'P1D';
    public const INTERVAL_1HOUR = 'PT1H';
    public const INTERVAL_1MINUTE = 'PT1M';

    public function get_method_to_run(): callable;
    public function get_first_run_time(): DateTime;
    public function get_interval(): DateInterval;
    public function get_args(): array;
}
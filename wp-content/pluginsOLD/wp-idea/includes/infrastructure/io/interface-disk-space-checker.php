<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\io;

interface Interface_Disk_Space_Checker
{
    public function get_used_percentage(): string;

    public function get_used(): int;

    public function get_max(): int;
}

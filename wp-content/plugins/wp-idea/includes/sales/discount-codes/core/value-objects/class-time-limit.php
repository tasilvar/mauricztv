<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\value_objects;

use DateTimeImmutable;

class Time_Limit
{
    private ?DateTimeImmutable $start_date;
    private ?DateTimeImmutable $end_date;

    public function __construct(
        ?DateTimeImmutable $start_date,
        ?DateTimeImmutable $end_date
    ) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function get_start_date(): ?DateTimeImmutable
    {
        return $this->start_date;
    }

    public function get_end_date(): ?DateTimeImmutable
    {
        return $this->end_date;
    }
}
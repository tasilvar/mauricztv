<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\value_objects;

class Max_Uses
{
    private int $max_uses;

    public function __construct(
        int $max_uses
    ) {
        $this->max_uses = $max_uses;
    }

    public function get(): int
    {
        return $this->max_uses;
    }
}
<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\value_objects;

class Uses
{
    private int $uses;

    public function __construct(
        int $uses
    ) {
        $this->uses = $uses;
    }

    public function get(): int
    {
        return $this->uses;
    }
}
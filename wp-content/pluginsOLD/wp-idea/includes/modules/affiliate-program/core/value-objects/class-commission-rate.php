<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\value_objects;

use bpmj\wpidea\modules\affiliate_program\core\exceptions\Invalid_Commission_Rate_Exception;

class Commission_Rate
{

    private int $commission_percentage;

    public function __construct(
        int $commission_percentage
    )
    {
        if($commission_percentage < 0) {
            throw new Invalid_Commission_Rate_Exception('Commission rate cannot be lower than 0.');
        }

        $this->commission_percentage = $commission_percentage;
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function get(): int
    {
        return $this->commission_percentage;
    }
}
<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\order;

use bpmj\wpidea\data_types\String_VO;

class Discount_Code extends String_VO
{
    public static function from_string($discount_code): self
    {
        return new self($discount_code);
    }
}
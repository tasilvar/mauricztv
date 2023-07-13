<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\dto;

class Purchase_Limit_DTO
{
    public ?int $limit = null;

    public ?int $items_left = null;

    public bool $unlimited = false;
}
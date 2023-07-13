<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\dto;

class Digital_Product_DTO
{
    public ?int $id = null;

    public string $name;

    public ?array $included_files = null;
}
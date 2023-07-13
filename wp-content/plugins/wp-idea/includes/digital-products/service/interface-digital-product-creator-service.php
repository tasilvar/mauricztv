<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\service;

use bpmj\wpidea\digital_products\dto\Digital_Product_DTO;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;

interface Interface_Digital_Product_Creator_Service
{
    public function save_digital_product(Digital_Product_DTO $dto): Digital_Product_ID;
}
<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\service;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;

interface Interface_Url_Resolver
{
    public function get_by_digital_product_id(Digital_Product_ID $id): ?Url;
}
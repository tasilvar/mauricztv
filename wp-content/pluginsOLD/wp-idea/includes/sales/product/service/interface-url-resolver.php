<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\service;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\sales\product\model\Product_ID;

interface Interface_Url_Resolver
{
    public function get_by_product_id(Product_ID $id): ?Url;
}
<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\repository;

use bpmj\wpidea\physical_product\model\Physical_Product;
use bpmj\wpidea\physical_product\model\Physical_Product_Collection;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;

interface Interface_Physical_Product_Repository
{
    public function save(Physical_Product $product): Physical_Product_ID;

    public function find(Physical_Product_ID $id): ?Physical_Product;

    public function find_all(): Physical_Product_Collection;

    public function count_all(): int;
}

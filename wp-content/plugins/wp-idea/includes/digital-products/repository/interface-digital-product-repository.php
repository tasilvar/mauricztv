<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\repository;

use bpmj\wpidea\digital_products\model\Digital_Product;
use bpmj\wpidea\digital_products\model\Digital_Product_Collection;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;

interface Interface_Digital_Product_Repository
{
    public function save(Digital_Product $product): Digital_Product_ID;

    public function find(Digital_Product_ID $id): ?Digital_Product;

    public function find_all(): Digital_Product_Collection;

    public function count_all(): int;
}

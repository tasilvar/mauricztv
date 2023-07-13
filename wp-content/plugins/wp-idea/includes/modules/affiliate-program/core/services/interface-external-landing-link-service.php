<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\services;

use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\{External_Link_ID, External_Url, Product_ID};
use bpmj\wpidea\modules\affiliate_program\core\entities\External_Landing_Link_Collection;

interface Interface_External_Landing_Link_Service
{
    public function add(Product_ID $product_id, External_Url $url): void;

    public function find_all(): External_Landing_Link_Collection;

    public function find_by_id(External_Link_ID $id): ?External_Landing_Link;

    public function find_first_with_matching_url(string $landing_url): ?External_Landing_Link;

    public function update(External_Link_ID $id, Product_ID $product_id, External_Url $url): void;

    public function delete(External_Link_ID $id): void;
}
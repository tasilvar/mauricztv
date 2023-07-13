<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\persistence;

use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO_Collection;

interface Interface_Physical_Product_Persistence
{
    public function find_by_id(int $id): ?Physical_Product_DTO;

    public function find_all(): Physical_Product_DTO_Collection;

    public function save_or_update_physical_product(?int $id, string $name): int;

    public function count_all(): int;
}

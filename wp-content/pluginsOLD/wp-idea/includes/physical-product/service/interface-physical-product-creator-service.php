<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\service;

use bpmj\wpidea\service\dto\Service_DTO;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;

interface Interface_Physical_Product_Creator_Service
{
    public function save_physical_product(Physical_Product_DTO $dto): Physical_Product_ID;
}

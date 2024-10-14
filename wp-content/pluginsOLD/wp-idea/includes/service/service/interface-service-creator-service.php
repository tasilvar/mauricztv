<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\service;

use bpmj\wpidea\service\dto\Service_DTO;
use bpmj\wpidea\service\model\Service_ID;

interface Interface_Service_Creator_Service
{
    public function save_service(Service_DTO $dto): Service_ID;
}

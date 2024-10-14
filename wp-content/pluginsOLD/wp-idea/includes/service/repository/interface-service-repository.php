<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\repository;

use bpmj\wpidea\service\model\Service;
use bpmj\wpidea\service\model\Service_Collection;
use bpmj\wpidea\service\model\Service_ID;

interface Interface_Service_Repository
{
    public function save(Service $service): Service_ID;

    public function find(Service_ID $id): ?Service;

    public function find_all(): Service_Collection;

    public function count_all(): int;
}

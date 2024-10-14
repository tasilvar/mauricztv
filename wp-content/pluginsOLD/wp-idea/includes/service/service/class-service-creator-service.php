<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\service;

use bpmj\wpidea\service\dto\Service_DTO;
use bpmj\wpidea\service\model\Service;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\service\model\Service_Name;
use bpmj\wpidea\service\repository\Interface_Service_Repository;

class Service_Creator_Service implements Interface_Service_Creator_Service
{
    private Interface_Service_Repository $service_repository;

    public function __construct(
        Interface_Service_Repository $service_repository
    ) {
        $this->service_repository = $service_repository;
    }

    public function save_service(Service_DTO $dto): Service_ID
    {
        $model = $this->create_model($dto);

        return $this->service_repository->save($model);
    }

    private function create_model(Service_DTO $dto): Service
    {
        return Service::create(
            $dto->id ? new Service_ID($dto->id) : null,
            new Service_Name($dto->name)
        );
    }
}

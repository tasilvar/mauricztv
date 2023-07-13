<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\service;

use bpmj\wpidea\service\dto\Service_DTO;
use bpmj\wpidea\service\model\Service;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\service\model\Service_Name;
use bpmj\wpidea\service\repository\Interface_Service_Repository;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;
use bpmj\wpidea\physical_product\repository\Interface_Physical_Product_Repository;
use bpmj\wpidea\physical_product\model\Physical_Product;
use bpmj\wpidea\physical_product\model\Physical_Product_Name;

class Physical_Product_Creator_Service implements Interface_Physical_Product_Creator_Service
{
    private Interface_Physical_Product_Repository $repository;

    public function __construct(
        Interface_Physical_Product_Repository $repository
    ) {
        $this->repository = $repository;
    }

    public function save_physical_product(Physical_Product_DTO $dto): Physical_Product_ID
    {
        $model = $this->create_model($dto);

        return $this->repository->save($model);
    }

    private function create_model(Physical_Product_DTO $dto): Physical_Product
    {
        return Physical_Product::create(
            $dto->id ? new Physical_Product_ID($dto->id) : null,
            new Physical_Product_Name($dto->name)
        );
    }
}

<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\service;

use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\digital_products\dto\Digital_Product_DTO;
use bpmj\wpidea\digital_products\dto\Included_File_DTO;
use bpmj\wpidea\digital_products\dto\Included_File_DTO_Collection;
use bpmj\wpidea\digital_products\model\Digital_Product;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\digital_products\model\Digital_Product_Name;
use bpmj\wpidea\digital_products\model\Included_File;
use bpmj\wpidea\digital_products\model\Included_File_Collection;

class Digital_Product_Creator_Service implements Interface_Digital_Product_Creator_Service
{
    private Interface_Digital_Product_Repository $digital_product_repository;

    public function __construct(
        Interface_Digital_Product_Repository $digital_product_repository
    )
    {
        $this->digital_product_repository = $digital_product_repository;
    }

    public function save_digital_product(Digital_Product_DTO $dto): Digital_Product_ID
    {
        $model = $this->create_model($dto);

        return $this->digital_product_repository->save($model);
    }

    private function create_model(Digital_Product_DTO $dto): Digital_Product
    {
        $collection = $this->create_included_file_collection($dto->included_files ?? []);

        return Digital_Product::create(
            $dto->id ? new Digital_Product_ID($dto->id) : null,
            new Digital_Product_Name($dto->name),
            $collection
        );
    }

    private function create_included_file_collection(array $files_array): Included_File_Collection
    {
        $file_collection = new Included_File_Collection();

        foreach ($files_array as $file) {
            $file_collection->append(
                new Included_File(
                    null,
                    $file['file_name'],
                    $file['file_url']
                )
            );
        }

        return $file_collection;
    }
}
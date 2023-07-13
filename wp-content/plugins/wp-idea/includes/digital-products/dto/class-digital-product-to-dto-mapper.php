<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\dto;

use bpmj\wpidea\digital_products\model\Digital_Product;
use bpmj\wpidea\digital_products\model\Included_File_Collection;
use bpmj\wpidea\digital_products\model\Included_File;

class Digital_Product_To_Dto_Mapper
{
    public function map_digital_product_to_dto(Digital_Product $digital_product): Digital_Product_DTO
    {
        $dto = new Digital_Product_DTO();
        $dto->included_files = $this->get_files_array_from_collection($digital_product->get_included_files());
        $dto->id = $digital_product->get_id()->to_int();
        $dto->name = $digital_product->get_name()->get_value();

        return $dto;
    }

    private function get_files_array_from_collection(
        Included_File_Collection $collection
    ): array
    {
        $array = [];

        foreach ($collection as $file) {
            /* @var Included_File $file */
            $array[] = [
                'file_name' => $file->get_name(),
                'file_url' => $file->get_url()
            ];
        }

        return $array;
    }

}
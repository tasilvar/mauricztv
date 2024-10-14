<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api\dto;

use bpmj\wpidea\sales\product\Product_Collection;

class Product_Collection_To_DTO_Collection_Mapper
{
    private Product_To_DTO_Mapper $product_to_DTO_mapper;

    public function __construct(
        Product_To_DTO_Mapper $product_to_DTO_mapper
    )
    {
        $this->product_to_DTO_mapper = $product_to_DTO_mapper;
    }

    public function map(Product_Collection $product_collection): Product_DTO_Collection
    {
        $product_dto_collection = Product_DTO_Collection::create();

        foreach ($product_collection as $product) {
            $product_dto_collection->add(
                $this->product_to_DTO_mapper->map($product)
            );
        }

        return $product_dto_collection;
    }
}
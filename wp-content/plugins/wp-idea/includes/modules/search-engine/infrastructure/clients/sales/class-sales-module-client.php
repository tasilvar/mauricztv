<?php

namespace bpmj\wpidea\modules\search_engine\infrastructure\clients\sales;

use bpmj\wpidea\modules\search_engine\core\clients\sales\dto\Product_DTO;
use bpmj\wpidea\modules\search_engine\core\clients\sales\dto\Product_DTO_Collection;
use bpmj\wpidea\modules\search_engine\core\clients\sales\Interface_Sales_Module_Client;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\sales\product\repository\Product_Query_Criteria;

class Sales_Module_Client implements Interface_Sales_Module_Client
{
    private Interface_Product_Repository $product_repository;

    public function __construct(Interface_Product_Repository $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    public function find_accessible_products_by_query(string $query): Product_DTO_Collection
    {
        $criteria = new Product_Query_Criteria();
        $criteria->set_phrase($query);
        $criteria->set_is_visible_in_catalog(true);
        $found_products = $this->product_repository->find_by_criteria($criteria);

        $product_dto_collection = new Product_DTO_Collection();

        foreach ($found_products as $found_product) {
            $product_dto_collection->append(
                new Product_DTO(
                    $found_product->get_id()->to_int(),
                    $found_product->get_name()->get_value(),
                    get_permalink($found_product->get_id()->to_int())
                )
            );
        }

        return $product_dto_collection;
    }
}
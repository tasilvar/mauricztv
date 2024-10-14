<?php

namespace bpmj\wpidea\modules\search_engine\core\clients\sales;

use bpmj\wpidea\modules\search_engine\core\clients\sales\dto\Product_DTO_Collection;

interface Interface_Sales_Module_Client
{
    public function find_accessible_products_by_query(string $query): Product_DTO_Collection;
}
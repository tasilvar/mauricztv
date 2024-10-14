<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api;

use bpmj\wpidea\sales\product\repository\Product_Query_Criteria;

class Api_Search_Criteria_To_Product_Query_Criteria_Mapper
{
    public function map(Product_API_Search_Criteria $search_criteria): Product_Query_Criteria
    {
        $query_criteria = new Product_Query_Criteria();
        $query_criteria->set_phrase($search_criteria->get_phrase());
        $query_criteria->set_is_visible_in_catalog($search_criteria->get_visible_in_catalog());
        $query_criteria->set_is_bundle($search_criteria->get_is_bundle());

        return $query_criteria;
    }
}
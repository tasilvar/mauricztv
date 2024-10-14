<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\service;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;
use bpmj\wpidea\sales\product\model\Product_ID;

class Url_Resolver implements Interface_Url_Resolver
{
    /**
     * @throws Invalid_Url_Exception
     */
    public function get_by_product_id(Product_ID $id): ?Url
    {
        $url = get_permalink($id->to_int());
        if (!$url) {
            return null;
        }

        return new Url($url);
    }
}
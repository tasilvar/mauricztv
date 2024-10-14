<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\service;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;

class Url_Resolver implements Interface_Url_Resolver
{
    /**
     * @throws Invalid_Url_Exception
     */
    public function get_by_digital_product_id(Digital_Product_ID $id): ?Url
    {
        $url = get_permalink($id->to_int());
        if (!$url) {
            return null;
        }

        return new Url($url);
    }
}
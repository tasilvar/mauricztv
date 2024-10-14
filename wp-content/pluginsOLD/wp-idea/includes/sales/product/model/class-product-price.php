<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

use bpmj\wpidea\sales\product\exception\Invalid_Product_Price_Exception;

class Product_Price
{
    private float $price;

    /**
     * @throws Invalid_Product_Price_Exception
     */
    public function __construct(
        float $price
    ) {
        if($price < 0) {
            throw new Invalid_Product_Price_Exception();
        }

        $this->price = $price;
    }

    public function get_value(): float
    {
        return $this->price;
    }
}
<?php

declare(strict_types=1);

namespace bpmj\wpidea\app\bundles;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\resources\Resource_Type;
use bpmj\wpidea\sales\product\model\Product_Name;
use bpmj\wpidea\sales\product\model\Product_Price;

class Bundle_Item_Display_Model
{
    private Product_Name $name;
    private ID $id;
    private Resource_Type $resource_type;
    private Product_Price $price;

    public function __construct(
        Product_Name $name,
        ID $id,
        Resource_Type $resource_type,
        Product_Price $price
    )
    {
        $this->name = $name;
        $this->id = $id;
        $this->resource_type = $resource_type;
        $this->price = $price;
    }

    public function get_name(): Product_Name
    {
        return $this->name;
    }

    public function get_id(): ID
    {
        return $this->id;
    }

    public function get_resource_type(): Resource_Type
    {
        return $this->resource_type;
    }

    public function get_price(): Product_Price
    {
        return $this->price;
    }
}
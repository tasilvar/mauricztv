<?php namespace bpmj\wpidea\sales\product;

use bpmj\wpidea\sales\product\model\Product;

class Product_Presenter
{

    /** @var Product */
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function to_array(): array
    {
        return [
            'id'    => $this->product->get_id()->to_int(),
            'name' => $this->product->get_name()->get_value()
        ];
    }
}
<?php

namespace bpmj\wpidea\modules\affiliate_program\core\entities;

use bpmj\wpidea\modules\affiliate_program\core\value_objects\{External_Link_ID, External_Url, Product_ID};

class External_Landing_Link
{
    private ?External_Link_ID $id;
    private Product_ID $product_id;
    private External_Url $url;

    public function __construct(
        ?External_Link_ID $id,
        Product_ID $product_id,
        External_Url $url
    ) {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->url = $url;
    }

    public function get_id(): ?External_Link_ID
    {
        return $this->id;
    }

    public function change_product_id(Product_ID $product_id): void
    {
        $this->product_id = $product_id;
    }

    public function get_product_id(): Product_ID
    {
        return $this->product_id;
    }

    public function change_url(External_Url $url): void
    {
        $this->url = $url;
    }

    public function get_url(): External_Url
    {
        return $this->url;
    }
}
<?php

namespace bpmj\wpidea\wolverine\product;

use bpmj\wpidea\sales\product\model\Gtu;

class Variant
{

    protected $id;
    protected $name;
    protected $price;
    protected $purchaseLimitExhausted;
    protected $gtu;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return  $this->name;
    }

    public function getPrice()
    {
        return  $this->price;
    }

    public function setId($id)
    {
        if (!is_int($id)) {
            throw new \Exception('Trying to set ID value other than integer');
        }
        $this->id = $id;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function purchaseLimitExhausted()
    {
        return $this->purchaseLimitExhausted;
    }

    public function setPurchaseLimitExhausted($exhausted)
    {
        $this->purchaseLimitExhausted = $exhausted;

        return $this;
    }

    public function setGtu(Gtu $gtu): self
    {
        $this->gtu = $gtu;
        return $this;
    }

    public function getGtu(): ?Gtu
    {
        return $this->gtu;
    }
}

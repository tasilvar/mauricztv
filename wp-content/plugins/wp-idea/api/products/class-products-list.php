<?php
namespace bpmj\wpidea\api\products;

use Countable;
use Iterator;

class Products_List implements Iterator, Countable
{
    private $position = 0;
    private $products = [];  

    public function __construct($products) {
        $this->position = 0;

        $this->products = $products;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->products[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->products[$this->position]);
    }

    public function count()
    {
        return count($this->products);
    }
}
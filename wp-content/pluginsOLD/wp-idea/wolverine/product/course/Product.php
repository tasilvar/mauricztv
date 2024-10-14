<?php
namespace bpmj\wpidea\wolverine\product\course;

use bpmj\wpidea\wolverine\course\settings\Settings;
use bpmj\wpidea\wolverine\product\Product as BaseProduct;

class Product extends BaseProduct
{
    public $accessSettings;
    public $startDate;
    public $course;

    public function __construct()
    {
        parent::__construct();
        $this->repository = new Repository();
        $this->accessSettings = new Settings();
    }

    public function setStartDate($date)
    {
        $this->startDate = $date;
        return $this;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function updateAccessSettings()
    {
        $this->accessSettings->setId($this->getId());
        $this->accessSettings->setStartDate($this->startDate);
        
        return $this;
    }
    
    public function initiateNewVariant()
    {
        $variant = new Variant();
        $variant->setId($this->variants->count() + 1);
        return $variant;
    }

    public function checkUserAccess($userId)
    {
        $productAccess = new Access($this->getId());

        return $productAccess->checkUserAccess($userId);
    }
}

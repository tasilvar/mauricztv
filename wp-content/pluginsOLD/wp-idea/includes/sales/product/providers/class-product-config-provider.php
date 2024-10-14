<?php

namespace bpmj\wpidea\sales\product\providers;

use bpmj\wpidea\Packages;

class Product_Config_Provider implements Interface_Product_Config_Provider
{
    private Packages $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
    }

    public function has_access_to_custom_purchase_links(): bool
    {
        return $this->packages->has_access_to_feature(Packages::FEAT_CUSTOM_PURCHASE_LINKS);
    }
}
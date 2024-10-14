<?php

namespace bpmj\wpidea\sales\product\providers;

interface Interface_Product_Config_Provider
{
    public function has_access_to_custom_purchase_links(): bool;
}
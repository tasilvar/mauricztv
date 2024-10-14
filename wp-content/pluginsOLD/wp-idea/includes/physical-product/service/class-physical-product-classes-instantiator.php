<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\service;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\instantiator\Instantiator;
use bpmj\wpidea\physical_product\web\Delivery_Address_Renderer;
use bpmj\wpidea\physical_product\actions\Delivery_Address_Validator;
use bpmj\wpidea\physical_product\filters\Delivery_Address_Saver;
use bpmj\wpidea\physical_product\filters\Delivery_Price_Adder;
use bpmj\wpidea\scopes\No_Scope;

class Physical_Product_Classes_Instantiator implements Interface_Initiable
{

    private Instantiator $instantiator;

    public function __construct(
        Instantiator $instantiator
    )
    {
        $this->instantiator = $instantiator;
    }

    public function init(): void
    {
        $scope = new No_Scope();

        $this->instantiator->create(Delivery_Address_Renderer::class, $scope);
        $this->instantiator->create(Delivery_Address_Validator::class, $scope);
        $this->instantiator->create(Delivery_Address_Saver::class, $scope);
        $this->instantiator->create(Delivery_Price_Adder::class, $scope);
    }
}
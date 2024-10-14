<?php
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Offers_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;

return [
    Interface_Offers_Persistence::class => DI\autowire(Offers_Persistence::class)
];

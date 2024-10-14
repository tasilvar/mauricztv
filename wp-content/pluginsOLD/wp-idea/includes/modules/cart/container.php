<?php

use bpmj\wpidea\modules\cart\core\handler\Interface_Cart_Handler;
use bpmj\wpidea\modules\cart\core\handler\Interface_Fees_Handler;
use bpmj\wpidea\modules\cart\infrastructure\handler\Cart_Handler;
use bpmj\wpidea\modules\cart\infrastructure\handler\Fees_Handler;

return [
    Interface_Cart_Handler::class => DI\autowire(Cart_Handler::class),
    Interface_Fees_Handler::class => DI\autowire(Fees_Handler::class),
];
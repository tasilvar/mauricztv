<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\cart\infrastructure\events;

use bpmj\wpidea\modules\cart\core\events\external\handlers\Checkout_Handler;

class Event_Handlers_Initiator
{
    public function __construct(
        Checkout_Handler $checkout_handler
    ) {
        $checkout_handler->init();
    }
}
<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\cart\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\cart\api\Cart_API;

class Checkout_Handler implements Interface_Initiable
{
    private Interface_Events $events;
    private Cart_API $cart_api;

    public function __construct(
        Interface_Events $events,
        Cart_API $cart_api
    ) {
        $this->events = $events;
        $this->cart_api = $cart_api;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PAGE_VIEWED, [$this, 'checkout']);
    }

    public function checkout(): void
    {
        if ($this->cart_api->is_checkout()) {
            $this->events->emit(Event_Name::CHECKOUT_INITIATED);
        }
    }
}
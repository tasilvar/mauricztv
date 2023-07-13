<?php

namespace bpmj\wpidea\modules\cart\core\services;

use bpmj\wpidea\modules\cart\core\handler\Interface_Fees_Handler;
use bpmj\wpidea\modules\cart\core\entities\Fee;
use bpmj\wpidea\modules\cart\core\collections\Fee_Collection;

class Fees
{
    private Interface_Fees_Handler $fees_handler;

    public function __construct(
        Interface_Fees_Handler $fees_handler
    )
    {
        $this->fees_handler = $fees_handler;
    }

    public function add_fee(Fee $fee): void
    {
        $this->fees_handler->add_fee($fee);
    }

    public function remove_fee($id): void
    {
        $this->fees_handler->remove_fee($id);
    }

    public function get_fee($id): ?Fee
    {
        return $this->fees_handler->get_fee($id);
    }

    public function get_fees(): Fee_Collection
    {
        return $this->fees_handler->get_fees();
    }
}
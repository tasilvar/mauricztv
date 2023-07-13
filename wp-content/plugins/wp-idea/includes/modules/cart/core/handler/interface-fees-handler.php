<?php

namespace bpmj\wpidea\modules\cart\core\handler;

use bpmj\wpidea\modules\cart\core\entities\Fee;
use bpmj\wpidea\modules\cart\core\collections\Fee_Collection;

interface Interface_Fees_Handler
{
    public function add_fee(Fee $fee): void;

    public function remove_fee($id): void;

    public function get_fee($id): ?Fee;

    public function get_fees(): Fee_Collection;
}
<?php

namespace bpmj\wpidea\modules\cart\api;

use bpmj\wpidea\modules\cart\core\services\Fees;
use bpmj\wpidea\modules\cart\core\entities\Fee;

class Fees_API
{
    public const TAX_RATE_FEE_INDEX = 'tax_rate';
    public const FEE_ID_FEE_INDEX = 'id';
    public const NET_AMOUNT_FEE_INDEX = 'net_amount';

    private Fees $fees;

    public function __construct(Fees $fees)
    {
        $this->fees = $fees;
    }

    public function add_fee(Fee $fee): void
    {
        $this->fees->add_fee($fee);
    }

    public function remove_fee($id): void
    {
        $this->fees->remove_fee($id);
    }

    public function get_fee($id): ?Fee
    {
        return $this->fees->get_fee($id);
    }
}
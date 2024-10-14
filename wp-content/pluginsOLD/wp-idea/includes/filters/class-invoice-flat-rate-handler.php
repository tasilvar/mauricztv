<?php

namespace bpmj\wpidea\filters;

use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;

class Invoice_Flat_Rate_Handler implements Interface_Initiable
{
    private Interface_Filters $filters;

    public function __construct(Interface_Filters $filters) {
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->filters->add( Filter_Name::INVOICE_FLAT_RATE_ENABLED, [ $this, 'invoice_flat_rate_enabled' ] );
    }

    public function invoice_flat_rate_enabled( bool $enabled ): bool
    {
        if ( ! Flat_Rate_Tax_Symbol_Helper::is_enabled() ) {
            return false;
        }

        return $enabled;
    }
}

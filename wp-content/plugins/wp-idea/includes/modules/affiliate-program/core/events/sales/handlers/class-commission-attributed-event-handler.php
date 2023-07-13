<?php

namespace bpmj\wpidea\modules\affiliate_program\core\events\sales\handlers;

use bpmj\wpidea\modules\affiliate_program\Affiliate_Program_Module;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\translator\Interface_Translator;
use EDD_Payment;

class Commission_Attributed_Event_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Events $events,
        Interface_Translator $translator
    ) {
        $this->events = $events;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->events->on(Affiliate_Program_Module::COMMISION_ATTRIBUTED, [$this, 'add_payment_note'], 10, 2);
    }

    public function add_payment_note(Partner $partner, int $payment_id): void
    {
        $payment = new EDD_Payment($payment_id);
        $name = $partner->get_full_name() ?
            $partner->get_full_name()->get_full_name() : $partner->get_email()->get_value();

        $payment->add_note(
            $this->translator->translate('affiliate_program.order_details.note') .
            $partner->get_email()->get_value() . ' (' . $name . ')'
        );
    }
}

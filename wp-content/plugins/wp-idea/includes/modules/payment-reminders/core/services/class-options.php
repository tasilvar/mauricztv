<?php

namespace bpmj\wpidea\modules\payment_reminders\core\services;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\options\Interface_Options;

class Options
{
    private Interface_Options $options;

    public function __construct(Interface_Options $options)
    {
        $this->options = $options;
    }

    public function get(string $option)
    {
        $settings_payment_reminders = $this->options->get(Settings_Const::PAYMENT_REMINDERS);
        return $settings_payment_reminders[$option] ?? null;
    }
}
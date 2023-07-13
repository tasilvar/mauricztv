<?php

namespace bpmj\wpidea\modules\payment_reminders\core\value_objects\email;

use bpmj\wpidea\data_types\Url;

class Payment_Reminder_Email_Product_Row
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get_name(): string
    {
        return $this->name;
    }
}

<?php

use bpmj\wpidea\infrastructure\mail\WPI_Mailer;
use bpmj\wpidea\modules\payment_reminders\core\services\Payment_Reminder_Email_Sender;

return [
    Payment_Reminder_Email_Sender::class => DI\autowire()
        ->constructorParameter('mailer', DI\autowire(WPI_Mailer::class))
];
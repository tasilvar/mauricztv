<?php

namespace bpmj\wpidea\modules\payment_reminders\core\services;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\data_types\mail\Message;
use bpmj\wpidea\data_types\mail\Subject;
use bpmj\wpidea\environment\Interface_Site;
use bpmj\wpidea\infrastructure\mail\Interface_Mailer;
use bpmj\wpidea\infrastructure\mail\Mail_Factory;
use bpmj\wpidea\modules\payment_reminders\core\value_objects\email\Payment_Reminder_Email;
use bpmj\wpidea\modules\payment_reminders\core\value_objects\email\Payment_Reminder_Email_Product_Row_Collection;
use Exception;

class Payment_Reminder_Email_Sender
{
    private Options $options;
    private Interface_Mailer $mailer;
    private Mail_Factory $mail_factory;
    private Interface_Site $site;

    public function __construct(Options $options, Interface_Mailer $mailer, Mail_Factory $mail_factory, Interface_Site $site)
    {
        $this->options = $options;
        $this->mailer = $mailer;
        $this->mail_factory = $mail_factory;
        $this->site = $site;
    }

    /**
     * @throws Exception
     */
    public function send(Payment_Reminder_Email $payment_reminder_email): void
    {
        $email = $this->mail_factory->create(
            $payment_reminder_email->get_to(),
            new Subject(str_replace('{payment_id}', $payment_reminder_email->get_order_id(), $this->options->get(Settings_Const::PAYMENT_REMINDERS_MESSAGE_SUBJECT))),
            $this->get_message($payment_reminder_email)
        );

        $this->mailer->send($email);
    }

    /**
     * @throws Exception
     */
    private function get_message(Payment_Reminder_Email $email): Message
    {
        return new Message(str_replace([
            '{name}',
            '{product_list}',
            '{amount}',
            '{payment_id}',
            '{date}',
            '{sitename}'
        ], [
            $email->get_name()->get_full_name(),
            $this->get_products_string($email->get_products()),
            $email->get_formatted_amount(),
            $email->get_order_id(),
            $email->get_date(),
            $this->site->get_name(),
        ], $this->get_message_template()));
    }

    private function get_products_string(Payment_Reminder_Email_Product_Row_Collection $products): string
    {
        $result = '<ul>';
        foreach ($products as $product) {
            $name = $product->get_name();
            $result .= "<li>{$name}</li>";
        }
        $result .= "</ul>";

        return $result;
    }

    private function get_message_template(): string
    {
        return $this->options->get(Settings_Const::PAYMENT_REMINDERS_MESSAGE_CONTENT);
    }
}
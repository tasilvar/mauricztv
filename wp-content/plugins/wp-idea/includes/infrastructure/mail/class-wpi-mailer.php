<?php
namespace bpmj\wpidea\infrastructure\mail;

class WPI_Mailer implements Interface_Mailer
{
    public function send(Mail $mail): void
    {
        EDD()->emails->send($mail->get_email_adress()->get_value(), $mail->get_subject()->get_value(), $mail->get_message()->get_value(), $mail->get_attachments()->get_value());
    }
}
<?php
namespace bpmj\wpidea\infrastructure\mail;

class WP_Mailer implements Interface_Mailer
{
    public function send(Mail $mail): void
    {
        wp_mail($mail->get_email_adress()->get_value(),$mail->get_subject()->get_value(), $mail->get_message()->get_value(), $mail->get_headers()->get_value(), $mail->get_attachments()->get_value());
    }
}
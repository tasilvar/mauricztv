<?php
declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\mail;

use bpmj\wpidea\data_types\{
    mail\Email_Address,
    mail\Subject,
    mail\Message,
    mail\Headers,
    mail\Attachments
};

class Mail_Factory
{
    public function create(Email_Address $mail_address, Subject $subject, Message $message, ?Headers $headers = null, ?Attachments $attachments = null): Mail
    {
        $headers = $headers ?? new Headers();
        $attachments = $attachments ?? new Attachments();

        return new Mail($mail_address, $subject, $message, $headers, $attachments);
    }
}
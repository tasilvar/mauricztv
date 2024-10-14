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

class Mail
{
    private $email_adress;
    private $subject;
    private $message;
    private $headers;
    private $attachments;

    public function __construct(Email_Address $email_adress, Subject $subject, Message $message, Headers $headers, Attachments $attachments)
    {
        $this->email_adress = $email_adress;
        $this->subject = $subject;
        $this->message = $message;
        $this->headers = $headers;
        $this->attachments = $attachments;
    }

    public function get_email_adress(): Email_Address
    {
        return $this->email_adress;
    }

    public function get_subject(): Subject
    {
        return $this->subject;
    }

    public function get_message(): Message
    {
        return $this->message;
    }

    public function get_headers(): Headers
    {
        return $this->headers;
    }

    public function get_attachments(): Attachments
    {
        return $this->attachments;
    }
}
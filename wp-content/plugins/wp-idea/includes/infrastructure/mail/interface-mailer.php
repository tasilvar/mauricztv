<?php
namespace bpmj\wpidea\infrastructure\mail;

interface Interface_Mailer
{
    public function send(Mail $mail): void;
}

<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\email_handler;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use PHPMailer\PHPMailer\PHPMailer;

class Email_Plain_Text_Handler implements Interface_Initiable
{
    private $actions;

    public function __construct(Interface_Actions $actions)
    {
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add('phpmailer_init', [$this, 'add_plain_text_email_type']);
    }

    public function add_plain_text_email_type(/* @todo PHPMailer */ $phpMailer): void
    {
        if ('text/html' !== $phpMailer->ContentType) {
            return;
        }

        $body = $phpMailer->Body;
        // Change anchors with text to links
        $body = preg_replace('/<a href="(.+)">(.+)<\/a>/', '$1', $body);
        $body = strip_tags($body);
        // Change all white chars to spaces
        $body = preg_replace('/\s/', ' ', $body);
        // Change two and more spaces next to each other, to new lines
        $body = preg_replace('/\s{2,}/', "\n", $body);

        $phpMailer->AltBody = $body;
    }
}
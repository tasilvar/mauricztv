<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use Psr\Log\LoggerInterface;

class Debug_Events_Handler implements Interface_Event_Handler
{
    private $events;

    private $logger;

    public function __construct(
        Interface_Events $events,
        LoggerInterface $logger
    )
    {
        $this->events = $events;
        $this->logger = $logger;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::DEBUG, function($data) {
            $this->logger->debug((is_string($data) || is_numeric($data)) ? $data : json_encode($data));
        });
    }
}
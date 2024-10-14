<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\logs\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use Exception;
use Psr\Log\LoggerInterface;

class Exception_Caught_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private LoggerInterface $logger;

    public function __construct(
        Interface_Events $events,
        LoggerInterface $logger
    ) {
        $this->events = $events;
        $this->logger = $logger;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::EXCEPTION_CAUGHT, function (Exception $exception) {
            $this->handle_exception_log($exception);
        });
    }

    private function handle_exception_log(Exception $exception): void
    {
        $this->logger->warning($exception->getMessage());
    }
}
<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\scheduled_events\Check_License_Data;

class Check_License_Data_Event_Handler implements Interface_Event_Handler
{
    private $check_license_data;

    private $events;

    public function __construct(
        Check_License_Data $check_license_data,
        Interface_Events $events
    )
    {
        $this->check_license_data = $check_license_data;
        $this->events = $events;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::NEW_VALID_LICENSE_HAS_BEEN_ENTERED, [$this->check_license_data, 'get_and_update_license_data'], 10,1);
    }
}
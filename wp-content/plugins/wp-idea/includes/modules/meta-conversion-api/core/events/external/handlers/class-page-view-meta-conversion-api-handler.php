<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\events\external\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\meta_conversion_api\core\services\Interface_Meta_Conversion_API_Sender;

class Page_View_Meta_Conversion_API_Handler implements Interface_Event_Handler
{
    private Interface_Events $events;
    private Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender;

    public function __construct(
        Interface_Events $events,
        Interface_Meta_Conversion_API_Sender $meta_conversion_api_sender
    ) {
        $this->events = $events;
        $this->meta_conversion_api_sender = $meta_conversion_api_sender;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::PAGE_VIEWED, [$this, 'send_data']);
    }

    public function send_data(): void
    {
        $this->meta_conversion_api_sender->send_data_for_page_view_event();
    }
}

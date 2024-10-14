<?php
namespace bpmj\wpidea\modules\webhooks\core\entities;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;


class Webhook
{
    private ID $id;
    private Webhook_Types_Of_Events $type_of_event;
    private Url $url;
    private Webhook_Status $status;

    public function __construct(
        Webhook_Types_Of_Events $type_of_event,
        Url $url,
        Webhook_Status $status
    )
    {
        $this->type_of_event = $type_of_event;
        $this->url = $url;
        $this->status = $status;
    }

    public function get_id(): ID
    {
        return $this->id;
    }

    public function set_id(ID $id): void
    {
        $this->id = $id;
    }

    public function get_type_of_event (): Webhook_Types_Of_Events
    {
        return $this->type_of_event;
    }

    public function get_url(): Url
    {
        return $this->url;
    }

    public function get_status(): Webhook_Status
    {
        return $this->status;
    }
}
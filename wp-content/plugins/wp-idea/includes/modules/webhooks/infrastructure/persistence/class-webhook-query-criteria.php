<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\infrastructure\persistence;

class Webhook_Query_Criteria
{
    public ?string $type_of_event;
    public ?string $url_like;
    public ?int$status;
    public ?int $id;

    public function __construct(
        ?string $type_of_event = null,
        ?string $url_like = null,
        ?int $status = null,
        ?int $id = null
    ) {
        $this->type_of_event = $type_of_event;
        $this->url_like = $url_like;
        $this->status = $status;
        $this->id = $id;
    }
}
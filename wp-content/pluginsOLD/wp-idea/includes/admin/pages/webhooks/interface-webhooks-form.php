<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\webhooks;

interface Interface_Webhooks_Form
{
    public function get_page_title(?int $id_webhook): string;

    public function get_data_to_the_form_by_id(int $id_webhook): array;

    public function get_webhook_event_types(): array;
}
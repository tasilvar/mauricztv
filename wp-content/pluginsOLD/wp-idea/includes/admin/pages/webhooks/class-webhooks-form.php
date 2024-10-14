<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\webhooks;

use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\translator\Interface_Translator;

class Webhooks_Form implements Interface_Webhooks_Form
{
    private Interface_Translator $translator;
    private Interface_Webhook_Repository $webhook_repository;

    public function __construct(
        Interface_Translator $translator,
        Interface_Webhook_Repository $webhook_repository
    ) {
        $this->translator = $translator;
        $this->webhook_repository = $webhook_repository;
    }

    public function get_page_title(?int $id_webhook): string
    {
        return ($id_webhook) ? $this->translator->translate('webhooks.form.edit') : $this->translator->translate('webhooks.form.add');
    }

    public function get_data_to_the_form_by_id(int $id_webhook): array
    {
        $webhook = $this->webhook_repository->find_by_id($id_webhook);

        if(!$webhook){
            return [];
        }

        return [
            'id_webhook' => $id_webhook,
            'type_of_event' => $webhook->get_type_of_event()->get_value(),
            'url' => $webhook->get_url()->get_value(),
            'status' => $webhook->get_status()->get_value()
        ];
    }

    public function get_webhook_event_types(): array
    {
        $type_events = [];

        foreach (Webhook_Types_Of_Events::VALID_EVENT as $event) {
            $type_events[] = [
                $event => $this->translator->translate('webhooks.event.' . $event)
            ];
        }

        return $type_events;
    }

}

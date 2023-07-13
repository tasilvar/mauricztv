<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\webhooks;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\data_types\{ID, Url};
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\webhooks\api\controllers\Admin_Webhooks_Ajax_Controller;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook_Collection;
use bpmj\wpidea\modules\webhooks\core\value_objects\{Webhook_Types_Of_Events};
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;

class Webhooks_Table_Data_Parser
{
    private Interface_Translator $translator;
    private Url_Generator $url_generator;

    public function __construct(
        Interface_Translator $translator,
        Url_Generator $url_generator
    ) {
        $this->translator = $translator;
        $this->url_generator = $url_generator;
    }

    public function parse_webhook_objects_to_plain_array(Webhook_Collection $webhooks): array
    {
        $data = [];

        foreach ($webhooks as $webhook) {

            $data[] = [
                'id' => $webhook->get_id()->to_int(),
                'type_of_event' => $webhook->get_type_of_event()->get_value(),
                'type_of_event_label' => $this->translator->translate('webhooks.event.'.$webhook->get_type_of_event()->get_value()),
                'url' => $webhook->get_url()->get_value(),
                'status' => $webhook->get_status()->get_value(),
                'status_label' => $this->get_name_status_label($webhook->get_status()->get_value()),
                'edit_webhook' => $this->get_edit_webhook_url($webhook->get_id()->to_int()),
                'delete_webhook' => $this->get_delete_webhook_url($webhook->get_id()->to_int()),
                'doc_webhook' => $this->get_doc_webhook_url($webhook->get_type_of_event()->get_value()),
                'change_status_webhook' => $this->get_change_status_webhook_url($webhook->get_id()->to_int()),
            ];
        }

        return $data;
    }

    public function webhook_data_array_to_webhook_model(array $webhook_data): Webhook
    {
        $type_of_event = new Webhook_Types_Of_Events( $webhook_data['type_of_event'] );
        $url           = new Url( $webhook_data['url'] );
        $status        = new Webhook_Status( (int)$webhook_data['status'] );

        $webhook = new Webhook($type_of_event,  $url, $status);

        if($webhook_data['id']) {
            $id = new ID( (int)$webhook_data['id'] );
            $webhook->set_id($id);
        }

        return $webhook;
    }

    public function get_name_status_label(int $status): string
    {
        $webhook_status = new Webhook_Status($status);

        return $this->translator->translate('webhooks.status.'.$webhook_status->get_name());
    }

    private function get_change_status_webhook_url(int $webhook_id): string
    {
        return $this->url_generator->generate(Admin_Webhooks_Ajax_Controller::class, 'change_status', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $webhook_id
        ]);
    }

    private function get_edit_webhook_url(int $webhook_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::WEBHOOKS,
            'view' => 'edit',
            'id' => $webhook_id
        ]);
    }

    private function get_doc_webhook_url(string $type_of_event): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::WEBHOOKS,
            'view' => 'doc',
            'event' => $type_of_event
        ]);
    }

    private function get_delete_webhook_url(int $webhook_id): string
    {
        return $this->url_generator->generate(Admin_Webhooks_Ajax_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $webhook_id
        ]);
    }

    public function process_sort_by(array $sort_by): Sort_By_Clause
    {
        $default_sort_by = (new Sort_By_Clause())
            ->sort_by('id', true);

        if(empty($sort_by)) {
            return $default_sort_by;
        }

        $parsed_sort_by = new Sort_By_Clause();

        foreach ($sort_by as $sort_by_condition) {
            $parsed_sort_by->sort_by($sort_by_condition['id'], $sort_by_condition['desc']);
        }

        return $parsed_sort_by;
    }

    public function get_criteria_from_filters_array(array $filters): Webhook_Query_Criteria
    {
        $type_of_event = $this->get_filter_value_if_present($filters, 'type_of_event');

        $url = $this->get_filter_value_if_present($filters, 'url');

        $status = $this->get_filter_value_if_present($filters, 'status');

        $status = $status ? (int)$status : null;

        return new Webhook_Query_Criteria($type_of_event, $url, $status);
    }

    /**
     * @return mixed|null
     */
    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(array_filter($filters, static function($filter, $key) use ($filter_name) {
                return $filter['id'] === $filter_name;
            }, ARRAY_FILTER_USE_BOTH))[0]['value'] ?? null;
    }
}
<?php
namespace bpmj\wpidea\modules\webhooks\core\services;

use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;

class Webhook_Prepare_Urls
{
    private Interface_Webhook_Repository $webhook_repository;

    public function __construct(
        Interface_Webhook_Repository $webhook_repository
    )
    {
        $this->webhook_repository = $webhook_repository;
    }

    public function get_urls(string $type_of_event): array
    {
        $criteria = new Webhook_Query_Criteria($type_of_event,null,Webhook_Status::ACTIVE);
        $webhook_urls = $this->webhook_repository->find_by_criteria($criteria);

        $urls = [];

        foreach($webhook_urls as $url){
            $urls[] = $url->get_url()->get_value();
        }

        return $urls;
    }
}

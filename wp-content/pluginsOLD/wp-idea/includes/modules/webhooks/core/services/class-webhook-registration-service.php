<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\services;

use bpmj\wpidea\modules\webhooks\core\factories\Interface_Webhook_Factory;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\value_objects\{Webhook_Types_Of_Events};
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;
use Exception;

class Webhook_Registration_Service implements Interface_Webhook_Registration_Service
{
    private Interface_Webhook_Repository $webhook_repository;
    private Interface_Webhook_Factory $webhook_factory;

    public function __construct(
        Interface_Webhook_Repository $webhook_repository,
        Interface_Webhook_Factory $webhook_factory
    ) {
        $this->webhook_repository = $webhook_repository;
        $this->webhook_factory = $webhook_factory;
    }

    public function subscribe($name, $url): bool
    {
        if($this->url_exists_in_table($name, $url)){
            return false;
        }

        try {
            $webhook = $this->webhook_factory->create($name, $url);
        }catch (Exception $e){
            return false;
        }

            $this->webhook_repository->save($webhook);

        return true;
    }

    public function unsubscribe($name, $url): bool
    {
        try {
            $type_of_event = new Webhook_Types_Of_Events($name);
        }catch (Exception $e){
            return false;
        }

        $criteria = $this->get_criteria($name, $url);
        $webhook = $this->webhook_repository->find_by_criteria($criteria);

        if(!$webhook->get_first()){
            return false;
        }

        $id_webhook = $webhook->get_first()->get_id();
        $this->webhook_repository->remove($id_webhook->to_int());

        return true;
    }

     private function url_exists_in_table(string $name, string $url): bool
     {
         $criteria = $this->get_criteria($name, $url);
         $count = $this->webhook_repository->count_by_criteria($criteria);

        return ($count > 0);
     }

     private function get_criteria(string $name, string $url): Webhook_Query_Criteria
     {
       return new Webhook_Query_Criteria($name, $url);
     }

}

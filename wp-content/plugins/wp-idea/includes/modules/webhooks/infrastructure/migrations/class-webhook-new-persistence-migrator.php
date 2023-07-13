<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\infrastructure\migrations;

use bpmj\wpidea\admin\pages\webhooks\Webhooks_Table_Data_Parser;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\value_objects\{Webhook_Types_Of_Events};
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Interface_Webhooks_Persistence;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;
use bpmj\wpidea\options\Interface_Options;

class Webhook_New_Persistence_Migrator
{
    private const WEBHOOKS_SLUG = 'wpi_webhook';

    private Interface_Webhook_Repository $webhook_repository;
    private Interface_Webhooks_Persistence $webhook_persistence;
    private Interface_Options $options;
    private Webhooks_Table_Data_Parser $data_parser;

    public function __construct(
        Interface_Webhook_Repository $webhook_repository,
        Interface_Webhooks_Persistence $webhook_persistence,
        Interface_Options $options,
        Webhooks_Table_Data_Parser $data_parser
    )
    {
        $this->webhook_repository = $webhook_repository;
        $this->webhook_persistence = $webhook_persistence;
        $this->options = $options;
        $this->data_parser = $data_parser;
    }

   public function migrate():void
   {
       $this->webhook_persistence->setup();
       $this->migrate_webhook_url_from_wp_option_to_wpi_webhook();
   }

    private function migrate_webhook_url_from_wp_option_to_wpi_webhook(): void
    {
        foreach($this->get_all_url_by_webhook_name() as $url){

            if(!$this->url_exists_in_wpi_webhooks_table($url)){

                $array_of_webhook_data = [
                    'type_of_event' => Webhook_Types_Of_Events::ORDER_PAID,
                    'url' => $url,
                    'status' => Webhook_Status::ACTIVE
                ];

                $webhook = $this->data_parser->webhook_data_array_to_webhook_model($array_of_webhook_data);
                $this->webhook_repository->save($webhook);

            }

        }
    }

    private function url_exists_in_wpi_webhooks_table(string $url): bool
    {
        $criteria = new Webhook_Query_Criteria(Webhook_Types_Of_Events::ORDER_PAID, $url);
        $count = $this->webhook_repository->count_by_criteria($criteria);

        return ($count > 0);
    }

    private function get_all_url_by_webhook_name(): array
    {
        $data_webhooks = $this->options->get(self::WEBHOOKS_SLUG);

        $url_array_by_webhook_name = $data_webhooks[Webhook_Types_Of_Events::ORDER_PAID] ?? ($data_webhooks[Webhook_Types_Of_Events::ORDER_PAID] = []);

        $urls = [];
        foreach($url_array_by_webhook_name as $host_name => $host_urls){
            foreach($host_urls as $url){
                $urls[] = $url;
            }
        }

        return $urls;
    }

}

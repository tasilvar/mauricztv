<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\repository;

use bpmj\wpidea\service\model\Service;
use bpmj\wpidea\service\model\Service_Collection;
use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\service\model\Service_Name;
use bpmj\wpidea\service\persistence\Interface_Service_Persistence;

class Service_Wp_Repository implements Interface_Service_Repository
{
    private Interface_Service_Persistence $persistence;

    public function __construct(
        Interface_Service_Persistence $persistence
    )
    {
        $this->persistence = $persistence;
    }

    public function save(Service $service): Service_ID
    {
        return $this->is_model_new_entity($service)
            ? $this->create($service)
            : $this->update($service);
    }

    public function find(Service_ID $id): ?Service
    {
        $product_data = $this->persistence->find_by_id($id->to_int());

        if(is_null($product_data)) {
            return null;
        }

        return $this->get_service_model(
            $id,
            new Service_Name($product_data['name'])
        );
    }

    public function find_all(): Service_Collection
    {
        $posts = $this->persistence->find_all();
        $collection = new Service_Collection();

        foreach ($posts as $post) {
            $id = new Service_ID($post['id']);
            $name = new Service_Name($post['name']);

            $collection->add($this->get_service_model($id, $name));
        }

        return $collection;
    }


    public function count_all(): int
    {
        return $this->persistence->count_all();
    }
    private function is_model_new_entity(Service $service): bool
    {
        return is_null($service->get_id());
    }

    private function create(Service $service): Service_ID
    {
        return new Service_ID(
            $this->persistence->save_or_update_service(
                $service->get_id() ? $service->get_id()->to_int() : null,
                $service->get_name()->get_value()
            )
        );
    }

    private function update(Service $service): Service_ID
    {
        return new Service_ID(
            $this->persistence->save_or_update_service(
                $service->get_id() ? $service->get_id()->to_int() : null,
                $service->get_name()->get_value()
            )
        );
    }

    private function get_service_model(Service_ID $id, Service_Name $name): Service
    {
        return Service::create(
            $id,
            $name
        );
    }
}

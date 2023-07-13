<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\repository;

use bpmj\wpidea\physical_product\model\Physical_Product;
use bpmj\wpidea\physical_product\model\Physical_Product_Collection;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;
use bpmj\wpidea\physical_product\model\Physical_Product_Name;
use bpmj\wpidea\physical_product\persistence\Interface_Physical_Product_Persistence;

class Physical_Product_Wp_Repository implements Interface_Physical_Product_Repository
{
    private Interface_Physical_Product_Persistence $persistence;

    public function __construct(
        Interface_Physical_Product_Persistence $persistence
    ) {
        $this->persistence = $persistence;
    }

    public function save(Physical_Product $product): Physical_Product_ID
    {
        return $this->is_model_new_entity($product)
            ? $this->create($product)
            : $this->update($product);
    }

    public function find(Physical_Product_ID $id): ?Physical_Product
    {
        $product_data = $this->persistence->find_by_id($id->to_int());

        if (is_null($product_data)) {
            return null;
        }

        return $this->get_model(
            $id,
            new Physical_Product_Name($product_data->name)
        );
    }

    public function find_all(): Physical_Product_Collection
    {
        $dtos = $this->persistence->find_all();
        $collection = new Physical_Product_Collection();

        foreach ($dtos as $dto) {
            $id = new Physical_Product_ID($dto->id);
            $name = new Physical_Product_Name($dto->name);

            $collection->add($this->get_model($id, $name));
        }

        return $collection;
    }

    public function count_all(): int
    {
        return $this->persistence->count_all();
    }

    private function is_model_new_entity(Physical_Product $product): bool
    {
        return is_null($product->get_id());
    }

    private function create(Physical_Product $product): Physical_Product_ID
    {
        return new Physical_Product_ID(
            $this->persistence->save_or_update_physical_product(
                $product->get_id() ? $product->get_id()->to_int() : null,
                $product->get_name()->get_value()
            )
        );
    }

    private function update(Physical_Product $product): Physical_Product_ID
    {
        return new Physical_Product_ID(
            $this->persistence->save_or_update_physical_product(
                $product->get_id() ? $product->get_id()->to_int() : null,
                $product->get_name()->get_value()
            )
        );
    }

    private function get_model(Physical_Product_ID $id, Physical_Product_Name $name): Physical_Product
    {
        return Physical_Product::create(
            $id,
            $name
        );
    }
}

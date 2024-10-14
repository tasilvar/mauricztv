<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\repository;

use bpmj\wpidea\digital_products\model\Digital_Product;
use bpmj\wpidea\digital_products\model\Digital_Product_Collection;
use bpmj\wpidea\digital_products\model\Digital_Product_ID;
use bpmj\wpidea\digital_products\model\Digital_Product_Name;
use bpmj\wpidea\digital_products\model\Included_File;
use bpmj\wpidea\digital_products\model\Included_File_Collection;
use bpmj\wpidea\digital_products\persistence\Interface_Digital_Product_Persistence;

class Digital_Product_Wp_Repository implements Interface_Digital_Product_Repository
{
    private Interface_Digital_Product_Persistence $persistence;

    public function __construct(
        Interface_Digital_Product_Persistence $persistence
    )
    {
        $this->persistence = $persistence;
    }

    public function save(Digital_Product $product): Digital_Product_ID
    {
        return $this->is_model_new_entity($product)
            ? $this->create($product)
            : $this->update($product);
    }

    public function find(Digital_Product_ID $id): ?Digital_Product
    {
        $product_data = $this->persistence->find_by_id($id->to_int());

        if(is_null($product_data)) {
            return null;
        }

        return $this->get_digital_product_model(
            $id,
            new Digital_Product_Name($product_data['name']),
            $this->get_product_files($id)
        );
    }

    public function find_all(): Digital_Product_Collection
    {
        $posts = $this->persistence->find_all();
        $collection = new Digital_Product_Collection();

        foreach ($posts as $post) {
            $id = new Digital_Product_ID($post['id']);
            $name = new Digital_Product_Name($post['name']);
            $included_file_collection = $this->get_product_files($id);

            $collection->add($this->get_digital_product_model($id, $name, $included_file_collection));
        }

        return $collection;
    }

    public function count_all(): int
    {
        return $this->persistence->count_all();
    }

    private function is_model_new_entity(Digital_Product $product): bool
    {
        return is_null($product->get_id());
    }

    private function create(Digital_Product $product): Digital_Product_ID
    {
        $id = new Digital_Product_ID(
            $this->persistence->save_or_update_product(
                $product->get_id() ? $product->get_id()->to_int() : null,
                $product->get_name()->get_value()
            )
        );

        $this->update_product_files($id, $product->get_included_files());

        return $id;
    }

    private function update(Digital_Product $product): Digital_Product_ID
    {
        $id = new Digital_Product_ID(
            $this->persistence->save_or_update_product(
                $product->get_id() ? $product->get_id()->to_int() : null,
                $product->get_name()->get_value()
            )
        );

        $this->update_product_files($id, $product->get_included_files());

        return $id;
    }

    private function update_product_files(Digital_Product_ID $id, Included_File_Collection $included_files): void
    {
        $files = [];

        foreach ($included_files as $index => $file) {
            /** @var Included_File $file */

            $files[] = [
                'index'			 => $index,
                'name'			 => $file->get_name(),
                'file'			 => $file->get_url(),
                'attachment_id'	 => $file->get_id() ?? $this->get_attachment_id_from_url($file->get_url()),
                'condition'		 => 'all'
            ];
        }

        $this->persistence->update_files_by_product_id($id->to_int(), $files);
    }

    private function get_attachment_id_from_url(string $url): ?int
    {
        $attachment_id = attachment_url_to_postid($url);

        return $attachment_id ?: null;
    }

    private function get_product_files(Digital_Product_ID $id): Included_File_Collection
    {
        $collection = new Included_File_Collection();
        $meta = $this->persistence->find_files_by_product_id($id->to_int());

        /** @var array{attachment_id: string, name: string, file: string} $item */
        foreach ($meta as $item) {
            $attachment_id = (int)($item['attachment_id'] ?? 0);
            $name = $item['name'] ?? '';
            $url = $item['file'] ?? '';

            $collection->append(new Included_File(
                $attachment_id,
                $name,
                $url
            ));
        }

        return $collection;
    }

    private function get_digital_product_model(Digital_Product_ID $id, Digital_Product_Name $name, Included_File_Collection $included_file_collection): Digital_Product
    {
        return Digital_Product::create(
            $id,
            $name,
            $included_file_collection
        );
    }
}

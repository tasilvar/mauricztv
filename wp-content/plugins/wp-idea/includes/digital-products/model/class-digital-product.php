<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\model;

class Digital_Product
{
    private ?Digital_Product_ID $id;
    private Included_File_Collection $included_files;
    private Digital_Product_Name $name;

    private function __construct(
        ?Digital_Product_ID $id,
        Digital_Product_Name $name,
        Included_File_Collection $included_file_collection
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->included_files = $included_file_collection;
    }

    public static function create(
        ?Digital_Product_ID $id,
        Digital_Product_Name $name,
        Included_File_Collection $included_file_collection
    ): self
    {
        return new self(
            $id,
            $name,
            $included_file_collection
        );
    }

    public function get_id(): ?Digital_Product_ID
    {
        return $this->id;
    }

    public function get_name(): Digital_Product_Name
    {
        return $this->name;
    }

    public function get_included_files(): Included_File_Collection
    {
        return $this->included_files;
    }
}
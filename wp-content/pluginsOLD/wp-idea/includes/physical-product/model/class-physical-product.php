<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\physical_product\model;

class Physical_Product
{
    private ?Physical_Product_ID $id;
    private Physical_Product_Name $name;

    private function __construct(
        ?Physical_Product_ID $id,
        Physical_Product_Name $name
    )
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return static
     */
    public static function create(
        ?Physical_Product_ID $id,
        Physical_Product_Name $name
    ): self
    {
        return new static(
            $id,
            $name,
        );
    }

    public function get_id(): ?Physical_Product_ID
    {
        return $this->id;
    }

    public function get_name(): Physical_Product_Name
    {
        return $this->name;
    }
}

<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\cart;

class Cart_Content
{
    private array $item_names;
    private array $item_details;

    public function set_item_names(array $item_names): void
    {
         $this->item_names = $item_names;
    }

    public function set_item_details(array $item_details): void
    {
        $this->item_details = $item_details;
    }

    public function get_item_names(): array
    {
        return $this->item_names;
    }

    public function get_item_details(): array
    {
        return $this->item_details;
    }
}

<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\entities;

use bpmj\wpidea\courses\core\collections\Course_Structure_Item_Collection;
use bpmj\wpidea\courses\core\value_objects\Drip;
use bpmj\wpidea\courses\core\value_objects\Drip_Unit;
use bpmj\wpidea\courses\core\value_objects\Drip_Value;
use bpmj\wpidea\courses\core\value_objects\Parent_ID;
use bpmj\wpidea\learning\course\Page_ID;

class Course_Structure_Item
{
    private ?string $mode;
    private ?Page_ID $page_id;
    private ?Parent_ID $parent_id;
    private ?string $title;
    private ?Drip $drip;
    private ?Course_Structure_Item_Collection $structure_subitems;
    private ?array $variable_prices;
    private ?Page_ID $cloned_from_id = null;

    private function __construct(
        ?string $mode,
        ?Page_ID $page_id,
        ?Parent_ID $parent_id,
        ?string $title,
        ?Drip $drip,
        ?Course_Structure_Item_Collection $structure_subitems = null,
        ?array $variable_prices = null
    ) {
        $this->mode = $mode;
        $this->page_id = $page_id;
        $this->parent_id = $parent_id;
        $this->title = $title;
        $this->drip = $drip;
        $this->structure_subitems = $structure_subitems;
        $this->variable_prices = $variable_prices;
    }

    public static function create(
        ?string $mode,
        ?Page_ID $page_id,
        ?Parent_ID $parent_id,
        ?string $title,
        ?Drip $drip,
        ?Course_Structure_Item_Collection $structure_subitems = null,
        ?array $variable_prices = null
    ): self
    {
        return new self(
            $mode,
            $page_id,
            $parent_id,
            $title,
            $drip,
            $structure_subitems,
            $variable_prices
        );
    }

    public function get_mode(): ?string
    {
        return $this->mode;
    }

    public function get_page_id(): ?Page_ID
    {
        return $this->page_id;
    }

    public function get_parent_id(): ?Parent_ID
    {
        return $this->parent_id;
    }

    public function get_title(): ?string
    {
        return $this->title;
    }

    public function get_drip(): ?Drip
    {
        return $this->drip;
    }

    public function get_structure_subitems(): ?Course_Structure_Item_Collection
    {
        return $this->structure_subitems;
    }

    public function get_variable_prices(): ?array
    {
        return $this->variable_prices;
    }

    public function get_cloned_from_id(): ?Page_ID
    {
        return $this->cloned_from_id;
    }

    public function __clone()
    {
        $this->cloned_from_id = $this->page_id;
        $this->page_id = null;
        $this->parent_id = null;

        $this->drip = is_null($this->drip)
            ? new Drip(new Drip_Value(0), new Drip_Unit(Drip_Unit::MINUTES))
            : new Drip($this->drip->get_drip_value(), $this->drip->get_drip_unit());

        $structure_subitems = $this->get_structure_subitems();
        if (!empty($structure_subitems)) {
            $cloned_structure_subitems = Course_Structure_Item_Collection::create();

            foreach ($structure_subitems as $subitem) {
                $cloned_structure_subitems->add(clone $subitem);
            }

            $this->structure_subitems = $cloned_structure_subitems;
        }
    }
}

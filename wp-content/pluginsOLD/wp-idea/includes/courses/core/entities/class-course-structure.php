<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\entities;

use bpmj\wpidea\courses\core\collections\Course_Structure_Item_Collection;
use bpmj\wpidea\courses\core\value_objects\{Drip, Drip_Unit, Drip_Value, Parent_ID};
use bpmj\wpidea\learning\course\{Course_ID, Page_ID};

class Course_Structure
{
    private Course_ID $course_id;
    private bool $access_to_dripping;
    private ?Drip $drip;
    private Course_Structure_Item_Collection $structure_items;

    private function __construct(
        Course_ID $course_id,
        bool $access_to_dripping,
        ?Drip $drip,
        Course_Structure_Item_Collection $structure_items
    ) {
        $this->course_id = $course_id;
        $this->access_to_dripping = $access_to_dripping;
        $this->drip = $drip;
        $this->structure_items = $structure_items;
    }

    public function to_array(): ?array
    {
        $structure_items = $this->get_structure_items();

        if (!$structure_items) {
            return null;
        }

        $array = [];

        foreach ($structure_items as $structure_item) {
            $page_id = $structure_item->get_page_id() ? $structure_item->get_page_id()->to_int() : null;

            $structure_subitems = $structure_item->get_structure_subitems() ? $this->parse_course_structure_subitem_collection_to_array(
                $structure_item->get_structure_subitems()
            ) : null;

            $drip = $structure_item->get_drip();

            $array[] = array_merge([
                'mode' => $structure_item->get_mode(),
                'created_id' => $page_id,
                'title' => $structure_item->get_title(),
                'drip_value' => $drip ? $drip->get_drip_value()->to_int() : null,
                'drip_unit' => $drip ? $drip->get_drip_unit()->get_value() : null,
                'module' => $structure_subitems,
                'variable_prices' => $structure_item->get_variable_prices(),
            ], $structure_item->get_cloned_from_id() ? ['cloned_from_id' => $structure_item->get_cloned_from_id()->to_int()] : []);
        }

        return $array;
    }

    public function get_course_id(): Course_ID
    {
        return $this->course_id;
    }

    public function set_course_id(Course_ID $course_id): void
    {
        $this->course_id = $course_id;
    }

    public function get_access_to_dripping(): bool
    {
        return $this->access_to_dripping;
    }

    public function get_drip(): ?Drip
    {
        return $this->drip;
    }

    public function get_structure_items(): Course_Structure_Item_Collection
    {
        return $this->structure_items;
    }

    private function parse_course_structure_subitem_collection_to_array(Course_Structure_Item_Collection $structure_subitems): array
    {
        $array = [];

        foreach ($structure_subitems as $structure_subitem) {
            $page_id = $structure_subitem->get_page_id() ? $structure_subitem->get_page_id()->to_int() : null;

            $drip = $structure_subitem->get_drip();

            $array[] = array_merge([
                'mode' => $structure_subitem->get_mode(),
                'created_id' => $page_id,
                'title' => $structure_subitem->get_title(),
                'drip_value' => $drip ? $drip->get_drip_value()->to_int() : '',
                'drip_unit' => $drip ? $drip->get_drip_unit()->get_value() : '',
                'variable_prices' => $structure_subitem->get_variable_prices(),
            ], $structure_subitem->get_cloned_from_id() ? ['cloned_from_id' => $structure_subitem->get_cloned_from_id()->to_int()] : []);
        }

        return $array;
    }

    public static function create(
        Course_ID $course_id,
        bool $access_to_dripping,
        Drip $drip,
        Course_Structure_Item_Collection $structure_items
    ): self {
        return new self(
            $course_id,
            $access_to_dripping,
            $drip,
            $structure_items
        );
    }

    public static function from_array(array $fields): Course_Structure
    {
        $access_to_dripping = !empty($fields['access_to_dripping']) ? $fields['access_to_dripping'] : false;
        $structure_items = self::get_course_structure_item_collection($fields['module'], $fields['drip'], $access_to_dripping);

        return new self(
            $fields['course_id'],
            $access_to_dripping,
            $fields['drip'],
            $structure_items
        );
    }

    private static function get_course_structure_item_collection(array $items, ?Drip $drip, bool $access_to_dripping): Course_Structure_Item_Collection
    {
        $models = [];

        foreach ($items as $item) {
            $models[] = self::parse_course_structure_array_element_to_model(null, $item, $drip, $access_to_dripping);
        }

        return Course_Structure_Item_Collection::create_from_array($models);
    }

    private static function parse_course_structure_array_element_to_model(
        ?Parent_ID $parent_id,
        array $item,
        ?Drip $drip,
        bool $access_to_dripping
    ): Course_Structure_Item {
        $page_id = self::get_page_id($item);
        $drip_unit = $drip ? $drip->get_drip_unit() : new Drip_Unit(Drip_Unit::MINUTES);
        $item_drip_value = isset($item['drip_value']) ? (int)$item['drip_value'] : 0;
        $item_drip_unit = !empty($item['drip_unit']) ? new Drip_Unit($item['drip_unit']) : $drip_unit;

        $subitems = isset($item['module']) ? self::get_course_structure_subitem_collection($item['module'], $page_id, $drip, $access_to_dripping) : null;
        $variable_prices = (!empty($item['variable_prices']) && is_array($item['variable_prices'])) ? $item['variable_prices'] : null;

        $drip_item = new Drip(
            new Drip_Value($access_to_dripping ? $item_drip_value : 0),
            $item_drip_unit
        );

        return Course_Structure_Item::create(
            $item['mode'] ?? null,
            $page_id,
            $parent_id,
            $item['title'] ?? null,
            $drip_item,
            $subitems,
            $variable_prices
        );
    }

    private static function get_course_structure_subitem_collection(
        array $subitems,
        ?Page_ID $page_id,
        ?Drip $drip,
        bool $access_to_dripping
    ): Course_Structure_Item_Collection {
        $models = [];

        $parent_id = $page_id ? new Parent_ID($page_id->to_int()) : null;

        foreach ($subitems as $subitem) {
            $models[] = self::parse_course_structure_array_element_to_model(
                $parent_id,
                $subitem,
                $drip,
                $access_to_dripping
            );
        }

        return Course_Structure_Item_Collection::create_from_array($models);
    }

    private static function get_page_id(array $item): ?Page_ID
    {
        if (isset($item['created_id'])) {
            return new Page_ID((int)$item['created_id']);
        }

        if (isset($item['id'])) {
            return new Page_ID((int)$item['id']);
        }

        return null;
    }
}

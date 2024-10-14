<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\infrastructure\repositories;

use bpmj\wpidea\courses\core\entities\Course_Structure;
use bpmj\wpidea\courses\core\repositories\Interface_Course_Structure_Repository;
use bpmj\wpidea\courses\core\service\Course_Pages_Modifier;
use bpmj\wpidea\courses\core\value_objects\Drip;
use bpmj\wpidea\courses\core\value_objects\Drip_Unit;
use bpmj\wpidea\courses\core\value_objects\Drip_Value;
use bpmj\wpidea\learning\course\Course_ID;

class Course_Structure_Repository implements Interface_Course_Structure_Repository
{
    private const DRIP_VALUE_META_KEY = 'drip_value';
    private const DRIP_UNIT_META_KEY = 'drip_unit';
    private const MODULE_META_KEY = 'module';

    private Course_Pages_Modifier $course_pages_modifier;

    public function __construct(
        Course_Pages_Modifier $course_pages_modifier
    ) {
        $this->course_pages_modifier = $course_pages_modifier;
    }

    public function save(Course_Structure $course_structure): bool
    {
        $course_id = $course_structure->get_course_id();

        if (!$course_id) {
            return false;
        }

        $structure_items = $this->course_pages_modifier->synchronize_with_structure($course_structure);

        if (!$structure_items) {
            return false;
        }

        $drip = $course_structure->get_drip();

        $drip_value = $drip ? $drip->get_drip_value()->to_int() : '';
        $drip_unit = $drip ? $drip->get_drip_unit()->get_value() : '';

        $this->update_course_meta($course_structure->get_course_id(), self::DRIP_VALUE_META_KEY, $drip_value);
        $this->update_course_meta($course_structure->get_course_id(), self::DRIP_UNIT_META_KEY, $drip_unit);
        $this->update_course_meta($course_structure->get_course_id(), self::MODULE_META_KEY, $structure_items);

        return true;
    }

    public function find_by_id(Course_ID $course_id): ?Course_Structure
    {
        if (!$course_id) {
            return null;
        }

        $drip_value = $this->get_course_meta($course_id, self::DRIP_VALUE_META_KEY);
        $drip_unit = $this->get_course_meta($course_id, self::DRIP_UNIT_META_KEY);

        $drip = new Drip(
            new Drip_Value((int)$drip_value),
            new Drip_Unit(!empty($drip_unit) ? $drip_unit : Drip_Unit::MINUTES)
        );

        $structure_items = $this->get_structure_array_elements($course_id);

        $structure = [
            'course_id' => $course_id,
            'access_to_dripping' => true,
            'drip' => $drip,
            'module' => $structure_items
        ];

        return Course_Structure::from_array($structure);
    }

    private function get_structure_array_elements(Course_ID $course_id): array
    {
        $module = $this->get_course_meta($course_id, self::MODULE_META_KEY);
        $structure_items = !empty($module) ? $module : [];

        foreach ($structure_items as $module_order => $module) {
            $page_id = (int)($module['created_id'] ?? $module['id']);
            if (!$this->exist($page_id)) {
                unset($structure_items[$module_order]);
                continue;
            }

            $structure_items[$module_order]['title'] = $this->get_title($page_id);

            if (!isset($module['module'])) {
                continue;
            }

            foreach ($module['module'] as $lesson_order => $lesson) {
                $item = $structure_items[$module_order]['module'][$lesson_order];
                $page_id = (int)($item['created_id'] ?? $item['id']);

                if (!$this->exist($page_id)) {
                    unset($structure_items[$module_order]['module'][$lesson_order]);
                    continue;
                }

                $structure_items[$module_order]['module'][$lesson_order]['title'] = $this->get_title($page_id);
            }
        }

        return $structure_items;
    }

    private function get_title(int $page_id): string
    {
        return get_the_title($page_id);
    }

    private function get_course_meta(Course_ID $id, string $key)
    {
        return get_post_meta($id->to_int(), $key, true);
    }

    private function update_course_meta(Course_ID $id, string $key, $value): void
    {
        update_post_meta($id->to_int(), $key, $value);
    }

    private function exist($page_id): bool
    {
        return !is_null(get_post($page_id));
    }
}

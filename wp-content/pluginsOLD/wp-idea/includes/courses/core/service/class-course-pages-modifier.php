<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\service;

use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\entities\Course_Structure;
use bpmj\wpidea\learning\course\{Course_ID, Page_ID};
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\model\Product_ID;

class Course_Pages_Modifier
{
    private const COURSE_ID_META_KEY = 'course_id';
    private const PRODUCT_ID_META_KEY = 'product_id';
    private const MODULE_META_KEY = 'module';
    private const REDIRECT_PAGE_META_KEY = '_bpmj_eddpc_redirect_page';
    private const REDIRECT_URL_META_KEY = '_bpmj_eddpc_redirect_url';
    private const ACCESS_START_META_KEY = '_bpmj_eddpc_access_start';

    private Courses $courses;
    private Interface_Product_API $product_api;

    public function __construct(
        Interface_Product_API $product_api,
        Courses $courses
    ) {
        $this->product_api = $product_api;
        $this->courses = $courses;
    }

    public function synchronize_with_structure(Course_Structure $course_structure): ?array
    {
        $course_id = $course_structure->get_course_id();

        if (!$course_id) {
            return null;
        }

        $page_id = (int)$this->get_course_meta($course_id, self::COURSE_ID_META_KEY);
        $product_id = (int)$this->get_course_meta($course_id, self::PRODUCT_ID_META_KEY);

        if (!$page_id || !$product_id) {
            return null;
        }

        $structure_items = $course_structure->to_array();

        if (!$structure_items) {
            return null;
        }

        $this->delete_removed_pages($course_structure);

        $page_id = new Page_ID($page_id);
        $product_id = new Product_ID($product_id);

        foreach ($structure_items as $module_order => $module) {
            if (isset($module['created_id'])) {
                $module_id = $this->update_module(
                    $module,
                    $module_order,
                    $page_id,
                    $course_structure->get_course_id(),
                    $product_id
                );
            } else {
                $module_id = $this->create_new_module(
                    $module,
                    $module_order,
                    $page_id,
                    $course_structure->get_course_id(),
                    $product_id
                );
            }

            $structure_items[$module_order]['created_id'] = $module_id;
            $structure_items[$module_order]['id'] = $module_id;
            unset($structure_items[$module_order]['title']);
            unset($structure_items[$module_order]['content']);

            if (isset($module['module'])) {
                foreach ($module['module'] as $lesson_order => $lesson) {
                    if (isset($lesson['created_id'])) {
                        $lesson_id = $this->update_lesson(
                            $module_id,
                            $lesson,
                            $lesson_order,
                            $page_id,
                            $course_structure->get_course_id(),
                            $product_id
                        );
                    } else {
                        $lesson_id = $this->create_new_lesson(
                            $module_id,
                            $lesson,
                            $lesson_order,
                            $page_id,
                            $course_structure->get_course_id(),
                            $product_id
                        );
                    }

                    $structure_items[$module_order]['module'][$lesson_order]['created_id'] = $lesson_id;
                    $structure_items[$module_order]['module'][$lesson_order]['id'] = $lesson_id;
                    unset($structure_items[$module_order]['module'][$lesson_order]['title']);
                    unset($structure_items[$module_order]['module'][$lesson_order]['content']);
                }
            }
        }

        return $structure_items;
    }

    private function delete_removed_pages(Course_Structure $course_structure): void
    {
        $course_id = $course_structure->get_course_id();

        if (!$course_id) {
            return;
        }

        $structure_items = $course_structure->to_array();

        if (!$structure_items) {
            return;
        }

        $old_pages_ids = [];
        $old_structure_items = $this->get_course_meta($course_id, self::MODULE_META_KEY);

        if ($old_structure_items) {
            foreach ($old_structure_items as $item) {
                $old_pages_ids[] = $item['id'];

                if (!isset($item['module'])) {
                    continue;
                }

                foreach ($item['module'] as $subitem) {
                    $old_pages_ids[] = $subitem['id'];
                }
            }
        }

        $current_pages_ids = [];

        foreach ($structure_items as $item) {
            if (isset($item['created_id'])) {
                $current_pages_ids[] = $item['created_id'];
            }

            if (!isset($item['module'])) {
                continue;
            }

            foreach ($item['module'] as $subitem) {
                if (!isset($subitem['created_id'])) {
                    continue;
                }
                $current_pages_ids[] = $subitem['created_id'];
            }
        }

        $pages_to_delete = array_diff($old_pages_ids, $current_pages_ids);
        foreach ($pages_to_delete as $page_id) {
            $this->delete_post((int) $page_id);
        }
    }

    private function update_module(
        array $module,
        int $module_order,
        Page_ID $page_id,
        Course_ID $course_id,
        Product_ID $product_id
    ): int {
        $module_restricted_to = $this->get_module_restricted($product_id, $module);

        $access_start = $this->get_access_start($page_id);

        $this->update_post([
            'ID' => $module['created_id'],
            'post_title' => $module['title'],
            'post_parent' => $page_id->to_int(),
            'menu_order' => $module_order,
            'meta_input' => [
                '_bpmj_eddpc_redirect_page' => $this->get_redirect_page($page_id),
                '_bpmj_eddpc_redirect_url' => $this->get_redirect_url($page_id),
                '_bpmj_eddpc_access_start_enabled' => !empty($access_start),
                '_bpmj_eddpc_access_start' => $access_start,
                '_bpmj_eddcm' => $course_id->to_int(),
                '_bpmj_eddpc_drip_value' => $module['drip_value'] ?? '',
                '_bpmj_eddpc_drip_unit' => $module['drip_unit'] ?? '',
                '_bpmj_eddpc_restricted_to' => $module_restricted_to,
            ],
        ]);

        return (int)$module['created_id'];
    }

    private function create_new_module(
        array $module,
        int $module_order,
        Page_ID $page_id,
        Course_ID $course_id,
        Product_ID $product_id
    ): int {
        $module_restricted_to = $this->get_module_restricted($product_id, $module);

        $access_start = $this->get_access_start($page_id);

        $redirect = [
            'page' => $this->get_redirect_page($page_id),
            'url' => $this->get_redirect_url($page_id),
        ];

        return $this->courses->insert($page_id->to_int(), $module, $product_id->to_int(), $module_order, $redirect, [
            '_bpmj_eddpc_access_start_enabled' => !empty($access_start),
            '_bpmj_eddpc_access_start' => $access_start,
            '_bpmj_eddcm' => $course_id->to_int(),
            '_bpmj_eddpc_drip_value' => $module['drip_value'] ?? '',
            '_bpmj_eddpc_drip_unit' => $module['drip_unit'] ?? '',
            '_bpmj_eddpc_restricted_to' => $module_restricted_to,
        ]);
    }

    private function update_lesson(
        int $module_id,
        array $lesson,
        int $lesson_order,
        Page_ID $page_id,
        Course_ID $course_id,
        Product_ID $product_id
    ): int {
        $lesson_restricted_to = $this->get_module_restricted($product_id, $lesson);

        $access_start = $this->get_access_start($page_id);

        $this->update_post([
            'ID' => $lesson['created_id'],
            'post_title' => $lesson['title'],
            'post_parent' => $module_id,
            'menu_order' => $lesson_order,
            'meta_input' => [
                '_bpmj_eddpc_redirect_page' => $this->get_redirect_page($page_id),
                '_bpmj_eddpc_redirect_url' => $this->get_redirect_url($page_id),
                '_bpmj_eddpc_access_start_enabled' => !empty($access_start),
                '_bpmj_eddpc_access_start' => $access_start,
                '_bpmj_eddcm' => $course_id->to_int(),
                '_bpmj_eddpc_drip_value' => $lesson['drip_value'] ?? '',
                '_bpmj_eddpc_drip_unit' => $lesson['drip_unit'] ?? '',
                '_bpmj_eddpc_restricted_to' => $lesson_restricted_to,
            ],
        ]);

        return (int)$lesson['created_id'];
    }

    private function create_new_lesson(
        int $module_id,
        array $lesson,
        int $lesson_order,
        Page_ID $page_id,
        Course_ID $course_id,
        Product_ID $product_id
    ): int {
        $lesson_restricted_to = $this->get_module_restricted($product_id, $lesson);

        $access_start = $this->get_access_start($page_id);

        $redirect = [
            'page' => $this->get_redirect_page($page_id),
            'url' => $this->get_redirect_url($page_id),
        ];

        return $this->courses->insert($module_id, $lesson, $product_id->to_int(), $lesson_order, $redirect, [
            '_bpmj_eddpc_access_start_enabled' => !empty($access_start),
            '_bpmj_eddpc_access_start' => $access_start,
            '_bpmj_eddcm' => $course_id->to_int(),
            '_bpmj_eddpc_drip_value' => $lesson['drip_value'] ?? '',
            '_bpmj_eddpc_drip_unit' => $lesson['drip_unit'] ?? '',
            '_bpmj_eddpc_restricted_to' => $lesson_restricted_to,
        ]);
    }

    private function get_module_restricted(Product_ID $product_id, array $structure_item): array
    {
        $price_variants_DTO = $this->product_api->get_price_variants($product_id->to_int());

        if (!$price_variants_DTO->has_pricing_variants) {
            return [
                ['download' => $product_id->to_int(), 'price_id' => 'all']
            ];
        }

        $module_restricted_to = [];

        if (empty($structure_item['variable_prices']) || !is_array($structure_item['variable_prices'])) {
            return [
                ['download' => $product_id->to_int(), 'price_id' => -1]
            ];
        }

        foreach ($structure_item['variable_prices'] as $price_id) {
            $module_restricted_to[] = ['download' => $product_id->to_int(), 'price_id' => $price_id];
        }

        return $module_restricted_to;
    }

    private function get_redirect_page(Page_ID $page_id): string
    {
        return $this->get_page_meta($page_id, self::REDIRECT_PAGE_META_KEY);
    }

    private function get_redirect_url(Page_ID $page_id): string
    {
        return $this->get_page_meta($page_id, self::REDIRECT_URL_META_KEY);
    }

    private function get_access_start(Page_ID $page_id): string
    {
        return $this->get_page_meta($page_id, self::ACCESS_START_META_KEY);
    }

    private function get_page_meta(Page_ID $id, string $key)
    {
        return get_post_meta($id->to_int(), $key, true);
    }

    private function get_course_meta(Course_ID $id, string $key)
    {
        return get_post_meta($id->to_int(), $key, true);
    }

    private function delete_post(int $id): void
    {
        if(0 === $id) {
            return;
        }
        wp_trash_post($id);
    }

    private function update_post(array $args): void
    {
        wp_update_post($args);
    }
}
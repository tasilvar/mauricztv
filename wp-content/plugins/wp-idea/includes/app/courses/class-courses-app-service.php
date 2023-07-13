<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\courses;

use bpmj\wpidea\Courses;
use bpmj\wpidea\courses\core\dto\Course_DTO;
use bpmj\wpidea\courses\core\entities\Course_Structure;
use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\courses\core\repositories\Interface_Course_Structure_Repository;
use bpmj\wpidea\courses\core\repositories\Interface_Course_With_Product_Repository;
use bpmj\wpidea\courses\core\service\Course_Creator_Service;
use bpmj\wpidea\courses\core\service\Course_Modifier;
use bpmj\wpidea\courses\core\service\Course_Pages_Modifier;
use bpmj\wpidea\courses\core\service\Course_Structure_Rebuilder;
use bpmj\wpidea\courses\core\service\Product_Modifier;
use bpmj\wpidea\learning\course\{Course_ID, Interface_Readable_Course_Repository};
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\service\Product_Creator_Service;

class Courses_App_Service
{
    private Interface_Readable_Course_Repository $readable_course_repository;
    private Product_Creator_Service $product_creator_service;
    private Course_Creator_Service $course_creator_service;
    private Interface_Course_With_Product_Repository $course_with_product_repository;
    private Interface_Course_Structure_Repository $course_structure_repository;
    private Course_Structure_Rebuilder $course_structure_rebuilder;
    private Course_Pages_Modifier $course_pages_modifier;
    private Product_Modifier $product_modifier;
    private Course_Modifier $course_modifier;
    private Courses $courses;
    private Product_Events $product_events;

    public function __construct(
        Interface_Readable_Course_Repository $readable_course_repository,
        Product_Creator_Service $product_creator_service,
        Course_Creator_Service $course_creator_service,
        Interface_Course_With_Product_Repository $course_with_product_repository,
        Interface_Course_Structure_Repository $course_structure_repository,
        Course_Structure_Rebuilder $course_structure_rebuilder,
        Course_Pages_Modifier $course_pages_modifier,
        Product_Modifier $product_modifier,
        Course_Modifier $course_modifier,
        Courses $courses,
        Product_Events $product_events
    ) {
        $this->readable_course_repository = $readable_course_repository;
        $this->product_creator_service = $product_creator_service;
        $this->course_creator_service = $course_creator_service;
        $this->course_with_product_repository = $course_with_product_repository;
        $this->course_structure_repository = $course_structure_repository;
        $this->course_structure_rebuilder = $course_structure_rebuilder;
        $this->course_pages_modifier = $course_pages_modifier;
        $this->product_modifier = $product_modifier;
        $this->course_modifier = $course_modifier;
        $this->courses = $courses;
        $this->product_events = $product_events;
    }

    public function save_course(Product_DTO $product_dto, Course_DTO $course_dto): Product_ID
    {
        $product_id = $this->product_creator_service->save_product($product_dto);

        $course_dto->product_id = $product_id->to_int();
        $this->course_creator_service->save_course($course_dto);

        return $product_id;
    }

    public function get_variable_prices(int $post_id): string
    {
        return $this->course_creator_service->get_variable_prices($post_id);
    }

    public function save_variable_prices(int $product_id, array $fields): array
    {
        $this->emit_variable_prices_updated_event($product_id, $fields);

        return $this->course_creator_service->save_variable_prices($product_id, $fields);
    }

    public function get_variable_prices_add_to_cart_links_html($product_id): ?string
    {
        return $this->course_creator_service->get_variable_prices_add_to_cart_links_html($product_id);
    }

    public function save_course_structure(array $fields): bool
    {
        $course_structure = Course_Structure::from_array($fields);

        $this->emit_course_structure_updated_event($course_structure->get_course_id(), $fields);

        return $this->course_structure_repository->save($course_structure);
    }

    public function update_course_pages(Course_ID $course_id): void
    {
        $course_structure = $this->course_structure_repository->find_by_id($course_id);

        if (!$course_structure) {
            return;
        }

        $this->course_pages_modifier->synchronize_with_structure($course_structure);
    }

    public function rebuild_course_structure(Course_ID $course_id): void
    {
        $course_structure = $this->course_structure_repository->find_by_id($course_id);

        if (!$course_structure) {
            return;
        }

        $rebuilded_course_structure = $this->course_structure_rebuilder->get_rebuilded_structure($course_structure);

        if (!$rebuilded_course_structure) {
            return;
        }

        $this->product_modifier->update_purchase_limit_for_variable_pricing($course_id);

        $this->course_structure_repository->save($rebuilded_course_structure);
    }

    public function find_course(Course_ID $course_id): ?Course_With_Product
    {
        return $this->course_with_product_repository->find($course_id);
    }

    public function disable_sale(Course_ID $course_id): void
    {
        $course_with_product = $this->course_with_product_repository->find($course_id);

        if (!$course_with_product) {
            return;
        }

        $this->course_modifier->disable_sale_for_variable_pricing($course_id, $course_with_product->get_product_id());
    }

    public function is_course_panel_page(int $page_id): bool
    {
        return $this->readable_course_repository->is_course_panel_page($page_id);
    }

    private function emit_variable_prices_updated_event(int $product_id, array $fields): void
    {
        $old_value = $this->course_creator_service->get_variable_prices_to_array($product_id);

        $course = $this->courses->get_course_by_product($product_id);

        $this->product_events->emit_course_variable_prices_updated_event($old_value, $fields, $course->ID);
    }

    private function emit_course_structure_updated_event(Course_ID $course_id, array $fields): void
    {
        $course_structure = $this->course_structure_repository->find_by_id($course_id);

        $old_value = [];

        if ($course_structure) {
            $old_value = $course_structure->to_array();
        }

        $this->product_events->emit_course_structure_updated_event($old_value, $fields, $course_id->to_int());
    }
}
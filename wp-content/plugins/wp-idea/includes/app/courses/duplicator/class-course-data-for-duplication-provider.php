<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\courses\duplicator;

use bpmj\wpidea\courses\core\collections\Course_Structure_Item_Collection;
use bpmj\wpidea\courses\core\dto\Course_DTO;
use bpmj\wpidea\courses\core\dto\Course_To_Dto_Mapper;
use bpmj\wpidea\courses\core\entities\Course_Structure;
use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\courses\core\value_objects\Drip;
use bpmj\wpidea\courses\core\value_objects\Drip_Unit;
use bpmj\wpidea\courses\core\value_objects\Drip_Value;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\sales\product\dto\Product_DTO;
use bpmj\wpidea\sales\product\dto\Product_To_DTO_Mapper;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\translator\Interface_Translator;

class Course_Data_For_Duplication_Provider
{
    private Course_To_Dto_Mapper $course_to_dto_mapper;
    private Product_To_DTO_Mapper $product_to_dto_mapper;
    private Interface_Translator $translator;

    public function __construct(
        Course_To_Dto_Mapper $course_to_dto_mapper,
        Product_To_DTO_Mapper $product_to_dto_mapper,
        Interface_Translator $translator
    )
    {
        $this->course_to_dto_mapper = $course_to_dto_mapper;
        $this->product_to_dto_mapper = $product_to_dto_mapper;
        $this->translator = $translator;
    }

    public function prepare_course_dto(Course_With_Product $course): ?Course_DTO
    {
        $course_dto = $this->get_course_dto($course);

        if(!$course_dto) {
            return null;
        }

        return $this->modify_course_dto_for_duplication($course_dto);
    }

    public function prepare_product_dto(Product $product): ?Product_DTO
    {
        $product_dto = $this->get_product_dto($product);

        if(!$product_dto) {
            return null;
        }

        return $this->modify_product_dto_for_duplication($product_dto);
    }

    public function prepare_course_structure(
        Course_Structure $course_structure,
        Course_ID $duplicated_course_id
    ): Course_Structure
    {
        $drip = is_null($course_structure->get_drip())
            ? new Drip(new Drip_Value(0), new Drip_Unit(Drip_Unit::MINUTES))
            : new Drip($course_structure->get_drip()->get_drip_value(), $course_structure->get_drip()->get_drip_unit());

        return Course_Structure::create(
            $duplicated_course_id,
            $course_structure->get_access_to_dripping(),
            $drip,
            $this->clone_course_structure_items($course_structure->get_structure_items())
        );
    }

    private function clone_course_structure_items(Course_Structure_Item_Collection $structure_items): Course_Structure_Item_Collection
    {
        $cloned_structure_items = Course_Structure_Item_Collection::create();

        foreach($structure_items as $item){
            $cloned_structure_items->add(clone $item);
        }

        return $cloned_structure_items;
    }

    private function get_course_dto(Course_With_Product $course): ?Course_DTO
    {
        $course_dto = $this->course_to_dto_mapper->map_course_to_dto($course);

        if(!$course_dto->product_id) {
            return null;
        }

        return $course_dto;
    }

    private function get_product_dto(Product $product): ?Product_DTO
    {
        return $this->product_to_dto_mapper->map_product_to_dto($product);
    }

    private function get_duplicated_course_suffix(): string
    {
        return ' ' . $this->translator->translate('courses.duplicated_course_suffix');
    }

    private function modify_product_dto_for_duplication(Product_DTO $product_dto): Product_DTO
    {
        $duplicated_course_name = $product_dto->name . $this->get_duplicated_course_suffix();

        $product_dto->id = null;
        $product_dto->linked_resource_id = null;
        $product_dto->name = $duplicated_course_name;
        $product_dto->slug = null;
        $product_dto->sales_disabled = true;
        $product_dto->hide_from_list = true;
        $product_dto->purchase_limit_items_left = $product_dto->purchase_limit;

        return $product_dto;
    }

    private function modify_course_dto_for_duplication(Course_DTO $course_dto): Course_DTO
    {
        $course_dto->cloned_from_id = $course_dto->id;
        $course_dto->id = null;
        $course_dto->product_id = null;
        $course_dto->page_id = null;

        return $course_dto;
    }
}
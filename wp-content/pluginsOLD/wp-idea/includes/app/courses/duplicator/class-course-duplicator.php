<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\app\courses\duplicator;

use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\courses\core\repositories\Interface_Course_Structure_Repository;
use bpmj\wpidea\courses\core\repositories\Interface_Course_With_Product_Repository;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\courses\core\dto\Course_DTO;
use bpmj\wpidea\sales\product\dto\Product_DTO;

class Course_Duplicator
{
    private Interface_Product_Repository $product_repository;
    private Courses_App_Service $courses_app_service;
    private Interface_Course_Structure_Repository $course_structure_repository;
    private Interface_Course_With_Product_Repository $course_with_product_repository;
    private Course_Data_For_Duplication_Provider $course_data_for_duplication_provider;

    public function __construct(
        Interface_Product_Repository $product_repository,
        Courses_App_Service $courses_app_service,
        Interface_Course_Structure_Repository $course_structure_repository,
        Interface_Course_With_Product_Repository $course_with_product_repository,
        Course_Data_For_Duplication_Provider $course_data_for_duplication_provider
    ) {
        $this->product_repository = $product_repository;
        $this->courses_app_service = $courses_app_service;
        $this->course_structure_repository = $course_structure_repository;
        $this->course_with_product_repository = $course_with_product_repository;
        $this->course_data_for_duplication_provider = $course_data_for_duplication_provider;
    }

    public function duplicate_course(Course_ID $source_course_id): ?Course_ID
    {
        $course = $this->get_course($source_course_id);

        if(!$course) {
            return null;
        }

        $product = $this->get_product($course->get_product_id());

        if(!$product) {
            return null;
        }

        $course_dto = $this->course_data_for_duplication_provider->prepare_course_dto($course);

        if(!$course_dto) {
            return null;
        }

        $product_dto = $this->course_data_for_duplication_provider->prepare_product_dto($product);

        if(!$product_dto) {
            return null;
        }

        return $this->do_duplication($product_dto, $course_dto, $source_course_id);
    }

    private function get_course(Course_ID $course_id): ?Course_With_Product
    {
        $course = $this->courses_app_service->find_course($course_id);

        if(!$course) {
            return null;
        }

        return $course;
    }

    private function get_product(Product_ID $product_id): ?Product
    {
        return $this->product_repository->find($product_id);
    }

    private function do_duplication(
        Product_DTO $product_dto,
        Course_DTO $course_dto,
        Course_ID $source_course_id
    ): ?Course_ID {
        $new_product_id = $this->courses_app_service->save_course(
            $product_dto,
            $course_dto
        );
        $new_course_id = $this->course_with_product_repository->get_course_id_by_product($new_product_id);

        $source_course_structure = $this->course_structure_repository->find_by_id($source_course_id);

        if ($source_course_structure && $new_course_id) {
            $new_course_structure = $this->course_data_for_duplication_provider->prepare_course_structure(
                $source_course_structure,
                $new_course_id
            );
            $this->course_structure_repository->save($new_course_structure);
        }

        if($product_dto->variable_prices) {
            $fields = [
                '_edd_price_options_mode' => $this->product_repository->get_meta($new_product_id, '_edd_price_options_mode'),
                'edd_variable_prices' => $product_dto->variable_prices,
                '_edd_default_price_id' => $this->product_repository->get_meta($new_product_id, '_edd_default_price_id')
            ];
            $this->courses_app_service->save_variable_prices($new_product_id->to_int(), $fields);
        }

        return $new_course_id;
    }
}
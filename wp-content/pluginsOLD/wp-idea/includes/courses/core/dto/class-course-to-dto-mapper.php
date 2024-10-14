<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\dto;

use bpmj\wpidea\courses\core\entities\Course_With_Product;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\sales\product\model\Product_ID;

class Course_To_Dto_Mapper
{
    public function map_course_to_dto(Course_With_Product $course): Course_DTO
    {
        $dto = new Course_DTO();
        $dto->id = $course->get_id() ? $course->get_id()->to_int() : null;
        $dto->product_id = $course->get_product_id()->to_int();
        $dto->page_id = $course->get_page_id() ? $course->get_page_id()->to_int() : null;
        $dto->redirect_page = $course->get_redirect_page();
        $dto->redirect_url = $course->get_redirect_url();
        $dto->certificate_template_id = $course->get_certificate_template_id();
        $dto->drip_value = $course->get_drip_value();
        $dto->drip_unit = $course->get_drip_unit();
        $dto->post_date = $course->get_post_date() ? $course->get_post_date()->format('Y-m-d H:i:s') : null;
        $dto->post_date_gmt = $course->get_post_date_gmt() ? $course->get_post_date_gmt()->format('Y-m-d H:i:s') : null;

        return $dto;
    }
}
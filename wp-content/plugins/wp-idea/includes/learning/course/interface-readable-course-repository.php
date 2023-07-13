<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\user\Interface_User;
use bpmj\wpidea\user\User;

interface Interface_Readable_Course_Repository
{
    public function find_by_id(Course_ID $id): ?Course;

    public function find_by_product_id(ID $product_id): ?Course;

    public function find_by_certificate_id(ID $certificate_id): ?Course;

    public function find_by_page_id(ID $page): ?Course;

    public function find_by_user(Interface_User $user): Course_Collection;

    public function find_by_user_id(int $user_id): Course_Collection;

    public function find_all(): Course_Collection;

    public function get_course_panel_id(Course_ID $id): int;

    public function get_course_price_for_user(Course_ID $course_id, User $user): ?string;

    public function is_course_panel_page(int $page_id): bool;

    public function is_course_lesson_page(int $page_id): bool;

    public function is_course_test_page(int $page_id): bool;

    public function is_course_module_page(int $page_id): bool;

    public function count(): int;
}

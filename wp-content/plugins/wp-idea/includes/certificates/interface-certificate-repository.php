<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\user\User;
use DateTime;

interface Interface_Certificate_Repository
{
    public function find_by_criteria(
        Certificate_Query_Criteria $criteria,
        int $page = 0,
        int $per_page = -1
    ): Certificate_Collection;

    public function count_by_criteria(Certificate_Query_Criteria $criteria): int;

    public function find_by_id(Certificate_ID $id): ?Certificate;

    public function delete(Certificate $certificate): void;

    public function update_regenerated_date(Certificate $certificate, DateTime $date_time): Certificate;

    public function create_certificate(Course $course, User $user, DateTime $date_generated = null, ?string $certificate_number = null): ?Certificate;

    public function update_certificate_pdf_content(Certificate $certificate, string $pdf_content): Certificate;

    public function get_created_at(Certificate $certificate): DateTime;

}
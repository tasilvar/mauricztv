<?php namespace bpmj\wpidea\certificates\regenerator;

use bpmj\wpidea\certificates\generator\Certificate_Pdf_Source_Generator;
use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use DateTime;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\certificates\Interface_Certificate;

class Certificate_Wp_Regenerator implements Interface_Certificate_Regenerator
{
    private Interface_Certificate_Repository $certificate_repository;
    private Interface_Readable_Course_Repository $course_repository;
    private Interface_User_Repository $user_repository;
    private Certificate_Pdf_Source_Generator $pdf_source_generator;

    public function __construct(
        Interface_Certificate_Repository $certificate_repository,
        Interface_Readable_Course_Repository $course_repository,
        Interface_User_Repository $user_repository,
        Certificate_Pdf_Source_Generator $pdf_source_generator
    ) {
        $this->certificate_repository = $certificate_repository;
        $this->course_repository      = $course_repository;
        $this->user_repository        = $user_repository;
        $this->pdf_source_generator   = $pdf_source_generator;
    }

    public function regenerate(Interface_Certificate $certificate): ?Interface_Certificate
    {
        $certificate_date_created = $this->certificate_repository->get_created_at($certificate);
        $this->certificate_repository->delete($certificate);

        $course = $this->course_repository->find_by_id($certificate->get_course_id());
        $user   = $this->user_repository->find_by_id($certificate->get_user_id());

        if ( ! ($course || $user)) {
            return null;
        }

        $new_cert = $this->certificate_repository->create_certificate($course, $user, $certificate_date_created, $certificate->get_certificate_number() ? $certificate->get_certificate_number()->get_value() : null);
        $pdf_html = $this->pdf_source_generator->get_pdf_source($course, $user, $certificate_date_created);
        $this->certificate_repository->update_certificate_pdf_content($new_cert, $pdf_html);

        return $this->certificate_repository->update_regenerated_date(
            $this->certificate_repository->find_by_id($new_cert->get_id()),
            new \DateTime('now')
        );
    }
}
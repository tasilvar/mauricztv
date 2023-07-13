<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\user\User;

class Certificate_Display_Model
{
    private const NOT_AVAILABLE = '-';

    private int $id;
    private string $course_name;
    private string $user_full_name;
    private string $user_email;
    private string $certificate_number;
    private string $created_at;

    public function __construct(Certificate $certificate, ?Course $course, ?User $user)
    {

        $this->id = $certificate->get_id()->to_int();
        $this->course_name = $course ? $course->get_title() : self::NOT_AVAILABLE;
        $this->user_full_name = $user && $user->full_name() ? $user->full_name()->get_full_name() : self::NOT_AVAILABLE;
        $this->user_email = $user ? $user->get_email() : self::NOT_AVAILABLE;
        $this->certificate_number = $certificate->get_certificate_number() ? $certificate->get_certificate_number()->get_value() : self::NOT_AVAILABLE;
        $this->created_at = date_format($certificate->get_created(), 'Y-m-d');
    }

    public function to_array(): array
    {
        return [
            'id' => $this->get_id(),
            'course_name' => $this->get_course_name(),
            'user_full_name' => $this->get_user_full_name(),
            'user_email' => $this->get_user_email(),
            'certificate_number' => $this->get_certificate_number(),
            'created_at' => $this->get_created_at()
        ];
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_course_name(): string
    {
        return $this->course_name;
    }

    public function get_user_full_name(): string
    {
        return $this->user_full_name;
    }

    public function get_user_email(): ?string
    {
        return $this->user_email;
    }

    public function get_certificate_number(): string
    {
        return $this->certificate_number;
    }

    public function get_created_at(): string
    {
        return $this->created_at;
    }
}
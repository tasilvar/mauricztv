<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz;

use bpmj\wpidea\data_types\personal_data\Full_Name;

class Resolved_Quiz
{
    public const RESULT_PASSED = 'passed';
    public const RESULT_FAILED = 'failed';
    public const RESULT_NOT_RATED_YET = 'not_rated_yet';

    private int $id;
    private string $title;
    private \DateTimeImmutable $completed_at;
    private int $points;
    private int $points_total;
    private string $result;
    private Full_Name $user_full_name;
    private string $user_email;
    private int $course_id;
    private string $course_title;
    private int $quiz_id;

    public function __construct(
        int $id,
        int $quiz_id,
        int $course_id,
        string $title,
        string $course_title,
        \DateTimeImmutable $completed_at,
        int $points,
        int $points_total,
        string $result,
        Full_Name $user_full_name,
        string $user_email
    )
    {
        $this->id = $id;
        $this->quiz_id = $quiz_id;
        $this->course_id = $course_id;
        $this->title = $title;
        $this->course_title = $course_title;
        $this->completed_at = $completed_at;
        $this->points = $points;
        $this->points_total = $points_total;
        $this->result = $result;
        $this->user_full_name = $user_full_name;
        $this->user_email = $user_email;
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function get_completed_at(): \DateTimeImmutable
    {
        return $this->completed_at;
    }

    public function get_points(): int
    {
        return $this->points;
    }

    public function get_points_total(): int
    {
        return $this->points_total;
    }

    public function get_result(): string
    {
        return $this->result;
    }

    public function get_user_full_name(): string
    {
        return $this->user_full_name->get_full_name();
    }

    public function get_user_first_name(): string
    {
        return $this->user_full_name->get_first_name();
    }

    public function get_user_last_name(): string
    {
        return $this->user_full_name->get_last_name();
    }

    public function get_user_email(): string
    {
        return $this->user_email;
    }

    public function get_course_id(): int
    {
        return $this->course_id;
    }

    public function get_course_title(): string
    {
        return $this->course_title;
    }

    public function get_quiz_id(): int
    {
        return $this->quiz_id;
    }
}